# SIKHUN.COM — AI AGENT MASTER PROMPT

> **Copy this entire prompt into Codex, Claude Code, Windsurf, or any AI coding agent before starting work.**

---

## WHO YOU ARE & YOUR TASK

You are an expert full-stack Laravel developer. Your task is to build **Sikhun.com** — a Bangladeshi AI-powered education platform — from scratch, following this document with strict precision. Do not improvise architecture. Do not skip steps. Do not combine phases.

**Read every section of this prompt before writing a single line of code.**

---

## PROJECT SUMMARY

Sikhun.com is a premium educational SaaS platform for Bangladeshi students (HSC, SSC, University, Job Preparation). Features include:

- **Digital Library** — Secure FlipBook reader (no PDF download ever)
- **AI Chat with Books** — RAG-powered chat (like NotebookLM)
- **AI Exam Engine** — AI-generated MCQ, CQ, short answer exams
- **AI Flashcard Generator** — Spaced repetition flashcards
- **AI Essay Grader** — Bengali and English essay grading
- **Study Schedule Maker** — AI-generated personalized schedules
- **Course System** — Mentor-led LMS with video/PDF lessons
- **Wallet & Payments** — bKash, Nagad, SSLCommerz, manual transfer
- **Subscription System** — Plans with AI quotas and gift books
- **Leaderboard** — Weekly/monthly/all-time exam rankings
- **Referral System** — Wallet rewards for referrals
- **Push Notifications** — AI-generated educational notifications
- **Support Chat** — AI-powered floating support widget
- **REST API** — Full mobile-ready API for future apps
- **Dark/Light Mode** — In student panel with localStorage persistence
- **Advanced SEO** — Structured data, sitemap, meta, canonical
- **Rich Demo Seeders** — Realistic sample data for development

---

## TECH STACK (FIXED — DO NOT CHANGE)

### Backend
- PHP 8.3+, Laravel 11.x, MySQL 8.0+, Redis 7.x
- Laravel Horizon (queue monitoring)
- Laravel Sanctum (API token auth)
- Laravel Reverb (WebSockets)
- Spatie Laravel Permission (roles/guards)
- Spatie Laravel Media Library (file/media)
- Laravel Scout + Meilisearch (full-text search)
- barryvdh/laravel-dompdf (PDF certificates/answer sheets)
- intervention/image (PDF watermarking)
- league/csv or maatwebsite/excel (exports)
- Firebase PHP Admin SDK (FCM push)
- pgvector (vector embeddings for RAG) OR Qdrant

### Frontend
- Vue.js 3.x + Inertia.js 2.x + Vite 6.x
- Tailwind CSS 4.x (class-based dark mode strategy)
- Pinia (state management)
- Turn.js 4.x (FlipBook reader)
- Chart.js / ApexCharts (analytics)
- Fonts: Syne (headings), DM Sans (body), Hind Siliguri (Bengali), JetBrains Mono (code)

### Payments
- SSLCommerz, bKash, Nagad, Manual/Bank transfer

---

## CRITICAL ARCHITECTURE RULES

1. **Multi-guard auth ONLY** — student guard (`web`) and admin guard (`admin`) are completely separate. Never mix them.
2. **PDFs NEVER public** — All books stored in `storage/app/private/books/`. Never in `public/`. Never expose PDF paths. Serve as watermarked signed-URL images via Turn.js.
3. **AI Provider Factory pattern** — All AI calls go through `AiProviderFactory::make()`. Never hardcode a provider anywhere.
4. **All heavy AI operations = background jobs** — Exam generation, flashcard generation, essay grading, schedule generation, notification generation ALL run via Laravel Queue jobs. Never block HTTP requests.
5. **AI Chat = Server-Sent Events (SSE)** — Not polling, not websockets for chat streaming.
6. **Leaderboard = Redis cache** — Never query full leaderboard table per page load.
7. **No N+1 queries** — Eager load all relationships.

---

## GUARDS & AUTH CONFIG

```php
// config/auth.php
'guards' => [
    'web' => ['driver' => 'session', 'provider' => 'students'],
    'admin' => ['driver' => 'session', 'provider' => 'admins'],
    'api' => ['driver' => 'sanctum', 'provider' => 'students'],
],
'providers' => [
    'students' => ['driver' => 'eloquent', 'model' => App\Models\Student::class],
    'admins'   => ['driver' => 'eloquent', 'model' => App\Models\Admin::class],
],
```

Student routes: `routes/web.php` → middleware `auth:web`
Admin routes: `routes/admin.php` → middleware `auth:admin`
API routes: `routes/api.php` → middleware `auth:sanctum`

---

## STUDENT TYPES

Every student selects type at registration. Stored as enum in DB.

| Type | DB Value | Primary Content |
|------|----------|-----------------|
| HSC | `hsc` | NCTB HSC books, HSC exam prompts |
| SSC | `ssc` | NCTB SSC books |
| University | `university` | University textbooks |
| Job Preparation | `job` | BCS, bank job, govt prep books |

This value affects: AI prompt context, book recommendations, leaderboard filters, notification targeting.

---

## IMPLEMENTATION ORDER (PHASES)

**CRITICAL: Complete each phase fully before starting the next. Do not skip ahead.**

### PHASE 1 — Foundation
- Laravel 11 new project scaffold
- Install all composer + npm packages listed in tech stack
- Configure multi-guard auth in `config/auth.php`
- Create and register `routes/admin.php` in `bootstrap/app.php`
- Create all 34 database migrations (see schema section) and run them
- Create all Model files with relationships and casts
- Create base seeders: AdminSeeder, PlanSeeder, AiProviderSeeder, DemoDataSeeder
- Configure Tailwind 4 with CSS variable color system (light + dark)
- Configure Redis for cache, queues, sessions
- Configure Laravel Horizon
- Storage symlink + confirm private book storage path

