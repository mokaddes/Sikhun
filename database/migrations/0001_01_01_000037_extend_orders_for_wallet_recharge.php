<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Adds 'wallet_recharge' as a fourth orderable_type and makes orderable_id
 * nullable (a wallet top-up has no related model to point to). Uses raw
 * SQL rather than Schema::table()->change() to avoid pulling in
 * doctrine/dbal purely for two column tweaks.
 */
return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE orders MODIFY orderable_type ENUM('book','course','subscription','wallet_recharge') NOT NULL");
        DB::statement('ALTER TABLE orders MODIFY orderable_id BIGINT UNSIGNED NULL');
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE orders MODIFY orderable_type ENUM('book','course','subscription') NOT NULL");
        DB::statement('ALTER TABLE orders MODIFY orderable_id BIGINT UNSIGNED NOT NULL');
    }
};
