<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('job_circulars', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->string('organization');
            $table->string('category')->default('government'); // government, bank, private, ngo, education, it_tech
            $table->string('job_type')->default('Full-time'); // Full-time, Contractor, Part-time, Internship
            $table->string('vacancy')->nullable(); // e.g. "১৫ জন"
            $table->string('salary')->nullable(); // e.g. "২০,০০০ - ৫৬,৮০০ টাকা"
            $table->string('location')->default('ঢাকা, বাংলাদেশ');
            $table->string('education_requirement')->nullable(); // e.g. "স্নাতক / এইচএসসি"
            $table->string('experience_requirement')->nullable(); // e.g. "প্রয়োজন নেই"
            $table->date('deadline');
            $table->date('published_date')->nullable();
            $table->longText('description');
            $table->text('application_instructions')->nullable();
            $table->string('apply_url', 1000)->nullable();
            $table->string('source_name')->nullable();
            $table->string('source_url', 1000)->nullable();
            $table->string('image_url', 1000)->nullable();
            $table->boolean('is_featured')->default(false);
            $table->unsignedInteger('views_count')->default(0);
            $table->timestamps();

            $table->index('category');
            $table->index('deadline');
            $table->index('is_featured');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('job_circulars');
    }
};
