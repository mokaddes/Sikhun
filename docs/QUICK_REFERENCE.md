# SIKHUN.COM — AGENT QUICK REFERENCE CHEATSHEET

> Paste this as a secondary reference alongside AGENT_PROMPT.md
> Use this for fast lookups during development

---

## 🔐 TEST CREDENTIALS

| Role | Email | Password | URL |
|------|-------|----------|-----|
| Super Admin | admin@sikhun.com | admin123 | /admin/login |
| Student 1 | student1@sikhun.com | student123 | /login |
| Student 2–20 | student{N}@sikhun.com | student123 | /login |

---

## 🚀 FIRST COMMANDS (run in order)

```bash
# 1. Create project
composer create-project laravel/laravel sikhun

# 2. Install PHP packages
composer require \
  laravel/sanctum \
  laravel/reverb \
  laravel/horizon \
  laravel/scout \
  spatie/laravel-permission \
  spatie/laravel-medialibrary \
  spatie/laravel-sitemap \
  inertiajs/inertia-laravel \
  tightenco/ziggy \
  barryvdh/laravel-dompdf \
  intervention/image-laravel \
  openai-php/laravel \
  kreait/firebase-php \
  guzzlehttp/guzzle

# 3. Install JS packages
npm install vue@^3 @inertiajs/vue3 pinia @vueuse/core \
  tailwindcss @tailwindcss/vite apexcharts vue-apexcharts \
  axios dayjs @heroicons/vue

# 4. Publish configs
php artisan vendor:publish --provider="Laravel\Sanctum\SanctumServiceProvider"
php artisan vendor:publish --provider="Laravel\Horizon\HorizonServiceProvider"
php artisan vendor:publish --provider="Spatie\Permission\PermissionServiceProvider"

# 5. Run migrations + seed
php artisan migrate:fresh --seed

# 6. Build frontend
npm run build

# 7. Start services
php artisan serve          # web server
php artisan horizon        # queue monitor
php artisan reverb:start   # websockets
npm run dev                # vite dev server
```

---

## 📁 KEY FILE LOCATIONS

```
routes/
  web.php          → Student web routes (auth:web)
  admin.php        → Admin routes (auth:admin)
  api.php          → REST API routes (auth:sanctum)
  console.php      → Scheduled commands

app/
  Models/          → All Eloquent models
  Services/        → Business logic (NOT in controllers)
  Services/Ai/     → AiProviderFactory + provider drivers
  Jobs/            → All background jobs
  Http/Controllers/Api/      → API controllers
  Http/Controllers/Student/  → Web student controllers
  Http/Controllers/Admin/    → Admin controllers
  Http/Requests/   → Form Request validation classes
  Contracts/       → AiProviderContract interface

resources/
  js/Pages/Public/   → Home, About, FAQ, etc.
  js/Pages/Student/  → All student panel pages
  js/Pages/Admin/    → All admin panel pages
  js/Components/Layout/  → StudentLayout, AdminLayout, PublicLayout
  js/Stores/         → Pinia stores (theme, auth, wallet, ai, exam)
  css/app.css        → CSS variables for light/dark modes

database/
  migrations/        → All 34 migrations
  seeders/           → All 14 seeders

storage/app/private/books/   ← PDFs live here ONLY (never public/)
public/sitemap.xml           ← Auto-generated sitemap
public/robots.txt            ← Manual file, never auto-generated
```

---

## 🗄️ MIGRATION ORDER (must follow exactly)

```
01. admins
02. students
03. categories
04. authors
05. publications
06. books
07. book_shelves
08. mentors
09. courses
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

---

## 🎨 COLOR TOKENS

### CSS Variables
```css
/* Light mode (:root) */
--primary: #6c63ff
--primary-hover: #5b53ee
--secondary: #00d4aa
--accent: #ff6b6b
--bg: #ffffff
--surface: #f8f8ff
--surface2: #f0f0fa
--border: #e2e2ee
--text: #1a1a2e
--text-muted: #6b6b8a

