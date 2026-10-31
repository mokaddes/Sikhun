<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Widen the parsed-content columns to LONGTEXT.
     *
     * MySQL needs a raw ALTER TABLE; every other driver (SQLite for the test
     * suite, Postgres for local dev) uses Schema::change(), which keeps the
     * test database buildable. Previously this ran raw MySQL SQL everywhere,
     * which is why `php artisan test` failed on all 35 tests.
     */
    private const COLUMNS = [
        'book_chapters' => 'content',
        'book_pages' => 'content',
        'book_elements' => 'content',
        'book_chunks' => 'content',
        'my_book_pages' => 'content',
    ];

    public function up(): void
    {
        $this->setColumnType('LONGTEXT', 'longText');
    }

    public function down(): void
    {
        $this->setColumnType('TEXT', 'text');
    }

    private function setColumnType(string $mysqlType, string $schemaMethod): void
    {
        if (DB::connection()->getDriverName() === 'mysql') {
            foreach (self::COLUMNS as $table => $column) {
                DB::statement("ALTER TABLE {$table} MODIFY {$column} {$mysqlType} NULL");
            }

            return;
        }

        foreach (self::COLUMNS as $table => $column) {
            if (! Schema::hasTable($table)) {
                continue;
            }

            // SQLite has no length limit on TEXT, so `change()` is a no-op
            // there but keeps the migration portable for Postgres.
            Schema::table($table, function (Blueprint $blueprint) use ($column, $schemaMethod) {
                $blueprint->{$schemaMethod}($column)->nullable()->change();
            });
        }
    }
};
