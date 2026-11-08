# Sikhun Project Requirement Audit Report

**অডিট ভিত্তি:** প্রদত্ত ১৪টি requirement audit result  
**ভাষা:** বাংলা  
**মূল্যায়ন নীতি:** code evidence, test result এবং production/deployment evidence আলাদা করে দেখা হয়েছে। যেসব জায়গায় implementation আছে কিন্তু test/credential/deployment প্রমাণ নেই, সেগুলোকে সম্পন্ন বলে overclaim করা হয়নি।

## ১. Executive summary

Sikhun-এ onboarding, free plan, AI exam/flashcard/essay generation, AI explanation ও book citation, Bengali localization, authentication, invoice, support/ticket, referral, blog taxonomy এবং sitemap—এসবের বেশিরভাগের জন্য migration/model, route, controller/service, Vue UI এবং feature-test স্তরে বাস্তব implementation পাওয়া গেছে। অর্থাৎ প্রকল্পটি কেবল পরিকল্পনা বা migration পর্যায়ে নেই।

তবে audit-এর প্রধান বাধা তিনটি: **(ক)** একাধিক feature test-এ HTTP 419 CSRF failure, **(খ)** AI, mail, Redis/queue, payment ও production deployment credential/runtime-এর অসম্পূর্ণ প্রমাণ, এবং **(গ)** কিছু feature-এর code আছে কিন্তু end-to-end বা production evidence নেই। বিশেষভাবে audience signal সংরক্ষিত হলেও personalization-এ ব্যবহারের প্রমাণ নেই; free-plan API purchase guard অসম্পূর্ণ; phone verification ও mobile password reset/email verification অনুপস্থিত; academic blog post seed data নেই; Google Search Console/VPS deployment সম্পন্ন হওয়ার প্রমাণ নেই।

**সামগ্রিক সিদ্ধান্ত:** codebase-এ উল্লেখযোগ্য অগ্রগতি আছে, কিন্তু production-ready/সম্পূর্ণ green release বলা যাবে না। P0 পর্যায়ে CSRF test harness, free-plan API guard, AI/provider/runtime এবং production operations ঠিক করা জরুরি।

## ২. Status overview — ১৪টি requirement

| # | Requirement | Status | সংক্ষিপ্ত সিদ্ধান্ত |
|---:|---|---|---|
| 1 | Target audience and positioning | আংশিক | onboarding ও positioning copy আছে; সংগৃহীত audience signal personalization/segmentation-এ ব্যবহারের প্রমাণ নেই। |
| 2 | AI question, flashcard, essay specificity ও full-book coverage | বাহ্যিক সেটআপ বাকি | retrieval-aware jobs/UI/tests আছে; live provider credential, strict schema validation ও worker readiness অপ্রমাণিত। |
| 3 | Font family ও Bengali numeric rendering | আংশিক | locale/helper/global font আছে; বহু raw Latin number, font alias mismatch ও dedicated test অনুপস্থিত। |
| 4 | AI explanation ও book-page references | সম্পন্ন (code scope) | access-aware citation, SSE, cache ও passing focused tests আছে; live provider/runtime আলাদা বাহ্যিক নির্ভরতা। |
| 5 | Mobile authentication, phone/email verification, password reset | আংশিক | mobile token auth আছে; phone verification এবং mobile reset/verification API নেই; POST tests-এ 419। |
| 6 | Free plan | আংশিক | web flow, quota, expiry ও UI আছে; API free purchase guard নেই এবং tests পুরো green নয়। |
| 7 | Student onboarding | প্রায় সম্পন্ন | ৪ ধাপ, persistence, skip, dashboard ও AI activation hook আছে; CSRF test failure এবং live AI smoke test বাকি। |
| 8 | Admin editing, recurring billing ও reminders | প্রায় সম্পন্ন | invoice/admin/recurring/reminder code আছে; admin write tests, SMTP/cron এবং auto-charge প্রমাণিত নয়। |
| 9 | Support bot, registration, invoices, purchase, guidance, KPI, tickets | আংশিক | বিস্তৃত implementation আছে; ১১টি focused test failure, AI/payment credential ও support KPI gap আছে। |
| 10 | Ticket, feedback ও report-answer options | প্রায় সম্পন্ন | web ticket/feedback/handoff/admin review আছে; tests 419-এ fail, API parity ও notifications নেই। |
| 11 | Referral management | সম্পন্ন (code scope) | tracking→registration→paid reward, fraud hold, admin review ও passing service tests আছে; HTTP/browser test বাকি। |
| 12 | Academic blog seed data ও tags | আংশিক | category/tag/source seed আছে; বাস্তব academic `BlogPost` ও pivot rows নেই। |
| 13 | Sitemap, Google Search Console ও VPS deployment | আংশিক | sitemap generator/robots/scheduler আছে; GSC/VPS/cron/worker/deploy evidence নেই। |
| 14 | Performance ও faster AI response | আংশিক | SSE, bounded retrieval, queue jobs ও latency log আছে; benchmark, Redis worker, index/production setup অপ্রমাণিত। |

