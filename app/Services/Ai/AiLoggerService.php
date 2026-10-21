<?php

namespace App\Services\Ai;

use App\Models\AiLog;
use App\Models\AiProvider;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Throwable;

class AiLoggerService
{
    /**
     * Record an AI execution into the ai_logs database table.
     */
    public static function log(
        AiProvider $provider,
        string $useCase,
        array $messages,
        ?string $response,
        string $status = 'success',
        ?string $errorMessage = null,
        ?int $latencyMs = null,
        array $options = []
    ): ?AiLog {
        try {
            // Determine user context
            $student = null;
            $admin = null;
            $userType = 'system';
            $userName = null;
            $userEmail = null;

            // 1. Check explicit options
            if (! empty($options['student_id'])) {
                $student = \App\Models\Student::find($options['student_id']);
            } elseif (! empty($options['student']) && $options['student'] instanceof \App\Models\Student) {
                $student = $options['student'];
            }

            if (! empty($options['admin_id'])) {
                $admin = \App\Models\Admin::find($options['admin_id']);
            } elseif (! empty($options['admin']) && $options['admin'] instanceof \App\Models\Admin) {
                $admin = $options['admin'];
            }

            // 2. Fall back to current authenticated user
            if (! $student && Auth::guard('web')->check()) {
                $student = Auth::guard('web')->user();
            }

            if (! $admin && Auth::guard('admin')->check()) {
                $admin = Auth::guard('admin')->user();
            }

            if ($student) {
                $userType = 'student';
                $userName = $student->name;
                $userEmail = $student->email;
            } elseif ($admin) {
                $userType = 'admin';
                $userName = $admin->name;
                $userEmail = $admin->email;
            } elseif (request() && request()->ip()) {
                $userType = 'guest';
            }

            // Estimate tokens if not provided
            $promptText = '';
            foreach ($messages as $m) {
                $content = $m['content'] ?? '';
                if (is_array($content)) {
                    $content = json_encode($content);
                }
                $promptText .= ($m['role'] ?? 'user').': '.(string) $content."\n";
            }

            $inputTokens = $options['input_tokens'] ?? self::estimateTokens($promptText);
            $outputTokens = $options['output_tokens'] ?? self::estimateTokens((string) $response);
            $totalTokens = $inputTokens + $outputTokens;

            // Safe truncate prompt and response to prevent exceeding mediumText/longText
            $safePrompt = mb_substr(json_encode($messages, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT), 0, 65000);
            $safeResponse = $response !== null ? mb_substr($response, 0, 65000) : null;
            $safeError = $errorMessage !== null ? mb_substr($errorMessage, 0, 2000) : null;

            return AiLog::create([
                'student_id' => $student?->id,
                'admin_id' => $admin?->id,
                'user_type' => $userType,
                'user_name' => $userName,
                'user_email' => $userEmail,
                'provider_id' => $provider->id,
                'provider_name' => $provider->name,
                'provider_type' => $provider->type,
                'model_name' => $provider->model_name,
                'use_case' => $useCase,
                'prompt' => $safePrompt,
                'response' => $safeResponse,
                'status' => $status,
                'error_message' => $safeError,
                'input_tokens' => $inputTokens,
                'output_tokens' => $outputTokens,
                'total_tokens' => $totalTokens,
                'latency_ms' => $latencyMs,
                'ip_address' => request()?->ip(),
            ]);
        } catch (Throwable $e) {
            Log::warning('Failed to write AI log entry: '.$e->getMessage());
            return null;
        }
    }

    private static function estimateTokens(string $text): int
    {
        $len = mb_strlen(trim($text));
        if ($len === 0) {
            return 0;
        }
        return (int) max(1, ceil($len / 3.8));
    }
}