### PHASE 2 — Auth + Admin Panel
- Student registration (type selection, email verification, referral code tracking)
- Student login/logout (web guard)
- Admin login/logout (admin guard, `/admin/login`)
- Admin dashboard with stat cards
- Admin CRUD: Categories, Authors, Publications, Books (PDF upload to private)
- Admin CRUD: Mentors, Courses, Sections, Lessons
- Admin CRUD: AI Providers with test button
- Admin CRUD: Plans
- Admin: Student management (list, profile, activate/deactivate, wallet adjust)
- Admin: Site Settings (JSON in site_settings table)
- Admin: Custom Pages CMS
- Admin: AI usage logs view

### PHASE 3 — Library + Reader + Wallet
- Student library page (filter by level, subject, category, price)
- Book detail page with cover, description, preview pages
- Wallet page: balance display, recharge, transaction history
- Payment gateway integration: SSLCommerz → bKash → Nagad → Manual
- Book purchase flow (wallet deduction + order + bookshelf)
- Direct payment flow (bypass wallet)
- Subscription purchase + gift book auto-assignment
- Bookshelf page
- Book reader (Turn.js + signed URL page images + watermarking + rate limiting)
- Reading session logging
- Referral tracking on registration + reward on first purchase

### PHASE 4 — AI Features
- AiProviderFactory + all 8 provider driver classes
- Book text chunking (PDF → chunks → vector store) as background job
- AI Chat: session, RAG retrieval, SSE streaming, token tracking, trial minutes
- AI Exam Engine: config form, job-based generation, timer, practice/exam modes, answer sheet PDF, leaderboard entry
- AI Flashcard Generator: job-based generation, flip card UI, spaced repetition, PDF export
- AI Essay Grader: submission form, job-based grading, result breakdown
- Study Schedule Maker: config form, job-based generation, calendar display, progress marking, PDF export
- All AI features gate-checked: subscription + trial minutes middleware

### PHASE 5 — Leaderboard + Referrals + Notifications
- Leaderboard: entries on exam complete, Redis cache, filters, weekly/monthly reset via scheduled commands
- Referral: dashboard, tracking, reward trigger, admin management
- Push Notifications: Reverb WebSocket, FCM, bell UI, preferences page, AI-generated daily notifications, admin broadcast

### PHASE 6 — Courses + Support + Public Site
- Course system: enrollment, lesson player (video/PDF/text), progress tracking
- Certificate of Completion PDF
- Support chat widget (floating, AI-powered, admin can take over)
- Public pages: Home, About, How It Works, FAQ, Contact
- Student analytics dashboard
- Admin analytics dashboard with charts

### PHASE 7 — REST API + Theme Mode + SEO + Polish
- Full REST API (see API section below)
- Dark/Light mode toggle with localStorage persistence
- Advanced SEO implementation (see SEO section below)
- Security audit (signed URLs, rate limits, CSRF, XSS, SQL injection review)
- Performance (eager loading, Redis caching, Vite build optimization)
- Error handling, custom error pages
- Email notifications: welcome, purchase confirmation, subscription expiry
- Beta test and deployment prep

---

## DATABASE SCHEMA

### Migration Run Order (MUST follow this sequence)

```
1.  admins
2.  students
3.  categories
4.  authors
5.  publications
6.  books
7.  book_shelves
8.  mentors
9.  courses
10. course_sections
11. course_lessons
12. course_enrollments
13. lesson_progress
14. plans
15. student_subscriptions
16. wallet_transactions
17. orders
18. ai_providers
19. ai_sessions
20. exam_sessions
21. flashcard_sets
22. flashcards
23. essay_submissions
24. study_schedules
25. leaderboard_entries
26. referrals
27. notifications
28. scheduled_notifications
29. push_subscriptions
30. student_notification_preferences
31. support_conversations
32. support_messages
33. site_settings
34. custom_pages
```

### Key Table Schemas

```sql
-- students
id, name, email, password, type ENUM('ssc','hsc','university','job'),
status ENUM('active','inactive') DEFAULT 'active',
wallet_balance DECIMAL(10,2) DEFAULT 0.00,
ai_trial_minutes_used INT DEFAULT 0,
referral_code VARCHAR(20) UNIQUE,
referred_by_student_id BIGINT NULL,
email_verified_at TIMESTAMP NULL,
avatar VARCHAR(255) NULL,
theme_mode ENUM('light','dark','system') DEFAULT 'system',
remember_token, timestamps

-- admins
id, name, email, password, role ENUM('super_admin','content_manager','support_agent'),
is_active BOOLEAN DEFAULT TRUE, remember_token, timestamps

-- books
id, title, slug UNIQUE, description TEXT, cover_image,
author_id, publication_id, category_id,
subject VARCHAR(100) NULL, level ENUM('ssc','hsc','university','job') NULL,
price DECIMAL(8,2) DEFAULT 0.00, is_free BOOLEAN DEFAULT FALSE,
pdf_path VARCHAR(500), total_pages INT DEFAULT 0,
is_published BOOLEAN DEFAULT FALSE, is_premium_gift BOOLEAN DEFAULT FALSE,
timestamps

-- ai_providers
id, name, type ENUM('openai','gemini','claude','groq','deepseek','ollama','vllm','huggingface'),
api_key TEXT NULL (encrypted cast),
model_name VARCHAR(200), api_endpoint VARCHAR(500) NULL,
use_case VARCHAR(50) [book_chat|exam_gen|flashcard_gen|essay_grade|schedule_gen|notification_gen|support_bot],
is_default BOOLEAN DEFAULT FALSE, is_active BOOLEAN DEFAULT TRUE,
max_tokens INT DEFAULT 2000, temperature DECIMAL(3,2) DEFAULT 0.70, timestamps

-- exam_sessions
id, student_id, source_type ENUM('book','chapter','page','topic','paragraph'),
source_book_id NULL, source_chapter NULL, source_page NULL, source_text TEXT NULL,
config JSON [type, count, duration, mode], questions JSON, answers JSON NULL,
score INT, total INT, percentage DECIMAL(5,2),
mode ENUM('practice','exam'), status ENUM('in_progress','completed','abandoned'),
started_at, completed_at, time_taken_seconds, timestamps

-- plans
id, name, slug, description, price_monthly DECIMAL(8,2),
ai_chat_minutes INT, ai_exam_count INT,
gift_book_ids JSON, trial_ai_minutes INT,
features JSON, is_active BOOLEAN, timestamps

-- leaderboard_entries
id, student_id, exam_session_id,
subject VARCHAR(100) NULL, book_id NULL, student_type,
score, total, percentage DECIMAL(5,2), questions_count,
week_number, month_number, year, timestamps

-- site_settings
id, key VARCHAR(100) UNIQUE, value JSON, timestamps

-- custom_pages
id, slug UNIQUE, title, content LONGTEXT,
meta_title, meta_description, meta_keywords,
og_title, og_description, og_image,
is_published BOOLEAN DEFAULT TRUE, timestamps
```

