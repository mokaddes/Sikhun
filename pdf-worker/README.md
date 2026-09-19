# Sikhun PDF Worker (OpenDataLoader)

External Node.js worker that parses a single book PDF into structured,
per-page JSON + extracted images for the Laravel importer.

## Why a separate worker

OpenDataLoader PDF is a Java-based engine with an official Node.js SDK
(`@opendataloader/pdf`). PDF parsing is CPU/memory heavy, so it must never
run inside an HTTP request — and it is not reimplemented in PHP. Laravel's
queue job (`ProcessBookPdf`) spawns this worker as a subprocess with a safe
argv array and reads back its canonical output.

Laravel remains responsible for orchestration, DB persistence
(`book_pages` / `book_chapters` / `book_elements` / `book_images` /
`book_tables` / `book_formulas` / `book_chunks`), permissions, queues and
the API/frontend. The worker only converts PDF → JSON/images.

## Requirements

- Node.js 20+
- Java 11+ on `PATH` (the official SDK delegates to a JVM)

Verify once:

```bash
node --version   # 20+
java -version    # 11+
```

## Install

```bash
cd pdf-worker
npm install
```

## Usage (invoked by Laravel — not normally run by hand)

```bash
node parse.js --input /abs/path/book.pdf --output /abs/run-dir [--ocr-lang eng+ben]
```

Output layout:

```
<run-dir>/
  manifest.json      page count + parser/version info
  page-0001.json     one canonical page file per PDF page
  images/<id>.<ext>  extracted image binaries
  book.md            secondary Markdown (informational)
```

Each canonical page file contains ordered `elements` with:

- `id` — source element id (kept as `book_elements.source_id`)
- `type` — heading | text | table | list | list_item | image | formula |
  caption | quote | page_header | page_footer | code | other
- `text`, `bbox` ({x,y,width,height}), `level`, `metadata`
- tables: structured `headers`/`rows`, `markdown`, `html`
- images: relative `file`, `alt`, `ocr`, `mime_type`, `width`, `height`
- formulas: `latex`

Exit codes: `0` OK · `1` conversion failure · `2` not runnable (missing
SDK/Java) — stderr carries the reason, which Laravel surfaces as the
book's `processing_error`.

## Troubleshooting

- `2`: run `npm install` inside `pdf-worker/`, and confirm `java -version`.
- The SDK spawns a JVM per invocation, so a 500 MB PDF is processed in
  **seconds to a few minutes**, not instantly. The Laravel job timeout is
  configured accordingly (`PDF_WORKER_TIMEOUT`).