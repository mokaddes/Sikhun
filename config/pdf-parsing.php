<?php

return [

    /*
    |--------------------------------------------------------------------------
    | PDF Parsing Pipeline
    |--------------------------------------------------------------------------
    |
    | 'auto'           → OpenDataLoader via the Node worker when it is
    |                   installed/enabled (see node_worker below), smalot
    |                   fallback otherwise
    | 'opendataloader' → force OpenDataLoader (Node worker); fails with a
    |                   clear error when the worker is not available
    | 'smalot'         → force the pure-PHP smalot parser
    |
    */

    'parser' => env('PDF_PARSER', 'auto'),

    'node_worker' => [
        // Master switch — disable on hosts that cannot run Node/Java so the
        // pipeline degrades to smalot instead of erroring.
        'enabled' => env('PDF_NODE_WORKER_ENABLED', true),
        // 'node' or an absolute node binary path.
        'node_path' => env('PDF_NODE_PATH', 'node'),
        // Absolute path to the worker entry point.
        'worker_path' => env('PDF_WORKER_PATH', base_path('pdf-worker/parse.js')),
        // Work directory root for a single parse run (created per book).
        'output_path' => env('PDF_OUTPUT_PATH', storage_path('app/pdf-parsing')),
        // Total seconds the worker may run before Laravel kills it (JVM
        // startup + a large PDF can take minutes; job timeout is 3600s).
        'timeout' => env('PDF_WORKER_TIMEOUT', 1200),
    ],

    'opendataloader' => [
        // e.g. 'eng+ben' — handed to the worker's --ocr-lang flag when set.
        'ocr_lang' => env('OPENDATALOADER_OCR_LANG'),
    ],

    /*
    | Chunking for RAG. Max chunk ~chars with overlap; tables/formulas are
    | never split mid-element (see BookChunkingService).
    */
    'chunk_max_chars' => env('PDF_CHUNK_MAX_CHARS', 1200),
    'chunk_overlap_chars' => env('PDF_CHUNK_OVERLAP_CHARS', 150),
];