---

## AI PROVIDER SYSTEM

### Contract (implement ALL methods)
```php
// app/Contracts/AiProviderContract.php
interface AiProviderContract {
    public function chat(array $messages, array $options = []): string;
    public function stream(array $messages, array $options = []): Generator;
    public function embed(string $text): array; // returns float[]
    public function isAvailable(): bool;
}
```

### Factory
```php
// app/Services/Ai/AiProviderFactory.php
class AiProviderFactory {
    public static function make(int $providerId): AiProviderContract
    public static function default(string $useCase = 'chat'): AiProviderContract
}
```

### Use Cases (admin configures per use case)
- `book_chat`, `exam_gen`, `flashcard_gen`, `essay_grade`, `schedule_gen`, `notification_gen`, `support_bot`

### Providers to implement
OpenAI, Gemini, Claude (Anthropic), Groq, DeepSeek, Ollama (local), vLLM (local), HuggingFace

---

## REST API ROUTES (routes/api.php)

All API routes use `auth:sanctum` middleware except auth routes.

```
POST   /api/auth/register
POST   /api/auth/login
POST   /api/auth/logout
GET    /api/auth/me
POST   /api/auth/forgot-password
POST   /api/auth/reset-password

GET    /api/library                    (filter: level, subject, category, search, free)
GET    /api/library/{book:slug}
POST   /api/library/{book}/purchase
GET    /api/bookshelf

GET    /api/reader/{book}/page/{n}     (signed URL image, rate limited)

POST   /api/ai/chat/sessions
GET    /api/ai/chat/sessions
GET    /api/ai/chat/sessions/{id}
POST   /api/ai/chat/sessions/{id}/message
GET    /api/ai/chat/sessions/{id}/stream   (SSE)
DELETE /api/ai/chat/sessions/{id}

POST   /api/exams
GET    /api/exams
GET    /api/exams/{id}
POST   /api/exams/{id}/answer
POST   /api/exams/{id}/complete
GET    /api/exams/{id}/result

POST   /api/flashcards
GET    /api/flashcards
GET    /api/flashcards/{set}
POST   /api/flashcards/{set}/review

POST   /api/essays
GET    /api/essays
GET    /api/essays/{id}

POST   /api/schedules
GET    /api/schedules
GET    /api/schedules/{id}
POST   /api/schedules/{id}/progress

GET    /api/courses
GET    /api/courses/{course:slug}
POST   /api/courses/{course}/enroll
GET    /api/courses/{course}/lessons/{lesson}
POST   /api/courses/{course}/lessons/{lesson}/complete

GET    /api/wallet
GET    /api/wallet/transactions
POST   /api/wallet/recharge

GET    /api/subscriptions/plans
POST   /api/subscriptions/purchase
GET    /api/subscriptions/active

GET    /api/leaderboard                (filter: type, subject, book, period)
GET    /api/leaderboard/my-rank

GET    /api/referrals
GET    /api/referrals/stats

GET    /api/profile
PUT    /api/profile
PUT    /api/profile/password
PUT    /api/profile/theme
PUT    /api/profile/notifications
POST   /api/profile/avatar
DELETE /api/profile/avatar
POST   /api/profile/fcm-token

GET    /api/notifications
PUT    /api/notifications/{id}/read
PUT    /api/notifications/read-all

POST   /api/support/conversations
GET    /api/support/conversations/{id}
POST   /api/support/conversations/{id}/messages

GET    /api/pages/{slug}
```

### API Response Format
Always return consistent JSON:
```json
{
  "success": true,
  "data": { ... },
  "message": "...",
  "meta": { "pagination": { ... } }
}
```

Error format:
```json
{
  "success": false,
  "message": "...",
  "errors": { "field": ["..."] }
}
```

### API Auth (Laravel Sanctum)
- Mobile apps send `Authorization: Bearer {token}` header
- Tokens issued on login, revoked on logout
- Token name: `sikhun_mobile_app`
- Add `api` guard config as shown in Guards section above

---

## DARK / LIGHT THEME MODE

### Implementation (Tailwind class strategy)

```js
// resources/js/Stores/theme.js (Pinia store)
import { defineStore } from 'pinia'

export const useThemeStore = defineStore('theme', {
  state: () => ({
    mode: localStorage.getItem('sikhun_theme') || 'system'
  }),
  actions: {
    setMode(mode) {
      this.mode = mode
      localStorage.setItem('sikhun_theme', mode)
      this.applyTheme()
    },
    applyTheme() {
      const root = document.documentElement
      if (this.mode === 'dark') {
        root.classList.add('dark')
      } else if (this.mode === 'light') {
        root.classList.remove('dark')
      } else {
        // system
        const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches
        prefersDark ? root.classList.add('dark') : root.classList.remove('dark')
      }
    }
  }
})
```

