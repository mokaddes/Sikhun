<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Time-boxed promotional windows where access to books, courses and AI
     * is free. A campaign targets every student (`scope = all`) or only an
     * explicit list (pivot table free_campaign_students).
     */
    public function up(): void
    {
        Schema::create('free_campaigns', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->enum('scope', ['all', 'selected'])->default('all');
            $table->timestamp('starts_at');
            $table->timestamp('ends_at');
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['is_active', 'starts_at', 'ends_at']);
        });

        Schema::create('free_campaign_students', function (Blueprint $table) {
            $table->id();
            $table->foreignId('free_campaign_id')->constrained('free_campaigns')->cascadeOnDelete();
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();

            $table->unique(['free_campaign_id', 'student_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('free_campaign_students');
        Schema::dropIfExists('free_campaigns');
    }
};
