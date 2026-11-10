<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('admission_notices', function (Blueprint $table) {
            $table->dateTime('application_start')->nullable()->change();
            $table->dateTime('application_deadline')->nullable()->change();
            $table->string('application_start_text')->nullable()->after('application_start');
            $table->string('application_deadline_text')->nullable()->after('application_deadline');
            $table->string('admit_card_date_text')->nullable()->after('exam_dates');
        });
    }

    public function down(): void
    {
        Schema::table('admission_notices', function (Blueprint $table) {
            $table->date('application_start')->nullable()->change();
            $table->date('application_deadline')->nullable()->change();
            $table->dropColumn(['application_start_text', 'application_deadline_text', 'admit_card_date_text']);
        });
    }
};
