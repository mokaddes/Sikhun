# SIKHUN.COM — PHASE TASK CHECKLIST

> Use this file to track progress. Check off each task as completed.
> Do not start Phase N+1 until ALL tasks in Phase N are checked.
> This is not a summary — it is the definitive task list.

---

## ✅ PHASE 1 — Foundation
*Goal: Zero-error scaffold. `migrate:fresh --seed` and `npm run build` both pass.*

### Project Setup
- [ ] `composer create-project laravel/laravel sikhun`
- [ ] All composer packages installed (see REQUIREMENTS.md §4)
- [ ] All npm packages installed (see REQUIREMENTS.md §5)
- [ ] `config/auth.php` — multi-guard (web/admin/api) configured
- [ ] `bootstrap/app.php` — `routes/admin.php` registered
- [ ] `config/queue.php` — three queue channels: `ai`, `notifications`, `default`
- [ ] `config/cache.php` — Redis as default cache
- [ ] `.env` — all variables from AGENT_PROMPT.md env section added (with placeholder values)
- [ ] `vite.config.js` — Vue + Inertia + Tailwind configured
- [ ] `tailwind.config.js` — `darkMode: 'class'` set, content paths correct
- [ ] `resources/css/app.css` — CSS variables for `:root` and `.dark` added
- [ ] `php artisan storage:link` — symlink created

### Migrations (run in exact order)
- [ ] 01 create_admins_table
- [ ] 02 create_students_table (includes `theme_mode` column)
- [ ] 03 create_categories_table
- [ ] 04 create_authors_table
- [ ] 05 create_publications_table
- [ ] 06 create_books_table
- [ ] 07 create_book_shelves_table
- [ ] 08 create_mentors_table
- [ ] 09 create_courses_table
- [ ] 10 create_course_sections_table
- [ ] 11 create_course_lessons_table
- [ ] 12 create_course_enrollments_table
- [ ] 13 create_lesson_progress_table
- [ ] 14 create_plans_table
- [ ] 15 create_student_subscriptions_table
- [ ] 16 create_wallet_transactions_table
- [ ] 17 create_orders_table
- [ ] 18 create_ai_providers_table (encrypted api_key)
- [ ] 19 create_ai_sessions_table
- [ ] 20 create_exam_sessions_table
- [ ] 21 create_flashcard_sets_table
- [ ] 22 create_flashcards_table
- [ ] 23 create_essay_submissions_table
- [ ] 24 create_study_schedules_table
- [ ] 25 create_leaderboard_entries_table
- [ ] 26 create_referrals_table
- [ ] 27 create_notifications_table
- [ ] 28 create_scheduled_notifications_table
- [ ] 29 create_push_subscriptions_table
- [ ] 30 create_student_notification_preferences_table
- [ ] 31 create_support_conversations_table
- [ ] 32 create_support_messages_table
- [ ] 33 create_site_settings_table
- [ ] 34 create_custom_pages_table
- [ ] `php artisan migrate:fresh` — ZERO errors

### Models (all with relationships + casts)
- [ ] Admin.php (guard: admin)
- [ ] Student.php (guard: web, HasApiTokens)
- [ ] Category.php (parent/children self-join)
- [ ] Author.php
- [ ] Publication.php
- [ ] Book.php (Scout searchable, published scope)
- [ ] BookShelf.php
- [ ] Mentor.php
- [ ] Course.php (Scout searchable)
- [ ] CourseSection.php
- [ ] CourseLesson.php
- [ ] CourseEnrollment.php
- [ ] LessonProgress.php
- [ ] Plan.php (JSON casts: gift_book_ids, features)
- [ ] StudentSubscription.php
- [ ] WalletTransaction.php
- [ ] Order.php
- [ ] AiProvider.php (encrypted cast on api_key)
- [ ] AiSession.php (JSON cast on messages)
- [ ] ExamSession.php (JSON casts: config, questions, answers)
- [ ] FlashcardSet.php
- [ ] Flashcard.php
- [ ] EssaySubmission.php (JSON cast on result)
- [ ] StudySchedule.php (JSON cast on schedule_data)
- [ ] LeaderboardEntry.php
- [ ] Referral.php
- [ ] Notification.php
- [ ] ScheduledNotification.php
- [ ] PushSubscription.php
- [ ] StudentNotificationPreference.php
- [ ] SupportConversation.php
- [ ] SupportMessage.php
- [ ] SiteSetting.php
- [ ] CustomPage.php

