# Sikhun — লোকাল সেটআপ (Windows)

এই গাইডটি Windows ১১ + Laragon-এ যাচাই করা। Linux/macOS-এ শুধু ১ম ধাপের `--ignore-platform-req` দুটো বাদ দিলেই হবে।

## যা দরকার

| সফটওয়্যার | ভার্সন | নোট |
|---|---|---|
| PHP | 8.2+ | `pdo_mysql`, `mbstring`, `gd`, `intl`, `zip`, `fileinfo`, `redis` এক্সটেনশন |
| Composer | 2.x | |
| MySQL | 8.x | ডেটাবেস নাম: `sikhun` |
| Redis | 5+ | cache, queue, session |
| Node.js | 20+ | |
| Meilisearch | ঐচ্ছিক | না থাকলে `SCOUT_DRIVER=collection` |

## ধাপে ধাপে

```powershell
git clone https://github.com/mokaddes/Sikhun.git
cd Sikhun
```

### ১. PHP ডিপেন্ডেন্সি

Laravel Horizon-এর `ext-pcntl` ও `ext-posix` Windows-এ নেই। তাই:

```powershell
composer config policy.advisories.block false
composer install --ignore-platform-req=ext-pcntl --ignore-platform-req=ext-posix
git checkout -- composer.json
```

> `policy.advisories.block false` শুধু Laravel 11-এর সিকিউরিটি অ্যাডভাইজরির কারণে ইনস্টল আটকাতে না দেওয়ার জন্য। প্রোডাকশনে Laravel 12-এ আপগ্রেড করুন।

### ২. JS ডিপেন্ডেন্সি

```powershell
npm install
npm install-scripts approve esbuild vue-demi
```

### ৩. `.env`

```powershell
Copy-Item .env.example .env
php artisan key:generate
```

Windows-এ Horizon/Reverb ছাড়া চালাতে `.env`-এ এগুলো বদলান:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=sikhun
DB_USERNAME=root
DB_PASSWORD=

SESSION_DRIVER=redis
CACHE_STORE=redis
QUEUE_CONNECTION=redis
BROADCAST_CONNECTION=reverb
```

> Redis না থাকলে: `SESSION_DRIVER=file`, `CACHE_STORE=file`, `QUEUE_CONNECTION=sync`, `BROADCAST_CONNECTION=log`।

### ৪. ডেটাবেস

```powershell
mysql -uroot -e "CREATE DATABASE IF NOT EXISTS sikhun CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
php artisan migrate:fresh --seed
php artisan storage:link
```

### ৫. ফ্রন্টএন্ড ও সার্ভার

```powershell
npm run build      # অথবা ডেভেলপমেন্টে: npm run dev
php artisan serve
```

খুলুন: `http://127.0.0.1:8000`

## ডিফল্ট লগইন

| ধরন | URL | ইমেইল | পাসওয়ার্ড |
|---|---|---|---|
| স্টুডেন্ট | `/login` | `student1@sikhun.com` | `student123` |
| অ্যাডমিন | `/admin/login` | `admin@sikhun.com` | `admin123` |

## AI ফিচার

AI চ্যাট/পরীক্ষা/ফ্ল্যাশকার্ড কাজ করতে `.env`-এ অন্তত একটি কী দিন:

```env
OPENAI_API_KEY=sk-...
# অথবা
GEMINI_API_KEY=...
GROQ_API_KEY=...
DEEPSEEK_API_KEY=...
```

এরপর অ্যাডমিন প্যানেল → AI Providers → **Test Connection** চেপে যাচাই করুন।

## জানা সীমাবদ্ধতা (লোকাল)

- **`php artisan test` ফেইল করে** — `2026_10_17_183941_change_content_columns_to_longtext_in_book_tables` মাইগ্রেশন MySQL-only SQL ব্যবহার করে, যা SQLite টেস্ট ডেটাবেসে চলে না। ঠিক করার উপায়: `DB::getDriverName() === 'mysql'` গার্ড যোগ করুন।
- **WebSocket এরর** — Reverb না চালালে কনসোলে `ws://localhost:8080` এরর দেখাবে। `php artisan reverb:start` চালান, বা `BROADCAST_CONNECTION=log` রাখুন।
- **Horizon** — Windows-এ চলে না। `QUEUE_CONNECTION=sync` ব্যবহার করুন, অথবা WSL/Linux-এ চালান।
- **Meilisearch** — না থাকলে `SCOUT_DRIVER=collection`।
- **সিড ডেটা** — ২৩টি বই (সবগুলো ফ্রি, PDF ছাড়া), ২টি কোর্স, ৩টি প্ল্যান, ০টি ব্লগ পোস্ট।
- **`public/build` কমিট করা** — তাই `npm run build` চালালে অনেক ফাইল পরিবর্তিত দেখাবে; এটি স্বাভাবিক।
