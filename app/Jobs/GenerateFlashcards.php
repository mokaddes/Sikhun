<?php

namespace App\Jobs;

use App\Models\FlashcardSet;
use App\Services\Ai\AiProviderFactory;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class GenerateFlashcards implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3;

    public $timeout = 90;

    public function __construct(private int $setId, private string $sourceText, private int $count)
    {
        $this->onQueue('ai');
    }

    public function handle(): void
    {
        $set = FlashcardSet::find($this->setId);

        if (! $set) {
            return;
        }

        try {
            $provider = AiProviderFactory::default('flashcard_gen');

            $response = $provider->chat([
                ['role' => 'system', 'content' => "You create study flashcards for Bangladeshi students. Generate exactly {$this->count} flashcard pairs from the provided content. Cards should be in Bengali unless the content is in English. Return ONLY valid JSON: {\"flashcards\": [{\"front\": \"...\", \"back\": \"...\"}]}"],
                ['role' => 'user', 'content' => mb_substr($this->sourceText, 0, 6000)],
            ], ['temperature' => 0.5, 'max_tokens' => 2000]);

            $clean = preg_replace('/```json\s*|\s*```/', '', trim($response));
            $data = json_decode($clean, true);

            foreach (($data['flashcards'] ?? []) as $card) {
                if (empty($card['front']) || empty($card['back'])) {
                    continue;
                }
                $set->flashcards()->create(['front' => $card['front'], 'back' => $card['back']]);
            }
        } catch (\Throwable $e) {
            // Leave the set with zero cards; the UI shows an empty state + retry.
        }
    }
}
