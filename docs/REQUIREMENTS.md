# SIKHUN.COM — COMPLETE REQUIREMENTS SPECIFICATION

> This document defines every functional and non-functional requirement for the Sikhun.com platform.
> AI agents must satisfy ALL requirements before marking any phase complete.

---

## 1. FUNCTIONAL REQUIREMENTS

---

### 1.1 Authentication & User Management

#### Student Auth
- [ ] REQ-AUTH-01: Student can register with name, email, password, student type (HSC/SSC/University/Job)
- [ ] REQ-AUTH-02: Registration validates unique email, minimum 8-char password, required type selection
- [ ] REQ-AUTH-03: Auto-generate unique referral code on registration (format: `SIKHU-XXXXX`)
- [ ] REQ-AUTH-04: If `?ref=CODE` in URL at registration, store `referred_by_student_id`
- [ ] REQ-AUTH-05: Email verification required before accessing student dashboard
- [ ] REQ-AUTH-06: Student login via email + password (web guard)
- [ ] REQ-AUTH-07: Remember Me functionality (30-day cookie)
- [ ] REQ-AUTH-08: Forgot password → email reset link → reset form
- [ ] REQ-AUTH-09: Student logout clears session
- [ ] REQ-AUTH-10: Inactive students (`status = inactive`) are blocked from login with clear error message
- [ ] REQ-AUTH-11: API login returns Sanctum token for mobile apps
- [ ] REQ-AUTH-12: API token revoked on logout

#### Admin Auth
- [ ] REQ-AUTH-13: Admin login at `/admin/login` (completely separate from student login)
- [ ] REQ-AUTH-14: Admin uses `admin` guard — cannot access student routes and vice versa
- [ ] REQ-AUTH-15: Three admin roles: `super_admin`, `content_manager`, `support_agent`
- [ ] REQ-AUTH-16: Role-based access: content_manager cannot access payment/student management
- [ ] REQ-AUTH-17: Admin logout clears admin session only

---

### 1.2 Digital Library

- [ ] REQ-LIB-01: Student can browse all published books
- [ ] REQ-LIB-02: Filter books by: level (HSC/SSC/University/Job), subject, category, free/paid, search query
- [ ] REQ-LIB-03: Full-text search powered by Meilisearch (Laravel Scout)
- [ ] REQ-LIB-04: Book detail page shows: title, author, publication, description, cover, price, level, subject, total pages
- [ ] REQ-LIB-05: First 3 pages of every book viewable without purchase (free preview)
- [ ] REQ-LIB-06: Access control in this exact order:
  1. `is_free = true` → immediate full access
  2. Book in student's `book_shelves` → full access
  3. Active subscription includes book as gift → add to shelf + full access
  4. Wallet balance ≥ price → show purchase prompt
  5. Wallet insufficient → show recharge/direct payment options
- [ ] REQ-LIB-07: PDF NEVER served directly. Pages served as signed URL images ONLY
- [ ] REQ-LIB-08: Signed URLs expire after 15 minutes
- [ ] REQ-LIB-09: Each page image watermarked with student name + student ID + "sikhun.com"
- [ ] REQ-LIB-10: Rate limit: max 5 page requests per 10 seconds per student (Redis-based)
- [ ] REQ-LIB-11: All reading sessions logged: student_id, book_id, pages_read, duration, ip_address
- [ ] REQ-LIB-12: Turn.js FlipBook UI with lazy page loading
- [ ] REQ-LIB-13: Signed URL refreshed via AJAX 2 minutes before expiry (frontend handles this)
- [ ] REQ-LIB-14: Right-click disabled on reader page, text selection disabled on book images
- [ ] REQ-LIB-15: Bookshelf page shows all owned books (purchased, gifted, free)

---

### 1.3 AI Chat with Books

