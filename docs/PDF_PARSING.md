# PDF Parsing Architecture

## Overview

Sikhun's PDF processing pipeline turns an uploaded book PDF into a structured,
searchable document — chapters, pages, elements, tables, images, formulas —
plus RAG-ready chunks, **without replacing the original PDF or the existing
watermarked Turn.js-style reader**. The original PDF remains the single source
of truth for reading; the parsed representation powers AI, search, and
chapter-level commerce.

```
Admin uploads PDF (Admin\BookController)
        ↓
ProcessBookPdf job (queued, async)
        ↓
PdfParserManager → OpenDataLoaderWorkerParser (Node worker, preferred) | SmalotPdfParser (fallback)
        ↓
ParsedDocument (vendor-neutral DTO)
        ↓   ┌──────────────────────────────────────────────┐
        │   text extracted anywhere?         NO → OCR pass  │
        │   (open runs its tesseract fallback)             │
        ↓   └──────────────────────────────────────────────┘
ParsedDocumentStorageService (single transaction)
        ├── book_chapters   (hierarchical, parent_id)
        ├── book_pages      (page ↔ chapter traceability)
        ├── book_elements   (text/heading/table/image/formula/…, reading order, bbox)
        ├── book_tables     (structured headers+rows, markdown, html)
        ├── book_images     (binary → private disk; description/OCR in DB)
        └── book_formulas   (LaTeX)
        ↓
BookChunkingService (structure-aware chunks: chapter_id, page_id, metadata, element_ids)
        ↓
EmbeddingService (batch, best-effort, non-fatal)
```

## Parsers

### OpenDataLoader PDF (preferred)

OpenDataLoader is a Java-based document-understanding engine wrapped by the
official Node SDK (`@opendataloader/pdf`). It cannot run inside PHP, so
`OpenDataLoaderWorkerParser` spawns the Node worker in `pdf-worker/parse.js`
via Laravel's `Process` facade (argv array — never shell strings) and reads
back its canonical per-page JSON + extracted images. The worker keeps
vendor-schema knowledge in `pdf-worker/parse.js`; Laravel maps its output in
`OpenDataLoaderWorkerParser`.

The worker writes **one JSON file per page** plus a small `manifest.json`
and an `images/` folder, so a large PDF is imported incrementally without
ever loading the whole parse result into PHP memory. Output is cleaned up
after each run.

**Installation**: requires **Node.js 20+** and **Java 11+** on the server.

```
cd pdf-worker && npm install
```

Then either rely on `auto`, or force it:

```
PDF_PARSER=auto                    # auto | opendataloader | smalot
PDF_NODE_WORKER_ENABLED=true
PDF_NODE_PATH=node                 # or absolute node binary path
PDF_WORKER_PATH=pdf-worker/parse.js
PDF_OUTPUT_PATH=storage/app/pdf-parsing
PDF_WORKER_TIMEOUT=1200
OPENDATALOADER_OCR_LANG=eng+ben    # optional OCR language hint
PDF_OCR_ENABLED=true               # tesseract OCR fallback master switch
# PDF_TESSERACT_PATH=tesseract     # override when not on PATH
# PDF_PDFTOPPM_PATH=pdftoppm
# PDF_OCR_DPI=200
PDF_OCR_LANG=eng+ben               # tesseract languages; defaults to OPENDATALOADER_OCR_LANG
```

If the worker (or Java) is missing, `isAvailable()` returns false and `auto`
degrades to smalot — a book never fails just because the worker is absent.
Forcing `PDF_PARSER=opendataloader` fails loudly instead.

### Input layer & OCR fallback

OpenDataLoader's structured pass extracts **reliable text only from
text-based PDFs**. When the whole document yields zero text (scanned or
image-only — the classic "No text could be extracted from this PDF" failure),
the worker automatically runs a **tesseract OCR pass**:

1. Every page is rasterized to PNG with `pdftoppm` (poppler-utils).
2. `tesseract` reads it back per page (`-l <lang>`, `--psm 3`).
3. OCR text becomes the page content (line → text element), re-entering the
   exact same chunking → embeddings → RAG pipeline.

Host prerequisites: `poppler-utils` and `tesseract-ocr` plus the language
data for every script in the books, e.g.:

```bash
sudo apt install poppler-utils tesseract-ocr tesseract-ocr-eng tesseract-ocr-ben
```

If both are missing the run exits successfully with empty pages, and the
Laravel job fails the book with a message telling the admin to install them
(and press Retry). OCR provenance (`ocr_used` / `ocr_tool` / `ocr_lang` /
`ocr_pages`) is written into `manifest.json` and surfaces in the job log.

### Smalot fallback

`SmalotPdfParser` (pure PHP, `smalot/pdfparser` from composer.json) runs
whenever the OpenDataLoader binary is absent. It extracts text, pages, and
heuristic chapters (PDF outline → numbered/Title-Case headings). Tables,
images, and formulas are **not** extracted in this mode — the book still
gets chunks and chapters, just no specialized elements.

### Swapping parsers later

`PdfParserContract` + `ParsedDocument` are the only interfaces the pipeline
depends on. Add a new implementation, register it in `PdfParserManager`, and
nothing else changes.

## Processing lifecycle

`books.processing_status`: `pending | processing | completed | failed`

- `processing_error` — failure reason for the admin UI
- `parser_name` / `parser_version` / `parsed_at` — provenance
- `pdf_content_hash` — SHA-256 of the PDF file; **an unchanged PDF is never
  reprocessed** (the job skips unless `force: true`, which the admin Retry
  button uses)

Failures are recoverable: `POST /admin/books/{book}/retry-processing`
re-dispatches with force. Partial failures degrade gracefully — a failed
image extraction never fails the book.

## Reprocessing safety

`ParsedDocumentStorageService::replaceAll()` deletes the previous parse and
writes the new one in **one DB transaction**, including deleting old chunks —
there is never a window where stale chunks are searchable next to new
structure.

## Observability

Every run logs `book_id`, stage counts (pages/chapters/elements/tables/
images/formulas/chunks/embedded), and errors to the default log channel.
Admin sees the same data on `/admin/books/{book}`.

## Troubleshooting

| Symptom | Cause | Fix |
|---|---|---|
| Status stuck `processing` | Worker died mid-run | Retry button (dispatches fresh with force) |
| `failed: Parser failure` | Worker missing / corrupt PDF | Check `processing_error`, install Node+Java or fix PDF |
| `failed: No text could be extracted…` | Scanned/image-only PDF | The worker auto-runs tesseract OCR; if it still fails, install `poppler-utils` + `tesseract-ocr` (+ language packs), set `PDF_OCR_LANG`, press Retry |
| No tables/images | Smalot fallback ran | Install the OpenDataLoader worker (Node + Java) for full extraction |
| Chunks exist, no embeddings | No embedding provider configured | Configure an OpenAI-compatible provider in `/admin/ai-providers` |
