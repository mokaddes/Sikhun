<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Adds 'book_chapter' as an orderable_type so chapter purchases flow
 * through the SAME orders/payment machinery as books/courses/subscriptions
 * (wallet instant-fulfill + ZiniPay gateway + webhook). Follows the exact
 * raw-SQL enum-extension pattern of 2026_08_19_000005_add_zinipay_payment.
 */
return new class extends Migration
{
    public function up(): void
    {
        // Raw ENUM modification is MySQL-only; sqlite (test suite) stores
        // enums as plain strings and needs no extension.
        if (DB::connection()->getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE orders MODIFY orderable_type ENUM('book','book_chapter','course','subscription','wallet_recharge') NOT NULL");
        }
    }

    public function down(): void
    {
        if (DB::connection()->getDriverName() === 'mysql') {
            // Only reversible while no book_chapter rows exist; safe for a
            // fresh rollback of this feature.
            DB::statement("ALTER TABLE orders MODIFY orderable_type ENUM('book','course','subscription','wallet_recharge') NOT NULL");
        }
    }
};