### StudentLayout.vue — Theme Toggle Component
```vue
<!-- ThemeToggle.vue -->
<template>
  <button @click="cycleTheme" class="theme-toggle-btn">
    <SunIcon v-if="theme.mode === 'light'" />
    <MoonIcon v-else-if="theme.mode === 'dark'" />
    <ComputerIcon v-else />
  </button>
</template>
```

### Tailwind Config
```js
// tailwind.config.js
module.exports = {
  darkMode: 'class',  // MUST be 'class' strategy
  // ...
}
```

### CSS Variables (both modes)
```css
/* resources/css/app.css */
:root {
  --primary: #6c63ff;
  --primary-hover: #5b53ee;
  --secondary: #00d4aa;
  --accent: #ff6b6b;
  --bg: #ffffff;
  --surface: #f8f8ff;
  --surface2: #f0f0fa;
  --border: #e2e2ee;
  --text: #1a1a2e;
  --text-muted: #6b6b8a;
}
.dark {
  --bg: #09090f;
  --surface: #111118;
  --surface2: #18181f;
  --border: #2a2a38;
  --text: #e8e8f0;
  --text-muted: #7a7a9a;
}
```

### Persist theme preference to backend
- On theme change, call `PUT /api/profile/theme` with `{ theme_mode: 'dark' }`
- On login, load student's `theme_mode` from profile and apply it
- The `students` table has a `theme_mode` column: `ENUM('light','dark','system') DEFAULT 'system'`

---

## ADVANCED SEO IMPLEMENTATION

### Meta Tags via Inertia Head

Every page must set these via `<Head>` component:
```vue
<Head>
  <title>{{ pageTitle }} | Sikhun.com</title>
  <meta name="description" :content="pageDescription" />
  <meta name="keywords" :content="pageKeywords" />
  <link rel="canonical" :href="canonicalUrl" />

  <!-- Open Graph -->
  <meta property="og:title" :content="pageTitle" />
  <meta property="og:description" :content="pageDescription" />
  <meta property="og:image" :content="ogImage" />
  <meta property="og:url" :content="canonicalUrl" />
  <meta property="og:type" content="website" />
  <meta property="og:site_name" content="Sikhun.com" />
  <meta property="og:locale" content="bn_BD" />

  <!-- Twitter Card -->
  <meta name="twitter:card" content="summary_large_image" />
  <meta name="twitter:title" :content="pageTitle" />
  <meta name="twitter:description" :content="pageDescription" />
  <meta name="twitter:image" :content="ogImage" />

  <!-- Article-specific (for book/course pages) -->
  <meta property="article:published_time" :content="publishedAt" />
  <meta property="article:modified_time" :content="updatedAt" />
</Head>
```

### Structured Data (JSON-LD)
Add to appropriate pages as `<script type="application/ld+json">`:

**Home page:**
```json
{
  "@context": "https://schema.org",
  "@type": "EducationalOrganization",
  "name": "Sikhun.com",
  "description": "AI-powered education platform for Bangladeshi students",
  "url": "https://sikhun.com",
  "sameAs": ["https://facebook.com/sikhun", "https://twitter.com/sikhun"],
  "areaServed": "BD",
  "inLanguage": ["bn", "en"]
}
```

**Book detail page:**
```json
{
  "@context": "https://schema.org",
  "@type": "Book",
  "name": "{book.title}",
  "author": { "@type": "Person", "name": "{author.name}" },
  "publisher": { "@type": "Organization", "name": "{publication.name}" },
  "inLanguage": "bn",
  "educationalLevel": "{book.level}",
  "url": "https://sikhun.com/library/{book.slug}"
}
```

**Course page:**
```json
{
  "@context": "https://schema.org",
  "@type": "Course",
  "name": "{course.title}",
  "description": "{course.description}",
  "provider": { "@type": "Organization", "name": "Sikhun.com" },
  "instructor": { "@type": "Person", "name": "{mentor.name}" },
  "offers": {
    "@type": "Offer",
    "price": "{course.price}",
    "priceCurrency": "BDT"
  }
}
```

**FAQ page:**
```json
{
  "@context": "https://schema.org",
  "@type": "FAQPage",
  "mainEntity": [
    {
      "@type": "Question",
      "name": "{question}",
      "acceptedAnswer": { "@type": "Answer", "text": "{answer}" }
    }
  ]
}
```

**Breadcrumb (on all deep pages):**
```json
{
  "@context": "https://schema.org",
  "@type": "BreadcrumbList",
  "itemListElement": [
    { "@type": "ListItem", "position": 1, "name": "Home", "item": "https://sikhun.com" },
    { "@type": "ListItem", "position": 2, "name": "Library", "item": "https://sikhun.com/library" },
    { "@type": "ListItem", "position": 3, "name": "{book.title}", "item": "https://sikhun.com/library/{book.slug}" }
  ]
}
```

### Sitemap (Auto-generated)
Install `spatie/laravel-sitemap` and generate:
```php
// routes/console.php or SitemapController
use Spatie\Sitemap\Sitemap;
use Spatie\Sitemap\Tags\Url;

Sitemap::create()
    ->add(Url::create('/'))
    ->add(Url::create('/library'))
    ->add(Url::create('/courses'))
    ->add(Url::create('/about'))
    ->add(Url::create('/how-it-works'))
    ->add(Url::create('/faq'))
    ->add(Url::create('/contact'))
    // Add all published books
    ->add(Book::published()->get()->map(fn($b) =>
        Url::create("/library/{$b->slug}")->setLastModificationDate($b->updated_at)
    ))
    // Add all active courses
    ->add(Course::active()->get()->map(fn($c) =>
        Url::create("/courses/{$c->slug}")->setLastModificationDate($c->updated_at)
    ))
    ->writeToFile(public_path('sitemap.xml'));
```

