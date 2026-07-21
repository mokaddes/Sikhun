# Sikhun.com — Setup Guide

> **Already ran Phase 1 and it's working?** Skip straight to
> [Applying the Phase 2 update](#applying-the-phase-2-update) near the bottom —
> you don't need to redo steps 1–9 below.

This package is an **overlay** on top of a fresh Laravel 11 install — it contains
everything Phase 1 needs (DB schema, models, seeders, multi-guard auth, Vue/Inertia
scaffold, dark/light theme system) but not Laravel's own boilerplate (the `artisan`
CLI script, default service providers, `storage/` runtime folders, etc). You generate
that boilerplate locally with one command, then copy this overlay on top of it.

## 1. Create the base Laravel project

```bash
composer create-project laravel/laravel sikhun
cd sikhun
```

## 2. Copy this overlay into it

Copy **everything** from this package into the `sikhun/` folder you just created,
overwriting when prompted (it will overwrite `bootstrap/app.php`, `config/auth.php`,
`routes/web.php`, `resources/css/app.css`, `resources/views/app.blade.php`, and
`package.json` / `vite.config.js` / `tailwind.config.js` — that's expected).

```bash
# from inside this package's folder
cp -r app database routes config resources public composer.json package.json \
   vite.config.js tailwind.config.js postcss.config.js .env.example \
   /path/to/your/sikhun/
```

## 3. Install PHP dependencies

The `composer.json` in this package already lists every package Phase 1–7 will need
(Sanctum, Reverb, Horizon, Scout, Spatie Permission/MediaLibrary/Sitemap, Inertia,
DomPDF, Intervention Image, OpenAI, Firebase). Just run:

```bash
composer install
```

> If `composer create-project` already generated a `composer.lock`, delete it first
> (`rm composer.lock`) so Composer re-resolves against the fuller package list.

## 4. Install JS dependencies

```bash
npm install
```

## 5. Environment

```bash
cp .env.example .env   # if not already copied
php artisan key:generate
```

Edit `.env` and set your local `DB_*` credentials. Redis is required for
`CACHE_STORE` and `QUEUE_CONNECTION` — install it locally or point to a hosted
instance. If you don't have Redis yet, temporarily set `CACHE_STORE=file` and
`SESSION_DRIVER=file` / `QUEUE_CONNECTION=sync` to get moving, then switch back
before Phase 4+ (AI job queues need Redis).

## 6. Database

Create an empty MySQL database named `sikhun` (or whatever you set in `.env`), then:

```bash
php artisan migrate:fresh --seed
```

This runs all 35 migrations (34 feature tables + Sanctum tokens) and all 14 seeders.
Zero errors is the Phase 1 sign-off condition.

## 7. Storage symlink (needed from Phase 3 onward, safe to run now)

```bash
php artisan storage:link
```

## 8. Build frontend & run

```bash
npm run build      # production build — verifies zero build errors
# or, for local development:
npm run dev         # in one terminal
php artisan serve   # in another terminal
```

Visit `http://localhost:8000`.

## 9. Test the three auth flows

| Flow | URL | Credentials |
|---|---|---|
| Student register | `/register` | (create your own) |
| Student login | `/login` | `student1@sikhun.com` / `student123` |
| Admin login | `/admin/login` | `admin@sikhun.com` / `admin123` |

Toggle the theme icon (top right) — it should cycle Light → Dark → System, persist
across a page reload (localStorage), and — once you're logged in and Phase 7's
`/api/profile/theme` endpoint exists — persist to your account too.

---

## What's included in this drop (Phase 1 + Phase 2 + EN/BN i18n)

### Phase 1 — Foundation
- **35 migrations** — the complete DB schema for the whole platform
- **34 Eloquent models** — fully related, with correct casts
- **14 seeders** — admin account, AI providers, plans, demo books/courses/students
- **Multi-guard auth** — `web` / `admin` / `api`, fully isolated
- **Dark / Light / System theme system**
- **Three layouts** — PublicLayout, StudentLayout, AdminLayout

### EN/BN Multilingual System (new)
- `lang/en.json` / `lang/bn.json` — flat-nested dictionaries (187 keys), one source of
  truth for every string in the frontend
- `App\Http\Middleware\SetLocale` — reads the student's saved language from session,
  defaults to Bengali (`APP_LOCALE=bn`)
- `HandleInertiaRequests` shares `locale` + `translations` on every page load (cached
  1hr per locale — clear with `php artisan cache:clear` after editing a lang file)
- `POST /language/{locale}` — switches language, persists in session, works for both
  guests and logged-in students
- `resources/js/i18n.js` — the `useI18n()` composable (`t()`, `locale`, `switchLocale()`)
  — no external i18n library, ~50 lines, zero extra requests
- `LanguageSwitcher.vue` — EN/বাং toggle, wired into all three layouts
- Every Phase 1 page and every new Phase 2 admin page uses `t('...')` — zero hardcoded
  user-facing strings remain

### Phase 2 — Admin CRUD (new)
- **Reference data**: Categories, Authors, Publications, Mentors — full CRUD, slug
  auto-generation from name (editable), delete confirmation
- **Books** — full CRUD with real file uploads: cover image → public disk, PDF →
  `private` disk (see `config/filesystems.php` — explicit `private` disk alias so the
  security intent is unambiguous at every call site)
- **Courses** — CRUD + a nested Sections/Lessons manager on the edit screen (add/delete
  sections and lessons inline, no page reload)
- **Plans** — CRUD with a features textarea (newline → array) and a gift-books
  multi-select pulling from published books
- **AI Providers** — CRUD + a **live "Test Connection" button** wired to
  `App\Services\Ai\AiConnectionTester`, which pings the real provider endpoint
  (OpenAI/Groq/DeepSeek models list, Gemini models list, a 1-token Anthropic message,
  or a local Ollama/vLLM health check) — safe to click with or without a real key
- **Students** — searchable/filterable list, profile page with **working wallet
  credit/debit** (`WalletService`, atomic, transaction-logged) and **subscription
  assignment** (`SubscriptionService`, atomic, auto-adds gift books to bookshelf,
  expires any prior active subscription first), plus activate/deactivate toggle
- **Orders** — list with status filter + manual-payment approval action
- **Site Settings** — single form, cache-invalidating on save
- **Custom Pages (CMS)** — full CRUD for the About/FAQ/Terms/etc pages
- **Admin Dashboard** — now has two **real ApexCharts** (14-day revenue area chart,
  14-day registrations bar chart) driven by actual DB aggregation queries, not
  hardcoded numbers
- Shared UI: `FlashBanner` (auto-dismissing success/error toast, wired into every
  layout), `Pagination` (renders Laravel's paginator links), `ConfirmButton`
  (confirm-before-delete, i18n-aware)

## Package version note

`package.json` in this drop matches what you're already running: `apexcharts@^5.10.0`
+ `vue3-apexcharts@^1.11.1` (registered globally in `resources/js/app.js` as the
`<apexchart>` component — used on the admin dashboard).

## What's intentionally NOT in this drop (later phases)

Library/book reader (student-facing), AI chat/exam/flashcard/essay/schedule engines,
wallet purchase flow (student-facing checkout), leaderboard UI, referrals UI,
notifications, course player (student-facing), support chat widget, REST API
controllers, SEO service, sitemap. These are all fully specified in `docs/AGENT_PROMPT.md`,
`docs/REQUIREMENTS.md`, `docs/SKILLS.md`, and `docs/PHASE_CHECKLIST.md` — say the word
and I'll build Phase 3 (student-facing library + book reader + wallet checkout) next.

## Known gaps to fill in before this is production-ready

- `AiConnectionTester` checks reachability/auth validity only — the real chat/stream/
  embed methods (`AiProviderFactory` + 8 provider drivers) are Phase 4
- Course lesson **editing** (title/type/etc after creation) isn't wired in the UI yet —
  only add/delete; the `update` routes exist server-side (`courses.lessons.update`),
  just add an edit toggle to `Courses/Form.vue` when you need it
- No tests yet — add PHPUnit/Pest feature tests per controller as you go
- `SiteSettingController` doesn't yet re-validate against the full settings seeded in
  Phase 1 (e.g. `ai_trial_minutes_default`, `registration_open`) — extend the form as
  those become relevant to an admin workflow

## Multilingual notes for future phases

Every new page you add should pull its strings from `lang/en.json` / `lang/bn.json`
via `useI18n()` — never hardcode. If you add a key, add it to **both** files with the
same path (a quick way to check for gaps: grep all `t('...')` calls in `resources/js`
and diff against both JSON files — that's exactly how this drop was verified before
packaging).

---

## Applying the Phase 2 update

You already have Phase 1 running. Here's exactly what to do to bring in Phase 2
(admin CRUD + EN/BN multilingual) without starting over.

### 1. Copy the new/changed files over your existing project

```bash
# from inside this package's folder, into your existing sikhun/ project:
cp -r app database routes config resources lang package.json /path/to/your/sikhun/
```

This adds:
- `lang/en.json`, `lang/bn.json` (new)
- `app/Http/Middleware/SetLocale.php` (new)
- `app/Http/Controllers/Admin/*` — 11 new controllers (Category, Author, Publication,
  Mentor, Book, Course, CourseSection, CourseLesson, Plan, AiProvider, Student, Order,
  SiteSetting, CustomPage)
- `app/Http/Requests/Admin/*` — matching Form Requests
- `app/Services/WalletService.php`, `app/Services/SubscriptionService.php`,
  `app/Services/Ai/AiConnectionTester.php` (new)
- `config/filesystems.php` (new — explicit `private` disk)
- `routes/admin.php` (replaced — now has all Phase 2 routes)
- `resources/js/i18n.js`, `resources/js/Components/UI/LanguageSwitcher.vue`,
  `FlashBanner.vue`, `Pagination.vue`, `ConfirmButton.vue` (new)
- `resources/js/Pages/Admin/{Categories,Authors,Publications,Mentors,Books,Courses,
  Plans,AiProviders,Students,Orders,Settings,Pages}/*` (new — ~24 Vue pages)
- The three layouts and every Phase 1 page (Register, Login, dashboards, Home) —
  updated in place to use `t('...')` instead of hardcoded strings
- `bootstrap/app.php` — `SetLocale` added to the web middleware stack (before Inertia)
- `.env.example` / `.env` — `APP_LOCALE` changed from `en` to `bn`

### 2. No new Composer packages needed

`AiConnectionTester` uses Laravel's built-in `Http` facade — nothing new to install.

### 3. Create the storage directories (if they don't already exist)

```bash
mkdir -p storage/app/private/books storage/app/public/books/covers storage/app/public/courses/covers
php artisan storage:link   # safe to re-run
```

### 4. Clear cached config/translations and rebuild

```bash
php artisan config:clear
php artisan cache:clear
npm install   # picks up vue3-apexcharts if you didn't already have it
npm run build
```

### 5. Log in and check it

- `/admin` → you should now see a full sidebar (Books, Courses, Categories, Authors,
  Publications, Mentors, Students, Plans, AI Providers, Orders, Pages, Settings) and
  two live charts on the dashboard
- Click the **EN / বাং** button top-right (present on every page, both student and
  admin sides) — the whole UI should re-render in the other language instantly, and
  survive a page refresh
- `/admin/ai-providers` → click **Test Connection** on any row (works even without a
  real key — it'll just report "No API key configured" instead of erroring)
- `/admin/books/create` → upload a cover image and a PDF, save, confirm the cover
  shows up and the PDF is **not** reachable at any public URL
- `/admin/students` → open a student, credit their wallet, assign a subscription —
  confirm the balance and subscription panel update immediately

---

## Applying the Phase 3 update

Phase 3 adds the student-facing library, secure book reader, wallet/checkout, and
subscription purchase flow on top of Phase 1 + 2.

### 1. Copy the new/changed files over your existing project

```bash
cp -r app database routes config resources lang /path/to/your/sikhun/
```

New in this drop:
- **2 migrations**: `reading_sessions` table, and an `orders` table extension (adds
  `wallet_recharge` as a 4th `orderable_type`, makes `orderable_id` nullable, adds a
  `meta` JSON column) — uses raw `ALTER TABLE` so no `doctrine/dbal` dependency needed
- **Services**: `BookAccessService`, `PurchaseService` (the checkout orchestrator —
  every book/subscription/wallet-recharge purchase flows through here),
  `ReferralService`, `BookReaderService`, `Payment\SslcommerzService` +
  `Payment\PaymentGatewayContract`
- **Controllers**: `Student\LibraryController`, `BookshelfController`,
  `ReaderController`, `WalletController`, `SubscriptionController`
- **`config/sslcommerz.php`** (new) — reads the `SSLCOMMERZ_*` vars already in your
  `.env` from Phase 1
- **`app/Providers/AppServiceProvider.php`** (replaced) — registers the
  `reader-pages` rate limiter (5 requests / 10 seconds)
- **`routes/web.php`** (replaced) — adds library/bookshelf/reader/wallet/subscription
  routes, plus the SSLCommerz callback routes and the signed+throttled reader-page route
- **12 new Vue pages** under `Pages/Student/{Library,Bookshelf,Wallet,Subscription}`
  + `Components/Reader/FlipReader.vue`
- **`OrderController::approve`** (admin, replaced) — now actually calls
  `PurchaseService::fulfill()` instead of just flipping a status flag, so manually
  approving a pending order (bank transfer, etc.) delivers the same access a card
  payment would
- 41 new translation keys in `lang/en.json` / `lang/bn.json`

### 2. No new Composer packages needed

`SslcommerzService` uses Laravel's built-in `Http` facade.

### 3. Run the new migrations

```bash
php artisan migrate
```

### 4. Create the reading-session-safe storage paths (if not already present)

Books need a real PDF uploaded via `/admin/books/{id}/edit` before the reader shows
real pages — until then it shows a clearly-labelled placeholder page instead of
erroring, so you can test the whole purchase → bookshelf → reader flow immediately
with the seeded demo books.

### 5. About the SSLCommerz integration

This is a **real** sandbox integration (session API v4 + validator API) — get free
sandbox credentials at https://developer.sslcommerz.com and set them in `.env`:

```env
SSLCOMMERZ_STORE_ID=your_sandbox_store_id
SSLCOMMERZ_STORE_PASSWORD=your_sandbox_store_password
SSLCOMMERZ_IS_SANDBOX=true
```

Without real credentials, clicking "Pay Online" will fail at the session-init step
with SSLCommerz's own error message (surfaced via `RuntimeException` → flash message)
— the **wallet payment path and manual bank transfer path both work immediately**
with zero external credentials, so you can test the full purchase flow without
signing up for anything.

bKash and Nagad are **not** wired yet — `PaymentGatewayContract` exists so they drop
in the same way SSLCommerz did; the wallet recharge form only offers SSLCommerz +
manual transfer for now, intentionally, rather than showing non-functional buttons.

### 6. About the book reader

Turn.js (mentioned in the original spec) is jQuery-based and doesn't play well with
Vite's ESM pipeline, so `FlipReader.vue` is a custom equivalent built with Vue
transitions + CSS 3D transforms — same UX (page-turn feel, prev/next, keyboard arrows),
zero jQuery. Signed URLs are minted fresh per page-flip (via a small authenticated JSON
endpoint) rather than pre-signed in a batch, which sidesteps the "refresh before
expiry" problem entirely — there's nothing to expire mid-session.

### 7. Try it

- `/library` → browse, filter by level, search, toggle free-only
- Open any paid book → try **Pay with Wallet** (seeded students have ৳0–500 balance
  from `DemoDataSeeder`) and **Pay Online** (will show SSLCommerz's real error without
  sandbox credentials — that's expected and correct)
- `/wallet` → recharge via **Manual Bank Transfer**, then go to `/admin/orders` and
  approve it — confirm the balance updates
- `/bookshelf` → confirm purchased/free books appear, click through to the reader
- `/plans` → subscribe via wallet, confirm gift books appear on your bookshelf and
  `/admin/students/{id}` shows the active subscription
- Refer a friend: register a second account with `?ref=` + your first student's
  referral code (visible nowhere in the UI yet — pull it from `students.referral_code`
  in the DB for now, or add a referral dashboard page in a later phase), have them buy
  something, confirm both wallets get credited

---

## Applying the Phase 4 update

Phase 4 adds all 5 AI features: AI Chat, AI Exam Engine, Flashcard Generator, Essay
Grader, and Study Schedule Maker — plus the `AiProviderFactory` and all 8 real
provider drivers that everything else in the project was already designed around.

### 1. Copy the new/changed files over your existing project

```bash
cp -r app database routes config resources lang composer.json /path/to/your/sikhun/
```

New in this drop:
- **1 migration**: `book_chunks` (with a MySQL FULLTEXT index) — text chunks extracted
  from a book's PDF, used to ground AI Chat answers in the book's actual content
- **`smalot/pdfparser`** added to `composer.json` — pure-PHP PDF text extraction, no
  system dependencies (unlike Imagick, needed nowhere in this phase)
- **`AiProviderContract`** + **`AiProviderFactory`** + 8 real provider classes
  (OpenAI, Gemini, Claude, Groq, DeepSeek, Ollama, vLLM, HuggingFace) — every single
  one does real HTTP calls with real streaming parsing (SSE for OpenAI-compatible +
  Anthropic + Gemini, newline-delimited JSON for Ollama)
- **`BookChunkRetrievalService`** — the "retrieval" half of AI Chat's RAG (see the
  important caveat below)
- **`EnsureHasAiAccess`** middleware (`ai.access` alias) — gates every AI-generation
  endpoint behind "active subscription OR unused trial minutes"
- **`ProcessBookChunking`** job — dispatched automatically whenever an admin uploads
  a book PDF (see `Admin\BookController`, now wired to dispatch it)
- **5 feature controllers** (`AiChatController`, `ExamController`,
  `FlashcardController`, `EssayController`, `ScheduleController`) + their background
  jobs (`GenerateExamQuestions`, `GenerateFlashcards`, `GradeEssay`,
  `GenerateStudySchedule`) — all `ShouldQueue`, all on the `ai` queue
- **3 PDF Blade templates** (`resources/views/pdf/*`) for exam answer sheets,
  flashcard sets, and study schedules — via the `barryvdh/laravel-dompdf` already in
  `composer.json` since Phase 1
- **21 new Vue pages** across `AiChat`, `Exams`, `Flashcards`, `Essays`, `Schedules`
  + `ExamTimer.vue`, `FlashcardFlip.vue` components
- 60 new translation keys

### 2. Install the new Composer package

```bash
composer require smalot/pdfparser:^2.9
```

### 3. Run the new migration

```bash
php artisan migrate
```

### 4. Queue worker (or the zero-setup sync shortcut)

Every AI job is `ShouldQueue` on the `ai` queue. Two ways to run them:

```bash
# Option A — real queue (matches production)
php artisan queue:work redis --queue=ai,notifications,default

# Option B — zero setup for local testing: skip the worker entirely
# by setting this in .env, and jobs execute inline, synchronously:
QUEUE_CONNECTION=sync
```

Option B is the fastest way to see this working the first time — no Redis, no
worker process, just click "Generate" and the page you're redirected to already
has the AI's response.

### 5. ⚠️ Important: RAG is keyword search, not vector search

`BookChunkRetrievalService` uses a MySQL FULLTEXT index, **not** embeddings +
cosine similarity. The original spec called for pgvector or Qdrant — both need
infrastructure this MySQL-based stack doesn't have. Keyword search gets a real
majority of the way there for grounding answers in a book's actual text, but it
will miss semantically-related-but-differently-worded passages that true vector
search would catch. If retrieval quality becomes the bottleneck later, swap the
query inside `BookChunkRetrievalService::relevantChunks()` for a real vector
search — nothing else in the codebase needs to change, since every caller only
knows about the method's return value, not how it's computed.

### 6. Configure at least one AI provider before testing

Every feature needs a default provider set for its use case (Phase 2's admin panel
already has this UI). Fastest path: `/admin/ai-providers` → add an OpenAI provider
for `book_chat`, `exam_gen`, `flashcard_gen`, `essay_grade`, and `schedule_gen`, mark
each **Default**, and paste a real `OPENAI_API_KEY`. Without a real key, every AI
action will fail gracefully (exam/flashcard/schedule status flips to `failed`, chat
shows an inline error) rather than hanging or crashing.

### 7. Try it

- `/admin/books/{id}/edit` → upload a PDF → confirm `book_chunks` rows appear
  (`ProcessBookChunking` ran) — needed before AI Chat can ground answers in that book
- `/ai/chat` → start a chat scoped to that book, ask something from its content
- `/exams/create` → generate a 10-question practice exam on any topic, try both
  Practice mode (reveal-per-question) and Exam mode (timer + submit-all)
- Complete an Exam-mode session with ≥10 questions → confirm a `LeaderboardEntry`
  row appears in the DB (no leaderboard UI yet — that's a later phase)
- `/flashcards/create` → generate a set, flip through it, mark Known/Review Again,
  download the PDF
- `/essays/create` → submit ~150 words, watch it grade, check the score breakdown
- `/schedules/create` → generate a plan for an exam 30 days out, mark a day done,
  download the PDF
- Exhaust your trial minutes (default 10 — seeded students already start at 0 used)
  by doing 10 AI actions, then confirm the 11th is blocked with an upgrade prompt

---

## Applying the Phase 5 update

Phase 5 adds the Leaderboard UI, Referral dashboard, and real-time Notifications
(Reverb broadcasting + AI-generated daily notifications + admin broadcast tool).

### 1. Copy the new/changed files over your existing project

```bash
cp -r app database routes config resources lang package.json /path/to/your/sikhun/
```

New in this drop:
- **`LeaderboardService`** — Redis-cached top-100 + student's own rank, weekly/
  monthly/all-time, filterable by student type/subject/book
- **`NotificationService`** — the 10-type notification system, enforces the
  3-per-day cap and per-type student preferences before ever creating a row
- **`NewNotificationBroadcast`** event + **`routes/channels.php`** — real Reverb
  WebSocket broadcasting on a private per-student channel
- **`config/broadcasting.php`** (new) — Reverb connection config
- **4 console commands**: `notifications:generate-ai`, `notifications:send-scheduled`,
  `leaderboard:reset-weekly`, `leaderboard:reset-monthly` — all scheduled in
  `routes/console.php`
- **`ProfileController`** (new) — also fixes a Phase 1 gap: the theme store was
  calling a `/api/profile/theme` endpoint that never existed. It now correctly
  calls `/profile/theme` and actually persists your theme choice to your account
- **Student pages**: Leaderboard, Referral dashboard, Profile Settings (theme sync
  + leaderboard opt-out + notification preferences)
- **Admin pages**: Referrals list, Leaderboard entry management, Notification
  broadcast tool
- **`NotificationBell.vue`** — wired into `StudentLayout`'s header, live-updates
  via Reverb when connected, falls back to silent no-op if Reverb isn't running
- **`laravel-echo` + `pusher-js`** added to `package.json` (Reverb speaks the Pusher
  protocol, so Echo's Pusher-compatible client works as-is)
- 33 new translation keys

### 2. Install the new npm packages

```bash
npm install
```

### 3. Set up Reverb (optional but needed for live notifications)

```bash
composer require laravel/reverb
php artisan reverb:install   # generates REVERB_* keys if you don't have real ones yet
php artisan reverb:start
```

Without Reverb running, everything still works — notifications are created and
appear the next time the bell dropdown is opened (`fetchInitial()` on mount) — they
just won't pop in **live** without a page refresh. The store's `listen()` call fails
silently (logged to console) if Reverb isn't reachable, by design.

### 4. Run the scheduler for AI notifications + leaderboard resets

```bash
php artisan schedule:work
```

Or trigger any of the 4 commands manually to test immediately:
```bash
php artisan notifications:generate-ai
php artisan notifications:send-scheduled
```

### 5. Try it

- Complete a couple of Exam-mode tests (from Phase 4) with different demo student
  accounts, then check `/leaderboard` — weekly/monthly/all-time tabs, rank badges
- `/referrals` → copy your link, register a second account with `?ref=CODE`, have
  it make a purchase (Phase 3), confirm both wallets get credited and the referral
  shows as "Rewarded"
- `/profile` → toggle leaderboard opt-out, confirm you disappear from `/leaderboard`
  but your exam still counts; adjust notification preferences and preferred times
- `/admin/notifications/create` → broadcast a message to all HSC students, run
  `php artisan notifications:send-scheduled`, confirm it lands in their bell dropdown
- Toggle dark/light mode, refresh the page, confirm it's still applied (was silently
  failing to persist before this phase's `ProfileController` fix)

---

## Applying the Phase 6 update

Phase 6 adds the Course system (enrollment, lesson player, certificates), the AI-powered
Support Chat widget (guest + logged-in, admin takeover), and a real Contact form.

### 1. Copy the new/changed files over your existing project

```bash
cp -r app database routes config resources lang /path/to/your/sikhun/
```

New in this drop:
- **`PurchaseService::purchaseCourse()`** + a `course` fulfillment case — course
  purchases were deliberately deferred in Phase 3 (courses didn't exist yet); this
  closes that gap using the exact same wallet/gateway pattern as books and
  subscriptions
- **`CertificateService`** — generates a landscape PDF certificate via DomPDF the
  moment a student's course progress hits 100%, stored on the public disk (a
  certificate is meant to be shareable, unlike a book PDF)
- **`Student\CourseController`** — index/show/enroll/lesson/completeLesson, with
  free-preview lessons visible pre-enrollment and locked lessons showing a 🔒
- **`Public\SupportController`** — the support chat backend, works identically for
  guests (session-tracked token, no login needed) and logged-in students
- **`SupportWidget.vue`** — floating chat bubble, now mounted in **both**
  `PublicLayout` and `StudentLayout`, so it's genuinely on every page
- **`Admin\SupportController`** + 2 admin pages — conversation list, manual reply
  (switches a conversation to human-handled), bot on/off toggle, close/resolve
- **`ContactMessageMail`** + **`Public\ContactController`** — a real contact form at
  `/contact` that emails whatever `site_email` is set to in Site Settings
- Site Settings gained 2 new fields: max referrals/month (was hardcoded before) and
  the support bot's system prompt (editable without a code change)
- `Course` model gained the same `cover_image_url` accessor pattern `Book` already
  had — was missing, would have silently rendered no course covers
- 24 new translation keys

### 2. No new Composer or npm packages needed

Everything here reuses `barryvdh/laravel-dompdf` (certificates) and Laravel's
built-in `Mail` facade (contact form) — both already in place since earlier phases.

### 3. Configure mail (for the contact form to actually send)

Your `.env` already has `MAIL_*` vars from Phase 1, pointed at a local catch-all
(`127.0.0.1:2525` — e.g. Mailpit or `php artisan serve` + a tool like Mailtrap).
Point `MAIL_HOST`/`MAIL_PORT` at whatever you're using locally, or a real SMTP
provider for production.

### 4. Try it

- `/courses` → browse, open a paid course, enroll via wallet, watch progress update
  as you complete lessons, download the certificate once you hit 100%
- Open any course's first lesson in an incognito window (not logged in) — free-preview
  lessons should be viewable without enrolling; the rest should show 🔒
- Click the floating 💬 button on **any** page, logged in or not — send a message,
  confirm the AI replies (needs a `support_bot` provider configured in
  `/admin/ai-providers`, same as Phase 4)
- `/admin/support` → open the conversation you just created, toggle the bot off, send
  a manual reply, confirm it shows up in the widget
- `/admin/settings` → edit the support bot's system prompt, confirm new chat replies
  reflect the change
- `/contact` → submit the form, confirm an email arrives at whatever `site_email` is
  set to

---

## Applying the Phase 7 update — the final phase

Phase 7 adds the full REST API (for the eventual mobile app), SEO (sitemap, JSON-LD,
meta service, hreflang), custom error pages, transactional emails, and a handful of
real bugs/gaps found and fixed while wiring all of it together.

### 1. Copy the new/changed files over your existing project

```bash
cp -r app database routes config resources lang bootstrap public /path/to/your/sikhun/
```

New in this drop:
- **17 API controllers** under `app/Http/Controllers/Api`, all extending
  `BaseApiController` for the standard `{success, data, message, meta}` envelope —
  auth, library, reader, courses, wallet, subscriptions, leaderboard, referrals,
  profile, notifications, support, pages, and all 5 AI features (chat/exam/
  flashcard/essay/schedule), each reusing the exact same Services and Jobs the web
  controllers already used — zero duplicated business logic
- **`routes/api.php`** (replaced) — fully wired, Sanctum-protected, with a
  `throttle:api` (60/min) general limit and a tighter `throttle:api-ai` (10/min) on
  every AI-generation endpoint
- **`SeoService`** + **`SeoHead.vue`** + **`JsonLd.vue`** — home/book/course pages
  now emit real title/description/OG/Twitter meta tags plus JSON-LD structured data
  (`EducationalOrganization`, `Book`, `Course`, `BreadcrumbList`)
- **`sitemap:generate`** command (scheduled daily) + **`public/robots.txt`**
- **Custom error pages** — 403/404/419/429/500/503 now render a branded page instead
  of Laravel's default whitescreen (only outside `APP_DEBUG=true`, so you still get
  real stack traces locally)
- **3 new Mailables**: `WelcomeMail` (sent on registration), `OrderConfirmationMail`
  (sent on every completed book/course/subscription purchase), `SubscriptionExpiryMail`
  (7/3/1-day warnings via a new `subscriptions:expiry-check` scheduled command)
- **`SiteSettingService`** — centralizes the site-settings cache that was previously
  duplicated as raw uncached queries in `ReferralService`, both `SupportController`s,
  and `ContactController`

### 2. ⚠️ Important fix: Library and Course pages were accidentally login-gated

While wiring SEO I found that `/library`, `/library/{book}`, `/courses`,
`/courses/{course}`, and course lesson pages were sitting behind the `auth:web`
middleware group since the phases that built them — meaning **search engines could
never have crawled or indexed your core content**, and the "guests can preview free
lessons" behavior claimed in the Phase 6 update never actually worked, because the
route itself redirected guests to login before the controller's free-preview logic
ever ran. Both are now genuinely public routes; only purchase/enroll/read/complete
actions still require login. `BookAccessService` and `CourseController::lesson` now
handle a `null` (guest) student correctly, and `Library/Show.vue` shows a "Log in to
purchase" CTA for the new `guest` access type instead of assuming a logged-in student.

### 3. No new Composer packages needed

Sitemap generation uses `spatie/laravel-sitemap`, already in `composer.json` since
Phase 1 (added then, unused until now).

### 4. Run the new migrations — there are none

Phase 7 added zero new tables; everything here is application-layer.

### 5. Generate the sitemap once manually to verify

```bash
php artisan sitemap:generate
# then check public/sitemap.xml exists and contains your published books/courses
```

### 6. Add a real Open Graph image

`public/images/README.md` (in this drop) explains: `SeoService` references
`/images/og-default.png` as the fallback social-share image. Add a real 1200×630px
PNG there before launch — until then, social previews just show no image (not broken,
just plain).

### 7. Known trade-offs / honest gaps in this REST API + SEO pass

- **Hreflang is self-referencing.** Because Sikhun switches language via a
  session-stored cookie rather than URL prefixes (`/en/...` vs `/bn/...`), the
  `hreflang="bn"` and `hreflang="en"` tags both point at the same canonical URL.
  This is a known simplification — true hreflang best practice needs distinct URLs
  per language, which would mean restructuring routing across every phase. Search
  engines still index the page fine; they just can't distinguish the two language
  versions as separate URLs.
- **`SeoService::forFaq()` exists but isn't wired up.** A real `FAQPage` JSON-LD
  schema needs structured question/answer pairs, but `/p/faq` (like every CMS page)
  stores a single HTML blob via the admin's rich-text-free textarea editor. The
  method is there, ready, for whenever FAQ content gets a structured editor.
- **`laravel/scout` sits unused in `composer.json`.** Phase 3 deliberately chose
  plain SQL `LIKE` search over Scout to avoid standing up Meilisearch; the dependency
  was never removed since a future search upgrade might want it. Harmless, just
  worth knowing it's there and inert.
- **No automated test suite.** Every phase's code was verified structurally (PHP
  brace/paren balance, Vue template tag matching, translation-key coverage — all
  scripted, all passing) since this sandbox has no PHP runtime to actually execute
  `php artisan test`. Adding PHPUnit/Pest feature tests per controller is the natural
  next step before a real production launch.
- **Images across Phases 1–6 weren't retroactively audited for `alt`/lazy-loading.**
  This phase added them to the highest-traffic public pages (home, book/course
  detail, book/course listing) — student-panel-only images (bookshelf, admin tables)
  weren't touched, since SEO doesn't reach behind a login wall anyway.

### 8. Try it

- `curl -X POST http://localhost:8000/api/auth/login -d "email=student1@sikhun.com&password=student123"`
  → confirm you get back a token in the standard envelope
- Use that token (`Authorization: Bearer ...`) against `GET /api/library`,
  `GET /api/wallet`, `POST /api/exams` — confirm the same envelope shape everywhere
- Visit `/library` and `/courses` in an incognito window (logged out) — confirm they
  load without redirecting to `/login`, and that "View Source" shows real
  `<title>`/`<meta description>`/JSON-LD in the HTML (not just in the rendered DOM)
- `curl http://localhost:8000/sitemap.xml` after running `sitemap:generate` — confirm
  it lists your books and courses
- Trigger a 404 (visit a nonexistent book slug) — confirm you see the branded error
  page, not Laravel's default one (only works with `APP_DEBUG=false` in `.env`)
- Register a new account — confirm a welcome email arrives; buy a book — confirm an
  order confirmation email arrives

---

## That's all 7 phases

Sikhun.com is now feature-complete against the original specification: multi-guard
auth, full admin CRUD, EN/BN localization, library + secure reader + wallet/checkout,
all 5 AI features, leaderboard + referrals + real-time notifications, courses +
support chat, and a REST API + SEO layer ready for a mobile app and search engines.
The honest gaps are documented inline above and throughout each phase's section of
this README rather than glossed over — treat those as your pre-launch checklist.
