<?php

namespace App\Services\Pdf;

use App\Contracts\PdfParserContract;
use App\Services\Pdf\OpenDataLoader\OpenDataLoaderPdfParser;
use App\Services\Pdf\Php\SmalotPdfParser;
use InvalidArgumentException;

/**
 * Resolves which parser to use for a run. Preference order:
 *   1. The parser named in config('pdf-parsing.parser') if available
 *   2. OpenDataLoader, if its binary is on the host
 *   3. Smalot (pure PHP, always available)
 *
 * This is the ONLY class callers need — jobs never instantiate a concrete
 * parser, which is what keeps parsers swappable.
 */
class PdfParserManager
{
    public function __construct(
        private OpenDataLoaderPdfParser $openDataLoader,
        private SmalotPdfParser $smalot,
    ) {}

    public function resolve(): PdfParserContract
    {
        $preferred = config('pdf-parsing.parser', 'auto');

        if ($preferred !== 'auto') {
            $parser = $this->namedParser((string) $preferred);
            if (! $parser->isAvailable()) {
                throw new PdfParserException("Configured parser '{$preferred}' is not available on this host.");
            }

            return $parser;
        }

        if ($this->openDataLoader->isAvailable()) {
            return $this->openDataLoader;
        }

        return $this->smalot;
    }

    private function namedParser(string $name): PdfParserContract
    {
        return match ($name) {
            'opendataloader' => $this->openDataLoader,
            'smalot' => $this->smalot,
            default => throw new InvalidArgumentException("Unknown parser '{$name}'."),
        };
    }
}
