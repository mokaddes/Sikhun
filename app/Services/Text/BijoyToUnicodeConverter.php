<?php

namespace App\Services\Text;

use DOMDocument;
use DOMElement;
use DOMNode;
use DOMXPath;
use ZipArchive;

class BijoyToUnicodeConverter
{
    private const BIJOY_FONTS = [
        'SutonnyMJ',
        'Sutonny',
        'SutonnyIIC',
        'SutonnyOMJ',
        'SabrenaTonnyMJ',
        'KongshoMJ',
        'Rod',
        'NikoshBan',
        'Bijoy',
        'Boishakhi',
    ];

    /**
     * Option-bullet legacy fonts where K/L/M/N represent A/B/C/D and
     * P/Q/R/S represent highlighted (correct answer) A/B/C/D.
     */
    private const OPTION_MARKER_FONTS = [
        'SulSym',
        'ProshnaP',
        'KarnaphuliP',
        'SushreeP',
    ];

    /**
     * Convert a Bijoy Classic ANSI string to Bengali Unicode.
     */
    public function convert(string $text): string
    {
        if ($text === '') {
            return '';
        }

        $map = [
            'Av' => 'আ',
            'cÖ' => 'প্র',
            '¤^' => 'ম্ব',
            '¤¢' => 'ম্ভ',
            '¤ú' => 'ম্প',
            '¤§' => 'ম্ম',
            '¤œ' => 'ম্ন',
            'š—' => 'ন্ত',
            'šÍ' => 'ন্ত',
            'š‘' => 'ন্তু',
            'š’' => 'ন্থ',
            '›`' => 'ন্দ',
            '›a' => 'ন্ধ',
            '›U' => 'ন্ট',
            '›W' => 'ন্ড',
            '¯’' => 'স্থ',
            '¯‹' => 'স্ক',
            'K¬' => 'ক্ল',
            'M¬' => 'গ্ল',
            'c¬' => 'প্ল',
            'e¬' => 'ব্ল',
            'm¬' => 'স্ল',
            'j¬' => 'ল্ল',
            'g½' => 'মঙ্গ',

            'A' => 'অ', 'B' => 'ই', 'C' => 'ঈ', 'D' => 'উ', 'E' => 'ঊ',
            'F' => 'ঋ', 'G' => 'এ', 'H' => 'ঐ', 'I' => 'ও', 'J' => 'ঔ',

            'K' => 'ক', 'L' => 'খ', 'M' => 'গ', 'N' => 'ঘ', 'O' => 'ঙ',
            'P' => 'চ', 'Q' => 'ছ', 'R' => 'জ', 'S' => 'ঝ', 'T' => 'ঞ',
            'U' => 'ট', 'V' => 'ঠ', 'W' => 'ড', 'X' => 'ঢ', 'Y' => 'ণ',
            'Z' => 'ত', '_' => 'থ', '`' => 'দ', 'a' => 'ধ', 'b' => 'ন',
            'c' => 'প', 'd' => 'ফ', 'e' => 'ব', 'f' => 'ভ', 'g' => 'ম',
            'h' => 'য', 'i' => 'র', 'j' => 'ল', 'k' => 'শ', 'l' => 'ষ',
            'm' => 'স', 'n' => 'হ', 'o' => 'ড়', 'p' => 'ঢ়', 'q' => 'য়',
            'r' => 'ৎ', 's' => 'ং', 't' => 'ঃ', 'u' => 'ঁ',

            // Vowel signs & hasant
            'v' => 'া',
            'w' => 'ি',
            'x' => 'ী',
            'y' => 'ু',
            'z' => 'ু',
            '~' => 'ূ',
            '‚' => 'ূ',
            '„' => 'ৃ',
            '…' => 'ৃ',
            '†' => 'ে',
            '‡' => 'ে',
            'ˆ' => 'ৈ',
            '‰' => 'ৈ',
            'Š' => 'ৗ',
            '&' => '্',

            // Digits
            '0' => '০', '1' => '১', '2' => '২', '3' => '৩', '4' => '৪',
            '5' => '৫', '6' => '৬', '7' => '৭', '8' => '৮', '9' => '৯',

            // Punctuation & diacritics
            '|' => '।',
            'Ñ' => '—',
            'Ò' => '“',
            'Ó' => '”',
            'Ô' => '‘',
            'Õ' => '’',
            'Ö' => '্র',
            'ª' => '্র',
            '«' => '্র',
            '¨' => '্য',
            '©' => 'র্',
            '®' => 'ষ্',

            // Single-codepoint Bijoy conjuncts & symbols
            '¡' => '্ব',
            '¢' => '্ভ',
            '£' => 'ক্ষ্ম',
            '¤' => 'ম্ম',
            '¥' => 'ম্ফ',
            '¦' => 'ল্ল',
            '§' => 'ম্ম',
            '¬' => '্ল',
            '­' => '্ল',
            '¯' => 'স্ত',
            '°' => 'ক্ক',
            '±' => 'ক্ট',
            '²' => 'ক্ত',
            '³' => 'ক্ত',
            '´' => 'ক্ষ',
            'µ' => 'ক্র',
            '¶' => 'ক্ষ',
            '·' => 'ক্স',
            '¸' => 'গ্ধ',
            '¹' => 'ঙ্ক',
            'º' => 'ঙ্গ',
            '»' => 'জ্জ',
            '¼' => 'ঙ্ক',
            '½' => 'ঙ্গ',
            '¾' => 'জ্জ',
            '¿' => 'ত্র',
            'À' => 'ঞ্চ',
            'Á' => 'জ্ঞ',
            'Â' => 'ঞ্জ',
            'Ã' => 'ণ্ঠ',
            'Ä' => 'ণ্ড',
            'Å' => 'ত্ত',
            'Æ' => 'ট্ট',
            'Ç' => 'দ্দ',
            'È' => 'ণ্ট',
            'É' => 'প্প',
            'Ê' => 'ন্ড',
            'Ë' => 'ত্ত',
            'Ì' => 'ম্ম',
            'Í' => 'ম্প',
            'Î' => 'ত্র',
            'Ï' => 'ল্ল',
            'Ð' => 'শ্ম',
            '×' => 'দ্ধ',
            'Ø' => 'দ্ব',
            'Ù' => 'ন্স',
            'Ú' => 'ন্ড',
            'Û' => 'ন্ধ',
            'Ü' => 'ন্ধ',
            'Ý' => 'ভ্র',
            'Þ' => 'ল্ক',
            'ß' => 'প্ল',
            'à' => 'শ্চ',
            'á' => 'শ্ন',
            'â' => 'শ্ব',
            'ã' => 'ষ্ক',
            'ä' => 'ষ্ট',
            'å' => 'ষ্ঠ',
            'æ' => 'ষ্ণ',
            'ç' => 'ষ্প',
            'è' => 'স্ফ',
            'é' => 'স্ক',
            'ê' => 'স্ট',
            'ë' => 'স্ত',
            'ì' => 'স্থ',
            'í' => 'ল্প',
            'î' => 'স্প',
            'ï' => 'স্ব',
            'ð' => 'হ্ম',
            'ñ' => 'হ্ন',
            'ò' => 'হ্ব',
            'ó' => 'ষ্ট',
            'ô' => 'ষ্ঠ',
            'õ' => 'গু',
            'ö' => 'শু',
            '÷' => 'হৃ',
            'ø' => '্ল',
            'ù' => 'স্ত',
            'ú' => '্প',
            'û' => 'হু',
            'ü' => 'ৃ',
            'ý' => 'ু',
            'þ' => '্র',
            'ÿ' => 'ক্ষ',

            // Windows-1252 smart chars seen in Bijoy docs
            'Œ' => '্র',
            'œ' => '্ন',
            'š' => 'ন্',
            '›' => 'ন্',
            'ž' => '্ম',
            'Ÿ' => 'য়',
            'ƒ' => '্র',
            '‹' => 'ক্স',
            '—' => '্ত',
            '˜' => '্',
            '^' => '্ব',
            'ū' => '×',
        ];

        $out = strtr($text, $map);

        // Fix kar reordering across conjunct clusters
        $cluster = '(?:[ক-হড়ঢ়য়](?:্[ক-হড়ঢ়য়যরবমলন])*)';
        $out = preg_replace("/([িেৈ])({$cluster})/u", '$2$1', $out) ?? $out;

        // Fix reph (র্) reordering before preceding cluster
        $out = preg_replace("/({$cluster}[ািীুূৃেৈোৌ]?)র্/u", 'র্$1', $out) ?? $out;

        // Combine split vowel kar parts
        $out = str_replace(['ো', 'ৌ', '্্'], ['ো', 'ৌ', '্'], $out);

        return $out;
    }

