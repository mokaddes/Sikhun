# Sikhun.com — Design Redesign + Pricing + Filters + Dashboard (changed/new files only)

⚠️ **Important context**: my sandbox reset between our last session and this one, so I
rebuilt these files fresh from the schema, routes, and patterns established earlier in
our conversation rather than editing your actual current files directly. They *should*
integrate cleanly — but I couldn't re-verify against your real current file contents
this time, so please read the manual integration steps below carefully rather than
just overwriting blind, especially for the two files with explicit "ADD THIS" snippets.

## What's in this package

| File | What changed |
|---|---|
| `resources/css/app.css` | New warm/editorial color palette (cream+amber+forest-green replacing indigo-tech) + `@tailwindcss/typography` plugin |
| `resources/js/Components/Layout/PublicLayout.vue` | Restyled nav (added Pricing link), restyled footer |
| `resources/js/Pages/Public/Home.vue` | Full rebuild — hero, category chips, top courses, how-it-works, popular books, AI feature showcase, **pricing section (new)**, testimonials (real, hidden if empty), final CTA |
| `app/Http/Controllers/Public/PageController.php` | `home()` now gathers real stats/courses/books/plans/testimonials |
| `app/Models/Testimonial.php` + migration | New — real, admin-managed, **empty by default** |
| `app/Http/Controllers/Admin/TestimonialController.php` + Request | New admin CRUD |
| `resources/js/Pages/Admin/Testimonials/{Index,Form}.vue` | New admin pages |
| `app/Http/Controllers/Student/LibraryController.php` | Added subject filter + sort (newest/popular/price) |
| `resources/js/Pages/Student/Library/Index.vue` | Redesigned with a real sidebar filter panel |
| `app/Http/Controllers/Student/CourseController.php` | Added category/free/sort filters |
| `resources/js/Pages/Student/Courses/Index.vue` | Redesigned with a matching sidebar filter panel |
| `app/Http/Controllers/Student/DashboardController.php` | Now gathers continue-reading, continue-course, recent exams, leaderboard snippet, subscription status |
| `resources/js/Pages/Student/Dashboard/Index.vue` | Redesigned — continue learning cards, recent exams, leaderboard widget, quick actions |

## On content vs. design (per your last message)

You said you don't need exact content, just the design vibe and content-**ful** pages —
that's exactly what I built: real data (your actual courses, books, plans) presented in
a richer, more complete layout, not literal copy of the reference screenshot's text.

## What I deliberately did NOT fabricate

Your reference image has star ratings, review counts, and named student testimonials.
I did not invent any of these:
- **No fake ratings/review counts** anywhere — course and book cards show real data
  (price, mentor, enrollment count, page count) instead.
- **No fake "Live Classes" section** — that's not a feature this platform has built
  (no live-session scheduling exists), so I didn't fabricate one on the homepage.
- **Testimonials are a real, empty-by-default system.** The homepage section only
  renders when `Testimonial::published()` returns at least one row — add real ones via
  `/admin/testimonials` (new nav item, see below) whenever you have them.

## Required manual steps (things I can't safely auto-apply without your actual files)

### 1. Add the Testimonials routes to `routes/admin.php`

Inside the `auth:admin` middleware group, add:
```php
use App\Http\Controllers\Admin\TestimonialController;
// ...
Route::resource('testimonials', TestimonialController::class)->except(['show']);
```

### 2. Add "Testimonials" to the admin sidebar nav

In `resources/js/Components/Layout/AdminLayout.vue`, add to the `nav` array:
```js
{ label: 'Testimonials', href: '/admin/testimonials' },
```

### 3. Install the new npm package and run the new migration

```bash
npm install @tailwindcss/typography
php artisan migrate
```

### 4. Copy the files

```bash
cp -r app database resources /path/to/your/sikhun/
php artisan config:clear
npm run build
```

## New translation keys needed

I deliberately reused your existing `lang/*.json` keys everywhere I could (`nav.*`,
`common.*`, `library.*`, `dashboard.*`, etc.) rather than introducing new ones — the
only genuinely new page-specific copy (hero headline, "How It Works" step text, AI
feature checklist, pricing labels) is written as inline bilingual computed strings
directly in `Home.vue` itself, so **no lang file merge should be required.** If you spot
any key that doesn't resolve (renders as the raw key name instead of text), it's most
likely one of these already-existing ones — double check it's still present in your
`lang/bn.json` / `lang/en.json`:

`library.title`, `library.all_levels`, `library.free_only`, `library.no_books`,
`courses_page.title`, `courses_page.no_courses`, `dashboard.welcome`, `dashboard.subtitle`,
`dashboard.wallet_balance`, `dashboard.books_owned`, `dashboard.exams_taken`,
`dashboard.courses_enrolled`, `bookshelf.continue_reading`, `leaderboard_page.your_rank`,
`plans_page.title`, `footer.tagline`, `footer.platform`, `footer.organization`,
`footer.legal`, `footer.terms`, `footer.privacy`, `footer.rights`.

## Try it

- `/` — full redesigned homepage; toggle dark mode and EN/বাং, confirm both work
  across every new section
- `/#pricing` — scroll-anchored pricing section pulling your real `Plan` rows
- `/library` and `/courses` — new sidebar filters (subject, category, sort, free-only)
- `/dashboard` — continue reading/course cards (log in as a student with an active
  `ReadingSession` or `CourseEnrollment` to see these populated), recent exams,
  leaderboard widget
- `/admin/testimonials` — add one real testimonial, then reload `/` and confirm the
  "What Our Students Say" section appears (it was hidden before you added one)