Schedule: `$schedule->command('sitemap:generate')->daily();`

### robots.txt (public/robots.txt)
```
User-agent: *
Allow: /
Disallow: /admin/
Disallow: /dashboard/
Disallow: /reader/
Disallow: /ai/
Disallow: /exams/
Disallow: /api/

Sitemap: https://sikhun.com/sitemap.xml
```

### Hreflang (Bengali + English)
```html
<link rel="alternate" hreflang="bn" href="https://sikhun.com{path}" />
<link rel="alternate" hreflang="en" href="https://sikhun.com{path}" />
<link rel="alternate" hreflang="x-default" href="https://sikhun.com{path}" />
```

### SEO Service Class
```php
// app/Services/SeoService.php
class SeoService {
    public function forBook(Book $book): array {
        return [
            'title' => "{$book->title} - {$book->level_label} | Sikhun.com",
            'description' => Str::limit(strip_tags($book->description), 160),
            'og_image' => $book->cover_image_url,
            'canonical' => route('library.show', $book->slug),
            'keywords' => implode(', ', [$book->title, $book->subject, $book->level, 'বই', 'Sikhun']),
        ];
    }
    public function forCourse(Course $course): array { ... }
    public function forHome(): array { ... }
}
```

### Performance SEO
- All public images: `loading="lazy"` + explicit `width`/`height`
- Critical CSS inlined in `<head>`
- Google Fonts via `font-display: swap`
- Preconnect hints: `<link rel="preconnect" href="https://fonts.googleapis.com">`
- Core Web Vitals: minimize CLS with skeleton loaders on all async content

---

## DEMO DATA SEEDERS

Create realistic demo data in `database/seeders/DemoDataSeeder.php`.

Run order in `DatabaseSeeder.php`:
```php
$this->call([
    AdminSeeder::class,
    AiProviderSeeder::class,
    PlanSeeder::class,
    CategorySeeder::class,
    AuthorSeeder::class,
    PublicationSeeder::class,
    BookSeeder::class,
    MentorSeeder::class,
    CourseSeeder::class,
    StudentSeeder::class,
    SiteSettingSeeder::class,
    CustomPageSeeder::class,
    DemoDataSeeder::class,  // orders, exams, leaderboard etc.
]);
```

### AdminSeeder
```php
Admin::create([
    'name' => 'Super Admin',
    'email' => 'admin@sikhun.com',
    'password' => bcrypt('admin123'),
    'role' => 'super_admin',
]);
```

### AiProviderSeeder
```php
AiProvider::insert([
    ['name' => 'OpenAI GPT-4o', 'type' => 'openai', 'model_name' => 'gpt-4o', 'use_case' => 'book_chat', 'is_default' => true, 'is_active' => true, 'max_tokens' => 2000, 'temperature' => 0.70],
    ['name' => 'OpenAI GPT-4o', 'type' => 'openai', 'model_name' => 'gpt-4o', 'use_case' => 'exam_gen', 'is_default' => true, 'is_active' => true, 'max_tokens' => 4000, 'temperature' => 0.30],
    ['name' => 'OpenAI GPT-4o', 'type' => 'openai', 'model_name' => 'gpt-4o', 'use_case' => 'flashcard_gen', 'is_default' => true, 'is_active' => true, 'max_tokens' => 2000, 'temperature' => 0.50],
    ['name' => 'OpenAI GPT-4o', 'type' => 'openai', 'model_name' => 'gpt-4o', 'use_case' => 'essay_grade', 'is_default' => true, 'is_active' => true, 'max_tokens' => 3000, 'temperature' => 0.20],
    ['name' => 'OpenAI GPT-4o', 'type' => 'openai', 'model_name' => 'gpt-4o', 'use_case' => 'schedule_gen', 'is_default' => true, 'is_active' => true, 'max_tokens' => 3000, 'temperature' => 0.40],
    ['name' => 'OpenAI GPT-4o', 'type' => 'openai', 'model_name' => 'gpt-4o', 'use_case' => 'notification_gen', 'is_default' => true, 'is_active' => true, 'max_tokens' => 300, 'temperature' => 0.80],
    ['name' => 'OpenAI GPT-4o', 'type' => 'openai', 'model_name' => 'gpt-4o', 'use_case' => 'support_bot', 'is_default' => true, 'is_active' => true, 'max_tokens' => 1000, 'temperature' => 0.60],
]);
```

### PlanSeeder
```php
Plan::insert([
    [
        'name' => 'স্টার্টার',
        'slug' => 'starter',
        'description' => 'নতুন শিক্ষার্থীদের জন্য আদর্শ পরিকল্পনা',
        'price_monthly' => 99.00,
        'ai_chat_minutes' => 60,
        'ai_exam_count' => 20,
        'gift_book_ids' => json_encode([]),
        'trial_ai_minutes' => 10,
        'features' => json_encode(['AI Chat with Books (60 min/month)', '20 AI Exams/month', 'Flashcard Generator', 'Basic Support']),
        'is_active' => true,
    ],
    [
        'name' => 'প্রো',
        'slug' => 'pro',
        'description' => 'সিরিয়াস শিক্ষার্থীদের জন্য সেরা পছন্দ',
        'price_monthly' => 199.00,
        'ai_chat_minutes' => 300,
        'ai_exam_count' => 100,
        'gift_book_ids' => json_encode([1, 2, 3]),
        'trial_ai_minutes' => 10,
        'features' => json_encode(['AI Chat (300 min/month)', '100 AI Exams/month', 'Essay Grader', 'Study Schedule', '3 Free Books', 'Priority Support']),
        'is_active' => true,
    ],
    [
        'name' => 'প্রিমিয়াম',
        'slug' => 'premium',
        'description' => 'সীমাহীন অ্যাক্সেস — HSC টপারদের জন্য',
        'price_monthly' => 399.00,
        'ai_chat_minutes' => 999,
        'ai_exam_count' => 999,
        'gift_book_ids' => json_encode([1, 2, 3, 4, 5]),
        'trial_ai_minutes' => 15,
        'features' => json_encode(['Unlimited AI Chat', 'Unlimited AI Exams', 'All AI Features', '5 Free Books', 'Leaderboard Badge', 'WhatsApp Support']),
        'is_active' => true,
    ],
]);
```

