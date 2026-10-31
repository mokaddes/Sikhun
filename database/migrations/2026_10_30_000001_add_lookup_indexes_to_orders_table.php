<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Hot lookup paths on `orders`:
     *  - the ZiniPay webhook resolves an order by gateway_invoice_id on
     *    every callback;
     *  - the student wallet/history screens filter by student + status.
     * Neither had an index, so both did a full table scan.
     */
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->index('gateway_invoice_id');
            $table->index(['student_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropIndex(['gateway_invoice_id']);
            $table->dropIndex(['student_id', 'status']);
        });
    }
};
