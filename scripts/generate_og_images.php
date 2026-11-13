<?php

$fontCandidates = [
    'C:/Windows/Fonts/segoeui.ttf',
    'C:/Windows/Fonts/arial.ttf',
    'C:/Windows/Fonts/tahoma.ttf',
];
$font = null;
foreach ($fontCandidates as $cand) {
    if (file_exists($cand)) {
        $font = $cand;
        break;
    }
}

function createBaseCard($title, $sub1, $sub2, $badge, $filename, $font) {
    $width = 1200;
    $height = 630;
    $img = imagecreatetruecolor($width, $height);
    imagealphablending($img, true);
    imagesavealpha($img, true);

    $bg = imagecolorallocate($img, 11, 15, 25);
    imagefill($img, 0, 0, $bg);

    for ($r = 350; $r > 0; $r -= 10) {
        $ratio = $r / 350;
        $alpha = (int)(127 - (20 * (1 - $ratio)));
        $cyan = imagecolorallocatealpha($img, 0, 242, 254, min(127, $alpha));
        imagefilledellipse($img, 180, 200, (int)($r * 2.2), (int)($r * 1.6), $cyan);

        $orange = imagecolorallocatealpha($img, 249, 115, 22, min(127, $alpha));
        imagefilledellipse($img, 1020, 450, (int)($r * 2.2), (int)($r * 1.6), $orange);
    }

    $gridColor = imagecolorallocatealpha($img, 56, 189, 248, 120);
    for ($x = 100; $x < 1200; $x += 120) imageline($img, $x, 0, $x, 630, $gridColor);
    for ($y = 80; $y < 630; $y += 90) imageline($img, 0, $y, 1200, $y, $gridColor);

    $frameBg = imagecolorallocatealpha($img, 15, 23, 42, 45);
    $frameBorder = imagecolorallocatealpha($img, 56, 189, 248, 80);
    imagefilledrectangle($img, 70, 60, 1130, 570, $frameBg);
    imagerectangle($img, 70, 60, 1130, 570, $frameBorder);

    $bookCyan = imagecolorallocate($img, 0, 242, 254);
    $bookAmber = imagecolorallocate($img, 249, 115, 22);
    $white = imagecolorallocate($img, 255, 255, 255);
    $textMuted = imagecolorallocate($img, 148, 163, 184);
    $accentCyan = imagecolorallocate($img, 56, 189, 248);

    imagefilledpolygon($img, [120, 170, 180, 195, 180, 280, 120, 255], $bookCyan);
    imagefilledpolygon($img, [180, 195, 240, 170, 240, 255, 180, 280], $bookAmber);
    imagepolygon($img, [120, 170, 180, 195, 240, 170, 240, 255, 180, 280, 120, 255], $white);
    imageline($img, 180, 195, 180, 280, $white);
    imagefilledellipse($img, 180, 140, 16, 16, $bookAmber);
    imagefilledellipse($img, 180, 140, 8, 8, $white);

    if ($font) {
        imagettftext($img, 46, 0, 275, 225, $white, $font, 'Sikhun');
        imagettftext($img, 46, 0, 440, 225, $bookAmber, $font, '.');
        imagettftext($img, 38, 0, 455, 225, $accentCyan, $font, 'com');

        imagettftext($img, 15, 0, 280, 270, $accentCyan, $font, $badge);
        imagettftext($img, 34, 0, 120, 360, $white, $font, $title);
        imagettftext($img, 20, 0, 120, 415, $textMuted, $font, $sub1);
        imagettftext($img, 20, 0, 120, 455, $textMuted, $font, $sub2);

        imagettftext($img, 18, 0, 120, 535, $accentCyan, $font, 'https://sikhun.com');
        imagettftext($img, 16, 0, 800, 535, $textMuted, $font, 'Bangladesh Leading AI Study Hub');
    }

    imagepng($img, $filename);
    imagedestroy($img);
    echo "Generated {$filename} (" . filesize($filename) . " bytes)\n";
}

createBaseCard(
    'Admission Circulars & Past Paper Practice 2026',
    'DU, RU, BUET, GST Cluster & Medical Admission Notices',
    '20,000+ Verified MCQ Question Bank with AI Explanations',
    '★ ADMISSION PREPARATION & EXAMS',
    __DIR__ . '/../public/brand-assets/og-image-1-admission-prep.png',
    $font
);

createBaseCard(
    'Interactive AI Academic Book Reader & Tutor',
    'Chat directly with textbook chapters & get instant summaries',
    'HSC ICT, Science & University Level Study Assistance',
    '⚡ GENERATIVE AI STUDY PLATFORM',
    __DIR__ . '/../public/brand-assets/og-image-2-ai-platform.png',
    $font
);

createBaseCard(
    'Sikhun.com — AI-Powered Learning in Bangladesh',
    'Smart Book Reader • Real-time MCQ Practice • AI Explainer',
    'Targeted Preparation for Academic, Admission & BCS Exams',
    '🌐 OFFICIAL BRAND SHOWCASE',
    __DIR__ . '/../public/brand-assets/og-image-3-brand-minimal.png',
    $font
);