- [ ] REQ-CHAT-01: Requires active subscription OR unused trial minutes
- [ ] REQ-CHAT-02: Student selects source: full book / specific chapter / page range / own PDF upload / no book
- [ ] REQ-CHAT-03: Backend extracts text from selected scope using PDF text extraction
- [ ] REQ-CHAT-04: Text chunks stored as vector embeddings (pgvector or Qdrant)
- [ ] REQ-CHAT-05: On message send → vector similarity search → retrieve top-5 relevant chunks (RAG)
- [ ] REQ-CHAT-06: Response streamed via Server-Sent Events (SSE) — NOT polling
- [ ] REQ-CHAT-07: Token usage tracked per session and deducted from subscription minutes
- [ ] REQ-CHAT-08: Trial minute countdown shown in UI
- [ ] REQ-CHAT-09: When trial/subscription exhausted → prompt to subscribe with upgrade button
- [ ] REQ-CHAT-10: Chat history persisted per session in `ai_sessions` table (messages as JSON)
- [ ] REQ-CHAT-11: Student can start new session or continue existing session
- [ ] REQ-CHAT-12: Markdown rendering in AI responses (code blocks, bold, lists)
- [ ] REQ-CHAT-13: Typing indicator while AI is generating

---

### 1.4 AI Exam Engine

- [ ] REQ-EXAM-01: Student configures exam: source, question type, count, duration, mode
- [ ] REQ-EXAM-02: Sources: book → chapter → page, or topic text input, or pasted paragraph
- [ ] REQ-EXAM-03: Question types: MCQ, CQ (Creative), Short Answer, True/False, Fill-in-blank
- [ ] REQ-EXAM-04: Question count: 5/10/15/20/25/30/custom (max 50)
- [ ] REQ-EXAM-05: Duration: No limit / 10/15/20/30/45/60/90/120 minutes
- [ ] REQ-EXAM-06: Modes: Practice (answer-by-answer) or Exam (submit all at end)
- [ ] REQ-EXAM-07: Question generation runs as background Laravel Job (NOT synchronous)
- [ ] REQ-EXAM-08: AI returns ONLY valid JSON matching the defined schema
- [ ] REQ-EXAM-09: Practice mode: show correct answer + AI explanation after each question
- [ ] REQ-EXAM-10: Exam mode: countdown timer enforced → auto-submit when time expires
- [ ] REQ-EXAM-11: Timer shows visual urgency (color changes to red at 20% remaining)
- [ ] REQ-EXAM-12: After exam completion: show all questions, student answers, correct answers, score
- [ ] REQ-EXAM-13: Generate downloadable Answer Sheet PDF (student name, date, questions, answers, score)
- [ ] REQ-EXAM-14: Retake creates new session with same config (questions regenerated)
- [ ] REQ-EXAM-15: Completed exam (mode=exam, minimum 10 questions) → create leaderboard entry
- [ ] REQ-EXAM-16: Exam counts deducted from subscription quota

---

### 1.5 AI Flashcard Generator

- [ ] REQ-FLASH-01: Generate from: book chapter, page, pasted text, topic name
- [ ] REQ-FLASH-02: AI generates 10–30 flashcard pairs (question + answer)
- [ ] REQ-FLASH-03: Generation runs as background job
- [ ] REQ-FLASH-04: Flip-card review UI with 3D CSS animation
- [ ] REQ-FLASH-05: Mark each card as "Known" or "Review Again"
- [ ] REQ-FLASH-06: Spaced repetition scheduling (SM-2 simplified algorithm)
  - Known: next_review = now + (2^review_count) days
  - Review Again: next_review = now + 1 hour
- [ ] REQ-FLASH-07: Cards saved per student in `flashcard_sets` + `flashcards` tables
- [ ] REQ-FLASH-08: Export flashcard set as two-column PDF (question | answer)
- [ ] REQ-FLASH-09: Share flashcard set via link (public/private toggle)

---

### 1.6 AI Essay Grader

