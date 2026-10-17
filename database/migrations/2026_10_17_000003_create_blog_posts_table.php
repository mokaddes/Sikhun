<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('blog_posts', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->string('title', 500);
            $table->string('excerpt', 1000)->nullable();
            $table->longText('content');
            $table->string('featured_image')->nullable();
            $table->foreignId('blog_category_id')->nullable()->constrained('blog_categories')->nullOnDelete();
            $table->string('meta_title', 300)->nullable();
            $table->string('meta_description', 500)->nullable();
            $table->enum('status', ['draft', 'published'])->default('draft');
            $table->timestamp('published_at')->nullable();
            $table->enum('origin', ['manual', 'auto'])->default('manual');
            $table->foreignId('blog_source_id')->nullable()->constrained('blog_sources')->nullOnDelete();
            $table->string('source_url', 500)->nullable();
            $table->string('source_url_hash', 64)->nullable()->index();
            $table->string('source_title', 500)->nullable();
            $table->string('content_hash', 64)->nullable()->index();
            $table->string('title_fingerprint', 255)->nullable()->index();
            $table->string('original_language', 10)->nullable();
            $table->json('ai_meta')->nullable();
            $table->unsignedBigInteger('view_count')->default(0);
            $table->foreignId('admin_id')->nullable()->constrained('admins')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['status', 'published_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('blog_posts');
    }
};
