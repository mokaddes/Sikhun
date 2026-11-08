<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Question Sources (Source Registry & Permission Management)
        Schema::create('question_sources', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('base_url', 500);
            $table->string('source_type', 50)->default('official_university'); // official_university, official_commission, educational_archive, public_portal, community
            $table->string('institution', 255)->nullable();
            $table->string('content_category', 50)->default('admission'); // admission, job, both
            $table->string('crawl_frequency', 50)->default('weekly'); // hourly, daily, weekly, monthly, manual
            $table->string('robots_txt_status', 50)->default('unchecked'); // allowed, disallowed, conditional, unchecked
            $table->timestamp('robots_txt_checked_at')->nullable();
            $table->string('tos_review', 50)->default('pending'); // pending, compliant, restricted, violation_risk
            $table->text('tos_notes')->nullable();
            $table->string('permission_status', 50)->default('pending_review'); // pending_review, authorized, public_domain, fair_use_educational, rejected
            $table->string('permission_document_url', 500)->nullable();
            $table->string('license_info', 255)->default('Educational Fair Use / Public Archive');
            $table->boolean('commercial_redistribution')->default(false);
            $table->text('attribution_requirements')->nullable();
            $table->date('license_expiry')->nullable();
            $table->unsignedInteger('rate_limit_rpm')->default(30); // max requests per minute
            $table->unsignedInteger('crawl_delay_ms')->default(2000); // milliseconds delay between requests
            $table->timestamp('last_crawl_at')->nullable();
            $table->string('last_crawl_status', 50)->nullable();
            $table->unsignedInteger('questions_imported_count')->default(0);
            $table->boolean('is_active')->default(true);
            $table->text('notes')->nullable();
            $table->json('meta')->nullable();
            $table->timestamps();

            $table->index(['source_type', 'is_active']);
            $table->index(['content_category', 'is_active']);
            $table->index(['permission_status']);
        });

        // 2. Question Subjects (Standardized taxonomy for Admission & Job Prep)
        Schema::create('question_subjects', function (Blueprint $table) {
            $table->id();
            $table->string('name_bn');
            $table->string('name_en');
            $table->string('slug')->unique();
            $table->string('segment', 50)->default('both'); // admission, job, both
            $table->string('icon', 100)->nullable();
            $table->integer('order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->json('meta')->nullable();
            $table->timestamps();

            $table->index(['segment', 'is_active']);
        });

        // 3. Question Topics / Subcategories
        Schema::create('question_topics', function (Blueprint $table) {
            $table->id();
            $table->foreignId('subject_id')->constrained('question_subjects')->cascadeOnDelete();
            $table->string('name_bn');
            $table->string('name_en');
            $table->string('slug');
            $table->string('segment', 50)->default('both'); // admission, job, both
            $table->integer('order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['subject_id', 'slug']);
            $table->index(['subject_id', 'is_active']);
        });

        // 4. Central Question Bank
        Schema::create('question_banks', function (Blueprint $table) {
            $table->id();
            $table->string('segment', 50)->index(); // admission, job
            $table->foreignId('subject_id')->constrained('question_subjects')->cascadeOnDelete();
            $table->foreignId('topic_id')->nullable()->constrained('question_topics')->nullOnDelete();
            $table->string('topic_name', 255)->nullable();
            $table->string('institution', 255)->nullable(); // University of Dhaka, BUET, BPSC, Bangladesh Bank
            $table->string('exam_name', 255)->nullable(); // DU KA Unit 2024-25, 46th BCS Preliminary
            $table->string('origin_tag', 100)->index(); // DU-24, BUET-23, BCS-45, BB-AD-24, RU-C-22
            $table->unsignedSmallInteger('year')->nullable()->index(); // 2024, 2023
            $table->string('session', 50)->nullable(); // 2024-25
            $table->string('shift_or_set', 50)->nullable(); // Set A, Morning Shift
            $table->longText('question_text');
            $table->string('question_type', 50)->default('mcq'); // mcq, written, true_false
            $table->json('options')->nullable(); // [{"key": "A", "text": "ক..."}, {"key": "B", "text": "খ..."}]
            $table->string('correct_option', 50); // A, B, C, D, etc.
            $table->longText('explanation')->nullable(); // In-depth conceptual analysis with step-by-step logic
            $table->decimal('marks', 5, 2)->default(1.00);
            $table->decimal('negative_marks', 5, 2)->default(0.25);
            $table->string('difficulty', 50)->default('medium'); // easy, medium, hard
            $table->foreignId('source_id')->nullable()->constrained('question_sources')->nullOnDelete();
            $table->string('source_url', 500)->nullable();
            $table->string('ingestion_type', 50)->default('manual'); // scraped, pdf_ocr, manual, ai_generated
            $table->string('status', 50)->default('published'); // published, pending_review, draft, archived
            $table->string('verification_status', 50)->default('verified'); // verified, unverified, disputed
            $table->foreignId('verified_by_admin_id')->nullable()->constrained('admins')->nullOnDelete();
            $table->timestamp('verified_at')->nullable();
            $table->json('meta')->nullable();
            $table->timestamps();

            $table->index(['segment', 'subject_id', 'status']);
            $table->index(['segment', 'year']);
            $table->index(['origin_tag', 'year']);
            $table->index(['difficulty']);
        });

        // 5. Ingestion Batches (PDF OCR uploads & Scraper runs)
        Schema::create('question_ingestion_batches', function (Blueprint $table) {
            $table->id();
            $table->string('batch_type', 50); // pdf_upload, web_scrape
            $table->foreignId('source_id')->nullable()->constrained('question_sources')->nullOnDelete();
            $table->string('title');
            $table->string('file_path', 500)->nullable();
            $table->unsignedInteger('total_raw_items')->default(0);
            $table->unsignedInteger('parsed_count')->default(0);
            $table->unsignedInteger('success_count')->default(0);
            $table->unsignedInteger('failed_count')->default(0);
            $table->string('status', 50)->default('pending'); // pending, processing, completed, failed
            $table->text('error_log')->nullable();
            $table->json('meta')->nullable();
            $table->timestamps();

            $table->index(['batch_type', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('question_ingestion_batches');
        Schema::dropIfExists('question_banks');
        Schema::dropIfExists('question_topics');
        Schema::dropIfExists('question_subjects');
        Schema::dropIfExists('question_sources');
    }
};