- [ ] REQ-ESSAY-01: Student selects grading type: HSC Bengali Essay, HSC English Essay, General Writing, Custom Rubric
- [ ] REQ-ESSAY-02: Student pastes or types essay (minimum 50 words)
- [ ] REQ-ESSAY-03: Grading runs as background job
- [ ] REQ-ESSAY-04: AI returns structured JSON: total score, breakdown by criteria, feedback, inline comments, improved version (optional)
- [ ] REQ-ESSAY-05: Result page shows score breakdown with visual progress bars
- [ ] REQ-ESSAY-06: Inline comments show which sentences are weak + why
- [ ] REQ-ESSAY-07: Student can toggle "Show Improved Version"
- [ ] REQ-ESSAY-08: Essay history: all past submissions with scores, sortable by date/score

---

### 1.7 Study Schedule Maker

- [ ] REQ-SCHED-01: Student inputs: exam date, subjects, hours/day, weak subjects, style, include weekends
- [ ] REQ-SCHED-02: Schedule generated as background job
- [ ] REQ-SCHED-03: Output: day-by-day plan from today until exam date
- [ ] REQ-SCHED-04: Weak subjects get proportionally more days
- [ ] REQ-SCHED-05: Calendar-style display (color-coded by subject)
- [ ] REQ-SCHED-06: Each day shows: subject, topic, duration hours, study tips
- [ ] REQ-SCHED-07: Student marks days as "Completed" → progress tracked
- [ ] REQ-SCHED-08: AI regeneration: if student falls behind, re-plan from today
- [ ] REQ-SCHED-09: Export schedule as PDF
- [ ] REQ-SCHED-10: Optional daily push reminder per schedule day

---

### 1.8 Course System (No AI)

- [ ] REQ-COURSE-01: Admin creates Mentor profiles
- [ ] REQ-COURSE-02: Admin creates Courses with: title, description, cover, price, level, category, mentor (optional)
- [ ] REQ-COURSE-03: Course structure: Course → Sections → Lessons
- [ ] REQ-COURSE-04: Lesson types: video (URL), text (rich text), PDF attachment
- [ ] REQ-COURSE-05: Mark first lesson as free preview (viewable without enrollment)
- [ ] REQ-COURSE-06: Student enrolls via wallet or direct payment
- [ ] REQ-COURSE-07: Progress tracking: lesson completion percentage per course
- [ ] REQ-COURSE-08: Certificate of Completion PDF (student name, course, date, mentor signature image)
- [ ] REQ-COURSE-09: Certificate auto-generated when all lessons completed
- [ ] REQ-COURSE-10: Certificate downloadable from student's profile

---

### 1.9 Wallet & Payment System

- [ ] REQ-PAY-01: Every student has a wallet balance (decimal, starts at 0.00)
- [ ] REQ-PAY-02: Student recharges wallet via: SSLCommerz, bKash, Nagad, or Manual/Bank Transfer
- [ ] REQ-PAY-03: All wallet transactions logged with before/after balance
- [ ] REQ-PAY-04: Transaction types: credit (recharge, referral_bonus, admin_credit) + debit (book_purchase, course_purchase, subscription_purchase)
- [ ] REQ-PAY-05: If wallet balance insufficient → two options: "Recharge Wallet" or "Pay Directly"
- [ ] REQ-PAY-06: "Pay Directly" goes to payment gateway for exact item amount
- [ ] REQ-PAY-07: Admin can manually credit/debit wallet with reason
- [ ] REQ-PAY-08: All orders stored in `orders` table with gateway transaction ID
- [ ] REQ-PAY-09: Order status: pending → completed → (optional: refunded)
- [ ] REQ-PAY-10: Refund: marks order as refunded, credits wallet
- [ ] REQ-PAY-11: Manual/Bank Transfer: admin approves in admin panel → credits wallet

---

### 1.10 Subscription System

