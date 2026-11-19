<?php

namespace App\Services;

use App\Models\Student;
use Illuminate\Support\Facades\DB;

/**
 * Handles GDPR / Google Play Store compliant student data export and account deletion.
 */
class AccountDataService
{
    public function exportForStudent(Student $student): array
    {
        return [
            'exported_at' => now()->toIso8601String(),
            'profile' => $student->only([
                'id', 'name', 'email', 'phone', 'type', 'status',
                'wallet_balance', 'referral_code', 'theme_mode', 'created_at',
            ]),
            'subscriptions' => $student->subscriptions()->with('plan:id,name,slug')->get()->toArray(),
            'orders' => $student->orders()->latest()->get([
                'order_number', 'orderable_type', 'orderable_id', 'amount', 'payment_method', 'status', 'created_at',
            ])->toArray(),
            'wallet_transactions' => $student->walletTransactions()->latest()->get([
                'type', 'amount', 'balance_after', 'description', 'created_at',
            ])->toArray(),
            'exam_sessions' => $student->examSessions()->latest()->get([
                'id', 'title', 'mode', 'score', 'total', 'status', 'created_at',
            ])->toArray(),
            'flashcard_sets' => $student->flashcardSets()->get(['id', 'title', 'created_at'])->toArray(),
            'my_books' => $student->myBooks()->get(['id', 'title', 'total_pages', 'created_at'])->toArray(),
        ];
    }

    public function deleteStudentAccount(Student $student): void
    {
        DB::transaction(function () use ($student) {
            $student->tokens()->delete();
            $student->notifications()->delete();
            $student->notificationPreferences()->delete();
            $student->aiSessions()->delete();
            $student->myBooks()->delete();
            $student->flashcardSets()->delete();
            $student->studySchedules()->delete();

            // Anonymize financial/audit records instead of breaking foreign keys
            $student->update([
                'name' => 'Deleted Student #'.$student->id,
                'email' => null,
                'phone' => null,
                'phone_hash' => null,
                'avatar' => null,
                'status' => 'inactive',
                'password' => bcrypt(str()->random(32)),
            ]);
        });
    }
}