### Seeders
- [ ] AdminSeeder — admin@sikhun.com / admin123
- [ ] AiProviderSeeder — 7 OpenAI entries (one per use_case), all is_default=true
- [ ] PlanSeeder — স্টার্টার (৳99), প্রো (৳199), প্রিমিয়াম (৳399)
- [ ] CategorySeeder — Academic + Non-Academic with Bengali subcategories
- [ ] AuthorSeeder — 5 Bengali author names
- [ ] PublicationSeeder — NCTB, Hasan Book House, Panjeree, others
- [ ] BookSeeder — 12+ books across HSC/SSC/University/Job levels, 2 free
- [ ] MentorSeeder — 3 mentors with bios
- [ ] CourseSeeder — 2 courses, 3 sections each, 5 lessons each
- [ ] StudentSeeder — 20 students (student1–20@sikhun.com / student123), mixed types
- [ ] SiteSettingSeeder — Bengali defaults for all settings
- [ ] CustomPageSeeder — placeholder content for 6 pages
- [ ] DemoDataSeeder — wallets, exam sessions, leaderboard, referrals
- [ ] DatabaseSeeder.php — calls all seeders in correct order
- [ ] `php artisan db:seed` — ZERO errors

### Vue/Inertia Setup
- [ ] `resources/js/app.js` — Inertia + Vue + Pinia wired up
- [ ] `resources/js/Stores/theme.js` — Pinia store (init, setMode, applyTheme)
- [ ] `resources/js/Stores/auth.js` — Pinia store
- [ ] `resources/js/Components/Layout/StudentLayout.vue` — calls theme.init() on mount
- [ ] `resources/js/Components/Layout/AdminLayout.vue`
- [ ] `resources/js/Components/Layout/PublicLayout.vue`
- [ ] `resources/js/Components/UI/ThemeToggle.vue` — Sun/Moon/System cycle
- [ ] `npm run build` — ZERO errors

### Phase 1 Sign-off
- [ ] `php artisan migrate:fresh --seed` passes with zero errors
- [ ] `npm run build` passes with zero errors
- [ ] Student registers at `/register` ✓
- [ ] Student logs in at `/login` ✓
- [ ] Admin logs in at `/admin/login` ✓
- [ ] Theme toggle persists in localStorage ✓

---

## ✅ PHASE 2 — Auth + Admin Panel
*Goal: Admin can fully manage all content. Student auth complete.*

### Student Auth
- [ ] StudentAuthController — register, login, logout, verify-email
- [ ] `RegisterRequest` — validation with type enum
- [ ] `LoginRequest` — validation
- [ ] Referral code captured from `?ref=` query param on register
- [ ] Email verification email sent on register
- [ ] Inactive student blocked at login with message
- [ ] Forgot password flow (email link → reset form)
- [ ] Auth pages: Register.vue, Login.vue, VerifyEmail.vue, ForgotPassword.vue, ResetPassword.vue

### Admin Auth
- [ ] AdminAuthController — login, logout
- [ ] Admin dashboard page — stat cards (total students, revenue, active subs, AI usage)
- [ ] Admin middleware `EnsureAdminIsAuthenticated`

### Admin CRUD Pages
- [ ] Books: list, create, edit, delete, publish/unpublish toggle
- [ ] Book PDF upload → goes to `storage/app/private/books/` ONLY
- [ ] Book cover image upload → Spatie Media Library
- [ ] Authors: list, create, edit, delete
- [ ] Publications: list, create, edit, delete
- [ ] Categories: list, create, edit, delete (with parent category)
- [ ] AI Providers: list, create, edit, delete, "Test Connection" button
- [ ] Plans: list, create, edit, delete (gift book multi-select)
- [ ] Mentors: list, create, edit, delete
- [ ] Courses: list, create, edit, delete
- [ ] Course Sections + Lessons CRUD (nested under course)
- [ ] Students: list with search/filter, view profile, activate/deactivate
- [ ] Students: manual wallet credit/debit form
- [ ] Students: assign/extend subscription
- [ ] Orders: list with status filter, approve manual payments
- [ ] Site Settings: all fields editable (name, logo, meta, contact, social)
- [ ] Custom Pages CMS: rich text editor (Quill.js or TipTap)
- [ ] Admin Analytics: revenue chart (30 days), registrations chart
- [ ] Phase 2 Sign-off: Admin can CRUD all entities without errors

