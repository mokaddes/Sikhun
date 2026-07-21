<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Small extra data bag for order fulfillment — e.g. how many months a
 * subscription purchase covers. Kept separate from the core columns so
 * we're not adding a new nullable column per future order type.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->json('meta')->nullable()->after('gateway_transaction_id');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('meta');
        });
    }
};