    /**
     * Extract converted UTF-8 Bengali lines & tables from a DOCX file in document order.
     * Preserves MCQ option markers (`ক) `, `[ANS]ক) `, etc.) when legacy bullet fonts
     * (SulSym, ProshnaP, KarnaphuliP) are used.
     *
     * @return array<int, string>
     */
    public function extractDocxLines(string $docxPath, bool $preserveCorrectTag = true): array
    {
        $zip = new ZipArchive();
        if ($zip->open($docxPath) !== true) {
            throw new \RuntimeException("Unable to open DOCX archive: {$docxPath}");
        }

        $xmlStr = $zip->getFromName('word/document.xml');
        $zip->close();

        if ($xmlStr === false) {
            return [];
        }

        $dom = new DOMDocument();
        $loaded = @$dom->loadXML($xmlStr);
        if (! $loaded) {
            return [];
        }

        $xpath = new DOMXPath($dom);
        $xpath->registerNamespace('w', 'http://schemas.openxmlformats.org/wordprocessingml/2006/main');

        $body = $xpath->query('/w:document/w:body')->item(0);
        if (! $body) {
            return [];
        }

        $lines = [];
        foreach ($body->childNodes as $child) {
            if ($child->nodeName === 'w:p') {
                $txt = $this->convertParagraph($child, $xpath, $preserveCorrectTag);
                if ($txt !== '') {
                    $lines[] = $txt;
                }
            } elseif ($child->nodeName === 'w:tbl') {
                foreach ($xpath->query('./w:tr', $child) as $tr) {
                    $cells = [];
                    foreach ($xpath->query('./w:tc', $tr) as $tc) {
                        $cellParts = [];
                        foreach ($xpath->query('.//w:p', $tc) as $cp) {
                            $ct = $this->convertParagraph($cp, $xpath, $preserveCorrectTag);
                            if ($ct !== '') {
                                $cellParts[] = $ct;
                            }
                        }
                        $cells[] = implode(' ', $cellParts);
                    }
                    $joined = trim(implode(' | ', $cells));
                    if (trim(str_replace('|', '', $joined)) !== '') {
                        $lines[] = $joined;
                    }
                }
            }
        }

        return $lines;
    }