## ৩. কাজ হয়েছে — তবে scope ও caveat-সহ

নিচের অংশগুলোতে কার্যকর code-level implementation এবং তুলনামূলক শক্ত evidence আছে:

- **AI explanation ও citation:** `app/Http/Controllers/Student/ExamController.php:279-397`, `app/Jobs/GenerateExamQuestions.php:33-101`, `database/migrations/2026_10_31_000003_create_ai_explanations_table.php`, `resources/js/Pages/Student/Exams/Result.vue:22-101`; `php artisan test tests/Feature/ExamExplanationTest.php`-এ ৪টি test pass। এটি access-aware implementation; live provider success আলাদাভাবে প্রমাণিত নয়।
- **Referral management:** `app/Services/ReferralService.php:16-231`, `app/Services/PurchaseService.php:208-210`, `routes/web.php:43-45,244-245`, `routes/api.php:46-80`; `php artisan test --filter=ReferralEnhancementsTest`-এ ৩টি test/২১ assertion pass।
- **Onboarding-এর মূল flow:** `app/Http/Controllers/Student/OnboardingController.php:31-83`, `resources/js/Pages/Student/Onboarding/Index.vue:8-126`, `app/Models/Student.php:143-162`, `routes/web.php:124-129`। তবে focused tests green নয়।
- **Support/ticket/feedback-এর code scope:** `app/Controllers/Student/TicketController.php`, `app/Http/Controllers/Public/SupportController.php`, `app/Http/Controllers/Admin/TicketController.php`, `app/Http/Controllers/Student/FeedbackController.php`, এবং সংশ্লিষ্ট Vue pages। Test failures থাকায় end-to-end pass দাবি করা হচ্ছে না।
- **Invoice domain:** `app/Services/InvoiceService.php:34-294`, `app/Console/Commands/GenerateRecurringInvoices.php`, `app/Console/Commands/MarkOverdueInvoices.php`, `routes/console.php:21-24`; ১২টির মধ্যে ৯টি invoice test pass। Admin mutation ও operational delivery বাকি।
- **Sitemap/robots code:** `app/Console/Commands/GenerateSitemap.php`, `routes/console.php`, `public/robots.txt`, `public/sitemap.xml`; live HTTP/GSC/deployment evidence নেই।

## ৪. আংশিক বা কাজ হয়নি/বাহ্যিকভাবে বাকি

### আংশিক implementation

