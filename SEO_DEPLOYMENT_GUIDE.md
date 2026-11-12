# Sikhun.com — SEO & GEO Production Deployment Guide

**Target Server:** 160.25.5.61 (Ubuntu 24.04 LTS, Nginx, PHP 8.3-FPM)  
**Root Directory:** `/var/www/html`  
**Application URL:** https://sikhun.com  
**DNS / Proxy:** Cloudflare with Full SSL/TLS & HSTS  

---

## 1. Pre-Deployment Checklist

Before deploying changes to the production server:

- [x] All 12 automated SEO unit & feature tests pass locally (`php artisan test tests/Feature/SeoSystemTest.php`).
- [x] All 19 core regression tests pass (`tests/Feature/PublicAdmissionFunnelTest.php`, etc.).
- [x] Frontend assets compiled without error via Vite (`npm run build`).
- [x] Static `public/sitemap.xml` deleted from repository so Nginx passes `/sitemap.xml` to Laravel.
- [x] `public/robots.txt` updated with `OAI-SearchBot` and `GPTBot` directives.
- [x] `public/llms.txt` present in root.

---

## 2. Step-by-Step Deployment Instructions

### Step 2.1: Commit & Push to Remote Repository
From the local workspace (`D:\projects\other\sikhun`):
```bash
git add .
git commit -m "feat(seo): complete technical SEO, dynamic XML sitemap, GEO llms.txt and structured data"
git push origin master
```

### Step 2.2: Pull & Deploy on Production VPS
Execute via SSH:
```bash
ssh root@160.25.5.61 "bash -c '
    set -e
    cd /var/www/html

    # Pull latest master commits
    git pull origin master

    # Remove static sitemap file if present on disk
    rm -f public/sitemap.xml

    # Clear application and routing caches
    php artisan optimize:clear

    # Rebuild production caches
    php artisan config:cache
    php artisan route:cache
    php artisan view:cache

    # Ensure storage and public permissions
    chown -R www-data:www-data storage bootstrap/cache public/robots.txt public/llms.txt
    chmod -R 775 storage bootstrap/cache

    # Reload PHP-FPM to ensure opcache serves latest classes
    systemctl reload php8.3-fpm

    echo \"Deployment Complete!\"
'"
```

---

## 3. Post-Deployment Verification & Testing

Verify endpoints using curl from the server or terminal:

### 3.1 Verify Robots.txt
```bash
curl -sI https://sikhun.com/robots.txt
# Expected: HTTP/2 200 OK, Content-Type: text/plain
```
Inspect contents:
```bash
curl -s https://sikhun.com/robots.txt | grep -E "OAI-SearchBot|GPTBot|Sitemap"
# Expected output:
# User-agent: OAI-SearchBot
# User-agent: GPTBot
# Sitemap: https://sikhun.com/sitemap.xml
```

### 3.2 Verify Dynamic XML Sitemap
```bash
curl -sI https://sikhun.com/sitemap.xml
# Expected: HTTP/2 200 OK, Content-Type: application/xml; charset=utf-8
```
Verify XML validity and sample links:
```bash
curl -s https://sikhun.com/sitemap.xml | head -n 25
# Expected: <?xml version="1.0" encoding="UTF-8"?>
# <urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"> ...
```

### 3.3 Verify Server-Rendered Raw HTML Meta & JSON-LD
```bash
curl -s https://sikhun.com/ | grep -E "canonical|og:title|application/ld\+json"
# Expected: Raw HTML output contains <link rel="canonical" href="https://sikhun.com/">
# and <script type="application/ld+json">
```

### 3.4 Verify Admission Notice SEO & Canonical
```bash
curl -s https://sikhun.com/admission-notice | grep -E "canonical|og:title|robots"
# Expected: Canonical href="https://sikhun.com/admission-notice"
# Expected: Robots content="index, follow"
```

### 3.5 Verify Private Path Protection (noindex)
```bash
curl -s https://sikhun.com/dashboard | grep "robots"
# Expected: <meta name="robots" content="noindex, nofollow">
```

### 3.6 Verify LLMs.txt
```bash
curl -sI https://sikhun.com/llms.txt
# Expected: HTTP/2 200 OK, Content-Type: text/plain
```

---

## 4. Rollback Plan

If unexpected production issues arise:
```bash
# 1. Rollback local git commit
git revert HEAD --no-edit
git push origin master

# 2. Pull on VPS and clear caches
ssh root@160.25.5.61 "cd /var/www/html && git pull origin master && php artisan optimize:clear && php artisan config:cache && php artisan route:cache && php artisan view:cache && systemctl reload php8.3-fpm"
```

---

## 5. Ongoing Monitoring & External Search Submissions

1. **Google Search Console (GSC):**
   - Submit `https://sikhun.com/sitemap.xml` under Sitemaps.
   - Run URL Inspection on `https://sikhun.com/` and check "Test Live URL" to verify Rich Results detection.
2. **Bing Webmaster Tools:**
   - Submit `https://sikhun.com/sitemap.xml`.
3. **OpenAI / ChatGPT Search:**
   - The updated `robots.txt` policy (`Allow: /` for `OAI-SearchBot`) ensures ChatGPT Search indexers can crawl and index public educational pages.
