<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE orders MODIFY payment_method ENUM('wallet','sslcommerz','bkash','nagad','manual','zinipay','mhpay') NOT NULL");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE orders MODIFY payment_method ENUM('wallet','sslcommerz','bkash','nagad','manual','zinipay') NOT NULL");
    }
};