- [ ] REQ-SUB-01: Admin creates/edits plans (name, price, ai_chat_minutes, ai_exam_count, gift_books)
- [ ] REQ-SUB-02: On subscription activation: gift books auto-added to student bookshelf
- [ ] REQ-SUB-03: Gift books remain readable after subscription expires
- [ ] REQ-SUB-04: AI features locked when subscription expires AND trial exhausted
- [ ] REQ-SUB-05: Trial minutes configurable per plan (admin sets)
- [ ] REQ-SUB-06: Student can see subscription status, expiry date, and remaining quotas on dashboard
- [ ] REQ-SUB-07: Subscription expiry warning notifications: 7 days, 3 days, 1 day before
- [ ] REQ-SUB-08: Admin can manually assign or extend a student's subscription

---

### 1.11 Leaderboard System

- [ ] REQ-LEAD-01: Three leaderboard types: Weekly (reset Monday), Monthly (reset 1st), All-Time
- [ ] REQ-LEAD-02: Filter by: student type (SSC/HSC/University/Job), subject, book, or platform-wide
- [ ] REQ-LEAD-03: Only Exam mode (not Practice) sessions count
- [ ] REQ-LEAD-04: Minimum 10 questions to qualify
- [ ] REQ-LEAD-05: Score = weighted percentage × question count
- [ ] REQ-LEAD-06: Top 100 displayed; student's own rank shown even if outside top 100
- [ ] REQ-LEAD-07: Badges: 🥇 #1, 🥈 #2, 🥉 #3, 🔥 Top 10, ⭐ Top 100
- [ ] REQ-LEAD-08: Leaderboard served from Redis cache (refreshed every 5 minutes)
- [ ] REQ-LEAD-09: Weekly/monthly reset via scheduled Laravel commands
- [ ] REQ-LEAD-10: Student can opt-out of public leaderboard from profile settings
- [ ] REQ-LEAD-11: Admin can remove individual entries from admin panel

---

### 1.12 Referral System

- [ ] REQ-REF-01: Every student has a unique referral code and shareable link
- [ ] REQ-REF-02: Referral link: `sikhun.com/register?ref={CODE}`
- [ ] REQ-REF-03: Friend registers using link → referral stored with status `pending`
- [ ] REQ-REF-04: On friend's FIRST paid purchase → both get wallet credit → referral status = `rewarded`
- [ ] REQ-REF-05: One-time reward only (second purchase does not re-trigger)
- [ ] REQ-REF-06: Self-referral blocked (same email)
- [ ] REQ-REF-07: Admin configures reward amounts (referrer reward + referee reward)
- [ ] REQ-REF-08: Admin configures max referral rewards per month
- [ ] REQ-REF-09: Student referral dashboard: total referred, total converted, total earned
- [ ] REQ-REF-10: Admin can view/manage all referral relationships

---

### 1.13 Push Notifications

- [ ] REQ-NOTIF-01: 10 notification types (see AGENT_PROMPT.md section 5.12)
- [ ] REQ-NOTIF-02: Student can enable/disable each notification type from preferences page
- [ ] REQ-NOTIF-03: Student sets preferred notification time
- [ ] REQ-NOTIF-04: Web push via Laravel Reverb + Vue Echo
- [ ] REQ-NOTIF-05: Mobile push via Firebase FCM
- [ ] REQ-NOTIF-06: Daily AI-generated educational notification (7 AM BDT via scheduled job)
- [ ] REQ-NOTIF-07: Max 3 notifications per day per student
- [ ] REQ-NOTIF-08: Notification bell in header shows unread count badge
- [ ] REQ-NOTIF-09: Marking a notification as read (individual + mark all read)
- [ ] REQ-NOTIF-10: Admin can broadcast to all students or by student type

---

### 1.14 Support Chat System

