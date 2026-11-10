<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('admission_notices', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->string('institution_name');
            $table->enum('university_type', ['public', 'guccho', 'private'])->default('public');
            $table->string('academic_year')->default('2026-2027');
            $table->json('units')->nullable();
            $table->date('application_start')->nullable();
            $table->date('application_deadline')->nullable();
            $table->json('exam_dates')->nullable();
            $table->string('minimum_gpa_ssc')->nullable();
            $table->string('minimum_gpa_hsc')->nullable();
            $table->string('total_gpa_required')->nullable();
            $table->string('application_fee')->nullable();
            $table->string('apply_url', 1000)->nullable();
            $table->string('official_website_url', 1000)->nullable();
            $table->string('notice_image_url', 1000)->nullable();
            $table->string('featured_image_url', 1000)->nullable();
            $table->longText('description');
            $table->text('eligibility_criteria')->nullable();
            $table->text('marks_distribution')->nullable();
            $table->string('status')->default('draft'); // draft, published
            $table->boolean('is_featured')->default(false);
            $table->unsignedInteger('views_count')->default(0);
            $table->string('source_name')->nullable();
            $table->string('source_url', 1000)->nullable();
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            $table->timestamp('published_at')->nullable();
            $table->timestamps();

            $table->index('university_type');
            $table->index('status');
            $table->index('application_deadline');
            $table->index('is_featured');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('admission_notices');
    }
};
