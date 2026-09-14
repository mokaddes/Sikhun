<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('courses', function (Blueprint $table) {
            // 'video' courses deliver content as sections/lessons; the two link
            // types hand the student a single external URL after purchase.
            $table->enum('delivery_type', ['video', 'enrollment_link', 'file_download'])
                ->default('video')
                ->after('description');
            $table->string('external_link', 500)->nullable()->after('delivery_type');
            $table->text('link_note')->nullable()->after('external_link');
        });
    }

    public function down(): void
    {
        Schema::table('courses', function (Blueprint $table) {
            $table->dropColumn(['delivery_type', 'external_link', 'link_note']);
        });
    }
};
