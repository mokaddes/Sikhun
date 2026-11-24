<?php

namespace App\Services;

use App\Models\QuestionBank;
use Illuminate\Database\Eloquent\Builder;

/**
 * Curated Programmatic SEO exam collections built on top of the live QuestionBank (38,499+ MCQs).
 *
 * Each slug powers an indexable public landing page (/questions/{slug}),
 * XML sitemap entry, /llms.txt citation, and internal links from Admission & Job circulars.
 */
class PublicQuestionCatalog
{
    /**
     * @return array<string, array<string, mixed>>
     */
    public static function collections(): array
    {
        return [
            'bcs-preliminary' => [
                'slug' => 'bcs-preliminary',
                'segment' => 'job',
                'title_bn' => 'বিসিএস প্রিলিমিনারি প্রশ্নব্যাংক ও সমাধান (BCS Preliminary)',
                'title_en' => 'BCS Preliminary Question Bank & Solutions',
                'short_bn' => 'বিসিএস প্রিলিমিনারি',
                'institution_bn' => 'বাংলাদেশ সরকারি কর্ম কমিশন (BPSC)',
                'description_bn' => '১০ম থেকে ৪৬তম বিসিএস প্রিলিমিনারি পরীক্ষার বিগত বছরের আসল MCQ প্রশ্ন, সঠিক উত্তর ও বিস্তারিত ব্যাখ্যা বিনামূল্যে প্র্যাকটিস করুন।',
                'keywords' => 'BCS preliminary question bank, বিসিএস প্রিলিমিনারি প্রশ্ন সমাধান, BPSC past MCQ, 47th BCS preparation, sikhun',
                'origin_tags' => ['BCS-PRELI-QUESTION-BANK'],
                'faqs' => [
                    ['question' => 'বিসিএস প্রিলিমিনারি পরীক্ষায় মোট নম্বর ও সময় কত?', 'answer' => 'বিসিএস প্রিলিমিনারি পরীক্ষায় ২০০টি MCQ প্রশ্নে মোট ২০০ নম্বর থাকে এবং সময় ২ ঘণ্টা। প্রতিটি ভুল উত্তরের জন্য ০.৫০ নম্বর কাটা যায়।'],
                    ['question' => 'Sikhun.com-এ কি বিসিএস বিগত বছরের প্রশ্ন ফ্রিতে প্র্যাকটিস করা যায়?', 'answer' => 'হ্যাঁ, Sikhun.com-এ বিসিএস প্রিলিমিনারি প্রশ্নব্যাংকের প্রশ্ন ও ব্যাখ্যা সরাসরি অনলাইনে ফ্রিতে দেখা ও প্র্যাকটিস করা যায়।'],
                ],
            ],
            'primary-teacher' => [
                'slug' => 'primary-teacher',
                'segment' => 'job',
                'title_bn' => 'প্রাথমিক সহকারী শিক্ষক নিয়োগ প্রশ্নব্যাংক ও সমাধান (DPE Primary)',
                'title_en' => 'Primary Assistant Teacher Recruitment Question Bank (DPE)',
                'short_bn' => 'প্রাথমিক শিক্ষক নিয়োগ',
                'institution_bn' => 'প্রাথমিক শিক্ষা অধিদপ্তর (DPE)',
                'description_bn' => 'প্রাথমিক বিদ্যালয়ের সহকারী শিক্ষক নিয়োগ পরীক্ষার (২০০৬–২০২৫) বিগত বছরের ৭,৫০০+ আসল MCQ প্রশ্ন ও ব্যাখ্যা।',
                'keywords' => 'primary teacher question bank, প্রাথমিক শিক্ষক নিয়োগ প্রশ্ন সমাধান, DPE assistant teacher MCQ, primary exam preparation',
                'origin_tags' => [
                    'PRIMARY-ASSISTANT-TEACHER-QUES',
                    'PRIMARY-TEACHER-2013',
                    'PRIMARY-TEACHER-2019',
                    'PRIMARY-TEACHER-2010',
                    'PRIMARY-TEACHER-2006',
                    'PRATHMIK-BIDZALY-SHKAREE-SIKSH',
                ],
                'institution_like' => 'প্রাথমিক শিক্ষা অধিদপ্তর',
                'faqs' => [
                    ['question' => 'প্রাথমিক সহকারী শিক্ষক নিয়োগ পরীক্ষায় কোন কোন বিষয় থেকে প্রশ্ন আসে?', 'answer' => 'বাংলা (২০), ইংরেজি (২০), গণিত (২০) এবং সাধারণ জ্ঞান ও বিজ্ঞান (২০) — মোট ৮০টি MCQ প্রশ্ন থাকে এবং নেগেটিভ মার্কিং ০.২৫।'],
                    ['question' => 'প্রাথমিক শিক্ষক নিয়োগে বিগত বছরের প্রশ্ন কতটা গুরুত্বপূর্ণ?', 'answer' => 'প্রাথমিক নিয়োগ পরীক্ষায় বিগত বছরের প্রশ্ন এবং একই টপিক থেকে প্রায় ৪০–৫০% প্রশ্ন পুনরাবৃত্তি হয়।'],
                ],
            ],
            'govt-job-2025' => [
                'slug' => 'govt-job-2025',
                'segment' => 'job',
                'title_bn' => 'সরকারি চাকরি প্রশ্নব্যাংক ২০২৫ ও ২০২৪ (সকল মন্ত্রণালয় ও অধিদপ্তর)',
                'title_en' => 'Bangladesh Govt Job Question Bank 2025 & 2024',
                'short_bn' => 'সরকারি চাকরি ২০২৫-২০২৪',
                'institution_bn' => 'সকল মন্ত্রণালয় ও সরকারি অধিদপ্তর',
                'description_bn' => '২০২৫ ও ২০২৪ সালে অনুষ্ঠিত বাংলাদেশের সকল মন্ত্রণালয়, অধিদপ্তর ও স্বায়ত্তশাসিত প্রতিষ্ঠানের সরকারি চাকরির পরীক্ষার আসল প্রশ্ন ও সমাধান।',
                'keywords' => 'govt job question solution 2025, সরকারি চাকরি প্রশ্ন সমাধান ২০২৫, recent job question bank bangladesh',
                'origin_tags' => [
                    '2025-GOVT-JOB-QUESTION-BANK',
                    '2024-GOVT-JOB-QUESTION-BANK',
                    '2023-GOVT-JOB-QUESTION-BANK',
                ],
                'faqs' => [
                    ['question' => 'সাম্প্রতিক সরকারি চাকরির প্রশ্নব্যাংক কেন পড়বেন?', 'answer' => 'সাম্প্রতিক ২০২৩–২০২৫ সালের সরকারি চাকরির প্রশ্নব্যাংক পড়লে বর্তমান প্রশ্নের ধরন, সাম্প্রতিক সাধারণ জ্ঞান ও গুরুত্বপূর্ণ রিপিটেড MCQ সহজেই আয়ত্ত করা যায়।'],
                ],
            ],
            'bangladesh-bank' => [
                'slug' => 'bangladesh-bank',
                'segment' => 'job',
                'title_bn' => 'বাংলাদেশ ব্যাংক ও সমন্বিত ব্যাংক নিয়োগ প্রশ্নব্যাংক (Bank Job MCQ)',
                'title_en' => 'Bangladesh Bank & Combined Bank Job Question Bank',
                'short_bn' => 'বাংলাদেশ ব্যাংক ও ব্যাংক জব',
                'institution_bn' => 'বাংলাদেশ ব্যাংক ও ব্যাংকার্স সিলেকশন কমিটি (BB/BSC)',
                'description_bn' => 'বাংলাদেশ ব্যাংক AD, অফিসার, সিনিয়র অফিসার ও সমন্বিত সরকারি ব্যাংক নিয়োগ পরীক্ষার বিগত বছরের MCQ প্রশ্ন ও সমাধান।',
                'keywords' => 'bangladesh bank question bank, bank job mcq solution, সমন্বিত ব্যাংক নিয়োগ প্রশ্ন সমাধান, BB AD past questions',
                'origin_tags' => ['BANGLADESH-BANK-QUESTION'],
                'faqs' => [
                    ['question' => 'ব্যাংক জব প্রিলিমিনারি পরীক্ষায় কোন বিষয়ে বেশি গুরুত্ব দিতে হয়?', 'answer' => 'বাংলাদেশ ব্যাংক ও সমন্বিত ব্যাংক প্রিলিমিনারিতে ইংরেজি, গণিত (Math), কম্পিউটার ও আইটি, বাংলা এবং সাধারণ জ্ঞান থেকে ১০০ নম্বরের MCQ হয়।'],
                ],
            ],
            'psc-non-cadre' => [
                'slug' => 'psc-non-cadre',
                'segment' => 'job',
                'title_bn' => 'পিএসসি নন-ক্যাডার প্রশ্নব্যাংক ও সমাধান (PSC Non-Cadre MCQ)',
                'title_en' => 'PSC Non-Cadre Question Bank & Solutions',
                'short_bn' => 'পিএসসি নন-ক্যাডার',
                'institution_bn' => 'বাংলাদেশ সরকারি কর্ম কমিশন (BPSC Non-Cadre)',
                'description_bn' => 'পিএসসি ৯ম থেকে ১২তম গ্রেডের নন-ক্যাডার নিয়োগ পরীক্ষার ১,০০০+ বিগত বছরের প্রশ্ন ও ব্যাখ্যা।',
                'keywords' => 'PSC non cadre question bank, পিএসসি নন ক্যাডার প্রশ্ন সমাধান, BPSC non cadre mcq',
                'origin_tags' => ['PSC-NON-CADRE-QUESTION-BANK'],
                'faqs' => [
                    ['question' => 'পিএসসি নন-ক্যাডার পরীক্ষার সিলেবাস কি বিসিএসের মতো?', 'answer' => 'হ্যাঁ, পিএসসি নন-ক্যাডার প্রিলিমিনারি পরীক্ষার অধিকাংশ প্রশ্নই বাংলা, ইংরেজি, সাধারণ জ্ঞান, গণিত ও দৈনন্দিন বিজ্ঞান থেকে বিসিএস স্ট্যান্ডার্ডে হয়ে থাকে।'],
                ],
            ],
            'ntrca' => [
                'slug' => 'ntrca',
                'segment' => 'job',
                'title_bn' => 'শিক্ষক নিবন্ধন (NTRCA) ও মাধ্যমিক শিক্ষক নিয়োগ প্রশ্নব্যাংক',
                'title_en' => 'NTRCA Teacher Registration Question Bank & Solutions',
                'short_bn' => 'শিক্ষক নিবন্ধন (NTRCA)',
                'institution_bn' => 'বেসরকারি শিক্ষক নিবন্ধন ও প্রত্যয়ন কর্তৃপক্ষ (NTRCA)',
                'description_bn' => 'স্কুল, স্কুল-২ ও কলেজ পর্যায়ের NTRCA শিক্ষক নিবন্ধন এবং মাধ্যমিক শিক্ষক নিয়োগ পরীক্ষার বিগত বছরের প্রশ্ন ও সমাধান।',
                'keywords' => 'NTRCA question bank, শিক্ষক নিবন্ধন প্রশ্ন সমাধান, 19th NTRCA preparation, school college level mcq',
                'origin_tags' => ['TEACHER-REGISTRATION-AND-CERTI', 'SECONDARY-TEACHER-RECRUITMENT-'],
                'faqs' => [
                    ['question' => 'NTRCA প্রিলিমিনারি পরীক্ষায় পাস নম্বর কত?', 'answer' => 'NTRCA প্রিলিমিনারি পরীক্ষায় ১০০ নম্বরের মধ্যে ন্যূনতম ৪০ নম্বর পেলে উত্তীর্ণ হওয়া যায়। প্রতিটি ভুল উত্তরের জন্য ০.২৫ নম্বর কাটা যায়।'],
                ],
            ],
            'bjs-assistant-judge' => [
                'slug' => 'bjs-assistant-judge',
                'segment' => 'job',
                'title_bn' => 'সহকারী জজ (BJS) নিয়োগ পরীক্ষার প্রশ্নব্যাংক ও সমাধান',
                'title_en' => 'Bangladesh Judicial Service (BJS) Assistant Judge Question Bank',
                'short_bn' => 'সহকারী জজ (BJS)',
                'institution_bn' => 'বাংলাদেশ জুডিশিয়াল সার্ভিস কমিশন (BJSC)',
                'description_bn' => 'বাংলাদেশ জুডিশিয়াল সার্ভিস (BJS) সহকারী জজ প্রিলিমিনারি পরীক্ষার বিগত বছরের আসল MCQ প্রশ্ন ও সমাধান।',
                'keywords' => 'BJS question bank, assistant judge exam question solution, সহকারী জজ নিয়োগ প্রশ্ন সমাধান',
                'origin_tags' => ['ASSISTANT-JUDGE-RECRUITMENT-EX'],
                'faqs' => [
                    ['question' => 'সহকারী জজ প্রিলিমিনারি পরীক্ষায় কত নম্বর থাকে?', 'answer' => 'BJS প্রিলিমিনারি পরীক্ষায় আইন ও সাধারণ বিষয় মিলিয়ে ১০০ নম্বরের MCQ পরীক্ষা অনুষ্ঠিত হয়।'],
                ],
            ],
            'du-admission' => [
                'slug' => 'du-admission',
                'segment' => 'admission',
                'title_bn' => 'ঢাকা বিশ্ববিদ্যালয় (DU) ভর্তি প্রশ্নব্যাংক ও সমাধান (ক, খ, গ, ঘ ইউনিট)',
                'title_en' => 'Dhaka University (DU) Admission Question Bank & Solutions',
                'short_bn' => 'ঢাকা বিশ্ববিদ্যালয় (DU)',
                'institution_bn' => 'ঢাকা বিশ্ববিদ্যালয় (DU)',
                'description_bn' => 'ঢাকা বিশ্ববিদ্যালয়ের বিজ্ঞান (ক), কলা ও আইন (খ), ব্যবসায় শিক্ষা (গ) এবং ঘ ইউনিটের বিগত বছরের ৪,৩০০+ ভর্তি প্রশ্ন ও ব্যাখ্যা।',
                'keywords' => 'DU admission question bank, ঢাকা বিশ্ববিদ্যালয় ভর্তি প্রশ্ন সমাধান, DU A B C unit past questions',
                'origin_tags' => ['DU-A', 'DU-B', 'DU-C', 'DU-D'],
                'faqs' => [
                    ['question' => 'ঢাকা বিশ্ববিদ্যালয়ের ভর্তি পরীক্ষায় নেগেটিভ মার্কিং কত?', 'answer' => 'ঢাকা বিশ্ববিদ্যালয়ের ভর্তি পরীক্ষার MCQ অংশে প্রতিটি ভুল উত্তরের জন্য ০.২৫ নম্বর কাটা যায়।'],
                    ['question' => 'ঢাকা বিশ্ববিদ্যালয়ে কি সেকেন্ড টাইম আছে?', 'answer' => 'না, ঢাকা বিশ্ববিদ্যালয়ের স্নাতক প্রথম বর্ষের ভর্তি পরীক্ষায় দ্বিতীয়বার (Second Time) আবেদনের সুযোগ নেই।'],
                ],
            ],
            'du-a-unit' => [
                'slug' => 'du-a-unit',
                'segment' => 'admission',
                'title_bn' => 'ঢাবি ক ইউনিট (বিজ্ঞান) ভর্তি প্রশ্নব্যাংক ও সমাধান (DU A Unit)',
                'title_en' => 'DU A Unit (Science) Admission Question Bank',
                'short_bn' => 'ঢাবি ক ইউনিট (DU-A)',
                'institution_bn' => 'ঢাকা বিশ্ববিদ্যালয় — বিজ্ঞান ইউনিট',
                'description_bn' => 'ঢাকা বিশ্ববিদ্যালয় বিজ্ঞান (ক) ইউনিটের পদার্থবিজ্ঞান, রসায়ন, উচ্চতর গণিত ও জীববিজ্ঞানের বিগত বছরের ১,৬৫০+ ভর্তি MCQ ও সমাধান।',
                'keywords' => 'DU A unit question bank, ঢাবি ক ইউনিট প্রশ্ন সমাধান, DU science admission MCQ',
                'origin_tags' => ['DU-A'],
                'faqs' => [
                    ['question' => 'ঢাবি বিজ্ঞান (ক) ইউনিটে কোন বিষয়গুলোতে পরীক্ষা হয়?', 'answer' => 'পদার্থবিজ্ঞান, রসায়ন, গণিত ও জীববিজ্ঞান (অথবা বাংলা/ইংরেজি ঐচ্ছিক) থেকে ৬০ নম্বরের MCQ ও ৪০ নম্বরের লিখিত পরীক্ষা হয়।'],
                ],
            ],
            'du-b-unit' => [
                'slug' => 'du-b-unit',
                'segment' => 'admission',
                'title_bn' => 'ঢাবি খ ইউনিট (কলা ও আইন) ভর্তি প্রশ্নব্যাংক ও সমাধান (DU B Unit)',
                'title_en' => 'DU B Unit (Arts & Law) Admission Question Bank',
                'short_bn' => 'ঢাবি খ ইউনিট (DU-B)',
                'institution_bn' => 'ঢাকা বিশ্ববিদ্যালয় — কলা, আইন ও সামাজিক বিজ্ঞান ইউনিট',
                'description_bn' => 'ঢাকা বিশ্ববিদ্যালয় খ ইউনিটের বাংলা, ইংরেজি ও সাধারণ জ্ঞানের বিগত বছরের আসল ভর্তি প্রশ্ন ও ব্যাখ্যা।',
                'keywords' => 'DU B unit question bank, ঢাবি খ ইউনিট প্রশ্ন সমাধান, DU arts admission MCQ',
                'origin_tags' => ['DU-B', 'DU-D'],
                'faqs' => [
                    ['question' => 'ঢাবি খ ইউনিটে কোন বিষয়গুলো থাকে?', 'answer' => 'বাংলা (১৫), ইংরেজি (১৫) ও সাধারণ জ্ঞান (৩০) মিলিয়ে ৬০ নম্বরের MCQ এবং ৪০ নম্বরের লিখিত পরীক্ষা অনুষ্ঠিত হয়।'],
                ],
            ],
            'du-c-unit' => [
                'slug' => 'du-c-unit',
                'segment' => 'admission',
                'title_bn' => 'ঢাবি গ ইউনিট (ব্যবসায় শিক্ষা) ভর্তি প্রশ্নব্যাংক ও সমাধান (DU C Unit)',
                'title_en' => 'DU C Unit (Business Studies) Admission Question Bank',
                'short_bn' => 'ঢাবি গ ইউনিট (DU-C)',
                'institution_bn' => 'ঢাকা বিশ্ববিদ্যালয় — ব্যবসায় শিক্ষা ইউনিট',
                'description_bn' => 'ঢাকা বিশ্ববিদ্যালয় গ ইউনিটের হিসাববিজ্ঞান, ব্যবসায় সংগঠন, ফিন্যান্স, মার্কেটিং, বাংলা ও ইংরেজির বিগত বছরের ভর্তি প্রশ্ন ও সমাধান।',
                'keywords' => 'DU C unit question bank, ঢাবি গ ইউনিট প্রশ্ন সমাধান, DU business studies MCQ',
                'origin_tags' => ['DU-C'],
                'faqs' => [
                    ['question' => 'ঢাবি গ ইউনিটে কোন বিষয়গুলো আবশ্যিক?', 'answer' => 'ব্যবসায় শিক্ষা শাখার জন্য বাংলা, ইংরেজি, হিসাববিজ্ঞান, ব্যবসায় নীতি ও প্রয়োগ এবং ফিন্যান্স/মার্কেটিং থেকে প্রশ্ন আসে।'],
                ],
            ],
            'ru-admission' => [
                'slug' => 'ru-admission',
                'segment' => 'admission',
                'title_bn' => 'রাজশাহী বিশ্ববিদ্যালয় (RU) ভর্তি প্রশ্নব্যাংক ও সমাধান (A, B, C ইউনিট)',
                'title_en' => 'Rajshahi University (RU) Admission Question Bank & Solutions',
                'short_bn' => 'রাজশাহী বিশ্ববিদ্যালয় (RU)',
                'institution_bn' => 'রাজশাহী বিশ্ববিদ্যালয় (RU)',
                'description_bn' => 'রাজশাহী বিশ্ববিদ্যালয়ের A (মানবিক), B (বাণিজ্য) ও C (বিজ্ঞান) ইউনিটের ৪,৩০০+ বিগত বছরের আসল ভর্তি MCQ প্রশ্ন ও ব্যাখ্যা।',
                'keywords' => 'RU admission question bank, রাজশাহী বিশ্ববিদ্যালয় ভর্তি প্রশ্ন সমাধান, RU A B C unit MCQ',
                'origin_tags' => ['RU-A', 'RU-B', 'RU-C'],
                'faqs' => [
                    ['question' => 'রাজশাহী বিশ্ববিদ্যালয়ের ভর্তি পরীক্ষায় কি সেকেন্ড টাইম আছে?', 'answer' => 'হ্যাঁ, রাজশাহী বিশ্ববিদ্যালয়ে দ্বিতীয়বার (Second Time) ভর্তি পরীক্ষায় অংশগ্রহণের সুযোগ রয়েছে।'],
                ],
            ],
            'gst-cluster' => [
                'slug' => 'gst-cluster',
                'segment' => 'admission',
                'title_bn' => 'জিএসটি গুচ্ছ বিশ্ববিদ্যালয় (GST) ভর্তি প্রশ্নব্যাংক ও সমাধান',
                'title_en' => 'GST Cluster University Admission Question Bank & Solutions',
                'short_bn' => 'জিএসটি গুচ্ছ (GST)',
                'institution_bn' => 'জিএসটি গুচ্ছ বিশ্ববিদ্যালয় (GST Cluster)',
                'description_bn' => 'জিএসটি গুচ্ছভুক্ত পাবলিক বিশ্ববিদ্যালয়সমূহের A, B ও C ইউনিটের বিগত বছরের ভর্তি প্রশ্ন, উত্তর ও ব্যাখ্যা।',
                'keywords' => 'GST admission question bank, গুচ্ছ ভর্তি প্রশ্ন সমাধান, GST cluster A B C unit MCQ',
                'origin_tags' => ['GST-A', 'GST-B', 'GST-C'],
                'institution_like' => 'জিএসটি গুচ্ছ',
                'faqs' => [
                    ['question' => 'জিএসটি গুচ্ছ ভর্তি পরীক্ষায় পাস নম্বর কত?', 'answer' => 'জিএসটি গুচ্ছ ভর্তি পরীক্ষায় ১০০ নম্বরের MCQ-তে ন্যূনতম ৩০ নম্বর পেলে উত্তীর্ণ হিসেবে গণ্য করা হয়।'],
                ],
            ],
            'buet-admission' => [
                'slug' => 'buet-admission',
                'segment' => 'admission',
                'title_bn' => 'বুয়েট ও প্রকৌশল বিশ্ববিদ্যালয় (BUET/RUET/CUET) ভর্তি প্রশ্নব্যাংক',
                'title_en' => 'BUET & Engineering University Admission Question Bank',
                'short_bn' => 'বুয়েট ও ইঞ্জিনিয়ারিং (BUET)',
                'institution_bn' => 'বাংলাদেশ প্রকৌশল বিশ্ববিদ্যালয় (BUET)',
                'description_bn' => 'বুয়েট (BUET), রুয়েট ও চুয়েট ইঞ্জিনিয়ারিং ভর্তি পরীক্ষার পদার্থবিজ্ঞান, রসায়ন ও উচ্চতর গণিতের বিগত বছরের প্রশ্ন ও ব্যাখ্যা।',
                'keywords' => 'BUET admission question bank, বুয়েট ভর্তি প্রশ্ন সমাধান, engineering admission MCQ bangladesh',
                'origin_tags' => ['BUET', 'RUET', 'CUET'],
                'faqs' => [
                    ['question' => 'বুয়েট প্রিলিমিনারি পরীক্ষায় কোন বিষয়গুলো থাকে?', 'answer' => 'বুয়েট প্রিলিমিনারি পরীক্ষায় পদার্থবিজ্ঞান, রসায়ন ও উচ্চতর গণিত থেকে ১০০ নম্বরের MCQ পরীক্ষা অনুষ্ঠিত হয়।'],
                ],
            ],
        ];
    }

