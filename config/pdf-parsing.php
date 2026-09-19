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
        // startup + a large scanned PDF OCR at ~10–15s/page can take over
        // an hour; keep below the job timeout). Job timeout is 10800s.
        'timeout' => env('PDF_WORKER_TIMEOUT', 10800),
    ],

    'opendataloader' => [
        // e.g. 'eng+ben' — handed to the worker's --ocr-lang flag when set.
        'ocr_lang' => env('OPENDATALOADER_OCR_LANG'),
    ],

    /*
    | OCR fallback for scanned/image-only PDFs.
    |
    | OpenDataLoader's structured pass extracts zero text from scanned
    | documents; the worker then rasterizes each page (poppler-utils'
    | pdftoppm) and reads it back with tesseract (poppler-utils +
    | tesseract-ocr + language packs must be installed on the host).
    | 'lang' defaults to opendataloader.ocr_lang so a single OCR language
    | setting drives both passes.
    */
    'ocr' => [
        'enabled' => env('PDF_OCR_ENABLED', true),
        // Absolute binary paths when they are not on the server's PATH.
        'tesseract_path' => env('PDF_TESSERACT_PATH', 'tesseract'),
        'pdftoppm_path' => env('PDF_PDFTOPPM_PATH', 'pdftoppm'),
        // Rasterization DPI — higher catches smaller text, slower to run.
        'dpi' => (int) env('PDF_OCR_DPI', 200),
        'lang' => env('PDF_OCR_LANG') ?: env('OPENDATALOADER_OCR_LANG'),
    ],

    /*
    | Chunking for RAG. Max chunk ~chars with overlap; tables/formulas are
    | never split mid-element (see BookChunkingService).
    */
    'chunk_max_chars' => env('PDF_CHUNK_MAX_CHARS', 1200),
    'chunk_overlap_chars' => env('PDF_CHUNK_OVERLAP_CHARS', 150),
];