1. **Audience:** `students.onboarding_progress`-এ goal/class/subject/book capture হচ্ছে; কিন্তু `app/Models/Student.php` ও সংশ্লিষ্ট dashboard/home flow-এ recommendation, segmentation analytics বা audience-specific UX-এর প্রমাণ নেই।
2. **Bengali numeric rendering:** `resources/js/utils/format.js:1-61` ও `resources/js/i18n.js:17-54` ভালো ভিত্তি দিলেও `resources/js/Pages/Student/Exams/Result.vue:161,168,171,180,184,188`, `resources/js/Pages/Admin/Books/Index.vue:164-166`, `resources/js/Pages/Admin/Leaderboard/Index.vue`-এর raw output mixed digits তৈরি করতে পারে। `tailwind.config.js:12-16`-এর `Hind Siliguri` alias এবং `resources/css/app.css:1`-এর imported `Anek Bangla` সামঞ্জস্যপূর্ণ নয়।
3. **Authentication:** `routes/api.php:34-37,46-48`-এ register/login/logout/me আছে, কিন্তু mobile email verification/reset নেই। Phone input (`resources/js/Pages/Auth/Register.vue:54-60`) থাকলেও OTP/phone verification flow নেই।
4. **Free plan:** `app/Http/Controllers/Student/SubscriptionController.php` web path-এ free purchase আটকে, কিন্তু `app/Services/PurchaseService.php` এবং `app/Http/Controllers/Api/SubscriptionController.php`-এ service-level guard নেই; API purchase path সুরক্ষিত নয়।
5. **Blog:** `database/seeders/BlogSeeder.php:15-111` category/tag/source তৈরি করে, কিন্তু `BlogPost` seed করে না। বর্তমান database state-এ `blog_posts=0`, `blog_post_tag=0`।
6. **Performance:** `AiChatController`-এ SSE আছে এবং `BookChunkRetrievalService` access-aware retrieval করে, কিন্তু benchmark, TTFT/p95/p99 threshold, MySQL FULLTEXT index/EXPLAIN এবং N+1 enforcement নেই।

### কাজ হয়নি বা বাহ্যিকভাবে বাকি

- **Production AI readiness:** `.env`-এ AI key/queue values empty বা অনিশ্চিত; `database/seeders/AiProviderSeeder.php` ও `app/Services/Ai/AiProviderFactory.php` mapping থাকলেও active valid credential/runtime প্রমাণিত নয়।
- **Production mail/payment:** `.env.example:39-45`-এ placeholder SMTP; `config/zinipay.php:1-9` ও `app/Services/Payment/ZinipayService.php:10-91`-এর live credential/callback evidence নেই।
- **GSC/VPS deployment:** repository-তে verification, Nginx/PHP-FPM, Supervisor/Horizon, cron, SSL/DNS বা deploy script নেই। `.github/workflows/ci.yml` production deploy করে না।
- **Recurring auto-payment:** recurring invoice generation আছে; invoice থেকে automatic charge/payment settlement প্রমাণিত নয়।
- **Mobile phone verification:** route/controller/service/job/provider/UI/test—কোনোটির evidence নেই।

## ৫. AI/RAG-specific findings

### ইতিবাচক দিক

- `app/Services/Ai/BookChunkRetrievalService.php:90-212` book/chapter access যাচাই করে, unauthorized selection reject করে এবং chapter/page/chunk provenance দেয়।
- `app/Jobs/GenerateExamQuestions.php:28-101` `exam_gen` provider-এ source context ও citation metadata পাঠায়। `app/Services/Ai/FlashcardSourceService.php:17-59` এবং `app/Jobs/GenerateFlashcards.php:32-94` flashcard source/provenance ধরে।
- Exam, flashcard ও essay generation queued job-এ আলাদা use-case mapping আছে: `database/seeders/AiProviderSeeder.php:16-35`, `app/Services/Ai/AiProviderFactory.php:41-123`।
- AI explanation-এ ২৪ ঘণ্টার cache ও source access gate আছে: `app/Http/Controllers/Student/ExamController.php:279-397`, `app/Models/AiExplanation.php`।
- SSE chat implementation: `app/Http/Controllers/Student/AiChatController.php:151-307`, `app/Http/Controllers/Api/AiChatController.php:57-119`, frontend stream parser `resources/js/Pages/Student/AiChat/Show.vue:121-211`।

### সীমাবদ্ধতা ও ঝুঁকি

