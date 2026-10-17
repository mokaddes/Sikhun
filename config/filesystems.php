<?php

use Illuminate\Support\Str;

return [

    'default' => env('FILESYSTEM_DISK', 'local'),

    'disks' => [

        'local' => [
            'driver' => 'local',
            'root' => storage_path('app/private'),
            'serve' => true,
            'throw' => false,
            'permissions' => [
                'file' => [
                    'public' => 0666,
                    'private' => 0666,
                ],
                'dir' => [
                    'public' => 0777,
                    'private' => 0777,
                ],
            ],
        ],

        // Explicit alias for 'local' — used everywhere in this codebase
        // (BookController, BookReaderService in Phase 3) so the intent
        // ("this file must never be public") is obvious at the call site.
        'private' => [
            'driver' => 'local',
            'root' => storage_path('app/private'),
            'serve' => true,
            'throw' => false,
            'permissions' => [
                'file' => [
                    'public' => 0666,
                    'private' => 0666,
                ],
                'dir' => [
                    'public' => 0777,
                    'private' => 0777,
                ],
            ],
        ],

        'public' => [
            'driver' => 'local',
            'root' => storage_path('app/public'),
            'url' => env('APP_URL').'/storage',
            'visibility' => 'public',
            'throw' => false,
        ],

    ],

    'links' => [
        public_path('storage') => storage_path('app/public'),
    ],

];
