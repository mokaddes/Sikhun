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

            $tx = WalletTransaction::create([
                'student_id' => $lockedStudent->id,
                'type' => 'credit',
                'category' => $category,
                'amount' => $amount,
                'balance_before' => $before,
                'balance_after' => $lockedStudent->wallet_balance,
                'reference' => $reference,
                'notes' => $notes,
            ]);

            try {
                $fmtAmount = number_format($amount, 2);
                $fmtBalance = number_format((float) $lockedStudent->wallet_balance, 2);
                app(NotificationService::class)->send(
                    student: $lockedStudent,
                    type: 'wallet_transaction',
                    title: "ওয়ালেটে ৳{$fmtAmount} যুক্ত হয়েছে",
                    body: "আপনার শিখুন ওয়ালেটে ৳{$fmtAmount} ক্রেডিট হয়েছে। নতুন ব্যালেন্স: ৳{$fmtBalance}।".($notes ? " ({$notes})" : ''),
                    data: ['url' => '/wallet', 'transaction_id' => $tx->id, 'amount' => $amount],
                    mandatory: true
                );
            } catch (\Throwable $e) {
                // Never break wallet transaction on notification failure
            }

            return $tx;
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

            $tx = WalletTransaction::create([
                'student_id' => $lockedStudent->id,
                'type' => 'debit',
                'category' => $category,
                'amount' => $amount,
                'balance_before' => $before,
                'balance_after' => $lockedStudent->wallet_balance,
                'reference' => $reference,
                'notes' => $notes,
            ]);

            try {
                if ((float) $lockedStudent->wallet_balance < 50) {
                    app(NotificationService::class)->send(
                        student: $lockedStudent,
                        type: 'wallet_low_balance',
                        title: 'ওয়ালেট ব্যালেন্স কম',
                        body: 'আপনার শিখুন ওয়ালেট ব্যালেন্স ৳'.number_format((float) $lockedStudent->wallet_balance, 2).'। নিরবচ্ছিন্ন পড়াশোনার জন্য রিচার্জ করুন।',
                        data: ['url' => '/wallet'],
                        mandatory: false
                    );
                }
            } catch (\Throwable $e) {
                // Non-fatal
            }

            return $tx;
        });
    }
}