- [ ] REQ-SUPPORT-01: Floating chat button on ALL pages (bottom-right, z-index above everything)
- [ ] REQ-SUPPORT-02: AI-powered bot using `support_bot` AiProvider use case
- [ ] REQ-SUPPORT-03: System prompt configurable by admin from admin panel
- [ ] REQ-SUPPORT-04: Chat history stored per session token (not tied to account for guests)
- [ ] REQ-SUPPORT-05: If student is logged in, chat tied to student account
- [ ] REQ-SUPPORT-06: Admin views all conversations in admin panel
- [ ] REQ-SUPPORT-07: Admin can send manual reply (converts to human support)
- [ ] REQ-SUPPORT-08: Admin can close/resolve conversations
- [ ] REQ-SUPPORT-09: Admin can toggle bot on/off

---

### 1.15 Admin Panel

- [ ] REQ-ADMIN-01: Separate login at `/admin/login`
- [ ] REQ-ADMIN-02: Dashboard with: total students, revenue today/month/all-time, active subscriptions, AI usage stats, recent orders
- [ ] REQ-ADMIN-03: Books CRUD: create/edit/delete/publish, upload PDF to private storage, set cover image
- [ ] REQ-ADMIN-04: Authors CRUD, Publications CRUD, Categories CRUD
- [ ] REQ-ADMIN-05: Courses CRUD with nested Sections and Lessons
- [ ] REQ-ADMIN-06: Mentors CRUD
- [ ] REQ-ADMIN-07: AI Providers CRUD: full settings + "Test Connection" button per provider
- [ ] REQ-ADMIN-08: Students list with search/filter, view profile, activate/deactivate, wallet adjust, assign subscription
- [ ] REQ-ADMIN-09: Plans CRUD with gift book selection
- [ ] REQ-ADMIN-10: Orders list with status filter, approve manual payments, issue refunds
- [ ] REQ-ADMIN-11: Site Settings: name, logo, favicon, meta, contact, social links
- [ ] REQ-ADMIN-12: Custom Pages CMS with rich text editor
- [ ] REQ-ADMIN-13: Analytics: revenue chart (30 days), registrations chart, AI usage + cost estimate
- [ ] REQ-ADMIN-14: Referral settings + fraud flags management
- [ ] REQ-ADMIN-15: Notification management: create, schedule, broadcast

---

### 1.16 REST API

- [ ] REQ-API-01: All endpoints in `routes/api.php`, prefixed `/api/`
- [ ] REQ-API-02: Protected via `auth:sanctum` middleware (except auth endpoints)
- [ ] REQ-API-03: Consistent JSON response format: `{success, data, message, meta}`
- [ ] REQ-API-04: Consistent error format: `{success: false, message, errors}`
- [ ] REQ-API-05: Pagination meta included for list endpoints
- [ ] REQ-API-06: API mirrors all web functionality (library, reader, AI features, courses, wallet, etc.)
- [ ] REQ-API-07: API rate limiting: `throttle:60,1` general, `throttle:10,1` for AI endpoints
- [ ] REQ-API-08: API versioning ready (structure allows `/api/v2/` in future)
- [ ] REQ-API-09: API documentation generated via comments or Scribe package
- [ ] REQ-API-10: FCM token registration endpoint for mobile push notifications

---

### 1.17 Dark / Light Theme Mode

- [ ] REQ-THEME-01: Toggle available in student panel header (Sun/Moon/System icons)
- [ ] REQ-THEME-02: Three modes: Light, Dark, System (follows OS preference)
- [ ] REQ-THEME-03: Preference persisted in localStorage (`sikhun_theme` key)
- [ ] REQ-THEME-04: Preference also saved to `students.theme_mode` column via API call
- [ ] REQ-THEME-05: On login, student's saved theme loaded from profile and applied
- [ ] REQ-THEME-06: Tailwind `darkMode: 'class'` strategy — `.dark` class on `<html>`
- [ ] REQ-THEME-07: ALL student-facing UI components fully styled for both modes
- [ ] REQ-THEME-08: Theme toggle does NOT cause page reload (instant class swap)
- [ ] REQ-THEME-09: Admin panel uses fixed dark theme (no toggle needed)
- [ ] REQ-THEME-10: System mode listens to `prefers-color-scheme` media query changes

