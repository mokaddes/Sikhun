<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('book_shelves', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->foreignId('book_id')->constrained('books')->cascadeOnDelete();
            $table->enum('source', ['free', 'purchased', 'subscription_gift', 'admin_gift'])->default('purchased');
            $table->timestamp('added_at')->useCurrent();
            $table->timestamps();

            $table->unique(['student_id', 'book_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('book_shelves');
    }
};
