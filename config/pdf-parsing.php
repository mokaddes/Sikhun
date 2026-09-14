<?php

return [

    /*
    |--------------------------------------------------------------------------
    | PDF Parsing Pipeline
    |--------------------------------------------------------------------------
    |
    | 'auto'      → OpenDataLoader when its binary is installed, smalot otherwise
    | 'opendataloader' | 'smalot' → force a specific parser
    |
    */

    'parser' => env('PDF_PARSER', 'auto'),

    'opendataloader' => [
        // Absolute path, or leave as-is if the binary is on the server's PATH.
        'binary_path' => env('OPENDATALOADER_BINARY', 'opendataloader-pdf'),
        'timeout' => env('OPENDATALOADER_TIMEOUT', 600),
        // e.g. 'eng+ben' — passed as --ocr-lang when set.
        'ocr_lang' => env('OPENDATALOADER_OCR_LANG'),
    ],

    /*
    | Chunking for RAG. Max chunk ~chars with overlap; tables/formulas are
    | never split mid-element (see BookChunkingService).
    */
    'chunk_max_chars' => env('PDF_CHUNK_MAX_CHARS', 1200),
    'chunk_overlap_chars' => env('PDF_CHUNK_OVERLAP_CHARS', 150),
];