---

### 1.18 Advanced SEO

- [ ] REQ-SEO-01: Every page has unique `<title>` (format: `{Page Title} | Sikhun.com`)
- [ ] REQ-SEO-02: Every page has `<meta name="description">` (max 160 chars)
- [ ] REQ-SEO-03: Every page has `<link rel="canonical">`
- [ ] REQ-SEO-04: Open Graph tags on all public pages (`og:title`, `og:description`, `og:image`, `og:url`, `og:type`)
- [ ] REQ-SEO-05: Twitter Card tags on all public pages
- [ ] REQ-SEO-06: Structured data (JSON-LD) per page type:
  - Home: `EducationalOrganization`
  - Book page: `Book`
  - Course page: `Course`
  - FAQ page: `FAQPage`
  - All deep pages: `BreadcrumbList`
- [ ] REQ-SEO-07: Sitemap auto-generated daily at `https://sikhun.com/sitemap.xml`
- [ ] REQ-SEO-08: `robots.txt` blocks private routes (admin, dashboard, reader, AI features)
- [ ] REQ-SEO-09: Hreflang tags for Bengali (`bn`) and English (`en`)
- [ ] REQ-SEO-10: All images have `alt` attributes
- [ ] REQ-SEO-11: All images have explicit `width` and `height` (prevent CLS)
- [ ] REQ-SEO-12: `loading="lazy"` on all below-fold images
- [ ] REQ-SEO-13: Google Fonts loaded with `font-display: swap` and preconnect hints
- [ ] REQ-SEO-14: `SeoService` class provides title/description/keywords/og per content type
- [ ] REQ-SEO-15: Custom meta tags configurable from admin Site Settings
- [ ] REQ-SEO-16: Book and course slugs are URL-friendly (auto-generated from title)
- [ ] REQ-SEO-17: 404 page with proper HTTP 404 status and helpful navigation
- [ ] REQ-SEO-18: No broken internal links (Laravel route model binding handles 404 gracefully)

---

### 1.19 Demo Seeders

- [ ] REQ-SEED-01: `AdminSeeder` — 1 super admin (admin@sikhun.com / admin123)
- [ ] REQ-SEED-02: `AiProviderSeeder` — 7 OpenAI providers (one per use_case), all is_default=true
- [ ] REQ-SEED-03: `PlanSeeder` — 3 plans: স্টার্টার (৳99), প্রো (৳199), প্রিমিয়াম (৳399)
- [ ] REQ-SEED-04: `CategorySeeder` — Academic + Non-Academic with Bengali subcategories
- [ ] REQ-SEED-05: `AuthorSeeder` — 5 real NCTB book authors (Bengali names)
- [ ] REQ-SEED-06: `PublicationSeeder` — NCTB, Hasan Book House, Panjeree, etc.
- [ ] REQ-SEED-07: `BookSeeder` — 12+ books covering HSC, SSC, University, Job levels; 2 free, rest paid
- [ ] REQ-SEED-08: `MentorSeeder` — 3 mentors with bios and expertise
- [ ] REQ-SEED-09: `CourseSeeder` — 2 courses with 3 sections and 5 lessons each
- [ ] REQ-SEED-10: `StudentSeeder` — 20 students (Bengali names, mix of all types, student1-20@sikhun.com / student123)
- [ ] REQ-SEED-11: `SiteSettingSeeder` — all default site settings in Bengali
- [ ] REQ-SEED-12: `CustomPageSeeder` — placeholder content for About, FAQ, Terms, Privacy, Contact, How It Works
- [ ] REQ-SEED-13: `DemoDataSeeder` — wallet transactions, exam sessions, leaderboard entries, referral relationships, support conversations
- [ ] REQ-SEED-14: `php artisan db:seed` runs ALL seeders cleanly with no errors
- [ ] REQ-SEED-15: Seeders are idempotent where possible (`updateOrCreate` over `create`)

