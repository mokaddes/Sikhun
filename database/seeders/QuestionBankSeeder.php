<?php

namespace Database\Seeders;

use App\Models\QuestionBank;
use App\Models\QuestionSource;
use App\Models\QuestionSubject;
use App\Models\QuestionTopic;
use Illuminate\Database\Seeder;

class QuestionBankSeeder extends Seeder
{
    public function run(): void
    {
        $duSource = QuestionSource::where('slug', 'university-of-dhaka-du')->first();
        $buetSource = QuestionSource::where('slug', 'buet-undergraduate-admission')->first();
        $dgmeSource = QuestionSource::where('slug', 'dgme-medical-education-bangladesh')->first();
        $bpscSource = QuestionSource::where('slug', 'bpsc-gov-bd-official')->first();
        $bbSource = QuestionSource::where('slug', 'bangladesh-bank-erecruitment')->first();

        $physics = QuestionSubject::where('slug', 'physics')->first();
        $chemistry = QuestionSubject::where('slug', 'chemistry')->first();
        $higherMath = QuestionSubject::where('slug', 'higher-math')->first();
        $biology = QuestionSubject::where('slug', 'biology')->first();
        $bangla = QuestionSubject::where('slug', 'bangla')->first();
        $english = QuestionSubject::where('slug', 'english')->first();
        $mathReasoning = QuestionSubject::where('slug', 'math-reasoning')->first();
        $bdAffairs = QuestionSubject::where('slug', 'bangladesh-affairs')->first();
        $intlAffairs = QuestionSubject::where('slug', 'international-affairs')->first();
        $ict = QuestionSubject::where('slug', 'computer-ict')->first();

        $questions = [
            // 1. Admission - DU Ka Unit (Physics)
            [
                'segment' => 'admission',
                'subject_id' => $physics?->id,
                'topic_name' => 'মহাকর্ষ ও অভিকর্ষ',
                'institution' => 'University of Dhaka',
                'exam_name' => 'DU Ka Unit Admission Test',
                'origin_tag' => 'DU-24',
                'year' => 2024,
                'session' => '2023-2024',
                'shift_or_set' => 'Set 1',
                'question_text' => 'ভূপৃষ্ঠ হতে কত উচ্চতায় অভিকর্ষজ ত্বরণ $g$-এর মান ভূপৃষ্ঠের মানের এক-চতুর্থাংশ ($\frac{1}{4}g$) হবে? (পৃথিবীর ব্যাসার্ধ $R$)',
                'question_type' => 'mcq',
                'options' => [
                    ['key' => 'A', 'text' => '$R$'],
                    ['key' => 'B', 'text' => '$2R$'],
                    ['key' => 'C', 'text' => '$\frac{R}{2}$'],
                    ['key' => 'D', 'text' => '$\frac{R}{4}$'],
                ],
                'correct_option' => 'A',
                'explanation' => 'উচ্চতা $h$-এ অভিকর্ষজ ত্বরণের সূত্র:
$$g\' = g \left(\frac{R}{R+h}\right)^2$$
শর্তানুসারে, $g\' = \frac{1}{4}g$:
$$\frac{1}{4}g = g \left(\frac{R}{R+h}\right)^2 \implies \frac{1}{2} = \frac{R}{R+h} \implies R+h = 2R \implies h = R$$
অতএব, ভূপৃষ্ঠ হতে $R$ উচ্চতায় অভিকর্ষজ ত্বরণ এক-চতুর্থাংশ হবে।',
                'marks' => 1.00,
                'negative_marks' => 0.25,
                'difficulty' => 'medium',
                'source_id' => $duSource?->id,
                'ingestion_type' => 'manual',
                'status' => 'published',
                'verification_status' => 'verified',
            ],

            // 2. Admission - DU Ka Unit (Higher Math)
            [
                'segment' => 'admission',
                'subject_id' => $higherMath?->id,
                'topic_name' => 'ক্যালকুলাস (অন্তরীকরণ ও যোগজীকরণ)',
                'institution' => 'University of Dhaka',
                'exam_name' => 'DU Ka Unit Admission Test',
                'origin_tag' => 'DU-24',
                'year' => 2024,
                'session' => '2023-2024',
                'shift_or_set' => 'Set 1',
                'question_text' => 'মান নির্ণয় কর: $\\lim_{x \\to 0} \\frac{e^{3x} - 1}{\\sin 2x}$',
                'question_type' => 'mcq',
                'options' => [
                    ['key' => 'A', 'text' => '$\\frac{3}{2}$'],
                    ['key' => 'B', 'text' => '$\\frac{2}{3}$'],
                    ['key' => 'C', 'text' => '$1$'],
                    ['key' => 'D', 'text' => '$0$'],
                ],
                'correct_option' => 'A',
                'explanation' => 'L\'Hôpital\'s Rule প্রয়োগ করে:
$x \to 0$ বসালে এটি $\frac{0}{0}$ অনির্ণেয় আকার ধারণ করে।
লব ও হর উভয়কে $x$-এর সাপেক্ষে অন্তরীকরণ করে:
$$\lim_{x \to 0} \frac{\frac{d}{dx}(e^{3x} - 1)}{\frac{d}{dx}(\sin 2x)} = \lim_{x \to 0} \frac{3e^{3x}}{2\cos 2x}$$
এখন $x = 0$ বসালে:
$$\frac{3e^0}{2\cos 0} = \frac{3(1)}{2(1)} = \frac{3}{2}$$',
                'marks' => 1.00,
                'negative_marks' => 0.25,
                'difficulty' => 'easy',
                'source_id' => $duSource?->id,
                'ingestion_type' => 'manual',
                'status' => 'published',
                'verification_status' => 'verified',
            ],

            // 3. Admission - BUET Engineering (Chemistry)
            [
                'segment' => 'admission',
                'subject_id' => $chemistry?->id,
                'topic_name' => 'পরিবেশ রসায়ন ও গ্যাসের সূত্রাবলী',
                'institution' => 'BUET',
                'exam_name' => 'BUET Undergraduate Admission Test',
                'origin_tag' => 'BUET-23',
                'year' => 2023,
                'session' => '2022-2023',
                'shift_or_set' => 'Shift 1',
                'question_text' => 'একটি আদর্শ গ্যাসের ক্ষেত্রে রুট মিন স্কয়ার বেগ ($C_{rms}$) এবং পরম তাপমাত্রা ($T$)-এর মধ্যে সম্পর্ক কী?',
                'question_type' => 'mcq',
                'options' => [
                    ['key' => 'A', 'text' => '$C_{rms} \\propto T$'],
                    ['key' => 'B', 'text' => '$C_{rms} \\propto \\sqrt{T}$'],
                    ['key' => 'C', 'text' => '$C_{rms} \\propto T^2$'],
                    ['key' => 'D', 'text' => '$C_{rms} \\propto \\frac{1}{\\sqrt{T}}$'],
                ],
                'correct_option' => 'B',
                'explanation' => 'বর্গমূলীয় গড় বর্গবেগ ($C_{rms}$)-এর সমীকরণ হলো:
$$C_{rms} = \sqrt{\frac{3RT}{M}}$$
যেখানে $R$ গ্যাস ধ্রুবক এবং $M$ হলো মোলার ভর। অতএব নির্দিষ্ট গ্যাসের জন্য:
$$C_{rms} \propto \sqrt{T}$$
অর্থাৎ বেগ পরম তাপমাত্রার বর্গমূলের সমানুপাতিক।',
                'marks' => 1.00,
                'negative_marks' => 0.25,
                'difficulty' => 'easy',
                'source_id' => $buetSource?->id,
                'ingestion_type' => 'manual',
                'status' => 'published',
                'verification_status' => 'verified',
            ],

            // 4. Admission - Medical MBBS (Biology)
            [
                'segment' => 'admission',
                'subject_id' => $biology?->id,
                'topic_name' => 'রক্ত ও সংবহন',
                'institution' => 'DGME',
                'exam_name' => 'National Medical MBBS Admission Test',
                'origin_tag' => 'DGME-24',
                'year' => 2024,
                'session' => '2023-2024',
                'shift_or_set' => 'National Set',
                'question_text' => 'মানুষের হৃৎপিণ্ডের প্রাকৃতিক পেসমেকার (Natural Pacemaker) কোনটি?',
                'question_type' => 'mcq',
                'options' => [
                    ['key' => 'A', 'text' => 'SA নোড (Sinoatrial Node)'],
                    ['key' => 'B', 'text' => 'AV নোড (Atrioventricular Node)'],
                    ['key' => 'C', 'text' => 'হিজের বান্ডল (Bundle of His)'],
                    ['key' => 'D', 'text' => 'পারকিঞ্জি তন্তু (Purkinje Fibers)'],
                ],
                'correct_option' => 'A',
                'explanation' => "ডান অলিন্দের প্রাচীরে অবস্থিত সাইনো-অ্যাট্রিয়াল নোড (SA Node) স্বয়ংক্রিয়ভাবে প্রতি মিনিটে ৭০-৮০ বার অ্যাকশন পটেনশিয়াল তৈরি করে হৃৎস্পন্দন শুরু করে। তাই একে প্রাকৃতিক পেসমেকার বলা হয়।\n(সংরক্ষিত পেসমেকার বা রিজার্ভ পেসমেকার বলা হয় AV নোডকে)।",
                'marks' => 1.00,
                'negative_marks' => 0.25,
                'difficulty' => 'easy',
                'source_id' => $dgmeSource?->id,
                'ingestion_type' => 'manual',
                'status' => 'published',
                'verification_status' => 'verified',
            ],

            // 5. Job - 46th BCS Preliminary (Bangla Literature)
            [
                'segment' => 'job',
                'subject_id' => $bangla?->id,
                'topic_name' => 'আধুনিক যুগ ও পঞ্চপাণ্ডব',
                'institution' => 'BPSC',
                'exam_name' => '46th BCS Preliminary Examination',
                'origin_tag' => 'BCS-46',
                'year' => 2024,
                'session' => '2024',
                'shift_or_set' => 'Set Jamuna',
                'question_text' => "'গীতাঞ্জলি' কাব্যের ইংরেজি অনুবাদ 'Song Offerings'-এর ভূমিকা লিখেছিলেন কে?",
                'question_type' => 'mcq',
                'options' => [
                    ['key' => 'A', 'text' => 'টি. এস. এলিয়ট'],
                    ['key' => 'B', 'text' => 'ডব্লিউ. বি. ইয়েটস'],
                    ['key' => 'C', 'text' => 'উইলিয়াম ওয়ার্ডসওয়ার্থ'],
                    ['key' => 'D', 'text' => 'জন কিটস'],
                ],
                'correct_option' => 'B',
                'explanation' => "রবীন্দ্রনাথ ঠাকুরের 'গীতাঞ্জলি' কাব্যের স্ব-অনূদিত ইংরেজি সংকলন 'Song Offerings' প্রকাশিত হয় ১৯১২ সালে। এর অনন্য ভূমিকা রচনা করেছিলেন আইরিশ কবি উইলিয়াম বাটলার ইয়েটস (W. B. Yeats)। এই কাব্যের জন্যই ১৯১৩ সালে রবীন্দ্রনাথ সাহিত্যে নোবেল পুরস্কার লাভ করেন।",
                'marks' => 1.00,
                'negative_marks' => 0.50,
                'difficulty' => 'easy',
                'source_id' => $bpscSource?->id,
                'ingestion_type' => 'manual',
                'status' => 'published',
                'verification_status' => 'verified',
            ],

            // 6. Job - 46th BCS Preliminary (Math Reasoning)
            [
                'segment' => 'job',
                'subject_id' => $mathReasoning?->id,
                'topic_name' => 'বীজগণিতীয় সূত্রাবলি ও উৎপাদক',
                'institution' => 'BPSC',
                'exam_name' => '46th BCS Preliminary Examination',
                'origin_tag' => 'BCS-46',
                'year' => 2024,
                'session' => '2024',
                'shift_or_set' => 'Set Padma',
                'question_text' => 'যদি $x + \\frac{1}{x} = 3$ হয়, তবে $x^3 + \\frac{1}{x^3}$-এর মান কত?',
                'question_type' => 'mcq',
                'options' => [
                    ['key' => 'A', 'text' => '$27$'],
                    ['key' => 'B', 'text' => '$18$'],
                    ['key' => 'C', 'text' => '$21$'],
                    ['key' => 'D', 'text' => '$24$'],
                ],
                'correct_option' => 'B',
                'explanation' => 'আমরা জানি ঘনের অনুসিদ্ধান্ত:
$$a^3 + b^3 = (a+b)^3 - 3ab(a+b)$$
এখানে $a = x$ এবং $b = \frac{1}{x}$:
$$x^3 + \frac{1}{x^3} = \left(x + \frac{1}{x}\right)^3 - 3\cdot x \cdot \frac{1}{x}\left(x + \frac{1}{x}\right)$$
মান বসিয়ে:
$$= (3)^3 - 3(3) = 27 - 9 = 18$$',
                'marks' => 1.00,
                'negative_marks' => 0.50,
                'difficulty' => 'easy',
                'source_id' => $bpscSource?->id,
                'ingestion_type' => 'manual',
                'status' => 'published',
                'verification_status' => 'verified',
            ],

            // 7. Job - 45th BCS Preliminary (Bangladesh Affairs)
            [
                'segment' => 'job',
                'subject_id' => $bdAffairs?->id,
                'topic_name' => 'বাংলাদেশের সংবিধান ও মূলনীতি',
                'institution' => 'BPSC',
                'exam_name' => '45th BCS Preliminary Examination',
                'origin_tag' => 'BCS-45',
                'year' => 2023,
                'session' => '2023',
                'shift_or_set' => 'Set Meghna',
                'question_text' => 'গণপ্রজাতন্ত্রী বাংলাদেশের সংবিধানে মৌলিক অধিকারের নিশ্চয়তা প্রদানকারী প্রধান অনুচ্ছেদ কোনটি?',
                'question_type' => 'mcq',
                'options' => [
                    ['key' => 'A', 'text' => 'অনুচ্ছেদ ২৭ থেকে ৪৪'],
                    ['key' => 'B', 'text' => 'অনুচ্ছেদ ৮ থেকে ২৫'],
                    ['key' => 'C', 'text' => 'অনুচ্ছেদ ৪৮ থেকে ৬৪'],
                    ['key' => 'D', 'text' => 'অনুচ্ছেদ ৬৫ থেকে ৯৩'],
                ],
                'correct_option' => 'A',
                'explanation' => "বাংলাদেশের সংবিধানের তৃতীয় ভাগে (অনুচ্ছেদ ২৭ থেকে ৪৪) নাগরিকদের মৌলিক অধিকারসমূহ সন্নিবেশিত রয়েছে।\n- দ্বিতীয় ভাগে (অনুচ্ছেদ ৮ থেকে ২৫) রয়েছে রাষ্ট্র পরিচালনার মূলনীতি।\n- চতুর্থ ভাগে (অনুচ্ছেদ ৪৮ থেকে ৬৪) রয়েছে নির্বাহী বিভাগ।\n- পঞ্চম ভাগে (অনুচ্ছেদ ৬৫ থেকে ৯৩) রয়েছে আইনসভা।",
                'marks' => 1.00,
                'negative_marks' => 0.50,
                'difficulty' => 'medium',
                'source_id' => $bpscSource?->id,
                'ingestion_type' => 'manual',
                'status' => 'published',
                'verification_status' => 'verified',
            ],

            // 8. Job - Bangladesh Bank AD (Computer & ICT)
            [
                'segment' => 'job',
                'subject_id' => $ict?->id,
                'topic_name' => 'ইন্টারনেট, ওয়েব ও সাইবার নিরাপত্তা',
                'institution' => 'Bangladesh Bank',
                'exam_name' => 'Bangladesh Bank Assistant Director (AD) Examination',
                'origin_tag' => 'BB-AD-24',
                'year' => 2024,
                'session' => '2024',
                'shift_or_set' => 'Shift Morning',
                'question_text' => 'Which protocol is primarily used for secure communications and encrypted data exchange across the World Wide Web?',
                'question_type' => 'mcq',
                'options' => [
                    ['key' => 'A', 'text' => 'HTTP'],
                    ['key' => 'B', 'text' => 'FTP'],
                    ['key' => 'C', 'text' => 'HTTPS (via TLS/SSL)'],
                    ['key' => 'D', 'text' => 'SMTP'],
                ],
                'correct_option' => 'C',
                'explanation' => "HTTPS (Hypertext Transfer Protocol Secure) encrypts communication over computer networks using Transport Layer Security (TLS) or Secure Sockets Layer (SSL), ensuring confidentiality, integrity, and authentication for web traffic.",
                'marks' => 1.00,
                'negative_marks' => 0.25,
                'difficulty' => 'easy',
                'source_id' => $bbSource?->id,
                'ingestion_type' => 'manual',
                'status' => 'published',
                'verification_status' => 'verified',
            ],

            // 9. Job - Bangladesh Bank Combined 9-Bank (English)
            [
                'segment' => 'job',
                'subject_id' => $english?->id,
                'topic_name' => 'Synonyms & Antonyms',
                'institution' => 'Bangladesh Bank (BSCS)',
                'exam_name' => 'Combined 9-Bank Senior Officer Recruitment',
                'origin_tag' => 'COMB-BANK-23',
                'year' => 2023,
                'session' => '2023',
                'shift_or_set' => 'Set B',
                'question_text' => "Choose the word most nearly OPPOSITE in meaning to the capitalized word: 'EPHEMERAL'",
                'question_type' => 'mcq',
                'options' => [
                    ['key' => 'A', 'text' => 'Transient'],
                    ['key' => 'B', 'text' => 'Permanent'],
                    ['key' => 'C', 'text' => 'Fugitive'],
                    ['key' => 'D', 'text' => 'Evancescent'],
                ],
                'correct_option' => 'B',
                'explanation' => "'Ephemeral' means lasting for a very short time (ক্ষণস্থায়ী / ক্ষণভঙ্গুর). Its exact antonym is 'Permanent' (চিরস্থায়ী / দীর্ঘস্থায়ী).\n'Transient', 'Fugitive', and 'Evanescent' are synonyms of ephemeral.",
                'marks' => 1.00,
                'negative_marks' => 0.25,
                'difficulty' => 'medium',
                'source_id' => $bbSource?->id,
                'ingestion_type' => 'manual',
                'status' => 'published',
                'verification_status' => 'verified',
            ],
        ];

        foreach ($questions as $q) {
            QuestionBank::updateOrCreate(
                [
                    'segment' => $q['segment'],
                    'origin_tag' => $q['origin_tag'],
                    'question_text' => $q['question_text'],
                ],
                $q
            );
        }
    }
}
