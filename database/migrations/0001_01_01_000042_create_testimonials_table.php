<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Admin-managed real student testimonials for the homepage. Deliberately
 * NOT seeded with any rows — a homepage "What Our Students Say" section
 * with invented names/quotes would be fabricated social proof, which this
 * app will not ship with. The section on the public homepage hides itself
 * entirely when this table is empty; add real ones via /admin/testimonials.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('testimonials', function (Blueprint $table) {
            $table->id();
            $table->string('student_name');
            $table->string('student_role')->nullable(); // e.g. "HSC Student, Dhaka"
            $table->string('avatar')->nullable();
            $table->text('quote');
            $table->unsignedTinyInteger('sort_order')->default(0);
            $table->boolean('is_published')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('testimonials');
    }
};
