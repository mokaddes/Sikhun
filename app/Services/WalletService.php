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
            $lockedStudent = Student::whereKey($student->id)->lockForUpdate()->first();
            $before = $lockedStudent->wallet_balance;
            $lockedStudent->increment('wallet_balance', $amount);
            $lockedStudent->refresh();

            $student->wallet_balance = $lockedStudent->wallet_balance;

            return WalletTransaction::create([
                'student_id' => $lockedStudent->id,
                'type' => 'credit',
                'category' => $category,
                'amount' => $amount,
                'balance_before' => $before,
                'balance_after' => $lockedStudent->wallet_balance,
                'reference' => $reference,
                'notes' => $notes,
            ]);
        });
    }

    public function debit(Student $student, float $amount, string $category, ?string $reference = null, ?string $notes = null): WalletTransaction
    {
        return DB::transaction(function () use ($student, $amount, $category, $reference, $notes) {
            $lockedStudent = Student::whereKey($student->id)->lockForUpdate()->first();

            if ($lockedStudent->wallet_balance < $amount) {
                throw new \RuntimeException('Insufficient wallet balance.');
            }

            $before = $lockedStudent->wallet_balance;
            $lockedStudent->decrement('wallet_balance', $amount);
            $lockedStudent->refresh();

            $student->wallet_balance = $lockedStudent->wallet_balance;

            return WalletTransaction::create([
                'student_id' => $lockedStudent->id,
                'type' => 'debit',
                'category' => $category,
                'amount' => $amount,
                'balance_before' => $before,
                'balance_after' => $lockedStudent->wallet_balance,
                'reference' => $reference,
                'notes' => $notes,
            ]);
        });
    }
}
