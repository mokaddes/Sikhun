<?php

namespace App\Contracts;

use App\Models\Book;

/**
 * Abstraction over the document-understanding layer so the parsing
 * pipeline never depends on a specific parser vendor. Implementations
 * must degrade gracefully: throw ParsedDocumentFailure ONLY when the
 * document cannot be processed at all; partial failures (missing tables,
 * no chapters detected, OCR errors on one page) should be skipped and
 * logged, never fatal.
 */
interface PdfParserContract
{
    /**
     * @throws \App\Services\Pdf\PdfParserException when the document cannot be parsed at all
     */
    public function parse(Book $book): ParsedDocument;

    /** Is the underlying parser runtime available on this host? */
    public function isAvailable(): bool;
}