---

## 2. NON-FUNCTIONAL REQUIREMENTS

### 2.1 Performance
- [ ] REQ-PERF-01: All Eloquent queries use eager loading (`with()`) — zero N+1 queries
- [ ] REQ-PERF-02: Leaderboard top-100 served from Redis (not DB query per request)
- [ ] REQ-PERF-03: Site settings cached in Redis (not queried per request)
- [ ] REQ-PERF-04: Book catalog cached with 30-minute TTL
- [ ] REQ-PERF-05: Vite production build with asset fingerprinting
- [ ] REQ-PERF-06: Images served via Spatie Media Library with optimized sizes
- [ ] REQ-PERF-07: Database indexes on all foreign keys and frequently filtered columns
- [ ] REQ-PERF-08: Queue workers separated by priority: `ai`, `notifications`, `default`

### 2.2 Security
- [ ] REQ-SEC-01: All PDFs in `storage/app/private/` — never accessible via URL
- [ ] REQ-SEC-02: Page signed URLs expire in 15 minutes (tamper-evident)
- [ ] REQ-SEC-03: AI API keys encrypted in database (`encrypted:` Eloquent cast)
- [ ] REQ-SEC-04: All forms protected by CSRF token
- [ ] REQ-SEC-05: All user input validated via Form Request classes
- [ ] REQ-SEC-06: No raw SQL with user input — Eloquent only
- [ ] REQ-SEC-07: File uploads validated for MIME type + max size + UUID filename
- [ ] REQ-SEC-08: AI endpoints rate limited: `throttle:10,1`
- [ ] REQ-SEC-09: Reader page endpoint rate limited: 5 requests per 10 seconds per student
- [ ] REQ-SEC-10: XSS protection: all output escaped in Blade and Vue templates

### 2.3 Reliability
- [ ] REQ-REL-01: AI failures fall back to secondary provider if configured
- [ ] REQ-REL-02: Failed queue jobs logged and retryable (max 3 attempts)
- [ ] REQ-REL-03: Custom 404, 403, 500 error pages
- [ ] REQ-REL-04: Laravel Horizon monitoring queue health
- [ ] REQ-REL-05: PDF page generation errors return user-friendly message (not 500)

### 2.4 Accessibility & UX
- [ ] REQ-UX-01: Fully responsive from 375px (mobile) to 1440px+ (desktop)
- [ ] REQ-UX-02: Bengali text renders correctly (Hind Siliguri font)
- [ ] REQ-UX-03: All interactive elements have hover/focus states
- [ ] REQ-UX-04: Loading states shown for all async operations
- [ ] REQ-UX-05: Skeleton loaders for content that loads async (prevent CLS)
- [ ] REQ-UX-06: Toast notifications for all success/error actions
- [ ] REQ-UX-07: Confirmation modal before any destructive action (delete, unsubscribe)
- [ ] REQ-UX-08: Empty states with helpful messages (no books in library, no exams taken, etc.)
- [ ] REQ-UX-09: Dark mode and light mode BOTH look polished — not an afterthought

### 2.5 Code Quality
- [ ] REQ-CODE-01: PSR-12 coding standards for PHP
- [ ] REQ-CODE-02: Composition API (not Options API) for all Vue components
- [ ] REQ-CODE-03: Typed props in Vue components
- [ ] REQ-CODE-04: No business logic in controllers — delegate to Service classes
- [ ] REQ-CODE-05: All AI calls go through AiProviderFactory — no direct API calls in controllers
- [ ] REQ-CODE-06: Environment variables for all secrets — no hardcoded credentials
- [ ] REQ-CODE-07: Meaningful variable and method names (no abbreviations like `$s` or `$tmp`)
- [ ] REQ-CODE-08: Comments on complex logic (especially RAG, signed URLs, SSE)