/* Dark mode (.dark) */
--bg: #09090f
--surface: #111118
--surface2: #18181f
--border: #2a2a38
--text: #e8e8f0
--text-muted: #7a7a9a
```

### Tailwind class quick reference
```
bg-white dark:bg-[#111118]          ← surface
bg-[#f8f8ff] dark:bg-[#18181f]     ← surface2
border-gray-200 dark:border-[#2a2a38]
text-gray-900 dark:text-[#e8e8f0]
text-gray-500 dark:text-[#7a7a9a]
bg-[#6c63ff] hover:bg-[#5b53ee]    ← primary button
```

---

## 🤖 AI PROVIDER USE CASES

| Use Case Key | Purpose | Temp | Max Tokens |
|---|---|---|---|
| `book_chat` | AI chat with book content | 0.70 | 2000 |
| `exam_gen` | Generate exam questions | 0.30 | 4000 |
| `flashcard_gen` | Generate flashcard pairs | 0.50 | 2000 |
| `essay_grade` | Grade essays | 0.20 | 3000 |
| `schedule_gen` | Generate study schedule | 0.40 | 3000 |
| `notification_gen` | Daily educational notification | 0.80 | 300 |
| `support_bot` | Support chat widget | 0.60 | 1000 |

### Always use:
```php
$provider = AiProviderFactory::default('exam_gen');  // ✅
// Never:
$response = OpenAI::chat()->create([...]);            // ❌
```

---

## 📡 API ENDPOINTS QUICK REF

### Auth (no token needed)
```
POST /api/auth/register
POST /api/auth/login
POST /api/auth/forgot-password
POST /api/auth/reset-password
```

### Auth (token required)
```
POST   /api/auth/logout
GET    /api/auth/me

GET    /api/library                  ?level=hsc&subject=physics&search=...&free=1
GET    /api/library/{book:slug}
POST   /api/library/{book}/purchase  {payment_method: wallet|sslcommerz|bkash|nagad}
GET    /api/bookshelf

GET    /api/reader/{book}/page/{n}   ← signed URL, rate limited

POST   /api/ai/chat/sessions         {source_type, source_book_id, ...}
GET    /api/ai/chat/sessions/{id}/stream  ← SSE endpoint

POST   /api/exams                    {source_type, config:{type,count,duration,mode}}
GET    /api/exams/{id}
POST   /api/exams/{id}/answer        {question_id, answer}
POST   /api/exams/{id}/complete
GET    /api/exams/{id}/result

GET    /api/leaderboard              ?period=weekly&type=hsc&subject=physics
GET    /api/leaderboard/my-rank

GET    /api/wallet
POST   /api/wallet/recharge          {amount, gateway: sslcommerz|bkash|nagad|manual}

PUT    /api/profile/theme            {theme_mode: light|dark|system}
POST   /api/profile/fcm-token        {fcm_token: "..."}
```

### API Response format (always)
```json
{
  "success": true,
  "data": { ... },
  "message": "Success",
  "meta": { "pagination": { "total": 100, "per_page": 20, "current_page": 1, "last_page": 5 } }
}
```

### API Error format (always)
```json
{
  "success": false,
  "message": "Validation failed",
  "errors": { "email": ["The email field is required."] }
}
```

---

## 🌙 DARK MODE CHECKLIST

- [ ] `tailwind.config.js` has `darkMode: 'class'`
- [ ] `useThemeStore` Pinia store created with `init()`, `setMode()`, `applyTheme()`
- [ ] `StudentLayout.vue` calls `theme.init()` on `onMounted`
- [ ] `ThemeToggle.vue` shows Sun/Moon/System icons and cycles through modes
- [ ] `students` table has `theme_mode ENUM('light','dark','system') DEFAULT 'system'`
- [ ] Login: load saved `theme_mode` from `/api/auth/me` and apply
- [ ] `PUT /api/profile/theme` saves to DB
- [ ] ALL student panel components have `dark:` Tailwind variants
- [ ] No unstyled white flash when loading in dark mode (apply class before render)

---

## 🔍 SEO CHECKLIST (every page)

- [ ] `<Head><title>{{ seo.title }}</title></Head>`
- [ ] `<meta name="description" :content="seo.description" />` (max 160 chars)
- [ ] `<link rel="canonical" :href="seo.canonical" />`
- [ ] OG tags: `og:title`, `og:description`, `og:image`, `og:url`, `og:type`
- [ ] Twitter tags: `twitter:card`, `twitter:title`, `twitter:description`, `twitter:image`
- [ ] `<JsonLd :data="jsonLdData" />` component with correct schema type
- [ ] `BreadcrumbList` JSON-LD on all pages deeper than 1 level
- [ ] All `<img>` tags have `alt`, `width`, `height`, `loading="lazy"` (except hero)
- [ ] `sitemap.xml` updated (auto-generated daily, force-generate during dev)
- [ ] `robots.txt` blocks: `/admin/`, `/dashboard/`, `/reader/`, `/api/`
- [ ] `SeoService::forBook()` / `forCourse()` / `forHome()` used in controller

---

## 📦 SEEDER QUICK REF

```bash
# Run all seeders
php artisan db:seed

# Run specific seeder
php artisan db:seed --class=StudentSeeder

# Fresh migrate + seed
php artisan migrate:fresh --seed
```

### Seeder run order in DatabaseSeeder.php
```php
$this->call([
    AdminSeeder::class,           // admin@sikhun.com / admin123
    AiProviderSeeder::class,      // 7 OpenAI configs (one per use_case)
    PlanSeeder::class,            // স্টার্টার, প্রো, প্রিমিয়াম
    CategorySeeder::class,        // Academic + Non-Academic with Bengali subs
    AuthorSeeder::class,          // 5 Bengali authors
    PublicationSeeder::class,     // NCTB, Panjeree, etc.
    BookSeeder::class,            // 12+ HSC/SSC/University/Job books
    MentorSeeder::class,          // 3 mentors
    CourseSeeder::class,          // 2 courses with sections + lessons
    StudentSeeder::class,         // 20 students (student1-20@sikhun.com)
    SiteSettingSeeder::class,     // Bengali default site settings
    CustomPageSeeder::class,      // About, FAQ, Terms, Privacy, Contact
    DemoDataSeeder::class,        // wallets, exams, leaderboard, referrals
]);
```

---

## ⚡ QUEUE CONFIGURATION

```env
QUEUE_CONNECTION=redis
```

```bash
# Start queue worker (priority order: ai > notifications > default)
php artisan queue:work redis --queue=ai,notifications,default --tries=3 --timeout=120
```

### Jobs and their queues
| Job | Queue | Timeout |
|-----|-------|---------|
| GenerateExamQuestions | ai | 120s |
| GenerateFlashcards | ai | 90s |
| GradeEssay | ai | 90s |
| GenerateStudySchedule | ai | 90s |
| GenerateAiNotification | notifications | 30s |
| SendPushNotification | notifications | 30s |
| ProcessBookChunking | default | 300s |

---

## 📅 SCHEDULED COMMANDS

```php
// routes/console.php
Schedule::command('notifications:generate-ai')->dailyAt('07:00');
Schedule::command('notifications:send-scheduled')->everyFiveMinutes();
Schedule::command('leaderboard:reset-weekly')->weekly()->mondays()->at('00:00');
Schedule::command('leaderboard:reset-monthly')->monthlyOn(1, '00:00');
Schedule::command('subscriptions:expire-check')->daily();
Schedule::command('sitemap:generate')->daily();
```

```bash
# Run scheduler locally
php artisan schedule:run
# Or keep running every minute
php artisan schedule:work
```

---

## 🔒 SECURITY RULES (never break these)

| Rule | WHY |
|------|-----|
| PDFs in `storage/app/private/books/` ONLY | Prevent direct URL access |
| Signed URLs expire in 15 minutes | Prevent URL sharing |
| Watermark every page image | Deter screenshots/piracy |
| Rate limit reader: 5 req/10 sec | Prevent bulk scraping |
| Encrypt AI API keys in DB | `encrypted:` cast on `api_key` column |
| Form Request for ALL POST/PUT | Prevent SQL injection + XSS |
| Never raw SQL with user input | Eloquent query builder only |
| `throttle:10,1` on AI API endpoints | Prevent AI cost abuse |

---

## 🐛 COMMON ERRORS & FIXES

### "Target class [auth:admin] does not exist"
→ Check `bootstrap/app.php` has `routes/admin.php` registered
→ Check `config/auth.php` has `admin` guard and `admins` provider

### "419 Page Expired" on form submit
→ Add `@csrf` to Blade forms
→ For API: ensure `axios` has CSRF token header configured

### AI chat not streaming
→ Check server has `fastcgi_finish_request` or use `ob_flush(); flush();`
→ Check Nginx doesn't buffer: `X-Accel-Buffering: no` header
→ Check `Content-Type: text/event-stream` header is set

### Signed URL returns 403
→ Check `APP_KEY` is set in `.env`
→ Check URL is not expired (15 min limit)
→ Check `APP_URL` matches the URL being accessed

### Queue jobs not running
→ Run `php artisan horizon` or `php artisan queue:work`
→ Check `QUEUE_CONNECTION=redis` in `.env`
→ Check Redis is running: `redis-cli ping`

### Meilisearch not finding books
→ Run `php artisan scout:import "App\Models\Book"`
→ Check `SCOUT_DRIVER=meilisearch` in `.env`
→ Check Meilisearch is running on port 7700

### Dark mode flash on page load
→ Apply theme class BEFORE Vue mounts
→ Add inline script in `<head>`: `if(localStorage.getItem('sikhun_theme')==='dark') document.documentElement.classList.add('dark')`

---

## 📊 STUDENT TYPES

| Display | DB Value | Target |
|---------|----------|--------|
| HSC | `hsc` | Class 11-12 students |
| SSC | `ssc` | Class 9-10 students |
| বিশ্ববিদ্যালয় | `university` | University students |
| চাকরির প্রস্তুতি | `job` | BCS, bank, govt job seekers |

---

## 📝 AI PROMPT TEMPLATES

### Exam generation system prompt
```
You are an expert Bangladeshi {level} level {subject} teacher.
Generate {count} {type} questions based on the provided content.
Questions must be in Bengali. Difficulty: {difficulty}.
Return ONLY valid JSON with this exact structure:
{
  "questions": [
    {
      "id": 1,
      "question": "...",
      "type": "mcq",
      "options": ["A", "B", "C", "D"],
      "correct_answer": "A",
      "explanation": "..."
    }
  ]
}
Do not include any text outside the JSON.
```

### Flashcard generation system prompt
```
You are an expert study assistant for Bangladeshi students.
Create {count} flashcard pairs from the provided content.
Cards should be in Bengali. Focus on key concepts.
Return ONLY valid JSON:
{
  "flashcards": [
    { "front": "question/concept", "back": "answer/explanation" }
  ]
}
```

### Essay grading system prompt
```
You are an expert {type} writing evaluator.
Grade the following essay and return ONLY valid JSON:
{
  "total_score": 85,
  "max_score": 100,
  "breakdown": {
    "content": { "score": 30, "max": 35, "feedback": "..." },
    "language": { "score": 25, "max": 30, "feedback": "..." },
    "structure": { "score": 20, "max": 25, "feedback": "..." },
    "originality": { "score": 10, "max": 10, "feedback": "..." }
  },
  "overall_feedback": "...",
  "strengths": ["...", "..."],
  "improvements": ["...", "..."]
}
```

---

## 🏆 LEADERBOARD LOGIC

```
Entry created when:
  - exam.mode === 'exam' (not practice)
  - exam.status === 'completed'
  - exam.total >= 10 (minimum questions)

Score formula:
  leaderboard_score = percentage × (questions_count / 10)

Redis cache key:
  leaderboard:{type}:{period}:{filter_hash}
  TTL: 5 minutes

Reset schedule:
  Weekly: every Monday 00:00 BDT
  Monthly: 1st of month 00:00 BDT
  All-time: never reset
```

---

## 💰 REFERRAL LOGIC

```
Flow:
  1. Student A shares: sikhun.com/register?ref=SIKHU-XXXXX
  2. Student B registers → referral stored (status: pending)
  3. Student B makes first paid purchase
  4. Trigger ReferralService::reward(referral)
  5. Student A gets +৳20 wallet credit (referrer_reward)
  6. Student B gets +৳20 wallet credit (referee_reward)
  7. Referral status → 'rewarded'
  8. Does NOT trigger again on second purchase

Blocked:
  - Same email as referrer
  - Free purchases (only paid)
  - Already rewarded referrals
  - Over monthly limit (default: 10 referrals/month)
```

---

## 📱 MOBILE APP NOTES (REST API)

When building mobile app, note:
- Auth: `POST /api/auth/login` → returns `{token: "..."}`
- All requests: `Authorization: Bearer {token}` header
- SSE streaming: native EventSource or library like `react-native-sse`
- Push: register FCM token via `POST /api/profile/fcm-token`
- Theme preference: `PUT /api/profile/theme` with `{theme_mode: 'dark'}`
- Pagination: check `meta.pagination.current_page < meta.pagination.last_page`
- Images: all book pages are JPEG images (not PDFs)

---

*Sikhun.com Quick Reference v2.0 | Always read AGENT_PROMPT.md first*
