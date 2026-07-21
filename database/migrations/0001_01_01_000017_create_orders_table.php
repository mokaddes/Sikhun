<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->string('order_number')->unique();
            $table->enum('orderable_type', ['book', 'course', 'subscription']);
            $table->unsignedBigInteger('orderable_id');
            $table->decimal('amount', 10, 2);
            $table->enum('payment_method', ['wallet', 'sslcommerz', 'bkash', 'nagad', 'manual']);
            $table->string('gateway_transaction_id')->nullable();
            $table->enum('status', ['pending', 'completed', 'failed', 'refunded'])->default('pending');
            $table->timestamps();

            $table->index(['orderable_type', 'orderable_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
