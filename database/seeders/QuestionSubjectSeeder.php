<?php

namespace Database\Seeders;

use App\Models\QuestionSubject;
use App\Models\QuestionTopic;
use Illuminate\Database\Seeder;

class QuestionSubjectSeeder extends Seeder
{
    public function run(): void
    {
        $subjects = [
            // Shared & Admission
            [
                'name_bn' => 'বাংলা (ভাষা ও সাহিত্য)',
                'name_en' => 'Bangla Language & Literature',
                'slug' => 'bangla',
                'segment' => 'both',
                'icon' => 'BookOpenIcon',
                'order' => 1,
                'topics' => [
                    ['name_bn' => 'ধ্বনি, বর্ণ ও উচ্চারণ', 'name_en' => 'Phonetics & Pronunciation', 'slug' => 'phonetics'],
                    ['name_bn' => 'সন্ধি ও ণ-ত্ব/ষ-ত্ব বিধান', 'name_en' => 'Sandhi & Natwa/Satwa', 'slug' => 'sandhi'],
                    ['name_bn' => 'শব্দ, উপসর্গ ও অনুসর্গ', 'name_en' => 'Prefixes, Suffixes & Vocabulary', 'slug' => 'prefixes-suffixes'],
                    ['name_bn' => 'কারক ও বিভক্তি', 'name_en' => 'Karak & Bibhakti', 'slug' => 'karak'],
                    ['name_bn' => 'সমাস', 'name_en' => 'Samas', 'slug' => 'samas'],
                    ['name_bn' => 'বাক্য শুদ্ধিকরণ ও বানান', 'name_en' => 'Sentence Correction & Spelling', 'slug' => 'correction-spelling'],
                    ['name_bn' => 'বাগধারা ও প্রবাদ-প্রবচন', 'name_en' => 'Idioms & Proverbs', 'slug' => 'idioms-proverbs'],
                    ['name_bn' => 'বাংলা সাহিত্যের প্রাচীন ও মধ্যযুগ', 'name_en' => 'Ancient & Medieval Literature', 'slug' => 'ancient-medieval-literature'],
                    ['name_bn' => 'আধুনিক যুগ ও পঞ্চপাণ্ডব', 'name_en' => 'Modern Era Literature', 'slug' => 'modern-era-literature'],
                    ['name_bn' => 'রবীন্দ্রনাথ, নজরুল ও গুরুত্বপূর্ণ সাহিত্যিক', 'name_en' => 'Major Authors (Tagore, Nazrul)', 'slug' => 'major-authors'],
                ],
            ],
            [
                'name_bn' => 'ইংরেজি (Grammar & Vocabulary)',
                'name_en' => 'English Language & Literature',
                'slug' => 'english',
                'segment' => 'both',
                'icon' => 'AcademicCapIcon',
                'order' => 2,
                'topics' => [
                    ['name_bn' => 'Parts of Speech & Identification', 'name_en' => 'Parts of Speech', 'slug' => 'parts-of-speech'],
                    ['name_bn' => 'Subject-Verb Agreement', 'name_en' => 'Subject-Verb Agreement', 'slug' => 'subject-verb-agreement'],
                    ['name_bn' => 'Right Form of Verbs & Tense', 'name_en' => 'Right Form of Verbs', 'slug' => 'right-form-of-verbs'],
                    ['name_bn' => 'Appropriate Prepositions', 'name_en' => 'Appropriate Prepositions', 'slug' => 'prepositions'],
                    ['name_bn' => 'Voice & Narration', 'name_en' => 'Voice & Narration', 'slug' => 'voice-narration'],
                    ['name_bn' => 'Synonyms & Antonyms', 'name_en' => 'Synonyms & Antonyms', 'slug' => 'synonyms-antonyms'],
                    ['name_bn' => 'Idioms, Phrases & Clauses', 'name_en' => 'Idioms & Phrases', 'slug' => 'idioms-phrases'],
                    ['name_bn' => 'Analogy & One-Word Substitution', 'name_en' => 'Analogy & Substitution', 'slug' => 'analogy-substitution'],
                    ['name_bn' => 'English Literary Periods & Movements', 'name_en' => 'Literary Periods', 'slug' => 'literary-periods'],
                    ['name_bn' => 'Famous Writers, Dramas & Quotes', 'name_en' => 'Writers & Quotes', 'slug' => 'famous-writers-quotes'],
                ],
            ],
            [
                'name_bn' => 'পদার্থবিজ্ঞান',
                'name_en' => 'Physics',
                'slug' => 'physics',
                'segment' => 'admission',
                'icon' => 'BoltIcon',
                'order' => 3,
                'topics' => [
                    ['name_bn' => 'ভেক্টর ও গতিবিদ্যা', 'name_en' => 'Vectors & Kinematics', 'slug' => 'vectors-kinematics'],
                    ['name_bn' => 'নিউটনীয় বলবিদ্যা', 'name_en' => 'Newtonian Mechanics', 'slug' => 'newtonian-mechanics'],
                    ['name_bn' => 'কাজ, শক্তি ও ক্ষমতা', 'name_en' => 'Work, Energy & Power', 'slug' => 'work-energy-power'],
                    ['name_bn' => 'মহাকর্ষ ও অভিকর্ষ', 'name_en' => 'Gravitation', 'slug' => 'gravitation'],
                    ['name_bn' => 'পর্যায়বৃত্ত গতি ও তরঙ্গ', 'name_en' => 'Periodic Motion & Waves', 'slug' => 'periodic-motion-waves'],
                    ['name_bn' => 'তাপগতিবিদ্যা', 'name_en' => 'Thermodynamics', 'slug' => 'thermodynamics'],
                    ['name_bn' => 'স্থির তড়িৎ ও চল তড়িৎ', 'name_en' => 'Electrostatics & Current Electricity', 'slug' => 'electricity'],
                    ['name_bn' => 'জ্যামিতিক ও ভৌত আলোকবিজ্ঞান', 'name_en' => 'Geometrical & Physical Optics', 'slug' => 'optics'],
                    ['name_bn' => 'আধুনিক পদার্থবিজ্ঞান ও পরমাণু মডেল', 'name_en' => 'Modern Physics & Atomic Model', 'slug' => 'modern-physics'],
                    ['name_bn' => 'সেমিকন্ডাক্টর ও ইলেকট্রনিক্স', 'name_en' => 'Semiconductors & Electronics', 'slug' => 'electronics'],
                ],
            ],
            [
                'name_bn' => 'রসায়ন',
                'name_en' => 'Chemistry',
                'slug' => 'chemistry',
                'segment' => 'admission',
                'icon' => 'SparklesIcon',
                'order' => 4,
                'topics' => [
                    ['name_bn' => 'গুণগত রসায়ন ও পরমাণুর গঠন', 'name_en' => 'Qualitative Chemistry & Atomic Structure', 'slug' => 'qualitative-chemistry'],
                    ['name_bn' => 'পর্যায় সারণি ও রাসায়নিক বন্ধন', 'name_en' => 'Periodic Table & Chemical Bonding', 'slug' => 'periodic-table-bonding'],
                    ['name_bn' => 'রাসায়নিক পরিবর্তন ও সাম্যাবস্থা', 'name_en' => 'Chemical Changes & Equilibrium', 'slug' => 'chemical-equilibrium'],
                    ['name_bn' => 'কর্মমুখী রসায়ন', 'name_en' => 'Practical Chemistry', 'slug' => 'practical-chemistry'],
                    ['name_bn' => 'পরিবেশ রসায়ন ও গ্যাসের সূত্রাবলী', 'name_en' => 'Environmental Chemistry & Gas Laws', 'slug' => 'environmental-chemistry'],
                    ['name_bn' => 'জৈব যৌগ (Organic Chemistry)', 'name_en' => 'Organic Chemistry', 'slug' => 'organic-chemistry'],
                    ['name_bn' => 'পরিমাণগত রসায়ন (Stoichiometry)', 'name_en' => 'Quantitative Chemistry', 'slug' => 'quantitative-chemistry'],
                    ['name_bn' => 'তড়িৎ রসায়ন', 'name_en' => 'Electrochemistry', 'slug' => 'electrochemistry'],
                ],
            ],
            [
                'name_bn' => 'উচ্চতর গণিত',
                'name_en' => 'Higher Mathematics',
                'slug' => 'higher-math',
                'segment' => 'admission',
                'icon' => 'CalculatorIcon',
                'order' => 5,
                'topics' => [
                    ['name_bn' => 'ম্যাট্রিক্স ও নির্ণায়ক', 'name_en' => 'Matrices & Determinants', 'slug' => 'matrices-determinants'],
                    ['name_bn' => 'সরলরেখা ও বৃত্ত', 'name_en' => 'Straight Lines & Circles', 'slug' => 'lines-circles'],
                    ['name_bn' => 'ত্রিকোণমিতি (১ম ও ২য় পত্র)', 'name_en' => 'Trigonometry', 'slug' => 'trigonometry'],
                    ['name_bn' => 'ক্যালকুলাস (অন্তরীকরণ ও যোগজীকরণ)', 'name_en' => 'Calculus (Diff & Int)', 'slug' => 'calculus'],
                    ['name_bn' => 'বিন্যাস ও সমাবেশ', 'name_en' => 'Permutation & Combination', 'slug' => 'permutation-combination'],
                    ['name_bn' => 'বহুপদী ও বহুপদী সমীকরণ', 'name_en' => 'Polynomials & Equations', 'slug' => 'polynomials'],
                    ['name_bn' => 'কনিক্স (পরাবৃত্ত, উপবৃত্ত, অধিবৃত্ত)', 'name_en' => 'Conics', 'slug' => 'conics'],
                    ['name_bn' => 'স্থিতিবিদ্যা ও গতিবিদ্যা', 'name_en' => 'Statics & Dynamics', 'slug' => 'statics-dynamics'],
                    ['name_bn' => 'সম্ভাবনা (Probability)', 'name_en' => 'Probability', 'slug' => 'probability'],
                ],
            ],
            [
                'name_bn' => 'জীববিজ্ঞান',
                'name_en' => 'Biology',
                'slug' => 'biology',
                'segment' => 'admission',
                'icon' => 'ShieldCheckIcon',
                'order' => 6,
                'topics' => [
                    ['name_bn' => 'কোষ ও এর গঠন (Cell Biology)', 'name_en' => 'Cell Structure & Division', 'slug' => 'cell-biology'],
                    ['name_bn' => 'অণুজীব (Microorganisms - Virus & Bacteria)', 'name_en' => 'Microbiology', 'slug' => 'microbiology'],
                    ['name_bn' => 'উদ্ভিদ শারীরতত্ত্ব (Photosynthesis & Respiration)', 'name_en' => 'Plant Physiology', 'slug' => 'plant-physiology'],
                    ['name_bn' => 'টিস্যু ও টিস্যুতন্ত্র', 'name_en' => 'Tissue & Tissue Systems', 'slug' => 'tissue-systems'],
                    ['name_bn' => 'প্রাণীর বিভিন্নতা ও শ্রেণিবিন্যাস', 'name_en' => 'Animal Diversity & Classification', 'slug' => 'animal-diversity'],
                    ['name_bn' => 'পরিপাক ও শোষণ', 'name_en' => 'Digestion & Absorption', 'slug' => 'digestion'],
                    ['name_bn' => 'রক্ত ও সংবহন', 'name_en' => 'Circulation & Heart', 'slug' => 'circulation'],
                    ['name_bn' => 'জিনতত্ত্ব ও বিবর্তন (Genetics & Evolution)', 'name_en' => 'Genetics & Evolution', 'slug' => 'genetics-evolution'],
                ],
            ],
            [
                'name_bn' => 'গাণিতিক যুক্তি ও মানসিক দক্ষতা',
                'name_en' => 'Mathematical Reasoning & Mental Ability',
                'slug' => 'math-reasoning',
                'segment' => 'job',
                'icon' => 'CalculatorIcon',
                'order' => 7,
                'topics' => [
                    ['name_bn' => 'বাস্তব সংখ্যা, লসাগু-গসাগু ও শতকরা', 'name_en' => 'Numbers, LCM-HCF & Percentage', 'slug' => 'numbers-percentage'],
                    ['name_bn' => 'লাভ-ক্ষতি ও সুদকষা (Simple & Compound)', 'name_en' => 'Profit-Loss & Interest', 'slug' => 'profit-loss-interest'],
                    ['name_bn' => 'অনুপাত, সমানুপাত ও মিশ্রণ', 'name_en' => 'Ratio, Proportion & Mixture', 'slug' => 'ratio-mixture'],
                    ['name_bn' => 'ঐকিক নিয়ম, কাজ ও সময়', 'name_en' => 'Unitary Method, Work & Time', 'slug' => 'work-time'],
                    ['name_bn' => 'বীজগণিতীয় সূত্রাবলি ও উৎপাদক', 'name_en' => 'Algebraic Formulas & Factors', 'slug' => 'algebraic-formulas'],
                    ['name_bn' => 'সূচক, লগারিদম ও ধারা', 'name_en' => 'Exponents, Logarithms & Series', 'slug' => 'exponents-log-series'],
                    ['name_bn' => 'জ্যামিতি, কোণ, ত্রিভুজ ও পরিমিতি', 'name_en' => 'Geometry, Triangles & Mensuration', 'slug' => 'geometry-mensuration'],
                    ['name_bn' => 'মৌখিক যুক্তি ও ভাষাগত দক্ষতা', 'name_en' => 'Verbal Reasoning', 'slug' => 'verbal-reasoning'],
                    ['name_bn' => 'সংখ্যা ও বর্ণ ধারা সমস্যা', 'name_en' => 'Number & Letter Series', 'slug' => 'series-puzzles'],
                    ['name_bn' => 'দিক নির্ণয় ও স্থানিক সম্পর্ক', 'name_en' => 'Direction & Spatial Relations', 'slug' => 'direction-spatial'],
                ],
            ],
            [
                'name_bn' => 'বাংলাদেশ বিষয়াবলি',
                'name_en' => 'Bangladesh Affairs',
                'slug' => 'bangladesh-affairs',
                'segment' => 'both',
                'icon' => 'GlobeAltIcon',
                'order' => 8,
                'topics' => [
                    ['name_bn' => 'প্রাচীন কাল হতে ১৯৪৭ পর্যন্ত বাংলা', 'name_en' => 'Ancient to 1947 Bengal History', 'slug' => 'ancient-bengal-history'],
                    ['name_bn' => '১৯৫২ থেকে ১৯৭১ ও মুক্তিযুদ্ধ', 'name_en' => 'Language Movement to Liberation War', 'slug' => 'liberation-war'],
                    ['name_bn' => 'বাংলাদেশের সংবিধান ও মূলনীতি', 'name_en' => 'Constitution of Bangladesh', 'slug' => 'constitution-of-bangladesh'],
                    ['name_bn' => 'সরকার ব্যবস্থা ও প্রশাসন (আইন, শাসন ও বিচার)', 'name_en' => 'Government & Administration', 'slug' => 'government-administration'],
                    ['name_bn' => 'বাংলাদেশের অর্থনীতি, বাজেট ও পঞ্চবার্ষিক পরিকল্পনা', 'name_en' => 'Economy, Budget & Planning', 'slug' => 'economy-budget'],
                    ['name_bn' => 'জাতীয় সম্পদ, নদী ও ভৌগোলিক পরিচিতি', 'name_en' => 'Natural Resources & Geography', 'slug' => 'resources-geography'],
                    ['name_bn' => 'মেগা প্রকল্প ও জাতীয় অর্জন', 'name_en' => 'Mega Projects & Achievements', 'slug' => 'mega-projects'],
                ],
            ],
            [
                'name_bn' => 'আন্তর্জাতিক বিষয়াবলি',
                'name_en' => 'International Affairs',
                'slug' => 'international-affairs',
                'segment' => 'both',
                'icon' => 'GlobeAltIcon',
                'order' => 9,
                'topics' => [
                    ['name_bn' => 'জাতিসংঘ ও এর অঙ্গসংস্থা সমূহ', 'name_en' => 'United Nations & Agencies', 'slug' => 'united-nations'],
                    ['name_bn' => 'আন্তর্জাতিক ও আঞ্চলিক জোট (SAARC, ASEAN, NATO, BRICS)', 'name_en' => 'Regional & Global Alliances', 'slug' => 'global-alliances'],
                    ['name_bn' => 'আন্তর্জাতিক অর্থনৈতিক প্রতিষ্ঠান (IMF, World Bank, ADB)', 'name_en' => 'Global Financial Bodies', 'slug' => 'global-financial-bodies'],
                    ['name_bn' => 'আন্তর্জাতিক চুক্তি, সনদ ও সম্মেলন', 'name_en' => 'Treaties, Conventions & Summits', 'slug' => 'treaties-summits'],
                    ['name_bn' => 'বিশ্ব রাজনীতি ও সাম্প্রতিক বিরোধ/যুদ্ধ', 'name_en' => 'Geopolitics & Global Conflicts', 'slug' => 'geopolitics-conflicts'],
                    ['name_bn' => 'পরিবেশ ও জলবায়ু কূটনীতি (COP, IPCC)', 'name_en' => 'Climate Diplomacy', 'slug' => 'climate-diplomacy'],
                ],
            ],
            [
                'name_bn' => 'কম্পিউটার ও তথ্যপ্রযুক্তি',
                'name_en' => 'Computer & ICT',
                'slug' => 'computer-ict',
                'segment' => 'both',
                'icon' => 'CpuChipIcon',
                'order' => 10,
                'topics' => [
                    ['name_bn' => 'কম্পিউটার হার্ডওয়্যার ও মেমোরি ব্যবস্থা', 'name_en' => 'Hardware & Memory', 'slug' => 'hardware-memory'],
                    ['name_bn' => 'সংখ্যা পদ্ধতি ও ডিজিটাল লজিক গেট', 'name_en' => 'Number Systems & Logic Gates', 'slug' => 'number-systems-logic'],
                    ['name_bn' => 'কম্পিউটার নেটওয়ার্কিং ও ক্লাউড কম্পিউটিং', 'name_en' => 'Networking & Cloud', 'slug' => 'networking-cloud'],
                    ['name_bn' => 'ইন্টারনেট, ওয়েব ও সাইবার নিরাপত্তা', 'name_en' => 'Internet & Cyber Security', 'slug' => 'internet-cyber-security'],
                    ['name_bn' => 'অপারেটিং সিস্টেম ও সফটওয়্যার অ্যাপ্লিকেশন', 'name_en' => 'Operating Systems & Software', 'slug' => 'operating-systems'],
                    ['name_bn' => 'ডাটাবেজ ও কৃত্রিম বুদ্ধিমত্তা (AI Basics)', 'name_en' => 'Database & Artificial Intelligence', 'slug' => 'database-ai'],
                ],
            ],
            [
                'name_bn' => 'সাধারণ বিজ্ঞান',
                'name_en' => 'General Science',
                'slug' => 'general-science',
                'segment' => 'job',
                'icon' => 'FireIcon',
                'order' => 11,
                'topics' => [
                    ['name_bn' => 'দৈনন্দিন বিজ্ঞান ও পদার্থবিদ্যার সাধারণ ধারণা', 'name_en' => 'Everyday Physics Concepts', 'slug' => 'everyday-physics'],
                    ['name_bn' => 'বায়ুমণ্ডল, জলবায়ু ও মহাবিশ্ব', 'name_en' => 'Atmosphere & Universe', 'slug' => 'atmosphere-universe'],
                    ['name_bn' => 'খাদ্য, পুষ্টি, ভিটামিন ও রোগব্যাধি', 'name_en' => 'Food, Nutrition & Diseases', 'slug' => 'nutrition-diseases'],
                    ['name_bn' => 'দৈনন্দিন রসায়ন ও ধাতু-অধাতুর ব্যবহার', 'name_en' => 'Everyday Chemistry', 'slug' => 'everyday-chemistry'],
                ],
            ],
            [
                'name_bn' => 'নৈতিকতা, মূল্যবোধ ও সুশাসন',
                'name_en' => 'Ethics, Values & Good Governance',
                'slug' => 'ethics-governance',
                'segment' => 'job',
                'icon' => 'ShieldCheckIcon',
                'order' => 12,
                'topics' => [
                    ['name_bn' => 'নৈতিকতা ও মূল্যবোধের সংজ্ঞা ও ধারণা', 'name_en' => 'Ethics & Values Concepts', 'slug' => 'ethics-concepts'],
                    ['name_bn' => 'সুশাসন ও জাতীয় শুদ্ধাচার কৌশল', 'name_en' => 'Good Governance & Integrity', 'slug' => 'governance-integrity'],
                    ['name_bn' => 'ই-গভর্ন্যান্স ও তথ্য অধিকার', 'name_en' => 'E-Governance & Right to Info', 'slug' => 'egovernance-rti'],
                ],
            ],
            [
                'name_bn' => 'হিসাববিজ্ঞান ও ব্যবসায় শিক্ষা',
                'name_en' => 'Accounting & Business Studies',
                'slug' => 'accounting-business',
                'segment' => 'both',
                'icon' => 'BanknotesIcon',
                'order' => 13,
                'topics' => [
                    ['name_bn' => 'হিসাব সমীকরণ ও দুতরফা দাখিলা', 'name_en' => 'Accounting Equation & Double Entry', 'slug' => 'accounting-equation'],
                    ['name_bn' => 'জাবেদা, খতিয়ান ও রেওয়ামিল', 'name_en' => 'Journal, Ledger & Trial Balance', 'slug' => 'ledger-trial-balance'],
                    ['name_bn' => 'আর্থিক বিবরণী ও অনুপাত বিশ্লেষণ', 'name_en' => 'Financial Statements & Ratios', 'slug' => 'financial-ratios'],
                    ['name_bn' => 'ব্যবসায় সংগঠন ও ব্যবস্থাপনার নীতি', 'name_en' => 'Principles of Management', 'slug' => 'management-principles'],
                    ['name_bn' => 'ব্যাংকিং ও বীমার মৌলিক ধারণা', 'name_en' => 'Banking & Insurance Basics', 'slug' => 'banking-insurance-basics'],
                ],
            ],
        ];

        foreach ($subjects as $sData) {
            $topics = $sData['topics'] ?? [];
            unset($sData['topics']);

            $subject = QuestionSubject::updateOrCreate(
                ['slug' => $sData['slug']],
                $sData
            );

            $order = 1;
            foreach ($topics as $tData) {
                $tData['subject_id'] = $subject->id;
                $tData['segment'] = $subject->segment;
                $tData['order'] = $order++;
                QuestionTopic::updateOrCreate(
                    [
                        'subject_id' => $subject->id,
                        'slug' => $tData['slug'],
                    ],
                    $tData
                );
            }
        }
    }
}
