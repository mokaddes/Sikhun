<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('students', function (Blueprint $table) {
            if (! Schema::hasColumn('students', 'deleted_at')) {
                $table->softDeletes();
            }
            if (! Schema::hasColumn('students', 'scheduled_purge_at')) {
                $table->timestamp('scheduled_purge_at')->nullable()->after('deleted_at');
            }
        });

        Schema::table('scheduled_notifications', function (Blueprint $table) {
            if (! Schema::hasColumn('scheduled_notifications', 'action_url')) {
                $table->string('action_url', 500)->nullable()->after('body');
            }
            if (! Schema::hasColumn('scheduled_notifications', 'send_email')) {
                $table->boolean('send_email')->default(false)->after('action_url');
            }
            if (! Schema::hasColumn('scheduled_notifications', 'target_student_id')) {
                $table->unsignedBigInteger('target_student_id')->nullable()->after('target_audience');
            }
        });

        // Allow 'admission' and any future audience types in MySQL (SQLite already stores enums as text)
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE scheduled_notifications MODIFY COLUMN target_audience VARCHAR(50) NOT NULL DEFAULT 'all'");
        }
    }

    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->dropColumn(['deleted_at', 'scheduled_purge_at']);
        });

        Schema::table('scheduled_notifications', function (Blueprint $table) {
            $table->dropColumn(['action_url', 'send_email', 'target_student_id']);
        });
    }
};
