---
name: code-writer
description: Write or modify code in the Sikhun Laravel + Inertia/Vue codebase so it matches the existing architecture, naming, i18n, and UI conventions. Use whenever adding a feature, controller, service, migration, Inertia page, or Vue component in this repo.
---

# Writing Code in Sikhun

Match the repo, not a tutorial. Before writing, find the closest existing example and follow its shape.

## Backend

**Placement**
- Controllers: `app/Http/Controllers/{Admin,Student,Api,Public}/`. Thin — validate, call a service, return.
- Business logic: `app/Services/`. One responsibility per class, constructor-injected.
- Validation: `app/Http/Requests/`. Never inline `$request->validate()` for anything reused.
- Money/access writes: wrap in `DB::transaction` and route through `PurchaseService`, `WalletService`, `SubscriptionService`, or `AccessGrantService`.
- Async: `app/Jobs/` for AI, PDF, video, mail. Name jobs by intent (`ProcessBookPdf`), not by mechanism.

**Eloquent**
- Always eager-load relations used in the response: `with(['author:id,name'])`, `withCount('enrollments')`.
- Select only needed columns on list endpoints.
- Prefer `firstOrCreate` for idempotent fulfillment; add unique indexes in migrations to back it up.
- Never put raw SQL in a migration unless guarded by the driver (`DB::getDriverName() === 'mysql'`).

**Conventions**
- Return types on every method.
- Guard clauses over nested `if`.
- Errors that a student should see: throw `\RuntimeException`, catch in the controller, `back()->with('error', ...)`.
- Never log secrets, API keys, or full AI prompts with user data.

## Frontend (Inertia + Vue 3)

**Placement**
- Pages: `resources/js/Pages/{Public,Student,Admin}/...` — path must match the string passed to `Inertia::render()`.
- Shared UI: `resources/js/Components/UI/`, layouts in `Components/Layout/`.
- Use `<script setup>` and the `useI18n()` composable from `@/i18n`.

**i18n — mandatory**
- Add every new string to **both** `lang/en.json` and `lang/bn.json`, then call `t('key')`.
- Do not add new `locale === 'en' ? 'A' : 'বি'` ternaries. The existing ones are debt, not a pattern.
- Bengali must be natural Bangla, not transliteration.

**UI**
- Use the existing CSS variables (`var(--bg)`, `var(--primary)`, `var(--text-muted)`) and Tailwind utilities; do not hardcode new hex colors outside the established palette.
- Dark mode is the default. Test both.
- Mobile-first: the student app uses a fixed bottom nav; leave bottom padding on scrollable pages.
- Every form input needs a `name`, a visible `<label>`, and an error slot. Current inputs missing these are a bug to fix, not copy.
- Empty states and error states are required, not optional.

## Tests

- Feature tests live in `tests/Feature`, use `RefreshDatabase`.
- A new endpoint without a test is incomplete. Cover: happy path, auth guard, validation failure, and one authorization edge.
- Tests must pass on sqlite (`phpunit.xml`). If a migration is MySQL-only, fix the migration, don't skip the test.

## Definition of done

1. `php artisan test` passes.
2. `npm run build` succeeds.
3. No new hardcoded user-facing strings.
4. No N+1 in list responses.
5. Money paths are transactional.
6. Both locales render without layout breakage.
