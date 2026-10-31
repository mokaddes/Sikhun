<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('referrals', function (Blueprint $table) {
            $table->string('fraud_status', 16)->default('clear')->after('status')->index();
            $table->json('risk_signals')->nullable()->after('fraud_status');
            $table->timestamp('registered_at')->nullable()->after('risk_signals');
            $table->timestamp('paid_at')->nullable()->after('registered_at');
            $table->timestamp('reviewed_at')->nullable()->after('paid_at');
            $table->foreignId('reviewed_by_admin_id')->nullable()->after('reviewed_at')
                ->constrained('admins')->nullOnDelete();
        });

        Schema::table('students', function (Blueprint $table) {
            $table->string('phone_hash', 64)->nullable()->index()->after('email');
        });

        Schema::create('referral_events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('referrer_student_id')->constrained('students')->cascadeOnDelete();
            $table->foreignId('referral_id')->nullable()->constrained('referrals')->cascadeOnDelete();
            $table->enum('event_type', ['click', 'registered', 'paid'])->index();
            $table->string('phone_hash', 64)->nullable()->index();
            $table->string('device_hash', 64)->nullable()->index();
            $table->string('ip_hash', 64)->nullable()->index();
            $table->string('session_hash', 64)->nullable()->index();
            $table->timestamps();

            $table->index(['referrer_student_id', 'event_type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('referral_events');

        Schema::table('students', function (Blueprint $table) {
            $table->dropColumn('phone_hash');
        });

        Schema::table('referrals', function (Blueprint $table) {
            $table->dropForeign(['reviewed_by_admin_id']);
            $table->dropColumn([
                'fraud_status', 'risk_signals', 'registered_at', 'paid_at',
                'reviewed_at', 'reviewed_by_admin_id',
            ]);
        });
    }
};
