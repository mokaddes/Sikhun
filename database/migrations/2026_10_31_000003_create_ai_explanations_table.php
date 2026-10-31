<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ai_explanations', function (Blueprint $table) {
            $table->id();
            $table->char('cache_key', 64)->unique();
            $table->text('question');
            $table->longText('correct_answer');
            $table->longText('student_answer')->nullable();
            $table->char('source_hash', 64);
            $table->longText('explanation');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ai_explanations');
    }
};
