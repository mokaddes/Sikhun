# Sikhun.com — Complete Technical SEO & GEO Audit Report

**Date of Audit:** November 12, 2026  
**Auditor:** Senior Laravel Developer & Technical SEO / GEO Specialist  
**Target Domain:** https://sikhun.com  
**Primary Locale:** bn-BD (Bengali) | **Secondary Locale:** en (English)  
**Platform Architecture:** Laravel 11.57.1, PHP 8.3/8.5, Inertia.js, Vue 3, Vite, Tailwind CSS, MySQL 8.0, Nginx

---

## 1. Executive Summary

A comprehensive architectural and on-page technical SEO & GEO (Generative Engine Optimization) audit was performed on Sikhun.com. The platform possesses strong underlying database content (23 published digital academic books, active courses, 50,000+ question bank items, and timely university admission circulars). However, several critical technical SEO bottlenecks were uncovered:

1. **Initial Raw HTML Head Void:** Because Inertia.js dynamically updates meta tags via client-side JavaScript (`<SeoHead>` in Vue), the raw server-rendered HTML in `resources/views/app.blade.php` lacked dynamic meta descriptions, canonical URLs, Open Graph tags, Twitter cards, and JSON-LD structured data. Search engine bots (first pass) and AI search engines (OAI-SearchBot, Perplexity, Gemini) received an empty `<title inertia>SIKHUN</title>` without descriptions or schemas.
2. **Missing Dynamic XML Sitemap:** `public/sitemap.xml` was a static, outdated file generated in October 2026, completely missing published university admission notices, newly uploaded books, and real-time content changes. No dynamic sitemap route or sitemap index existed.
3. **Robots.txt Incomplete for AI Engines:** `public/robots.txt` lacked modern crawler directives for `OAI-SearchBot` (allowed for ChatGPT Search) vs `GPTBot` (disallowed for scraper training), and did not disallow newly added private routes like `/mistakes/`.
4. **Key Public Portals Missing SEO Integration:** The University Admission Notices portal (`/admission-notice`, `/admission-notice/{slug}`), Job News portal (`/job-news`), Student Tools (`/tools`), and Contact page did not inject SEO metadata or JSON-LD structured data.
5. **Absence of GEO Assets (`llms.txt`):** No `public/llms.txt` was configured to describe Sikhun's educational entity, book reader, question bank, and admission features to LLMs.
6. **No Automated `noindex` Guarantee for Authenticated Routes:** Private student dashboard routes and error pages lacked explicit `<meta name="robots" content="noindex, nofollow">` in the server-rendered HTML.

---

## 2. Detailed Findings & Issue Register

| ID | Issue Description | Affected File / URL | SEO Impact | Priority | Recommended Fix | Status |
| :--- | :--- | :--- | :--- | :--- | :--- | :--- |
| **AUD-01** | **Server-rendered raw HTML lacks dynamic meta tags & JSON-LD** | `resources/views/app.blade.php` | Search bots & AI crawlers fetching raw HTML receive no description, canonical, or schema. | **Critical** | Dynamically render title, description, canonical link, OG, Twitter, and JSON-LD in `app.blade.php` head. | Pending Implementation |
| **AUD-02** | **Static, out-of-date `sitemap.xml` with no dynamic route** | `public/sitemap.xml`, `routes/web.php` | New university admission notices and book releases are not indexed promptly. | **Critical** | Implement dynamic `SitemapController` with caching, content-type `application/xml`, and sitemap index support. | Pending Implementation |
| **AUD-03** | **Crawler policy lacks OAI-SearchBot & GPTBot directives** | `public/robots.txt` | ChatGPT Search bot accessibility undefined; GPTBot training scraper unmanaged. | **High** | Update `public/robots.txt` to explicitly allow `OAI-SearchBot` and disallow `GPTBot`, plus block `/mistakes/`. | Pending Implementation |
| **AUD-04** | **Admission notices portal completely missing SEO & Schema** | `AdmissionController.php`, `Admissions/Index.vue`, `Show.vue` | High-traffic admission circulars (DU, RU, BUET, GST) miss rankings & search rich results. | **High** | Add `forAdmissionIndex()` & `forAdmissionNotice()` in `SeoService`, pass to props, and render `<SeoHead>`. | Pending Implementation |
| **AUD-05** | **Job News, Tools, and Contact pages missing SEO integration** | `JobController.php`, `ToolController.php`, `ContactController.php` | Category and hub pages lack canonical URLs and descriptions. | **Medium** | Implement dedicated SEO methods in `SeoService` and integrate `<SeoHead>`. | Pending Implementation |
| **AUD-06** | **Absence of `llms.txt` for Generative Engine Optimization** | `public/llms.txt` | AI search engines lack structured platform reference for citations and recommendations. | **Medium** | Create standard `public/llms.txt` documenting Sikhun.com's mission, tools, and resources. | Pending Implementation |
| **AUD-07** | **Inconsistent `noindex, nofollow` on private student pages** | `resources/views/app.blade.php`, `HandleInertiaRequests.php` | Authenticated dashboard paths could leak into search index if linked externally. | **High** | Enforce `<meta name="robots" content="noindex, nofollow">` for all non-public and error responses in root Blade view. | Pending Implementation |
| **AUD-08** | **Missing Google Sitelinks SearchBox (`WebSite` + `SearchAction`)** | `app/Services/SeoService.php` (`forHome()`) | Misses enhanced Google SERP search bar for library and question bank. | **Medium** | Add `WebSite` schema with `SearchAction` targeting `/library?search={search_term_string}`. | Pending Implementation |
| **AUD-09** | **Error pages (404/500) lack robots noindex directive** | `resources/js/Pages/Error/Show.vue` | Soft 404 or transient errors might be cached or indexed by search engines. | **Medium** | Add `<meta name="robots" content="noindex, nofollow">` to `Error/Show.vue` and fallback error templates. | Pending Implementation |
| **AUD-10** | **Missing canonical self-reference on all public URLs** | All public controllers | Risk of duplicate content issues between query params, trailing slashes, or alternate protocols. | **High** | Enforce absolute self-referencing canonical URL on every public page through `SeoService`. | Pending Implementation |

