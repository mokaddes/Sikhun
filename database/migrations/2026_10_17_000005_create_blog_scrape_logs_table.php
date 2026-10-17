<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('blog_scrape_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('blog_source_id')->nullable()->constrained('blog_sources')->nullOnDelete();
            $table->unsignedInteger('found')->default(0);
            $table->unsignedInteger('duplicates')->default(0);
            $table->unsignedInteger('blocked')->default(0);
            $table->unsignedInteger('drafted')->default(0);
            $table->unsignedInteger('failed')->default(0);
            $table->text('error')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('finished_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('blog_scrape_logs');
    }
};
