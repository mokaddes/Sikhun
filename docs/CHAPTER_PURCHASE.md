# Chapter Purchase

## Model

A book may be sold whole (existing flow) or chapter-by-chapter when
`books.chapter_purchase_enabled = true`. Top-level `book_chapters` rows with
a non-null `price` become individually purchasable; descendant sections
(level ≥ 2) are always included with their parent chapter.

Ownership lives in `student_book_chapters` with a **DB-level unique
constraint on (student_id, book_id, chapter_id)** — two simultaneous purchase
attempts can never create duplicate ownership.

## Purchase flow (reuses the existing payment machinery — no second system)

```
POST /library/{book}/chapters/{chapter}/purchase  (web, auth:web)
POST /api/library/{book}/chapters/{chapter}/purchase (API, sanctum)
        ↓
PurchaseService::purchaseChapter()
        ├── creates Order (orderable_type = 'book_chapter')
        ├── wallet → instant debit + fulfill
        └── zinipay → gateway redirect; webhook completes + fulfills
        ↓
fulfill(): firstOrCreate into student_book_chapters (idempotent)
```

The `orders.orderable_type` enum gained `book_chapter` via additive
migration. Chapter orders carry `meta.book_id` so fulfillment can link
ownership without a lookup chain.

## Access rules (all centralized in BookAccessService)

| Who | Book | Chapters | Pages |
|---|---|---|---|
| Full-book owner (bought / gifted / subscription gift / campaign / coupon) | ✓ | all ✓ | all ✓ |
| Chapter owner | ✗ | owned + descendants | pages in owned chapters |
| Free book | ✓ | all ✓ | all ✓ |
| Guest | ✗ | ✗ | ✗ |

`canAccessPage()` resolves the page's chapter (via `book_pages.chapter_id`)
and checks chapter access — the reader's `pageUrl` endpoint gates every
page on it, server-side, on every request. The FlipReader component
additionally skips over locked pages for smooth navigation, but the server
is the authority.

## API surface

```
GET  /api/library/{book}/chapters            → chapter list + owned/purchasable flags
GET  /api/library/{book}/access              → access_type + accessible_chapter_ids
POST /api/library/{book}/chapters/{chapter}/purchase
```

Web equivalents: the book detail page renders the chapter purchase UI
directly; `POST /library/{book}/chapters/{chapter}/purchase` submits it.

## AI interaction

Chapter-scoped RAG sees `accessibleChapterIds()` — a chapter owner chatting
about the book only ever retrieves content from chapters they own (see
`docs/RAG_ARCHITECTURE.md`).

## Admin

- Toggle chapter selling per book: `chapter_purchase_enabled` checkbox on
  the book form.
- Chapter prices are stored on `book_chapters.price` (parsed chapters get
  `null`; set prices after parsing — chapter price editing UI is a
  follow-up; the API/DB layer supports it now).
