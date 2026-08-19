<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Coupons grant full platform access (books + courses + AI) to a
     * student who otherwise has no package or wants access beyond their
     * package limits. Two issuance models are supported:
     *   - Direct assign: `student_id` is set; the student is simply granted.
     *   - Public code: `code` is set; any student can redeem it (limited by
     *     `max_uses`).
     * Access lasts either a fixed `duration_days` from grant/redemption or
     * a fixed calendar window (`starts_at`/`ends_at`) — admin picks per coupon.
     */
    public function up(): void
    {
        Schema::create('coupons', function (Blueprint $table) {
            $table->id();
            $table->string('code', 40)->nullable()->unique();
            $table->string('name');
            $table->text('description')->nullable();
            $table->foreignId('student_id')->nullable()->constrained('students')->nullOnDelete();
            $table->integer('duration_days')->nullable();
            $table->timestamp('starts_at')->nullable();
            $table->timestamp('ends_at')->nullable();
            $table->unsignedInteger('max_uses')->nullable();
            $table->unsignedInteger('uses_count')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['is_active', 'starts_at', 'ends_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('coupons');
    }
};
