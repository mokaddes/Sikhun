<?php

namespace App\Services\Pdf;

use RuntimeException;

/**
 * Thrown when a document cannot be parsed AT ALL (corrupt PDF, missing
 * binary, timeout). Partial failures inside a document — one page failing
 * OCR, a table that didn't extract — are handled with graceful degradation
 * inside the parsers and never surface as this exception.
 */
class PdfParserException extends RuntimeException {}
