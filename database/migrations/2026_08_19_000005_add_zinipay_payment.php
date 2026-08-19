<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Swaps the hosted payment gateway from SSLCommerz to ZiniPay:
 *  - adds 'zinipay' to the orders.payment_method enum (keeping 'sslcommerz'
 *    so historical rows stay valid), and
 *  - stores the ZiniPay invoice id on the order so the webhook/success
 *    callback can find and server-side verify it later.
 */
return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE orders MODIFY payment_method ENUM('wallet','sslcommerz','bkash','nagad','manual','zinipay') NOT NULL");

        Schema::table('orders', function (Blueprint $table) {
            $table->string('gateway_invoice_id')->nullable()->after('gateway_transaction_id');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('gateway_invoice_id');
        });

        DB::statement("ALTER TABLE orders MODIFY payment_method ENUM('wallet','sslcommerz','bkash','nagad','manual') NOT NULL");
    }
};