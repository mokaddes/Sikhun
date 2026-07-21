<?php

namespace App\Services;

use App\Models\Student;
use App\Models\WalletTransaction;
use Illuminate\Support\Facades\DB;

class WalletService
{
    public function credit(Student $student, float $amount, string $category, ?string $reference = null, ?string $notes = null): WalletTransaction
    {
        return DB::transaction(function () use ($student, $amount, $category, $reference, $notes) {
            $before = $student->wallet_balance;
            $student->increment('wallet_balance', $amount);
            $student->refresh();

            return WalletTransaction::create([
                'student_id' => $student->id,
                'type' => 'credit',
                'category' => $category,
                'amount' => $amount,
                'balance_before' => $before,
                'balance_after' => $student->wallet_balance,
                'reference' => $reference,
                'notes' => $notes,
            ]);
        });
    }

    public function debit(Student $student, float $amount, string $category, ?string $reference = null, ?string $notes = null): WalletTransaction
    {
        if ($student->wallet_balance < $amount) {
            throw new \RuntimeException('Insufficient wallet balance.');
        }

        return DB::transaction(function () use ($student, $amount, $category, $reference, $notes) {
            $before = $student->wallet_balance;
            $student->decrement('wallet_balance', $amount);
            $student->refresh();

            return WalletTransaction::create([
                'student_id' => $student->id,
                'type' => 'debit',
                'category' => $category,
                'amount' => $amount,
                'balance_before' => $before,
                'balance_after' => $student->wallet_balance,
                'reference' => $reference,
                'notes' => $notes,
            ]);
        });
    }
}
