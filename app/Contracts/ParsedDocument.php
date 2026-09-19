<?php

namespace App\Contracts;

/**
 * Normalized output of a document-understanding parser. Vendor-specific
 * payloads always live under `metadata` — never promoted to first-class
 * fields — so swapping OpenDataLoader for another parser later touches
 * only the parser implementation, not the storage layer or RAG.
 */
class ParsedDocument
{
    /** @var array<int, array{page_number: int, content: ?string, metadata: ?array}> */
    public array $pages = [];

    /**
     * @var array<int, array{
     *     chapter_number: ?string, title: string, level: int,
     *     start_page: ?int, end_page: ?int, parent_index: ?int, metadata: ?array
     * }>
     * Flat list in reading order; `parent_index` refers to the index of the
     * parent entry in this same array (null = top-level chapter).
     */
    public array $chapters = [];

    /**
     * @var array<int, array{
     *     type: string, page_number: ?int, content: ?string, metadata: ?array,
     *     bbox: ?array, heading_path: ?string[], source_id: ?string
     * }>
     * Document elements in reading order. type is one of BookElement::TYPES.
     * `source_id` is the parser's own element id (e.g. OpenDataLoader's),
     * kept so results remain traceable to the exact extracted element.
     */
    public array $elements = [];

    /** @var array<int, array{chapter_index: ?int, page_number: ?int, element_index: ?int, title: ?string, content: ?array, markdown: ?string, html: ?string, metadata: ?array}> */
    public array $tables = [];

    /** @var array<int, array{chapter_index: ?int, page_number: ?int, element_index: ?int, binary: ?string, extension: ?string, alt_text: ?string, description: ?string, ocr_text: ?string, bbox: ?array, metadata: ?array}> */
    public array $images = [];

    /** @var array<int, array{chapter_index: ?int, page_number: ?int, element_index: ?int, latex: ?string, content: ?string, bbox: ?array, metadata: ?array}> */
    public array $formulas = [];

    public string $parserName = '';

    public string $parserVersion = '';

    /**
     * OCR fallback provenance (scanned PDFs read back with tesseract).
     * Populated by parsers that support OCR; null when the doc came from
     * pure text extraction.
     */
    public bool $ocrUsed = false;

    public ?string $ocrTool = null;

    public ?string $ocrLang = null;

    public ?int $ocrPages = null;
}