    public static function find(string $slug): ?array
    {
        return self::collections()[$slug] ?? null;
    }

    /**
     * Build an Eloquent query for a given collection definition.
     */
    public static function queryForCollection(array $collection): Builder
    {
        $query = QuestionBank::query()
            ->published()
            ->where('segment', $collection['segment'])
            ->where('question_text', '!=', '');

        $tags = $collection['origin_tags'] ?? [];
        $instLike = $collection['institution_like'] ?? null;

        if (! empty($tags) || $instLike) {
            $query->where(function (Builder $q) use ($tags, $instLike) {
                if (! empty($tags)) {
                    $q->whereIn('origin_tag', $tags);
                }
                if ($instLike) {
                    $q->orWhere('institution', 'like', "%{$instLike}%");
                }
            });
        }

        return $query;
    }

    /**
     * Match a JobCircular to the best public collection slug + origin tags.
     */
    public static function matchJobCircular(string $title, ?string $organization = null, ?string $category = null): array
    {
        $haystack = mb_strtolower($title.' '.($organization ?? '').' '.($category ?? ''));

        if (str_contains($haystack, 'bcs') || str_contains($haystack, 'বিসিএস') || str_contains($haystack, 'bpsc') || str_contains($haystack, 'কর্ম কমিশন')) {
            return self::collections()['bcs-preliminary'];
        }
        if (str_contains($haystack, 'primary') || str_contains($haystack, 'প্রাথমিক') || str_contains($haystack, 'dpe')) {
            return self::collections()['primary-teacher'];
        }
        if (str_contains($haystack, 'bank') || str_contains($haystack, 'ব্যাংক')) {
            return self::collections()['bangladesh-bank'];
        }
        if (str_contains($haystack, 'ntrca') || str_contains($haystack, 'নিবন্ধন') || str_contains($haystack, 'শিক্ষক') || str_contains($haystack, 'teacher')) {
            return self::collections()['ntrca'];
        }
        if (str_contains($haystack, 'judge') || str_contains($haystack, 'জজ') || str_contains($haystack, 'judicial') || str_contains($haystack, 'bjs')) {
            return self::collections()['bjs-assistant-judge'];
        }
        if (str_contains($haystack, 'non-cadre') || str_contains($haystack, 'নন-ক্যাডার') || str_contains($haystack, 'psc')) {
            return self::collections()['psc-non-cadre'];
        }

        return self::collections()['govt-job-2025'];
    }
}
