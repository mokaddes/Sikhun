# Sikhun.com — Hotfix + Feature Update

This drop addresses three things you reported, in order of what you asked about first.

---

## 1. The navigation reload bug — root cause found and fixed

**What was happening:** clicking a link did a full page reload once, then worked as a
normal SPA navigation on the second click — consistently, mostly noticeable in the
admin panel.

**Root cause:** `bootstrap/app.php` never configured Laravel to trust reverse proxy
headers (`X-Forwarded-For`, `X-Forwarded-Proto`, etc). If your production server sits
behind **Nginx, Cloudflare, a load balancer, or cPanel's proxy** — which almost every
real deployment does — Laravel can't reliably tell whether the original request was
HTTPS. This makes session and CSRF cookies behave inconsistently: a cookie gets set
or read incorrectly on some requests, the session looks "lost" for that one request,
Inertia can't complete its normal AJAX navigation, and it falls back to a full browser
reload to recover. The retry then works because the browser now has a valid cookie.

This is a very common production gotcha — it doesn't show up in local dev
(`php artisan serve` has no proxy in front of it) which is exactly why it wasn't
caught earlier.

### The fix (already applied in this drop)

- **`bootstrap/app.php`** — added `$middleware->trustProxies(at: '*', headers: ...)`.
  If you are running on a bare VPS with **no** reverse proxy in front of PHP-FPM at
  all, remove this block — trusting proxy headers you don't actually have in front of
  you is a security downgrade, not a no-op.
- **`resources/js/bootstrap.js`** — added `withCredentials`, explicit
  `xsrfCookieName`/`xsrfHeaderName` so axios (which every Inertia form submission and
  AJAX call uses) handles the session cookie consistently.
- **`.env.example`** — added `SESSION_DOMAIN` and `SESSION_SECURE_COOKIE` with
  guidance comments. **Set `SESSION_SECURE_COOKIE=true` once you're serving over
  HTTPS in production** — leaving it `false` on a live HTTPS site is a separate real
  security bug (the cookie would still be accepted over plain HTTP).

### If it's still happening after this fix

1. Open your browser's Network tab, click a link, and check the response to the
   Inertia XHR request. If you see a `302` or `419` there, that confirms a
   session/auth issue — check `APP_URL` in `.env` matches the actual domain exactly
   (including `https://`), and confirm Redis (your session driver) is actually running
   and reachable.
2. Confirm `public/hot` does **not** exist on your server. This file is created by
   `npm run dev` and its presence make Vite serve dev-mode assets that don't match
   what's actually deployed — always run `npm run build` for production and confirm
   `public/hot` is absent (it's git-ignored, but can survive if you ever ran `npm run
   dev` directly on the server).
3. After every deploy: `php artisan optimize:clear` then `php artisan config:cache
   route:cache view:cache`, in that order — stale cached config is a common source of
   exactly this kind of inconsistent behavior.

---

## 2. AI Provider credentials — now genuinely multi-select

**What changed:** an `AiProvider` row used to represent both credentials AND exactly
one use case, forcing you to create duplicate rows with the same API key just to
cover multiple features. That's fixed — a provider row is now purely credentials
(name, type, key, model, endpoint), and a new `ai_provider_use_cases` pivot table lets
you assign **any number of use cases** to one set of credentials, each independently
markable as the platform-wide default for that use case.

- **New migration**: `ai_provider_use_cases` table, with your existing data
  automatically migrated over (nothing is lost — your current providers' use cases
  carry across exactly as they were configured).
- **`/admin/ai-providers/create`** now shows a checklist of all 7 use cases with a
  "select all" shortcut, and a small "default" checkbox next to each selected one.
- **The seeder** (`AiProviderSeeder`) now creates exactly **one** provider row
  assigned to all 7 use cases and marked default for all of them — matching what you
  asked for out of the box.
- **The Index page** now shows use-case badges (with a ★ on whichever ones this
  provider is default for) instead of one row per use case.

### Run the new migration

```bash
php artisan migrate
```

---

## 3. Dynamic public pages — now bilingual, with a real rich text editor

**What changed:** `custom_pages` (About, FAQ, Terms, Privacy, Contact, How It Works)
used to have one `title`/`content` regardless of language — switching to English
showed the same Bengali text. Fixed.

- **New migration** adds `title_bn` / `title_en` / `content_bn` / `content_en` /
  `meta_title_bn` / `meta_title_en` / `meta_description_bn` / `meta_description_en`.
  Your existing content is automatically copied into **both** language columns during
  migration, so nothing breaks — go into `/admin/pages` and fill in real English
  translations at your own pace. The old single-language columns are kept (not
  dropped) for backward compatibility, but nothing reads them anymore going forward.
- **`/admin/pages/{id}/edit`** now has a Bengali/English tab switcher, each tab fully
  independent (title, content, meta title, meta description).
- **A real rich text editor** (TipTap — bold/italic/strikethrough, H2/H3 headings,
  bullet/numbered lists, links, blockquotes, undo/redo) replaces the old raw-HTML
  textarea for page content, in both language tabs.
- **The public page** (`/p/{slug}`) now genuinely switches content when you toggle
  EN/বাং — the backend resolves which language to serve based on the current locale
  (same mechanism as every other bilingual string in the app), so there's no
  client-side logic to get wrong.

### Install the new npm packages and run the new migration

```bash
npm install    # picks up @tiptap/vue-3, @tiptap/starter-kit, @tiptap/extension-link
php artisan migrate
```

---

## Applying this update

```bash
cp -r app database routes resources lang bootstrap .env.example /path/to/your/sikhun/
composer dump-autoload
npm install
php artisan migrate
php artisan config:clear
php artisan cache:clear
npm run build
```

Then go to `/admin/ai-providers` and confirm your existing provider(s) still show
their correct use-case badges (the migration should have carried this over
automatically — worth a manual check regardless), and `/admin/pages` to start filling
in English translations for your CMS pages.

## One more thing worth checking on your server specifically

Since the reload bug was proxy-related, it's worth confirming what's actually sitting
in front of your app:

- **Shared hosting / cPanel**: usually has its own reverse proxy layer — the
  `trustProxies(at: '*')` fix should resolve it, but if you know the specific proxy
  IP range, using that instead of `'*'` is more correct security-wise.
- **VPS with Nginx → PHP-FPM directly**: same fix applies if Nginx is doing SSL
  termination (i.e., Nginx speaks HTTPS to the browser but HTTP to PHP-FPM) — which
  is by far the most common setup.
- **Behind Cloudflare**: same fix applies, and additionally double-check
  `SESSION_SECURE_COOKIE=true` and that your Cloudflare SSL mode is "Full" or "Full
  (strict)", not "Flexible" — Flexible mode is a known source of redirect loops with
  Laravel apps specifically.
