<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Expand students.type to support 'admission' alongside 'ssc', 'hsc', 'university', 'job'
        // Using string(50) for maximum reliability and flexibility across MySQL/MariaDB
        Schema::table('students', function (Blueprint $table) {
            $table->string('type', 50)->default('hsc')->change();
        });

        // 2. Add student type group & plan group access control to question_banks
        Schema::table('question_banks', function (Blueprint $table) {
            $table->json('allowed_student_types')->nullable()->after('segment'); // e.g. ["ssc", "hsc", "admission", "job", "university"] or null (all)
            $table->json('allowed_plan_ids')->nullable()->after('allowed_student_types'); // e.g. [1, 2, 3] or ["free"] or null (all)
            $table->boolean('is_public_to_all')->default(false)->after('allowed_plan_ids');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('question_banks', function (Blueprint $table) {
            $table->dropColumn(['allowed_student_types', 'allowed_plan_ids', 'is_public_to_all']);
        });

        Schema::table('students', function (Blueprint $table) {
            $table->enum('type', ['ssc', 'hsc', 'university', 'job'])->default('ssc')->change();
        });
    }
};