---

## ✅ PHASE 3 — Library + Reader + Wallet
*Goal: Student can buy and read a book.*

### Library
- [ ] LibraryController@index — paginated, all filters working
- [ ] LibraryController@show — book detail page
- [ ] `Book::published()` scope
- [ ] `Book::search()` via Laravel Scout + Meilisearch
- [ ] Library.vue — grid layout, filter sidebar/bar, search bar
- [ ] BookDetail.vue — cover, description, author, price, access button
- [ ] `SeoService::forBook()` used in book detail
- [ ] JSON-LD `Book` schema on book detail page
- [ ] BreadcrumbList JSON-LD on book detail page

### Book Access Logic
- [ ] `BookAccessService::studentHasAccess(student, book)` — checks in order: free, owned, subscription gift, wallet
- [ ] `BookAccessService::accessType()` — returns: free|owned|subscription_gift|purchasable|insufficient_funds
- [ ] Access type drives CTA button on book detail page

### Book Purchase
- [ ] `PurchaseBookService::purchaseWithWallet(student, book)` — atomic debit + order + bookshelf
- [ ] `PurchaseBookService::initiateGatewayPayment(student, book, gateway)` — SSLCommerz/bKash/Nagad
- [ ] SSLCommerz sandbox integration (test checkout)
- [ ] bKash integration
- [ ] Nagad integration
- [ ] Manual/Bank transfer request + admin approval flow
- [ ] Bookshelf.vue — list all owned books

### Wallet
- [ ] WalletController — index (balance + transactions), recharge
- [ ] `WalletService::credit()` and `WalletService::debit()` with atomic DB transaction
- [ ] Wallet.vue — balance display, transaction history, recharge button
- [ ] Payment gateway callback handlers (success, fail, cancel)

### Subscription Purchase
- [ ] `SubscriptionService::purchase(student, plan, gateway)` — creates subscription + gifts books
- [ ] Plans.vue — subscription plans comparison page
- [ ] Active subscription shown on student dashboard

### Book Reader
- [ ] `BookReaderService::getPageImageUrl()` — returns signed URL (15-min expiry)
- [ ] `BookReaderService::renderPage()` — PDF → Imagick → watermark → JPEG base64
- [ ] Reader route with signed URL middleware + rate limiter (5 req/10 sec)
- [ ] `ReaderController@servePage` — validates signature, checks access, renders page
- [ ] FlipReader.vue — Turn.js integration, lazy page loading
- [ ] Signed URL auto-refresh (AJAX 2 min before expiry)
- [ ] Right-click disabled, text selection disabled on reader page
- [ ] Reading session logged to DB

### Referral Trigger
- [ ] `ReferralService::reward()` called on first paid purchase
- [ ] Both referrer and referee credited
- [ ] Referral status updated to `rewarded`

### Phase 3 Sign-off
- [ ] Student can purchase a book via SSLCommerz sandbox ✓
- [ ] Student can read purchased book in Turn.js reader ✓
- [ ] Pages are watermarked ✓
- [ ] Signed URL expires and is refreshed ✓
- [ ] PDF URL is never exposed directly ✓

---

## ✅ PHASE 4 — AI Features
*Goal: All 5 AI features generate results via background jobs.*

### AI Provider System
- [ ] `AiProviderContract` interface — chat, stream, embed, isAvailable
- [ ] `AiProviderFactory::make()` and `::default(useCase)` methods
- [ ] OpenAiProvider — chat, stream, embed
- [ ] GeminiProvider — chat, stream, embed
- [ ] ClaudeProvider — chat, stream, embed
- [ ] GroqProvider — chat, stream, embed
- [ ] DeepSeekProvider — chat, stream (via OpenAI-compatible API)
- [ ] OllamaProvider — chat, stream, embed (local endpoint)
- [ ] VllmProvider — chat, stream (local endpoint)
- [ ] HuggingFaceProvider — chat, embed
- [ ] Admin "Test Connection" button calls `provider->isAvailable()`

### AI Middleware
- [ ] `EnsureHasAiAccess` — checks subscription OR trial minutes
- [ ] Trial minutes counter incremented after each AI use
- [ ] Quota exhaustion shows upgrade prompt

