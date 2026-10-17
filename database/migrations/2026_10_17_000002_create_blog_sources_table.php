<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('blog_sources', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('url', 500);
            $table->enum('type', ['rss', 'sitemap', 'html_list'])->default('rss');
            $table->string('link_selector')->nullable();
            $table->string('title_selector')->nullable();
            $table->string('content_selector')->nullable();
            $table->foreignId('default_category_id')->nullable()->constrained('blog_categories')->nullOnDelete();
            $table->boolean('is_active')->default(true);
            $table->unsignedSmallInteger('max_per_run')->default(5);
            $table->timestamp('last_run_at')->nullable();
            $table->string('last_status', 50)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('blog_sources');
    }
};