---

## 3. DEFINITION OF DONE PER PHASE

### Phase 1 Done When:
- `php artisan migrate:fresh` runs all 34 migrations with zero errors
- `php artisan db:seed` runs all seeders with zero errors
- `npm run build` completes with zero errors
- Admin can log in at `/admin/login`
- Student can register and log in at `/login`
- Dark/light toggle works and persists in localStorage

### Phase 2 Done When:
- Admin can CRUD books, courses, mentors, AI providers, plans, students
- AI provider "Test Connection" button returns success for OpenAI
- Site settings can be saved and read

### Phase 3 Done When:
- Student can purchase a book via wallet (SSLCommerz sandbox)
- Student can read a purchased book via Turn.js with signed URL pages
- Pages are watermarked with student name
- Signed URL expires and AJAX refresh provides new URL

### Phase 4 Done When:
- AI chat streams responses via SSE (test with OpenAI key)
- AI exam generates 10 MCQ questions for a given topic
- Flashcard set generated with 10 cards
- Essay grading returns structured JSON result
- Study schedule generated for a given exam date

### Phase 5 Done When:
- Completed exam appears on leaderboard within 5 minutes (after Redis refresh)
- Referral reward credited when referred student makes first purchase
- Push notification received in browser via Reverb WebSocket

### Phase 6 Done When:
- Course enrollment and lesson completion tracked
- Certificate PDF downloaded after 100% completion
- Support chat widget responds via AI

### Phase 7 Done When:
- `GET /api/library` returns paginated book list with auth token
- All public pages have correct `<title>`, `<meta description>`, OG tags
- `GET /sitemap.xml` returns valid XML with all books and courses
- Dark mode applied on all student pages without any unstyled elements

---

## 4. PACKAGE LIST (composer.json require section)

```json
{
  "require": {
    "php": "^8.3",
    "laravel/framework": "^11.0",
    "laravel/sanctum": "^4.0",
    "laravel/reverb": "^1.0",
    "laravel/horizon": "^5.0",
    "laravel/scout": "^10.0",
    "spatie/laravel-permission": "^6.0",
    "spatie/laravel-medialibrary": "^11.0",
    "spatie/laravel-sitemap": "^7.0",
    "inertiajs/inertia-laravel": "^2.0",
    "tightenco/ziggy": "^2.0",
    "barryvdh/laravel-dompdf": "^3.0",
    "intervention/image-laravel": "^1.0",
    "meilisearch/meilisearch-php": "^1.0",
    "http-interop/http-factory-guzzle": "^1.0",
    "kreait/firebase-php": "^7.0",
    "openai-php/laravel": "^0.10",
    "guzzlehttp/guzzle": "^7.0"
  },
  "require-dev": {
    "laravel/pail": "^1.0",
    "laravel/pint": "^1.0",
    "fakerphp/faker": "^1.23"
  }
}
```

## 5. PACKAGE LIST (package.json dependencies)

```json
{
  "dependencies": {
    "vue": "^3.4",
    "@inertiajs/vue3": "^2.0",
    "pinia": "^2.1",
    "@vueuse/core": "^10.0",
    "tailwindcss": "^4.0",
    "@tailwindcss/vite": "^4.0",
    "apexcharts": "^3.45",
    "vue-apexcharts": "^1.6",
    "axios": "^1.6",
    "dayjs": "^1.11",
    "@heroicons/vue": "^2.1",
    "turnjs": "^4.1"
  },
  "devDependencies": {
    "vite": "^6.0",
    "@vitejs/plugin-vue": "^5.0",
    "laravel-vite-plugin": "^1.0",
    "autoprefixer": "^10.4",
    "postcss": "^8.4"
  }
}
```

---

*Last Updated: 2026 | Sikhun.com Requirements v2.0*