### Book Chunking
- [ ] `ProcessBookChunking` job — PDF → text chunks → vector embeddings
- [ ] Triggered when admin publishes a book
- [ ] Chunks stored in pgvector or Qdrant

### AI Chat (REQ-CHAT-*)
- [ ] `AiChatController@create` — create session
- [ ] `AiChatController@stream` — SSE streaming endpoint
- [ ] RAG: retrieve top-5 similar chunks, inject into system prompt
- [ ] Session messages persisted to `ai_sessions.messages` JSON
- [ ] Token usage tracked (approximate: char count / 4)
- [ ] ChatBox.vue — SSE client, typing indicator, markdown renderer
- [ ] Trial countdown shown in UI

### AI Exam Engine (REQ-EXAM-*)
- [ ] `CreateExamRequest` — validates all config fields
- [ ] `ExamController@create` — creates session (status: generating), dispatches `GenerateExamQuestions` job
- [ ] `GenerateExamQuestions` job — calls AI, parses JSON, updates session status
- [ ] Frontend polls for status change (every 3 seconds) until `in_progress`
- [ ] Practice mode: ExamPractice.vue — one question at a time, answer, reveal
- [ ] Exam mode: ExamTimer.vue — countdown, all questions, auto-submit
- [ ] ExamResult.vue — score, all Q&A, explanations
- [ ] Answer sheet PDF via DomPDF
- [ ] Retake creates new session with same config
- [ ] Leaderboard entry created on exam completion (mode=exam, total≥10)

### AI Flashcard Generator (REQ-FLASH-*)
- [ ] `FlashcardController@generate` — dispatches `GenerateFlashcards` job
- [ ] `GenerateFlashcards` job — calls AI, parses JSON, creates records
- [ ] FlashcardSet.vue — flip card 3D CSS animation
- [ ] Mark Known/Review Again
- [ ] Spaced repetition: next_review calculated and stored
- [ ] Export flashcard PDF (two-column: question | answer)

### AI Essay Grader (REQ-ESSAY-*)
- [ ] `EssayController@submit` — dispatches `GradeEssay` job
- [ ] `GradeEssay` job — calls AI, parses JSON result
- [ ] EssayResult.vue — score breakdown with progress bars, inline comments
- [ ] Essay history page

### Study Schedule Maker (REQ-SCHED-*)
- [ ] `ScheduleController@generate` — dispatches `GenerateStudySchedule` job
- [ ] `GenerateStudySchedule` job — calls AI, parses JSON
- [ ] ScheduleCalendar.vue — color-coded calendar view
- [ ] Mark day as Completed
- [ ] Regenerate from today
- [ ] Export PDF

### Phase 4 Sign-off
- [ ] AI chat streams via SSE with real OpenAI key ✓
- [ ] Exam generates 10 MCQ questions and displays them ✓
- [ ] Flashcard set generates and flip animation works ✓
- [ ] Essay graded with score breakdown ✓
- [ ] Study schedule generated for exam 30 days away ✓

---

## ✅ PHASE 5 — Leaderboard + Referrals + Notifications
*Goal: Leaderboard shows live data. Referral rewards work. Push notifications delivered.*

### Leaderboard (REQ-LEAD-*)
- [ ] `LeaderboardService::getTopStudents(type, period, filters)` — Redis cached
- [ ] `LeaderboardService::getStudentRank(student, type, period, filters)`
- [ ] `LeaderboardService::invalidate(type, period)` — clears cache
- [ ] Leaderboard entry auto-created on exam completion
- [ ] Leaderboard.vue — tabs: Weekly/Monthly/All-Time, filter by type/subject
- [ ] Rank badges: 🥇🥈🥉🔥⭐ displayed
- [ ] Student's own rank shown even if outside top 100
- [ ] `leaderboard:reset-weekly` command — clears weekly entries + cache
- [ ] `leaderboard:reset-monthly` command — clears monthly entries + cache
- [ ] Opt-out toggle in student profile settings
- [ ] Admin can delete individual leaderboard entries

### Referral System (REQ-REF-*)
- [ ] `ReferralService::reward(referral)` — credits both, blocks duplicates
- [ ] Referral.vue — dashboard (referred, converted, earned)
- [ ] Referral link generator with copyable button
- [ ] Admin referral settings (reward amounts, monthly limit)
- [ ] Admin referral list — all pairs with status

