<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Adds separate Bengali/English fields so a page's content actually
 * changes when the visitor switches language, instead of showing the same
 * text regardless of locale. The old single title/content/meta_* columns
 * are backfilled into BOTH _bn and _en so nothing existing breaks — an
 * admin can then go fill in real English translations at their own pace.
 * Old columns are kept (nullable, unused going forward) rather than
 * dropped, since dropping them would be a breaking change for zero benefit.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('custom_pages', function (Blueprint $table) {
            $table->string('title_bn')->nullable()->after('title');
            $table->string('title_en')->nullable()->after('title_bn');
            $table->longText('content_bn')->nullable()->after('content');
            $table->longText('content_en')->nullable()->after('content_bn');
            $table->string('meta_title_bn')->nullable()->after('meta_title');
            $table->string('meta_title_en')->nullable()->after('meta_title_bn');
            $table->string('meta_description_bn')->nullable()->after('meta_description');
            $table->string('meta_description_en')->nullable()->after('meta_description_bn');
        });

        foreach (DB::table('custom_pages')->get() as $page) {
            DB::table('custom_pages')->where('id', $page->id)->update([
                'title_bn' => $page->title,
                'title_en' => $page->title,
                'content_bn' => $page->content,
                'content_en' => $page->content,
                'meta_title_bn' => $page->meta_title,
                'meta_title_en' => $page->meta_title,
                'meta_description_bn' => $page->meta_description,
                'meta_description_en' => $page->meta_description,
            ]);
        }
    }

    public function down(): void
    {
        Schema::table('custom_pages', function (Blueprint $table) {
            $table->dropColumn([
                'title_bn', 'title_en', 'content_bn', 'content_en',
                'meta_title_bn', 'meta_title_en', 'meta_description_bn', 'meta_description_en',
            ]);
        });
    }
};
