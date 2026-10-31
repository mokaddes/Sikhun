<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('flashcard_sets', function (Blueprint $table) {
            $table->json('generation_data')->nullable();
            $table->string('generation_status', 20)->default('completed')->index();
            $table->unsignedSmallInteger('generation_attempts')->default(0);
        });
    }

    public function down(): void
    {
        Schema::table('flashcard_sets', function (Blueprint $table) {
            $table->dropIndex(['generation_status']);
            $table->dropColumn(['generation_data', 'generation_status', 'generation_attempts']);
        });
    }
};
