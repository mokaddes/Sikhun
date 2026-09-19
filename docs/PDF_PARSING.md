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
        ↓
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
```

If the worker (or Java) is missing, `isAvailable()` returns false and `auto`
degrades to smalot — a book never fails just because the worker is absent.
Forcing `PDF_PARSER=opendataloader` fails loudly instead.

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
| No tables/images | Smalot fallback ran | Install the OpenDataLoader worker (Node + Java) for full extraction |
| Chunks exist, no embeddings | No embedding provider configured | Configure an OpenAI-compatible provider in `/admin/ai-providers` |