- “Full-book coverage” সর্বোচ্চ default ৪৮টি evenly sampled chunk; এটি representative coverage, exhaustive every-page coverage নয়। `tests/Feature/FlashcardBookCoverageTest.php:65-149` ও `app/Services/Ai/BookChunkRetrievalService.php` এই সীমা নির্দেশ করে।
- Exam question count/type এবং essay result-এর strict schema validation নেই। `GradeEssay` array পেলেই completed হতে পারে; malformed structured response ঝুঁকি আছে।
- Actual provider response-সহ successful end-to-end generation test নেই। Retry suites-এ 419 failure আছে।
- `.env`-এ `QUEUE_CONNECTION=sync` ও `CACHE_STORE=file`; তাই production-grade worker/cache performance বলা যাবে না।
- `book_chunks` content-এর FULLTEXT index migration-এ দৃশ্যমান নয়; MySQL production-এ `EXPLAIN` যাচাই দরকার।
- AI logs-এ prompt/response payload retention, redaction ও size policy অপ্রমাণিত।

## ৬. Security ও credentials risk note

- 419 failure মূলত feature-test CSRF/session setup-এর সমস্যা বলে প্রতীয়মান; এটি production route failure প্রমাণ করে না, কিন্তু passing regression evidence অনুপস্থিত রাখে।
- AI, SMTP এবং ZiniPay secret source code-এ hard-code করার evidence নেই; `.env.example` placeholder-based। তবু production-এ valid secret, rotation, secret manager এবং access restriction যাচাই করা হয়নি।
- Free plan purchase-এর ক্ষেত্রে web controller guard যথেষ্ট নয়; `PurchaseService`-এ service-level rejection না থাকলে API path দিয়ে bypass সম্ভাবনা থাকে।
- Book/chapter/page access control RAG leakage রোধে ভালো evidence আছে: `tests/Feature/ChapterAccessTest.php:88-150,157-213,215-318` এবং `tests/Feature/ExamExplanationTest.php:107-204`।
- Support, ticket, invoice ও referral-এ ownership checks আছে; তবে POST/PATCH/DELETE tests 419-এ থেমে যাওয়ায় security regression evidence অসম্পূর্ণ।
- Production mail/queue/scheduler সক্রিয় না হলে verification, password reset, invoice reminder, AI jobs ও scheduled expiry বাস্তবে নাও চলতে পারে।

## ৭. Priority-based phase plan

### P0 — release blocker / অবিলম্বে

1. সব feature test-এ CSRF handling ঠিক করুন এবং green evidence নিন: `FreePlanAndOnboardingTest`, `InvoiceTest`, `PasswordResetTest`, `SupportTicketFeedbackTest`, `SupportWidgetAndNotificationsTest`, `AiGenerationRetryTest`, `AiChatAttachBookTest`, `BlogModuleTest`।
2. `app/Services/PurchaseService.php`-এ free plan service-level guard যোগ করুন; API test লিখুন যাতে 422 হয় এবং order/subscription তৈরি না হয়।
3. অন্তত একটি AI provider credential, active use-case mapping, Redis/cache, `ai` queue worker এবং process supervisor configure করে staging smoke test চালান।
4. SMTP, `APP_URL`, scheduler cron, queue worker এবং ZiniPay sandbox/live callback configuration যাচাই করুন।
5. Sitemap live generate করে HTTP 200/valid XML যাচাই করুন এবং Google Search Console property verify ও sitemap submit করুন।

### P1 — production hardening

1. `onboarding_progress`-কে versioned/queryable structured profile বা indexed fields-এ আনুন; goal অনুযায়ী অন্তত একটি recommendation/filter flow ও feature test করুন।
2. Exam/flashcard/essay provider response-এর JSON schema, exact count/type, score/breakdown validation যোগ করুন; fake provider integration tests লিখুন।
3. সব raw numeric interpolation shared `n`, `money`, `digits` helper-এ আনুন; `Anek Bangla`/`Hind Siliguri` single source-of-truth করুন।
4. Mobile email verification/reset এবং phone OTP requirement-এর product decision বাস্তবায়ন করুন বা scope থেকে স্পষ্টভাবে বাদ দিন।
5. Academic Bengali/English blog post seed করুন এবং tags pivot sync করুন; seeded public tag/category test যোগ করুন।
6. Recurring billing requirement auto-charge হলে payment intent/order/webhook settlement যোগ করুন; না হলে “recurring invoice generation” scope হিসেবে document করুন।
7. TTFT, p95/p99, throughput, provider failure rate, query count ও retrieval EXPLAIN benchmark চালু করুন।

