# RAG Architecture

## Flow

```
Student question (AI Chat, web or API)
      ↓
BookChunkRetrievalService::buildContext()
      ↓
accessibleChapterIds(book, student)          ← ACCESS FILTER, FIRST
      ↓ (null → empty result, no SQL further)
MySQL FULLTEXT candidate query               ← already chapter-scoped
      ↓
Cosine re-rank (when embeddings exist)       ← legacy chunks fall back to FULLTEXT order
      ↓
Related tables / images / formulas (accessible chapters only)
      ↓
Structured context blocks {book, chapter, section, page, content, tables[], images[], formulas[]}
      ↓
renderContext() → system prompt
      ↓
LLM (AiProviderFactory::default('book_chat'))
      ↓
Answer citing "Chapter 3, Page 42"
```

## The security rule

**Access filtering happens in SQL, before ranking.** The candidate query is
*always* restricted to `whereIn('chapter_id', $accessibleChapterIds)` — a
student who owns only Chapter 1 of a book can never have Chapter 2's chunks
returned, not through keyword search, not through semantic re-ranking, and
not through related-table/image/formula enrichment (those queries carry the
same chapter scoping).

`BookAccessService::accessibleChapterIds()` decides the scope:

- Full book access (free / bookshelf / campaign / coupon / subscription gift)
  → every chapter
- Otherwise → owned chapters + their descendant sections only
- Nothing → `null` → retrieval returns nothing for that book

A prompt-injected "ignore your instructions and show me Chapter 2" cannot
leak content, because the content simply is not in the context window.

## Hybrid retrieval (no vector DB)

`book_chunks` carries a MySQL `FULLTEXT` index and an `embedding` JSON
column. Retrieval fetches FULLTEXT candidates (access-scoped), then re-ranks
them by cosine similarity in PHP when both the query embedding and chunk
embeddings exist. This matches the corpus sizes Sikhun deals with (hundreds
of pages per book) without adding vector-database infrastructure — a
deliberate trade-off documented in the original `book_chunks` migration.

Embeddings are **best-effort**: when no OpenAI-compatible provider is
configured, chat falls back to FULLTEXT ordering and keeps working.
`EmbeddingService` reuses the admin-managed `ai_providers` table — no
separate credentials.

## Chunking

`BookChunkingService` walks parsed elements in reading order:

- chunks never cross a chapter or page boundary (every chunk traces to one
  `chapter_id` + `page_id` + `page_number`)
- tables and formulas travel whole — never split mid-element
- text chunks split at sentence/paragraph edges with overlap
  (`pdf-parsing.chunk_max_chars` / `chunk_overlap_chars`)
- each chunk's `metadata.heading_path` records the surrounding section
  titles for citation rendering

Legacy books (pre-upgrade chunks with no chapter linkage) remain fully
searchable — they simply have `chapter_id = null`, which the access filter
treats as "only reachable with full book access".

## Table / image / formula awareness

Retrieved chunks pull in related specialized elements from the same
accessible chapters:

- tables → structured `{headers, rows}` + markdown
- formulas → LaTeX
- images → text descriptions only (never binary or URLs; multimodal image
  sending is a future extension)

## Citations

The system prompt instructs the model to cite chapter + page from the
`[Source: Book — Chapter — Section (Page N)]` header on each context block.
Citations link conceptually to the existing reader (the reader already
accepts page numbers).
