<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('books', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('cover_image')->nullable();
            $table->foreignId('author_id')->nullable()->references('id')->on('authors')->nullOnDelete();
            $table->foreignId('publication_id')->nullable()->references('id')->on('publications')->nullOnDelete();
            $table->foreignId('category_id')->nullable()->references('id')->on('categories')->nullOnDelete();
            $table->string('subject', 100)->nullable();
            $table->enum('level', ['ssc', 'hsc', 'university', 'job'])->nullable();
            $table->decimal('price', 8, 2)->default(0);
            $table->boolean('is_free')->default(false);
            $table->string('pdf_path', 500)->nullable(); // storage/app/private/... NEVER public
            $table->integer('total_pages')->default(0);
            $table->boolean('is_published')->default(false);
            $table->boolean('is_premium_gift')->default(false);
            $table->unsignedInteger('reading_count')->default(0);
            $table->timestamps();

            $table->index(['level', 'subject']);
            $table->index('is_published');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('books');
    }
};