    private function convertParagraph(DOMNode $p, DOMXPath $xpath, bool $preserveCorrectTag): string
    {
        $runs = $xpath->query('.//w:r', $p);
        $segments = [];

        foreach ($runs as $r) {
            $fontNode = $xpath->query('.//w:rPr/w:rFonts', $r)->item(0);
            $font = '';
            if ($fontNode instanceof DOMElement) {
                $font = $fontNode->getAttribute('w:ascii')
                    ?: $fontNode->getAttribute('w:hAnsi')
                    ?: $fontNode->getAttribute('w:cs');
            }

            $tNodes = $xpath->query('.//w:t', $r);
            $chunk = '';
            foreach ($tNodes as $t) {
                $chunk .= $t->textContent;
            }
            if ($chunk === '') {
                continue;
            }

            // Handle SulSym / ProshnaP MCQ option letter markers cleanly
            if (in_array($font, self::OPTION_MARKER_FONTS, true)) {
                $mappedMarker = $this->mapOptionMarker($chunk, $preserveCorrectTag);
                $segments[] = ['type' => 'literal', 'text' => $mappedMarker];
                continue;
            }

            $isBijoy = in_array($font, self::BIJOY_FONTS, true)
                || preg_match('/[†‡ˆ‰©¶ÁÖª«¨¤ÆÈ³œš›û×Ø]/u', $chunk) === 1;

            $segType = $isBijoy ? 'bijoy' : 'literal';
            $lastIdx = count($segments) - 1;
            if ($lastIdx >= 0 && $segments[$lastIdx]['type'] === $segType) {
                $segments[$lastIdx]['text'] .= $chunk;
            } else {
                $segments[] = ['type' => $segType, 'text' => $chunk];
            }
        }

        $line = '';
        foreach ($segments as $seg) {
            $line .= $seg['type'] === 'bijoy' ? $this->convert($seg['text']) : $seg['text'];
        }

        $line = str_replace("\u{00A0}", ' ', $line);

        return trim((string) preg_replace('/\s+/u', ' ', $line));
    }

    private function mapOptionMarker(string $raw, bool $preserveCorrectTag): string
    {
        $out = '';
        $len = mb_strlen($raw, 'UTF-8');
        for ($i = 0; $i < $len; $i++) {
            $ch = mb_substr($raw, $i, 1, 'UTF-8');
            $out .= match ($ch) {
                'K' => ' ক) ',
                'L' => ' খ) ',
                'M' => ' গ) ',
                'N' => ' ঘ) ',
                'P' => ($preserveCorrectTag ? ' [ANS]ক) ' : ' ক) '),
                'Q' => ($preserveCorrectTag ? ' [ANS]খ) ' : ' খ) '),
                'R' => ($preserveCorrectTag ? ' [ANS]গ) ' : ' গ) '),
                'S' => ($preserveCorrectTag ? ' [ANS]ঘ) ' : ' ঘ) '),
                default => $ch,
            };
        }

        return $out;
    }
}