### CategorySeeder
```php
Category::insert([
    ['name' => 'একাডেমিক', 'slug' => 'academic', 'type' => 'academic'],
    ['name' => 'নন-একাডেমিক', 'slug' => 'non-academic', 'type' => 'non_academic'],
    ['name' => 'বিজ্ঞান', 'slug' => 'science', 'type' => 'academic', 'parent_id' => 1],
    ['name' => 'মানবিক', 'slug' => 'humanities', 'type' => 'academic', 'parent_id' => 1],
    ['name' => 'ব্যবসায় শিক্ষা', 'slug' => 'business', 'type' => 'academic', 'parent_id' => 1],
    ['name' => 'উপন্যাস', 'slug' => 'novel', 'type' => 'non_academic', 'parent_id' => 2],
    ['name' => 'প্রবন্ধ', 'slug' => 'essay', 'type' => 'non_academic', 'parent_id' => 2],
]);
```

### StudentSeeder (20 demo students)
```php
$types = ['hsc', 'ssc', 'university', 'job'];
$names = ['রাহেলা বেগম', 'করিম হোসেন', 'সুমাইয়া আক্তার', 'তানভীর আহমেদ', ...]; // 20 Bengali names

for ($i = 1; $i <= 20; $i++) {
    Student::create([
        'name' => $names[$i-1],
        'email' => "student{$i}@sikhun.com",
        'password' => bcrypt('student123'),
        'type' => $types[array_rand($types)],
        'status' => 'active',
        'wallet_balance' => rand(0, 500) + 0.00,
        'referral_code' => 'SIKHU-' . strtoupper(Str::random(5)),
        'email_verified_at' => now(),
    ]);
}
```

### BookSeeder (sample books with real NCTB subjects)
```php
// Create authors and publications first, then:
$books = [
    ['title' => 'পদার্থবিজ্ঞান প্রথম পত্র', 'subject' => 'physics', 'level' => 'hsc', 'price' => 49.00, 'is_free' => false],
    ['title' => 'পদার্থবিজ্ঞান দ্বিতীয় পত্র', 'subject' => 'physics', 'level' => 'hsc', 'price' => 49.00, 'is_free' => false],
    ['title' => 'রসায়ন প্রথম পত্র', 'subject' => 'chemistry', 'level' => 'hsc', 'price' => 49.00, 'is_free' => false],
    ['title' => 'রসায়ন দ্বিতীয় পত্র', 'subject' => 'chemistry', 'level' => 'hsc', 'price' => 49.00, 'is_free' => false],
    ['title' => 'জীববিজ্ঞান প্রথম পত্র', 'subject' => 'biology', 'level' => 'hsc', 'price' => 49.00, 'is_free' => false],
    ['title' => 'উচ্চতর গণিত প্রথম পত্র', 'subject' => 'math', 'level' => 'hsc', 'price' => 49.00, 'is_free' => false],
    ['title' => 'বাংলা প্রথম পত্র (HSC)', 'subject' => 'bangla', 'level' => 'hsc', 'price' => 0.00, 'is_free' => true],
    ['title' => 'ইংরেজি প্রথম পত্র (HSC)', 'subject' => 'english', 'level' => 'hsc', 'price' => 0.00, 'is_free' => true],
    ['title' => 'সাধারণ বিজ্ঞান (SSC)', 'subject' => 'science', 'level' => 'ssc', 'price' => 39.00, 'is_free' => false],
    ['title' => 'গণিত (SSC)', 'subject' => 'math', 'level' => 'ssc', 'price' => 39.00, 'is_free' => false],
    ['title' => 'BCS প্রস্তুতি গাইড', 'subject' => null, 'level' => 'job', 'price' => 99.00, 'is_free' => false],
    ['title' => 'ব্যাংক জব প্রস্তুতি', 'subject' => null, 'level' => 'job', 'price' => 79.00, 'is_free' => false],
];
```

### DemoDataSeeder (wallet transactions, exam sessions, leaderboard entries)
```php
// Create sample wallet transactions for each student
// Create 3-5 completed exam sessions per student
// Create leaderboard entries from exam sessions
// Create some support conversations
// Create sample referral relationships
```

### SiteSettingSeeder
```php
$settings = [
    'site_name' => 'Sikhun.com',
    'site_tagline' => 'বাংলাদেশের প্রথম AI-চালিত শিক্ষা প্ল্যাটফর্ম',
    'site_email' => 'support@sikhun.com',
    'site_phone' => '+880 1XXXXXXXXX',
    'meta_title' => 'Sikhun.com — AI-Powered Learning for Bangladeshi Students',
    'meta_description' => 'Read books digitally, chat with AI, take AI exams, and build study schedules. Built for HSC, SSC, University and Job Preparation students.',
    'meta_keywords' => 'sikhun, শিখুন, HSC, SSC, Bangladesh education, AI learning, digital books',
    'referrer_reward_amount' => 20,
    'referee_reward_amount' => 20,
    'max_referral_per_month' => 10,
    'ai_trial_minutes_default' => 10,
    'registration_open' => true,
    'maintenance_mode' => false,
];

foreach ($settings as $key => $value) {
    SiteSetting::updateOrCreate(['key' => $key], ['value' => json_encode($value)]);
}
```