### Push Notifications (REQ-NOTIF-*)
- [ ] Laravel Reverb + Vue Echo wired for web push
- [ ] FCM configured for mobile push (future use)
- [ ] `NotificationService::send(student, type, title, body)`
- [ ] `SendPushNotification` job dispatches to Reverb + FCM
- [ ] `notifications:generate-ai` command — daily educational notification
- [ ] `notifications:send-scheduled` command — every 5 minutes
- [ ] Notification bell in StudentLayout — shows unread count badge
- [ ] Notifications.vue — list, mark read, mark all read
- [ ] NotificationPreferences.vue — toggle each type, set preferred time
- [ ] Max 3 notifications/day enforced
- [ ] Admin: notification broadcast form (all students or by type)

### Phase 5 Sign-off
- [ ] Complete exam → leaderboard entry appears within 5 min ✓
- [ ] Referral reward credited on friend's first purchase ✓
- [ ] Push notification received in browser ✓

---

## ✅ PHASE 6 — Courses + Support + Public Site
*Goal: Courses enrollable. Support chat live. Public site complete.*

### Course System (REQ-COURSE-*)
- [ ] CourseController — index, show (public), enroll, lesson player, complete
- [ ] Courses.vue — grid, filter by level/category
- [ ] CourseDetail.vue — sections, lessons list, enroll button, mentor bio
- [ ] LessonPlayer.vue — video/PDF/text lesson viewer
- [ ] Progress tracking — marks lesson complete, updates percentage
- [ ] Certificate generation — DomPDF, student name + course + date
- [ ] Certificate download from profile
- [ ] `SeoService::forCourse()` used in course detail
- [ ] JSON-LD `Course` schema on course page

### Support Chat (REQ-SUPPORT-*)
- [ ] SupportWidget.vue — floating button, chat window
- [ ] Guest and logged-in student sessions
- [ ] AI response via `support_bot` AiProvider
- [ ] `SupportController` — create conversation, send message
- [ ] Admin: support conversation list, manual reply, close/resolve
- [ ] Admin: bot on/off toggle and system prompt editor

### Public Site
- [ ] Home.vue — hero, features, plans, testimonials, CTA
- [ ] About.vue — loads from `custom_pages` where slug='about'
- [ ] HowItWorks.vue — loads from `custom_pages`
- [ ] Faq.vue — loads from `custom_pages`, JSON-LD FAQPage schema
- [ ] Contact.vue — form submission (email notification)
- [ ] PublicLayout — header, footer, nav (auth links if logged in)
- [ ] All public pages SEO-complete (title, description, OG, canonical)
- [ ] Home page JSON-LD `EducationalOrganization` schema

### Phase 6 Sign-off
- [ ] Student can enroll in course and complete lessons ✓
- [ ] Certificate PDF downloadable after 100% completion ✓
- [ ] Support chat widget responds with AI ✓
- [ ] All public pages render with correct meta tags ✓

---

## ✅ PHASE 7 — REST API + Theme + SEO + Polish
*Goal: Production-ready. All tests pass. API documented.*

### REST API (REQ-API-*)
- [ ] All endpoints in `routes/api.php` with `auth:sanctum`
- [ ] `Api\AuthController` — register, login, logout, me
- [ ] `Api\LibraryController` — index, show, purchase
- [ ] `Api\ReaderController` — page (signed URL)
- [ ] `Api\AiChatController` — sessions CRUD + stream (SSE)
- [ ] `Api\ExamController` — create, show, answer, complete, result
- [ ] `Api\FlashcardController` — generate, index, show, review
- [ ] `Api\EssayController` — submit, index, show
- [ ] `Api\ScheduleController` — generate, index, show, progress
- [ ] `Api\CourseController` — index, show, enroll, lesson, complete
- [ ] `Api\WalletController` — balance, transactions, recharge
- [ ] `Api\SubscriptionController` — plans, purchase, active
- [ ] `Api\LeaderboardController` — index, my-rank
- [ ] `Api\ReferralController` — index, stats
- [ ] `Api\ProfileController` — show, update, password, theme, avatar, fcm-token
- [ ] `Api\NotificationController` — index, read, read-all
- [ ] `Api\SupportController` — create conversation, messages
- [ ] `Api\PageController` — show custom page by slug
- [ ] Consistent `{success, data, message, meta}` JSON format on ALL responses
- [ ] `throttle:60,1` on general API endpoints
- [ ] `throttle:10,1` on AI API endpoints

