<?php

namespace App\Contracts;

/**
 * Anything that owns a stored PDF on the private disk. Both the admin
 * catalog's Book and a student's own uploaded MyBook qualify, so the
 * parsing + page-rendering pipeline can treat them identically without
 * depending on a specific model.
 */
interface PdfParsable
{
    /** Primary key, only used for cache keys / worker temp dirs. */
    public function pdfSourceId(): int;

    /** Relative path of the PDF on the private disk, or null. */
    public function pdfFilePath(): ?string;
}