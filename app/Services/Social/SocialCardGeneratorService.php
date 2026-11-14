<?php

namespace App\Services\Social;

use App\Models\AdmissionNotice;
use App\Models\BlogPost;
use App\Models\Course;
use App\Models\JobCircular;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class SocialCardGeneratorService
{
    protected string $storageDir = 'social-cards';

    public function __construct()
    {
        // Ensure storage directory exists
        if (! Storage::disk('public')->exists($this->storageDir)) {
            Storage::disk('public')->makeDirectory($this->storageDir);
        }
    }

    /**
     * Generate SVG & PNG card for an Admission Notice
     */
    public function generateAdmissionCard(AdmissionNotice $notice): string
    {
        $filename = "admission_{$notice->id}_{$notice->slug}";
        
        $institution = $notice->institution_name ?: 'বিশ্ববিদ্যালয় ভর্তি ২০২৬-২০২৭';
        $deadline = $notice->application_deadline_text ?: ($notice->application_deadline ? $notice->application_deadline->format('d/m/Y') : 'শীঘ্রই শেষ হচ্ছে');
        $fee = $notice->application_fee ?: 'নির্ধারিত ফি';
        $units = is_array($notice->units) ? count($notice->units) . ' টি ইউনিট' : 'সকল ইউনিট';

        $badges = [
            ['label' => '🏛️ প্রতিষ্ঠান', 'value' => $institution],
            ['label' => '⏳ শেষ তারিখ', 'value' => $deadline],
            ['label' => '💰 আবেদন ফি', 'value' => $fee],
            ['label' => '📚 শাখা/ইউনিট', 'value' => $units],
        ];

        return $this->createCard(
            filename: $filename,
            category: '🎓 বিশ্ববিদ্যালয় ভর্তি বিজ্ঞপ্তি',
            title: $notice->title,
            badges: $badges,
            themeColor: '#06b6d4', // Cyan / Teal
            gradientStart: '#042f2e',
            gradientEnd: '#0f172a'
        );
    }

    /**
     * Generate SVG & PNG card for a Job Circular
     */
    public function generateJobCard(JobCircular $job): string
    {
        $filename = "job_{$job->id}_{$job->slug}";

        $deadline = $job->deadline ? $job->deadline->format('d M, Y') : 'চলমান';
        $vacancy = $job->vacancy ? "{$job->vacancy} জন" : 'বিজ্ঞপ্তি দেখুন';
        $salary = $job->salary ?: 'নিয়মানুযায়ী';
        $jobType = $job->job_type ?: 'ফুল-টাইম';

        $badges = [
            ['label' => '🏢 প্রতিষ্ঠান', 'value' => $job->organization ?: 'নিয়োগদাতা'],
            ['label' => '👥 পদসংখ্যা', 'value' => $vacancy],
            ['label' => '⏰ আবেদনের শেষ', 'value' => $deadline],
            ['label' => '💼 চাকরির ধরন', 'value' => $jobType],
        ];

        return $this->createCard(
            filename: $filename,
            category: '💼 সরকারি ও বেসরকারি চাকরির খবর',
            title: $job->title,
            badges: $badges,
            themeColor: '#3b82f6', // Electric Blue
            gradientStart: '#172554',
            gradientEnd: '#0f172a'
        );
    }

    /**
     * Generate SVG & PNG card for a Blog Post
     */
    public function generateBlogCard(BlogPost $post): string
    {
        $filename = "blog_{$post->id}_{$post->slug}";

        $category = $post->category?->name ?: 'শিক্ষা ও পরামর্শ';
        $author = 'শিখুন সম্পাদকীয় দল';
        $readTime = ceil(mb_strlen(strip_tags($post->content ?? '')) / 500) . ' মিনিট পড়ার সময়';

        $badges = [
            ['label' => '🏷️ ক্যাটাগরি', 'value' => $category],
            ['label' => '✍️ লেখক', 'value' => $author],
            ['label' => '📖 সময়', 'value' => $readTime],
        ];

        return $this->createCard(
            filename: $filename,
            category: '📝 শিক্ষামূলক ব্লগ ও ক্যারিয়ার গাইড',
            title: $post->title,
            badges: $badges,
            themeColor: '#8b5cf6', // Violet
            gradientStart: '#2e1065',
            gradientEnd: '#0f172a'
        );
    }

    /**
     * Generate SVG & PNG card for a Course
     */
    public function generateCourseCard(Course $course): string
    {
        $filename = "course_{$course->id}_{$course->slug}";

        $price = $course->price ? '৳' . number_format($course->price) : 'ফ্রি / Free';
        $instructor = $course->mentor?->name ?: 'শিখুন ইন্সট্রাক্টর';
        $level = $course->level ?: 'সকল লেভেল';
        $lessons = ($course->lessons_count ?? ($course->relationLoaded('sections') ? $course->sections->flatMap->lessons->count() : 0)) . ' টি লেসন';

        $badges = [
            ['label' => '👨‍🏫 ইন্সট্রাক্টর', 'value' => $instructor],
            ['label' => '💎 কোর্স ফি', 'value' => $price],
            ['label' => '📊 লেভেল', 'value' => $level],
            ['label' => '🎯 কনটেন্ট', 'value' => $lessons],
        ];

        return $this->createCard(
            filename: $filename,
            category: '🚀 অনলাইন স্কিল ও একাডেমি কোর্স',
            title: $course->title,
            badges: $badges,
            themeColor: '#f97316', // Orange
            gradientStart: '#431407',
            gradientEnd: '#0f172a'
        );
    }

    /**
     * Create modern SVG and convert to PNG for social platforms
     */
    protected function createCard(
        string $filename,
        string $category,
        string $title,
        array $badges,
        string $themeColor,
        string $gradientStart,
        string $gradientEnd
    ): string {
        $sanitizedTitle = e(mb_substr(trim(strip_tags($title)), 0, 90));
        // Word wrap title into lines
        $titleLines = $this->wrapText($sanitizedTitle, 38, 3);

        $badgeNodes = '';
        $bx = 70;
        $by = 380;
        $boxW = 250;
        $boxH = 82;
        $gap = 20;

        foreach (array_slice($badges, 0, 4) as $idx => $b) {
            $col = $idx % 2;
            $row = intdiv($idx, 2);
            $curX = 70 + ($col * (500 + 40));
            $curY = 370 + ($row * 95);

            $label = e($b['label'] ?? '');
            $val = e(mb_substr($b['value'] ?? '', 0, 32));

            $badgeNodes .= <<<XML
    <g transform="translate({$curX}, {$curY})">
        <rect width="500" height="78" rx="16" fill="rgba(255, 255, 255, 0.05)" stroke="rgba(255, 255, 255, 0.12)" stroke-width="1.5"/>
        <text x="24" y="32" font-family="'Noto Sans Bengali', 'Lohit Bengali', 'FreeSans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif" font-size="15" fill="{$themeColor}" font-weight="600">{$label}</text>
        <text x="24" y="60" font-family="'Noto Sans Bengali', 'Lohit Bengali', 'FreeSans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif" font-size="19" fill="#f8fafc" font-weight="bold">{$val}</text>
    </g>
XML;
        }

        $titleTspans = '';
        $titleY = 210;
        foreach ($titleLines as $line) {
            $safeLine = e($line);
            $titleTspans .= "<tspan x=\"70\" y=\"{$titleY}\">{$safeLine}</tspan>\n";
            $titleY += 52;
        }

        $svg = <<<SVG
<svg width="1200" height="630" viewBox="0 0 1200 630" xmlns="http://www.w3.org/2000/svg">
    <defs>
        <linearGradient id="bgGrad" x1="0%" y1="0%" x2="100%" y2="100%">
            <stop offset="0%" stop-color="{$gradientStart}"/>
            <stop offset="50%" stop-color="#090d16"/>
            <stop offset="100%" stop-color="{$gradientEnd}"/>
        </linearGradient>
        <radialGradient id="glow" cx="85%" cy="15%" r="60%">
            <stop offset="0%" stop-color="{$themeColor}" stop-opacity="0.25"/>
            <stop offset="100%" stop-color="{$themeColor}" stop-opacity="0"/>
        </radialGradient>
        <filter id="cardShadow" x="-10%" y="-10%" width="120%" height="120%">
            <feDropShadow dx="0" dy="12" stdDeviation="16" flood-color="#000" flood-opacity="0.45"/>
        </filter>
    </defs>

    <!-- Background -->
    <rect width="1200" height="630" fill="url(#bgGrad)"/>
    <rect width="1200" height="630" fill="url(#glow)"/>

    <!-- Decorative Top Bar -->
    <rect x="0" y="0" width="1200" height="6" fill="{$themeColor}"/>

    <!-- Header Section -->
    <g transform="translate(70, 60)">
        <!-- Brand Logo Badge -->
        <rect width="210" height="46" rx="23" fill="rgba(255, 255, 255, 0.08)" stroke="rgba(255, 255, 255, 0.15)" stroke-width="1"/>
        <circle cx="25" cy="23" r="10" fill="{$themeColor}"/>
        <text x="46" y="29" font-family="'Noto Sans Bengali', 'Lohit Bengali', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif" font-size="18" font-weight="800" fill="#ffffff" letter-spacing="0.5">SIKHUN.COM</text>

        <!-- Category Badge -->
        <g transform="translate(230, 0)">
            <rect width="360" height="46" rx="23" fill="{$themeColor}" fill-opacity="0.18" stroke="{$themeColor}" stroke-opacity="0.4" stroke-width="1.2"/>
            <text x="24" y="29" font-family="'Noto Sans Bengali', 'Lohit Bengali', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif" font-size="17" font-weight="700" fill="{$themeColor}">{$category}</text>
        </g>
    </g>

    <!-- Title Section -->
    <text font-family="'Noto Sans Bengali', 'Lohit Bengali', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif" font-size="40" font-weight="800" fill="#ffffff" letter-spacing="-0.5">
        {$titleTspans}
    </text>

    <!-- Highlights Badges Grid -->
    {$badgeNodes}

    <!-- Footer Bar -->
    <line x1="70" y1="565" x2="1130" y2="565" stroke="rgba(255, 255, 255, 0.12)" stroke-width="1"/>
    <g transform="translate(70, 580)">
        <text x="0" y="15" font-family="'Noto Sans Bengali', 'Lohit Bengali', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif" font-size="16" fill="{$themeColor}" font-weight="700">👉 বিস্তারিত তথ্য ও অনলাইন লিংক জানতে প্রথম কমেন্ট চেক করুন</text>
        <text x="1060" y="15" text-anchor="end" font-family="'Noto Sans Bengali', 'Lohit Bengali', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif" font-size="14" fill="#94a3b8">sikhun.com • AI Learning Platform</text>
    </g>
</svg>
SVG;

        $safeSlug = preg_replace('/[^A-Za-z0-9_\-]/', '_', substr(md5($filename), 0, 10));
        $cleanFilename = "{$filename}_{$safeSlug}";

        $svgRelPath = "{$this->storageDir}/{$cleanFilename}.svg";
        $pngRelPath = "{$this->storageDir}/{$cleanFilename}.png";

        // Save SVG to public storage
        Storage::disk('public')->put($svgRelPath, $svg);

        // Convert to PNG via rsvg-convert (best quality typography), Imagick, or GD with TTF
        $this->convertSvgToPng($svg, $svgRelPath, $pngRelPath, $sanitizedTitle, $category, $themeColor);

        // If PNG exists, return PNG url (since Facebook API requires JPG/PNG for photo posts)
        if (Storage::disk('public')->exists($pngRelPath)) {
            return asset('storage/' . $pngRelPath);
        }

        return asset('storage/' . $svgRelPath);
    }

    /**
     * Convert SVG to PNG file preserving Bengali typography.
     */
    protected function convertSvgToPng(
        string $svg,
        string $svgRelPath,
        string $pngRelPath,
        string $title,
        string $category,
        string $themeColor
    ): void {
        $pngFullPath = Storage::disk('public')->path($pngRelPath);
        $svgFullPath = Storage::disk('public')->path($svgRelPath);

        // Method 1: rsvg-convert (highest fidelity vector rendering with native Bengali fonts)
        $rsvgBinary = is_file('/usr/bin/rsvg-convert')
            ? '/usr/bin/rsvg-convert'
            : (trim(@shell_exec('which rsvg-convert 2>/dev/null') ?? ''));

        if (! empty($rsvgBinary)) {
            try {
                $cmd = escapeshellarg($rsvgBinary) . ' -w 1200 -h 630 -f png -o ' . escapeshellarg($pngFullPath) . ' ' . escapeshellarg($svgFullPath);
                @exec($cmd, $out, $ret);
                if ($ret === 0 && file_exists($pngFullPath) && filesize($pngFullPath) > 1000) {
                    return;
                }
            } catch (\Throwable $e) {
                Log::warning('SocialCard: rsvg-convert failed: ' . $e->getMessage());
            }
        }

        // Method 2: Imagick (if SVG delegate configured)
        if (extension_loaded('imagick')) {
            try {
                $im = new \Imagick();
                $im->setBackgroundColor(new \ImagickPixel('transparent'));
                $im->readImageBlob($svg);
                $im->setImageFormat('png24');
                $im->writeImage($pngFullPath);
                $im->clear();
                $im->destroy();
                if (file_exists($pngFullPath) && filesize($pngFullPath) > 1000) {
                    return;
                }
            } catch (\Throwable $e) {
                Log::warning('SocialCard: Imagick SVG to PNG failed, falling back to GD: ' . $e->getMessage());
            }
        }

        // Method 3: GD Canvas render with TrueType Bengali Font
        try {
            $img = imagecreatetruecolor(1200, 630);
            $bg = imagecolorallocate($img, 15, 23, 42); // slate-900
            $textWhite = imagecolorallocate($img, 255, 255, 255);
            $textMuted = imagecolorallocate($img, 148, 163, 184);

            // Parse theme color hex
            $r = hexdec(substr($themeColor, 1, 2));
            $g = hexdec(substr($themeColor, 3, 2));
            $b = hexdec(substr($themeColor, 5, 2));
            $accent = imagecolorallocate($img, $r, $g, $b);

            imagefilledrectangle($img, 0, 0, 1200, 630, $bg);
            imagefilledrectangle($img, 0, 0, 1200, 6, $accent);

            // Find an installed Bengali TTF font
            $fontCandidates = [
                '/usr/share/fonts/truetype/noto/NotoSansBengali-Bold.ttf',
                '/usr/share/fonts/truetype/lohit-bengali/Lohit-Bengali.ttf',
                '/usr/share/fonts/truetype/fonts-beng-extra/Mukti.ttf',
                'C:/Windows/Fonts/vrindab.ttf',
                'C:/Windows/Fonts/kalpurush.ttf',
                'C:/Windows/Fonts/arial.ttf',
            ];

            $ttfFont = null;
            foreach ($fontCandidates as $font) {
                if (file_exists($font)) {
                    $ttfFont = $font;
                    break;
                }
            }

            if ($ttfFont && function_exists('imagettftext')) {
                // Header badge
                imagettftext($img, 18, 0, 70, 75, $accent, $ttfFont, 'SIKHUN.COM | ' . $category);
                // Title lines
                $wrappedLines = $this->wrapText($title, 34, 3);
                $y = 190;
                foreach ($wrappedLines as $line) {
                    imagettftext($img, 30, 0, 70, $y, $textWhite, $ttfFont, $line);
                    $y += 58;
                }
                // Footer
                imagettftext($img, 16, 0, 70, 580, $textMuted, $ttfFont, 'sikhun.com • বিস্তারিত তথ্য ও অনলাইন লিংক জানতে প্রথম কমেন্ট চেক করুন');
            } else {
                // Last-resort fallback without UTF-8 breaking
                imagestring($img, 5, 70, 50, "SIKHUN.COM", $accent);
                imagestring($img, 5, 70, 160, "Sikhun.com Learning Platform", $textWhite);
                imagestring($img, 4, 70, 580, "Visit sikhun.com - Details in First Comment", $textMuted);
            }

            imagepng($img, $pngFullPath, 6);
            imagedestroy($img);
        } catch (\Throwable $e) {
            Log::warning('SocialCard: GD fallback failed: ' . $e->getMessage());
        }
    }

    /**
     * Wrap unicode string into lines of maximum length
     */
    protected function wrapText(string $text, int $maxChars = 40, int $maxLines = 3): array
    {
        $words = preg_split('/\s+/u', $text, -1, PREG_SPLIT_NO_EMPTY);
        $lines = [];
        $current = '';

        foreach ($words as $word) {
            if (mb_strlen($current . ' ' . $word) <= $maxChars) {
                $current = empty($current) ? $word : ($current . ' ' . $word);
            } else {
                if (! empty($current)) {
                    $lines[] = $current;
                }
                $current = $word;
                if (count($lines) >= $maxLines - 1) {
                    break;
                }
            }
        }

        if (! empty($current) && count($lines) < $maxLines) {
            $lines[] = $current;
        }

        return $lines;
    }
}