### Dark / Light Mode (REQ-THEME-*)
- [ ] All student pages tested in both modes — no white/black unstyled elements
- [ ] Theme stored in localStorage AND DB (on login, DB value applied)
- [ ] No page reload on theme toggle
- [ ] System mode reacts to OS preference change
- [ ] Inline `<head>` script prevents flash of wrong theme on page load

### Advanced SEO (REQ-SEO-*)
- [ ] `SeoService` used in every controller that renders a page
- [ ] All 6 JSON-LD schemas implemented: Org, Book, Course, FAQ, Breadcrumb, Product
- [ ] `spatie/laravel-sitemap` configured and generates `sitemap.xml`
- [ ] `sitemap:generate` command scheduled daily
- [ ] `public/robots.txt` — blocks admin, dashboard, reader, api
- [ ] Hreflang tags on all public pages
- [ ] All images have alt, width, height attributes
- [ ] `loading="lazy"` on all below-fold images
- [ ] Google Fonts with `font-display: swap` and preconnect
- [ ] `404.vue` page with 404 HTTP status and helpful nav links
- [ ] No broken internal routes (route model binding handles 404)

### Security Audit
- [ ] No PDF accessible via `/storage/` URL
- [ ] Signed URL verification tested (tampered URL → 403)
- [ ] All form inputs pass through Form Request classes
- [ ] AI API keys encrypted in DB (test: DB value is ciphertext)
- [ ] Reader rate limit tested: 6th request in 10 sec → 429

### Performance
- [ ] Zero N+1 queries (run `debugbar` or check query log)
- [ ] Leaderboard served from Redis (check cache hit)
- [ ] Site settings served from Redis cache
- [ ] `php artisan optimize` and `php artisan config:cache` work
- [ ] `npm run build` — production assets fingerprinted

### Email Notifications
- [ ] Welcome email on registration
- [ ] Book purchase confirmation email
- [ ] Subscription expiry warning emails (7 days, 3 days, 1 day)
- [ ] Password reset email

### Custom Error Pages
- [ ] `resources/js/Pages/Error/404.vue`
- [ ] `resources/js/Pages/Error/403.vue`
- [ ] `resources/js/Pages/Error/500.vue`
- [ ] Registered in Laravel exception handler

### Phase 7 Sign-off
- [ ] `GET /api/library` returns paginated books with valid auth token ✓
- [ ] All public pages pass Google's Rich Results Test ✓
- [ ] `GET /sitemap.xml` returns valid XML with all books + courses ✓
- [ ] Dark mode applied on ALL student pages with no unstyled elements ✓
- [ ] Student can complete full flow: register → buy book → read → take exam → see leaderboard ✓
- [ ] Admin can complete full flow: add book → add AI provider → manage student ✓

---

## 🏁 FINAL DEPLOYMENT CHECKLIST

- [ ] `APP_ENV=production` in `.env`
- [ ] `APP_DEBUG=false`
- [ ] Real API keys set (not placeholders)
- [ ] SSLCommerz `IS_SANDBOX=false`
- [ ] `php artisan key:generate`
- [ ] `php artisan config:cache`
- [ ] `php artisan route:cache`
- [ ] `php artisan view:cache`
- [ ] `php artisan event:cache`
- [ ] `npm run build` (production)
- [ ] Queue workers running via Supervisor
- [ ] Laravel Horizon running via Supervisor
- [ ] Laravel Reverb running via Supervisor (or Soketi)
- [ ] Redis persistent storage enabled
- [ ] Cron job running `php artisan schedule:run`
- [ ] Storage symlink created on server
- [ ] `storage/app/private/books/` not web-accessible (verified with Nginx config)
- [ ] SSL certificate installed
- [ ] `APP_URL` set to `https://sikhun.com`
- [ ] `SITEMAP_URL` set to `https://sikhun.com/sitemap.xml`
- [ ] Sitemap submitted to Google Search Console
- [ ] `robots.txt` uploaded to `public/`
- [ ] All DNS records pointing correctly

---

*Sikhun.com Phase Checklist v2.0 | Check every box before moving to next phase*
