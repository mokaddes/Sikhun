<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('support_tickets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->foreignId('conversation_id')->nullable()->constrained('support_conversations')->nullOnDelete();
            $table->string('subject', 160);
            $table->string('category', 100)->nullable();
            $table->enum('priority', ['low', 'normal', 'high', 'urgent'])->default('normal');
            $table->enum('status', ['open', 'pending', 'resolved', 'closed'])->default('open');
            $table->foreignId('assigned_admin_id')->nullable()->constrained('admins')->nullOnDelete();
            $table->timestamp('sla_due_at')->nullable()->index();
            $table->timestamps();

            $table->index(['status', 'priority']);
            $table->index(['student_id', 'created_at']);
        });

        Schema::create('support_ticket_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ticket_id')->constrained('support_tickets')->cascadeOnDelete();
            $table->enum('sender_type', ['student', 'admin', 'bot']);
            $table->text('body');
            $table->json('attachments')->nullable();
            $table->timestamps();

            $table->index(['ticket_id', 'id']);
        });

        Schema::create('feedback', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->enum('type', ['feedback', 'answer_report', 'question_report']);
            $table->enum('target_type', ['exam_question', 'flashcard', 'ai_answer', 'book'])->nullable();
            $table->unsignedBigInteger('target_id')->nullable();
            $table->string('target_key', 100)->nullable();
            $table->unsignedTinyInteger('rating')->nullable();
            $table->text('message')->nullable();
            $table->json('context')->nullable();
            $table->enum('status', ['new', 'reviewed', 'resolved'])->default('new');
            $table->text('admin_note')->nullable();
            $table->timestamps();

            $table->index(['status', 'created_at']);
            $table->index(['target_type', 'target_id']);
            $table->index(['student_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('feedback');
        Schema::dropIfExists('support_ticket_messages');
        Schema::dropIfExists('support_tickets');
    }
};