### P2 — optimization ও governance

1. Audience funnel analytics, support KPI, bot containment, SLA/resolution metrics যোগ করুন।
2. Support FAQ/content source ও deterministic guidance tests যোগ করুন।
3. API ticket/feedback parity, browser smoke tests এবং route ownership regression tests যোগ করুন।
4. AI log retention/redaction, cache cleanup এবং reminder retry/backoff policy নির্ধারণ করুন।
5. Version-controlled Nginx/Supervisor/cron/deploy runbook এবং idempotent CI/CD deploy script যোগ করুন।

## ৮. Acceptance criteria

Release acceptance-এর আগে নিম্নলিখিত criteria পূরণ করতে হবে:

1. সংশ্লিষ্ট feature suites-এ কোনো CSRF 419 নেই; full suite green বা প্রত্যেক known failure-এর signed exception আছে।
2. Web ও API উভয় path-এ free plan purchase 422/বাতিল হয়; কোনো order বা subscription তৈরি হয় না।
3. Mock provider test-এ exam/flashcard/essay exact schema, count/type, failure status, retry এবং provenance persist যাচাই হয়।
4. Staging-এ valid AI provider, Redis queue worker এবং SSE smoke test সফল; first token/complete/error metrics সংরক্ষিত।
5. RAG access test-এ unauthorized book/chapter/page থেকে prompt বা citation leakage নেই; citation থাকলে valid reader URL, না থাকলে explicit fallback দেখা যায়।
6. Bengali locale-এ representative public/student/admin/invoice/exam/leaderboard সংখ্যাগুলো Bengali digit-এ; English locale-এ Latin digit; font network failure-এ fallback কাজ করে।
7. Registration, login, email verification, password reset, inactive account, API token revocation এবং—যদি scope-এ থাকে—phone OTP end-to-end test pass।
8. Onboarding completed/skip/first AI activation, out-of-order step এবং recommendation behavior feature test-এ প্রমাণিত।
9. Invoice admin create/edit, server-side totals, recurring generation, overdue/reminder retry এবং ownership/print access test pass।
10. Support ticket, feedback, answer report, handoff, admin review/status এবং ownership tests pass; প্রয়োজন হলে API parity ও notification test pass।
11. Referral HTTP flow, paid non-wallet reward idempotency, fraud hold এবং admin approval test pass।
12. অন্তত ৩টি academic bilingual blog post published status-এ, academic category/tag pivot-সহ public route-এ দৃশ্যমান।
13. Live `/sitemap.xml` ও `/robots.txt` HTTP 200; valid XML; GSC verified এবং sitemap submitted/read status evidence সংরক্ষিত।
14. Production runbook-এ Nginx/PHP-FPM, HTTPS, cron, queue worker, SMTP, backups, deploy/rollback ও health checks documented এবং staging/production smoke test সম্পন্ন।

## Final verdict

Sikhun-এর core product surface-এ বাস্তব code implementation উল্লেখযোগ্য এবং বেশ কিছু অংশ code scope-এ সম্পন্ন। কিন্তু test harness-এর 419 failure, external provider/runtime setup, API/security edge case, missing academic seed posts এবং GSC/VPS operational evidence-এর কারণে প্রকল্পটিকে এখনই সম্পূর্ণ বা production-ready ঘোষণা করা নির্ভুল হবে না। P0 blocker সমাধান এবং acceptance criteria-র evidence সংগ্রহের পর পুনরায় audit চালানো উচিত।

[পূর্ণ রিপোর্ট ফাইল ডাউনলোড করুন](sandbox:/home/ubuntu/sikhun-project-report-bn.md)
