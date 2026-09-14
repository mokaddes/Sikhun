<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('course_lessons', function (Blueprint $table) {
            // Private-disk path for an admin-uploaded video. Kept separate from
            // video_url so a lesson can use either an uploaded file or an
            // external embed (YouTube etc.).
            $table->string('video_path')->nullable()->after('video_url');
        });
    }

    public function down(): void
    {
        Schema::table('course_lessons', function (Blueprint $table) {
            $table->dropColumn('video_path');
        });
    }
};
