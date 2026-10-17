<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('blog_blocklist', function (Blueprint $table) {
            $table->id();
            $table->string('source_url_hash', 64)->nullable()->unique();
            $table->string('content_hash', 64)->nullable()->index();
            $table->string('title_fingerprint', 255)->nullable()->index();
            $table->string('source_url', 500)->nullable();
            $table->string('source_title', 500)->nullable();
            $table->string('reason')->nullable();
            $table->foreignId('blocked_by')->nullable()->constrained('admins')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('blog_blocklist');
    }
};