### CustomPageSeeder
```php
$pages = [
    ['slug' => 'about', 'title' => 'আমাদের সম্পর্কে', 'meta_description' => 'Sikhun.com সম্পর্কে জানুন'],
    ['slug' => 'how-it-works', 'title' => 'কিভাবে কাজ করে', 'meta_description' => 'Sikhun.com কিভাবে ব্যবহার করবেন'],
    ['slug' => 'faq', 'title' => 'সাধারণ প্রশ্নোত্তর', 'meta_description' => 'Sikhun.com সম্পর্কিত প্রশ্ন ও উত্তর'],
    ['slug' => 'terms', 'title' => 'ব্যবহারের শর্তাবলী', 'meta_description' => 'Sikhun.com এর ব্যবহারের শর্তাবলী'],
    ['slug' => 'privacy', 'title' => 'গোপনীয়তা নীতি', 'meta_description' => 'Sikhun.com এর গোপনীয়তা নীতি'],
    ['slug' => 'contact', 'title' => 'যোগাযোগ', 'meta_description' => 'Sikhun.com এর সাথে যোগাযোগ করুন'],
];
```

---

## PDF SECURITY (NON-NEGOTIABLE)

```php
// app/Services/BookReaderService.php

public function getPageImageUrl(Book $book, int $page, Student $student): string
{
    // 1. Check student has access to book
    // 2. Convert PDF page to image via Imagick/Ghostscript
    // 3. Watermark with student name + ID
    // 4. Cache watermarked image temporarily
    // 5. Return signed URL (15-min expiry)
    
    return URL::temporarySignedRoute(
        'reader.page',
        now()->addMinutes(15),
        ['book' => $book->id, 'page' => $page, 'student' => $student->id]
    );
}
```

Rate limit on page endpoint: `throttle:5,0.167` (5 per 10 seconds)

---

## BOOK READER (Turn.js)

```vue
<!-- resources/js/Components/BookReader/FlipReader.vue -->
<!-- Turn.js receives page IMAGE URLs, never PDF URLs -->
<!-- Pages fetched lazily as student flips -->
<!-- Signed URL refreshed via AJAX 2 minutes before expiry -->
<!-- Prevent right-click, text selection, drag on images -->
```

---

## SSE STREAMING (AI Chat)

```php
// AiChatController@stream
return response()->stream(function() {
    $stream = AiProviderFactory::default('book_chat')->stream($messages);
    foreach ($stream as $chunk) {
        echo "data: " . json_encode(['content' => $chunk]) . "\n\n";
        ob_flush(); flush();
    }
    echo "data: [DONE]\n\n";
    ob_flush(); flush();
}, 200, [
    'Content-Type' => 'text/event-stream',
    'X-Accel-Buffering' => 'no',
    'Cache-Control' => 'no-cache',
    'Connection' => 'keep-alive',
]);
```

---

## ENVIRONMENT VARIABLES

```env
APP_NAME="Sikhun.com"
APP_ENV=local
APP_KEY=base64:...
APP_DEBUG=true
APP_URL=http://localhost
APP_TIMEZONE=Asia/Dhaka
APP_LOCALE=en

DB_CONNECTION=mysql
DB_DATABASE=sikhun
DB_USERNAME=root
DB_PASSWORD=

CACHE_DRIVER=redis
QUEUE_CONNECTION=redis
SESSION_DRIVER=redis

REDIS_HOST=127.0.0.1
REDIS_PORT=6379

# AI Providers
OPENAI_API_KEY=
GEMINI_API_KEY=
ANTHROPIC_API_KEY=
GROQ_API_KEY=
DEEPSEEK_API_KEY=
HUGGINGFACE_API_KEY=
OLLAMA_ENDPOINT=http://localhost:11434
VLLM_ENDPOINT=http://localhost:8000

# Payments
SSLCOMMERZ_STORE_ID=
SSLCOMMERZ_STORE_PASSWORD=
SSLCOMMERZ_IS_SANDBOX=true
BKASH_APP_KEY=
BKASH_APP_SECRET=
BKASH_USERNAME=
BKASH_PASSWORD=
NAGAD_MERCHANT_ID=
NAGAD_MERCHANT_KEY=

# Firebase
FIREBASE_PROJECT_ID=
FIREBASE_CREDENTIALS_FILE=storage/app/firebase-credentials.json

# Reverb (WebSocket)
REVERB_APP_ID=sikhun-local
REVERB_APP_KEY=sikhun-key
REVERB_APP_SECRET=sikhun-secret
REVERB_HOST=localhost
REVERB_PORT=8080

# SEO
APP_CANONICAL_URL=https://sikhun.com
SITEMAP_URL=https://sikhun.com/sitemap.xml

# Referral rewards
REFERRAL_REWARD_REFERRER=20
REFERRAL_REWARD_REFEREE=20
```

---

## DIRECTORY STRUCTURE