---

## 3. Technology Stack & Environment Verification

- **PHP Version:** 8.3.6 (Production VPS Ubuntu 24.04), 8.5.12 (CLI local)
- **Framework:** Laravel 11.57.1
- **Frontend Architecture:** Inertia.js 1.2 / 2.0 with Vue 3, Composition API (`<script setup>`)
- **Assets Bundler:** Vite 6 with Tailwind CSS
- **Web Server:** Nginx 1.24.0 with HTTP/2 and Let's Encrypt TLS
- **Database:** MySQL 8.0.46
- **Reverse Proxy:** Cloudflare with Strict SSL and HSTS
- **Analytics:** Google Tag Manager (`GTM-W8R245XB`), Google Analytics 4 (`G-102CKE347K`), Meta Pixel (`1562071828675426`)

---

## 4. Crawlability & Indexability Audit

### 4.1 Crawler Directives (`robots.txt`)
- Existing `public/robots.txt` covers basic disallows for `/admin/`, `/dashboard/`, `/wallet/`, `/exams/`.
- **Gaps:** Lacks modern AI bot directives (`OAI-SearchBot`, `GPTBot`), lacks disallow for newly introduced `/mistakes/` and `/reader/` subpaths, and lacks explicit disallow for query-string search URLs if needed.

### 4.2 XML Sitemap
- Existing `public/sitemap.xml` is static and fixed at 199 lines.
- **Gaps:** Completely misses university admission notices (`/admission-notice/*`), new books, new blog posts, and dynamic category updates. No HTTP cache headers or dynamic generation exist.

### 4.3 HTTP Status Codes & Redirections
- Valid pages return `200 OK`.
- Legacy URLs have permanent 301 redirects (`/jobs` -> `/job-news`, `/calculators` -> `/tools`).
- Missing resources return `404 Not Found` with branded `Error/Show.vue` screen.
- Server error handling prevents stack trace disclosure when debug mode is disabled.

---

## 5. Structured Data (Schema.org) Audit

| Page Type | Current Schema | Target Schema | Status |
| :--- | :--- | :--- | :--- |
| **Homepage** | `EducationalOrganization` | `EducationalOrganization` + `WebSite` + `SearchAction` | Needs Enhancement |
| **Library Index** | `CollectionPage` | `CollectionPage` + `BreadcrumbList` | Compliant |
| **Book Detail** | `Book` + `BreadcrumbList` | `Book` + `BreadcrumbList` + `Offer` | Compliant |
| **Courses Index** | `CollectionPage` | `CollectionPage` + `BreadcrumbList` | Compliant |
| **Course Detail** | `Course` + `BreadcrumbList` + `Offer` | `Course` + `BreadcrumbList` + `Offer` | Compliant |
| **Blog Post** | `Article` + `BreadcrumbList` | `Article` + `BreadcrumbList` | Compliant |
| **Admission Notice** | None | `SpecialAnnouncement` / `WebPage` + `BreadcrumbList` | **Missing (Critical)** |
| **Job Circular** | None | `JobPosting` / `WebPage` + `BreadcrumbList` | **Missing** |
| **FAQ Page** | `FAQPage` | `FAQPage` + `BreadcrumbList` | Compliant |

---

## 6. Generative Engine Optimization (GEO) Audit

1. **AI Discovery:** Currently, AI search engines (like ChatGPT Search, Perplexity, Gemini) must scrape JavaScript-heavy client code. Server-rendered meta and structured data are required for reliable indexing.
2. **Entity Definition:** Sikhun is well-defined as an AI-powered education platform in Bangladesh for HSC, SSC, University Admission, and Competitive Exams.
3. **Structured Context:** Providing `public/llms.txt` enables AI engines to directly parse platform features, study tools, textbook reader capabilities, and admission circular links.
4. **Referral Tracking:** AI search referrals (`chatgpt.com`, `perplexity.ai`, `gemini.google.com`) should be captured and analyzed in analytics.

---

## 7. Action Plan & Next Steps

1. **Phase 2 (Technical Foundation):**
   - Upgrade `app/Services/SeoService.php` to cover all public pages with full schema and canonical support.
   - Inject server-rendered SEO meta and JSON-LD directly into `resources/views/app.blade.php`.
   - Update `public/robots.txt` with OAI-SearchBot and GPTBot policies.
   - Build dynamic XML sitemap system (`SitemapController`) and remove static sitemap.
2. **Phase 3 (Structured Data):**
   - Implement `SpecialAnnouncement` / `WebPage` schema for admission notices and rich search action on homepage.
3. **Phase 4 & 5 (Google SEO & GEO):**
   - Implement `public/llms.txt`.
   - Generate `SEO_KEYWORD_STRATEGY.md` and `SEO_CONTENT_ROADMAP.md`.
4. **Phase 8 (Testing & Verification):**
   - Write comprehensive automated feature tests (`tests/Feature/SeoSystemTest.php`).
   - Validate XML sitemaps, robots.txt, schema JSON-LD, canonical URLs, and noindex headers.
5. **Phase 9 (Deployment & Reporting):**
   - Deploy to VPS, verify live responses, and produce documentation reports.
