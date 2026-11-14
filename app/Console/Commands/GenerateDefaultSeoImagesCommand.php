<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class GenerateDefaultSeoImagesCommand extends Command
{
    protected $signature = 'seo:generate-defaults';
    protected $description = 'Generate high-resolution default OG / SEO images for Admission, Job, and Blog sections.';

    public function handle(): int
    {
        $this->info('Generating default OG images...');

        $images = [
            [
                'filename' => 'og-admission',
                'category' => '🎓 বিশ্ববিদ্যালয় ভর্তি প্রস্তুতি',
                'title' => "বিশ্ববিদ্যালয় ভর্তি বিজ্ঞপ্তি ২০২৬-২০২৭\nবিগত বছরের প্রশ্নব্যাংক ও সমাধান",
                'badges' => [
                    ['label' => '🏛️ কভারেজ', 'value' => 'ঢাবি, রাবি, চবি, বুয়েট, মেডিকেল'],
                    ['label' => '📚 প্রশ্নব্যাংক', 'value' => '১৫,০০০+ সলভড প্রশ্ন'],
                    ['label' => '🎯 প্র্যাকটিস', 'value' => 'ইউনিটভিত্তিক মডেল টেস্ট'],
                    ['label' => '⚡ সমাধান', 'value' => 'ইনস্ট্যান্ট এআই এক্সপ্লেনেশন'],
                ],
                'themeColor' => '#06b6d4',
                'gradientStart' => '#042f2e',
                'gradientEnd' => '#0f172a',
            ],
            [
                'filename' => 'og-job',
                'category' => '💼 সরকারি ও বেসরকারি চাকরির খবর',
                'title' => "সরকারি ও বেসরকারি চাকরির সার্কুলার\nবিসিএস ও ব্যাংক প্রস্তুতি প্রশ্নব্যাংক",
                'badges' => [
                    ['label' => '🏢 কভারেজ', 'value' => 'বিসিএস, ব্যাংক, মন্ত্রণালয়, শিক্ষক'],
                    ['label' => '📋 সার্কুলার', 'value' => 'লাইভ আপডেট ও নোটিফিকেশন'],
                    ['label' => '📖 প্রশ্নব্যাংক', 'value' => 'বিগত ৩৫ বছরের রিয়েল প্রশ্ন'],
                    ['label' => '💡 এআই টুলস', 'value' => 'রিসেন্ট সিলেবাস মডেল টেস্ট'],
                ],
                'themeColor' => '#3b82f6',
                'gradientStart' => '#172554',
                'gradientEnd' => '#0f172a',
            ],
            [
                'filename' => 'og-blog',
                'category' => '📝 শিক্ষামূলক ব্লগ ও ক্যারিয়ার গাইড',
                'title' => "এইচএসসি, ভর্তি ও ক্যারিয়ার গাইডলাইন\nশিক্ষা বিষয়ক বিশ্লেষণ ও স্টাডি নোট",
                'badges' => [
                    ['label' => '✍️ লেখকদল', 'value' => 'শিখুন সম্পাদকীয় ও অভিজ্ঞ শিক্ষক'],
                    ['label' => '🎯 বিষয়বস্তু', 'value' => 'স্টাডি হ্যাকস ও শর্টকাট কৌশল'],
                    ['label' => '🚀 কভারেজ', 'value' => 'HSC, ভর্তি, স্কিল ও ক্যারিয়ার'],
                    ['label' => '✨ ফিচার', 'value' => 'স্মার্ট AI রিডিং ও সারসংক্ষেপ'],
                ],
                'themeColor' => '#8b5cf6',
                'gradientStart' => '#2e1065',
                'gradientEnd' => '#0f172a',
            ],
        ];

        foreach ($images as $img) {
            $this->createCard(
                $img['filename'],
                $img['category'],
                $img['title'],
                $img['badges'],
                $img['themeColor'],
                $img['gradientStart'],
                $img['gradientEnd']
            );
        }

        $this->info('All default SEO OG images generated successfully!');
        return self::SUCCESS;
    }

    private function createCard(
        string $filename,
        string $category,
        string $title,
        array $badges,
        string $themeColor,
        string $gradientStart,
        string $gradientEnd
    ): void {
        $badgeNodes = '';
        foreach (array_slice($badges, 0, 4) as $idx => $b) {
            $col = $idx % 2;
            $row = intdiv($idx, 2);
            $curX = 70 + ($col * (500 + 40));
            $curY = 370 + ($row * 95);

            $label = htmlspecialchars($b['label'] ?? '', ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
            $val = htmlspecialchars($b['value'] ?? '', ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');

            $badgeNodes .= <<<XML
    <g transform="translate({$curX}, {$curY})">
        <rect width="500" height="78" rx="16" fill="rgba(255, 255, 255, 0.05)" stroke="rgba(255, 255, 255, 0.12)" stroke-width="1.5"/>
        <text x="24" y="32" font-family="'Noto Sans Bengali', 'Lohit Bengali', -apple-system, sans-serif" font-size="15" fill="{$themeColor}" font-weight="600">{$label}</text>
        <text x="24" y="60" font-family="'Noto Sans Bengali', 'Lohit Bengali', -apple-system, sans-serif" font-size="19" fill="#f8fafc" font-weight="bold">{$val}</text>
    </g>
XML;
        }

        $titleLines = explode("\n", $title);
        $titleTspans = '';
        $titleY = 210;
        foreach ($titleLines as $line) {
            $safeLine = htmlspecialchars($line, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
            $titleTspans .= "<tspan x=\"70\" y=\"{$titleY}\">{$safeLine}</tspan>\n";
            $titleY += 56;
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
    </defs>

    <rect width="1200" height="630" fill="url(#bgGrad)"/>
    <rect width="1200" height="630" fill="url(#glow)"/>
    <rect x="0" y="0" width="1200" height="6" fill="{$themeColor}"/>

    <g transform="translate(70, 60)">
        <rect width="210" height="46" rx="23" fill="rgba(255, 255, 255, 0.08)" stroke="rgba(255, 255, 255, 0.15)" stroke-width="1"/>
        <circle cx="25" cy="23" r="10" fill="{$themeColor}"/>
        <text x="46" y="29" font-family="'Noto Sans Bengali', 'Lohit Bengali', sans-serif" font-size="18" font-weight="800" fill="#ffffff" letter-spacing="0.5">SIKHUN.COM</text>

        <g transform="translate(230, 0)">
            <rect width="360" height="46" rx="23" fill="{$themeColor}" fill-opacity="0.18" stroke="{$themeColor}" stroke-opacity="0.4" stroke-width="1.2"/>
            <text x="24" y="29" font-family="'Noto Sans Bengali', 'Lohit Bengali', sans-serif" font-size="17" font-weight="700" fill="{$themeColor}">{$category}</text>
        </g>
    </g>

    <text font-family="'Noto Sans Bengali', 'Lohit Bengali', sans-serif" font-size="40" font-weight="800" fill="#ffffff" letter-spacing="-0.5">
        {$titleTspans}
    </text>

    {$badgeNodes}

    <line x1="70" y1="565" x2="1130" y2="565" stroke="rgba(255, 255, 255, 0.12)" stroke-width="1"/>
    <g transform="translate(70, 580)">
        <text x="0" y="15" font-family="'Noto Sans Bengali', 'Lohit Bengali', sans-serif" font-size="16" fill="{$themeColor}" font-weight="700">👉 শিখুন — বাংলাদেশের প্রথম AI স্টাডি ডেস্ক ও প্রশ্নব্যাংক প্ল্যাটফর্ম</text>
        <text x="1060" y="15" text-anchor="end" font-family="'Noto Sans Bengali', 'Lohit Bengali', sans-serif" font-size="14" fill="#94a3b8">sikhun.com</text>
    </g>
</svg>
SVG;

        $tempSvg = public_path("images/{$filename}.svg");
        $targetPng = public_path("images/{$filename}.png");
        file_put_contents($tempSvg, $svg);

        // Convert to PNG via rsvg-convert if available
        $rsvg = is_file('/usr/bin/rsvg-convert') ? '/usr/bin/rsvg-convert' : (trim(@shell_exec('which rsvg-convert 2>/dev/null') ?? ''));
        if (! empty($rsvg)) {
            $cmd = escapeshellarg($rsvg) . ' -w 1200 -h 630 -f png -o ' . escapeshellarg($targetPng) . ' ' . escapeshellarg($tempSvg);
            @exec($cmd);
        }

        // Fallback to Imagick if rsvg-convert is not available
        if (! file_exists($targetPng) && extension_loaded('imagick')) {
            try {
                $im = new \Imagick();
                $im->setBackgroundColor(new \ImagickPixel('transparent'));
                $im->readImageBlob($svg);
                $im->setImageFormat('png24');
                $im->writeImage($targetPng);
                $im->clear();
                $im->destroy();
            } catch (\Throwable $e) {}
        }

        // Fallback to GD if still missing
        if (! file_exists($targetPng)) {
            $img = imagecreatetruecolor(1200, 630);
            $bg = imagecolorallocate($img, 15, 23, 42);
            imagefilledrectangle($img, 0, 0, 1200, 630, $bg);
            imagepng($img, $targetPng);
            imagedestroy($img);
        }

        @unlink($tempSvg);
        $this->line("Created: {$targetPng}");
    }
}