```
sikhun/
├── app/
│   ├── Console/Commands/
│   │   ├── GenerateAiNotifications.php
│   │   ├── GenerateSitemap.php
│   │   ├── ResetWeeklyLeaderboard.php
│   │   ├── ResetMonthlyLeaderboard.php
│   │   └── ExpireSubscriptions.php
│   ├── Contracts/AiProviderContract.php
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Api/          ← all REST API controllers
│   │   │   ├── Student/      ← web student controllers
│   │   │   ├── Admin/        ← admin controllers
│   │   │   └── Auth/         ← StudentAuthController, AdminAuthController
│   │   ├── Middleware/
│   │   │   ├── EnsureStudentIsActive.php
│   │   │   ├── EnsureHasSubscription.php
│   │   │   └── EnsureHasAiAccess.php
│   │   └── Requests/         ← Form Request classes for all POST/PUT
│   ├── Jobs/
│   │   ├── GenerateExamQuestions.php
│   │   ├── GenerateFlashcards.php
│   │   ├── GradeEssay.php
│   │   ├── GenerateStudySchedule.php
│   │   ├── GenerateAiNotification.php
│   │   ├── SendPushNotification.php
│   │   └── ProcessBookChunking.php
│   ├── Models/               ← all 30+ models with relationships
│   └── Services/
│       ├── Ai/
│       │   ├── AiProviderFactory.php
│       │   └── Providers/ (OpenAi, Gemini, Claude, Groq, DeepSeek, Ollama, Vllm, HuggingFace)
│       ├── BookReaderService.php
│       ├── ExamGeneratorService.php
│       ├── WalletService.php
│       ├── SubscriptionService.php
│       ├── LeaderboardService.php
│       ├── ReferralService.php
│       ├── NotificationService.php
│       └── SeoService.php
├── resources/
│   ├── js/
│   │   ├── Pages/
│   │   │   ├── Public/       (Home, About, HowItWorks, FAQ, Contact)
│   │   │   ├── Auth/         (Register, Login)
│   │   │   ├── Student/      (all student pages)
│   │   │   └── Admin/        (all admin pages)
│   │   ├── Components/
│   │   │   ├── Layout/       (StudentLayout, AdminLayout, PublicLayout)
│   │   │   ├── UI/           (Button, Modal, Toast, FlipCard, Timer, etc.)
│   │   │   ├── BookReader/   (FlipReader.vue)
│   │   │   ├── AiChat/       (ChatBox.vue with SSE)
│   │   │   ├── Seo/          (JsonLd.vue, MetaTags.vue)
│   │   │   └── Support/      (SupportWidget.vue)
│   │   └── Stores/
│   │       ├── auth.js
│   │       ├── theme.js       ← dark/light mode Pinia store
│   │       ├── wallet.js
│   │       ├── ai.js
│   │       ├── exam.js
│   │       └── notifications.js
│   └── css/app.css            ← CSS variables for both modes
├── routes/
│   ├── web.php               ← student web routes
│   ├── admin.php             ← admin routes
│   └── api.php               ← REST API routes
├── database/
│   ├── migrations/           ← all 34 migrations in order
│   └── seeders/
│       ├── DatabaseSeeder.php
│       ├── AdminSeeder.php
│       ├── AiProviderSeeder.php
│       ├── PlanSeeder.php
│       ├── CategorySeeder.php
│       ├── AuthorSeeder.php
│       ├── PublicationSeeder.php
│       ├── BookSeeder.php
│       ├── MentorSeeder.php
│       ├── CourseSeeder.php
│       ├── StudentSeeder.php
│       ├── SiteSettingSeeder.php
│       ├── CustomPageSeeder.php
│       └── DemoDataSeeder.php
└── storage/app/private/books/   ← PDFs ONLY HERE, never public
```

---

## SCHEDULED COMMANDS

```php
// routes/console.php (Laravel 11)
use Illuminate\Support\Facades\Schedule;

Schedule::command('notifications:generate-ai')->dailyAt('07:00');
Schedule::command('notifications:send-scheduled')->everyFiveMinutes();
Schedule::command('leaderboard:reset-weekly')->weekly()->mondays()->at('00:00');
Schedule::command('leaderboard:reset-monthly')->monthlyOn(1, '00:00');
Schedule::command('subscriptions:expire-check')->daily();
Schedule::command('sitemap:generate')->daily();
```

---

## QUEUE CONFIGURATION

```php
// Three queues in priority order
'ai'            // AI generation jobs (highest priority)
'notifications' // Push notification jobs
'default'       // Everything else
```

```bash
php artisan queue:work redis --queue=ai,notifications,default
```

---

## TESTING CREDENTIALS (after seeding)

| Role | Email | Password |
|------|-------|----------|
| Super Admin | admin@sikhun.com | admin123 |
| Student 1 | student1@sikhun.com | student123 |
| Student 2 | student2@sikhun.com | student123 |
| ... | student{N}@sikhun.com | student123 |

---

## WHAT NOT TO DO

- ❌ Do NOT use Filament, Livewire, or Alpine.js
- ❌ Do NOT expose PDF files in `public/` or via direct URL
- ❌ Do NOT hardcode any AI provider (always use AiProviderFactory)
- ❌ Do NOT run AI generation synchronously in HTTP requests
- ❌ Do NOT use polling for AI chat (use SSE)
- ❌ Do NOT query leaderboard without Redis cache
- ❌ Do NOT mix student and admin guards
- ❌ Do NOT use `dd()` or `dump()` in production code
- ❌ Do NOT write raw SQL with user input (use Eloquent always)
- ❌ Do NOT skip Form Request validation classes

---

## QUICK REFERENCE: IMPLEMENTATION PRIORITY

When in doubt about what to build next, follow this priority:

1. Multi-guard auth (web + admin + api) — foundation of everything
2. All 34 migrations in correct order
3. Book reader PDF security — this is the core monetization gate
4. AI Provider Factory — everything AI depends on this
5. REST API controllers mirror web controllers (reuse service classes)
6. Theme mode (dark/light) — simple Pinia store + Tailwind class strategy
7. SEO — add to every page as you build it, not at the end
8. Seeders — create realistic demo data so you can test every feature

---

*Sikhun.com | Laravel 11 + Vue 3 + Inertia.js | Bangladesh AI Education Platform*
*Agent Prompt Version: 2.0 | Includes: REST API + Theme Mode + Advanced SEO + Demo Seeders*
