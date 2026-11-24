<script setup>
import { ref, computed } from 'vue';
import katex from 'katex';
import 'katex/dist/katex.min.css';
import { downloadBrandedPdf, openBrandedPrintWindow } from '@/utils/studentPdfExport';
import {
    MagnifyingGlassIcon,
    ClipboardDocumentIcon,
    CheckIcon,
    ArrowDownTrayIcon,
    PrinterIcon,
    CalculatorIcon,
    BookmarkIcon,
    SparklesIcon,
} from '@heroicons/vue/24/outline';

const props = defineProps({
    en: { type: Boolean, default: false },
});

const subTab = ref('formulas'); // 'formulas' | 'solver' | 'converter' | 'constants'
const selectedLevel = ref('hsc'); // 'ssc' | 'hsc' | 'job'
const formulaSubject = ref('all'); // 'all' | 'physics' | 'math' | 'chemistry'
const formulaSearch = ref('');
const selectedIds = ref([]);
const studentName = ref('');
const isGeneratingPdf = ref(false);
const copiedId = ref(null);

const formulas = [
    // ================= SSC LEVEL =================
    { id: 'ssc-p1', level: 'ssc', subject: 'physics', topic: 'গতি (Motion)', name: 'গতির মৌলিক সমীকরণসমূহ', latex: 'v = u + at, \\quad s = ut + \\frac{1}{2}at^2, \\quad v^2 = u^2 + 2as', plain: 'v = u + at; s = ut + 0.5at²; v² = u² + 2as', desc: 'u = আদিবেগ, v = শেষবেগ, a = সুষম ত্বরণ, t = সময়, s = সরণ' },
    { id: 'ssc-p2', level: 'ssc', subject: 'physics', topic: 'বল (Force)', name: 'নিউটনের ২য় সূত্র ও ভরবেগ', latex: 'F = ma, \\quad m_1 u_1 + m_2 u_2 = m_1 v_1 + m_2 v_2', plain: 'F = ma', desc: 'প্রযুক্ত বল ভর ও ত্বরণের গুণফলের সমান; সংঘর্ষে মোট ভরবেগ সংরক্ষিত থাকে' },
    { id: 'ssc-p3', level: 'ssc', subject: 'physics', topic: 'কাজ, ক্ষমতা ও শক্তি', name: 'গতিশক্তি, বিভবশক্তি ও ক্ষমতা', latex: 'E_k = \\frac{1}{2}mv^2, \\quad E_p = mgh, \\quad P = \\frac{W}{t}', plain: 'Ek = 0.5mv², Ep = mgh, P = W/t', desc: 'শক্তির একক জুল (J) এবং ক্ষমতার একক ওয়াট (W)' },
    { id: 'ssc-p4', level: 'ssc', subject: 'physics', topic: 'পদার্থের অবস্থা ও চাপ', name: 'চাপ, ঘনত্ব ও প্যাসকেলের সূত্র', latex: 'P = \\frac{F}{A} = h\\rho g, \\quad \\frac{F_1}{A_1} = \\frac{F_2}{A_2}', plain: 'P = F/A = hρg', desc: 'তরলের গভীরে চাপ উচ্চতা, ঘনত্ব ও অভিকর্ষজ ত্বরণের সমানুপাতিক' },
    { id: 'ssc-p5', level: 'ssc', subject: 'physics', topic: 'তরঙ্গ ও শব্দ', name: 'তরঙ্গবেগ ও প্রতিধ্বনি দূরত্ব', latex: 'v = f\\lambda, \\quad d = \\frac{vt}{2}', plain: 'v = fλ, d = vt/2', desc: 'ন্যূনতম প্রতিধ্বনি শোনার সময়কাল ০.১ সেকেন্ড (২০°C এ দূরত্ব ১৬.৬ মি.)' },
    { id: 'ssc-p6', level: 'ssc', subject: 'physics', topic: 'আলোর প্রতিফলন ও প্রতিসরণ', name: 'দর্পণ ও লেন্সের সমীকরণ ও ক্ষমতা', latex: '\\frac{1}{u} + \\frac{1}{v} = \\frac{1}{f}, \\quad P = \\frac{1}{f\\text{ (m)}}', plain: '1/u + 1/v = 1/f', desc: 'লেন্সের ক্ষমতার একক ডায়াপ্টার (D)' },
    { id: 'ssc-p7', level: 'ssc', subject: 'physics', topic: 'চলতড়িৎ', name: 'ওহমের সূত্র ও তড়িৎ ক্ষমতা', latex: 'V = IR, \\quad R_s = R_1 + R_2, \\quad \\frac{1}{R_p} = \\frac{1}{R_1} + \\frac{1}{R_2}', plain: 'V = IR', desc: 'শ্রেণি ও সমান্তরাল সমবায়ে তুল্য রোধ নির্ণয়' },

    { id: 'ssc-m1', level: 'ssc', subject: 'math', topic: 'বীজগণিত (Algebra)', name: 'বর্গ ও মান নির্ণয়ের সূত্র', latex: '(a+b)^2 = (a-b)^2 + 4ab, \\quad a^2 + b^2 = (a+b)^2 - 2ab', plain: '(a+b)² = (a-b)² + 4ab', desc: 'মান নির্ণয়ের সবচেয়ে বেশি ব্যবহৃত দুটি অভেদ' },
    { id: 'ssc-m2', level: 'ssc', subject: 'math', topic: 'বীজগণিত (Algebra)', name: 'ঘন নির্ণয়ের সূত্র', latex: 'a^3 + b^3 = (a+b)^3 - 3ab(a+b)', plain: 'a³ + b³ = (a+b)³ - 3ab(a+b)', desc: 'ঘনের মান নির্ণয় ও উৎপাদকে বিশ্লেষণ' },
    { id: 'ssc-m3', level: 'ssc', subject: 'math', topic: 'সমান্তর ও গুণোত্তর ধারা', name: 'n-তম পদ ও সমষ্টি', latex: 'S_n = \\frac{n}{2}\\{2a + (n-1)d\\}, \\quad S_n = \\frac{a(r^n - 1)}{r - 1}', plain: 'Sn = n/2[2a+(n-1)d]', desc: 'সমান্তর ধারার সমষ্টি এবং গুণোত্তর ধারার সমষ্টি (r > 1)' },
    { id: 'ssc-m4', level: 'ssc', subject: 'math', topic: 'ত্রিকোণমিতি', name: 'মৌলিক ত্রিকোণমিতিক অভেদসমূহ', latex: '\\sin^2\\theta + \\cos^2\\theta = 1, \\quad \\sec^2\\theta - \\tan^2\\theta = 1, \\quad \\csc^2\\theta - \\cot^2\\theta = 1', plain: 'sin²θ + cos²θ = 1', desc: 'সকল সূক্ষ্মকোণ θ এর জন্য প্রযোজ্য' },
    { id: 'ssc-m5', level: 'ssc', subject: 'math', topic: 'পরিমিতি (Mensuration)', name: 'সমবাহু ত্রিভুজ, বৃত্ত ও সিলিন্ডার', latex: 'A_{\\triangle} = \\frac{\\sqrt{3}}{4}a^2, \\quad A_{\\circ} = \\pi r^2, \\quad V_{\\text{cyl}} = \\pi r^2 h', plain: 'Area = (√3/4)a², πr², V = πr²h', desc: 'সমবাহু ত্রিভুজের ক্ষেত্রফল, বৃত্তের ক্ষেত্রফল ও বেলনের আয়তন' },

    { id: 'ssc-c1', level: 'ssc', subject: 'chemistry', topic: 'মোল ও রাসায়নিক গণনা', name: 'মোল সংখ্যা ও মোলার ঘনমাত্রা', latex: 'n = \\frac{w}{M} = \\frac{V_{\\text{STP}}}{22.4\\text{ L}}, \\quad S = \\frac{1000w}{MV(\\text{mL})}', plain: 'n = w/M, S = 1000w/(MV)', desc: 'w = ভর (g), M = আণবিক ভর, S = মোলারিটি (M)' },
    { id: 'ssc-c2', level: 'ssc', subject: 'chemistry', topic: 'শতকরা সংযুতি ও pH', name: 'মৌলের শতকরা সংযুতি ও pH', latex: '\\%\\text{ মৌল} = \\frac{\\text{পারমাণবিক ভর} \\times \\text{পরমাণু সংখ্যা}}{\\text{আণবিক ভর}} \\times 100, \\quad \\text{pH} = -\\log_{10}[\\text{H}^+]', plain: 'pH = -log[H+]', desc: 'দ্রবণের অম্লত্ব ও ক্ষারকত্ব নির্ণয়' },

    // ================= HSC & ADMISSION LEVEL =================
    { id: 'hsc-p1', level: 'hsc', subject: 'physics', topic: 'ভেক্টর (Vectors)', name: 'ভেক্টর লব্ধির মান ও দিক', latex: 'R = \\sqrt{P^2 + Q^2 + 2PQ\\cos\\alpha}, \\quad \\tan\\theta = \\frac{Q\\sin\\alpha}{P + Q\\cos\\alpha}', plain: 'R = √(P² + Q² + 2PQ cosα)', desc: 'সামান্তরিক সূত্র থেকে দুটি ভেক্টরের লব্ধি ও কোণ নির্ণয়' },
    { id: 'hsc-p2', level: 'hsc', subject: 'physics', topic: 'প্রাসের গতি (Projectile)', name: 'উড্ডয়নকাল, সর্বোচ্চ উচ্চতা ও পাল্লা', latex: 'T = \\frac{2u\\sin\\theta}{g}, \\quad H = \\frac{u^2\\sin^2\\theta}{2g}, \\quad R = \\frac{u^2\\sin 2\\theta}{g}', plain: 'R = u² sin(2θ) / g', desc: 'θ = ৪৫° হলে অনুভূমিক পাল্লা R সর্বোচ্চ হয় (R_max = u²/g)' },
    { id: 'hsc-p3', level: 'hsc', subject: 'physics', topic: 'বৃত্তাকার গতি ও বলবিদ্যা', name: 'কেন্দ্রমুখী বল ও ব্যাংকিং কোণ', latex: 'F_c = \\frac{mv^2}{r} = m\\omega^2 r, \\quad \\tan\\theta = \\frac{v^2}{rg}', plain: 'Fc = mv²/r, tanθ = v²/(rg)', desc: 'রাস্তার বাঁকে নিরাপদ সর্বোচ্চ বেগ নির্ণয়' },
    { id: 'hsc-p4', level: 'hsc', subject: 'physics', topic: 'মহাকর্ষ ও মুক্তিবেগ', name: 'অভিকর্ষজ ত্বরণ ও মুক্তিবেগ', latex: 'g = \\frac{GM}{R^2}, \\quad v_e = \\sqrt{2gR} = \\sqrt{\\frac{2GM}{R}}', plain: 've = √(2gR) ≈ 11.2 km/s', desc: 'পৃথিবীর পৃষ্ঠে মুক্তিবেগের মান প্রায় ১১.২ কিমি/সেকেন্ড' },
    { id: 'hsc-p5', level: 'hsc', subject: 'physics', topic: 'সরল ছন্দিত স্পন্দন (SHM)', name: 'দোলনকাল ও বেগ সমীকরণ', latex: 'T = 2\\pi\\sqrt{\\frac{L}{g}}, \\quad v = \\omega\\sqrt{A^2 - x^2}', plain: 'T = 2π√(L/g)', desc: 'সরল দোলকের পর্যায়কাল ও যেকোনো অবস্থানে কণার বেগ' },
    { id: 'hsc-p6', level: 'hsc', subject: 'physics', topic: 'তাপগতিবিদ্যা (Thermodynamics)', name: '১ম সূত্র, রুদ্ধতাপীয় ও কার্নো দক্ষতা', latex: 'dQ = dU + dW, \\quad PV^\\gamma = \\text{ধ্রুবক}, \\quad \\eta = \\left(1 - \\frac{T_2}{T_1}\\right)\\times 100\\%', plain: 'η = (1 - T2/T1) × 100%', desc: 'কার্নো ইঞ্জিনের দক্ষতায় তাপমাত্রা অবশ্যই কেলভিন (K) এককে বসাতে হবে' },
    { id: 'hsc-p7', level: 'hsc', subject: 'physics', topic: 'স্থির ও চলতড়িৎ', name: 'কুলম্বের সূত্র, ধারকত্ব ও জুলের তাপীয় ক্রিয়া', latex: 'F = \\frac{1}{4\\pi\\varepsilon_0}\\frac{q_1 q_2}{r^2}, \\quad U = \\frac{1}{2}CV^2, \\quad H = I^2 Rt', plain: 'U = 0.5 C V², H = I²Rt', desc: 'সঞ্চিত স্থিতিশক্তি ও তড়িৎ প্রবাহে উৎপন্ন তাপ' },
    { id: 'hsc-p8', level: 'hsc', subject: 'physics', topic: 'আধুনিক পদার্থবিজ্ঞান ও পরমাণু', name: 'আইনস্টাইনের ফটোতড়িৎ ও তেজস্ক্রিয় ক্ষয়', latex: 'hf = \\phi_0 + K_{\\max}, \\quad N = N_0 e^{-\\lambda t}, \\quad T_{1/2} = \\frac{0.693}{\\lambda}', plain: 'T₁/₂ = 0.693 / λ', desc: 'ফোটন শক্তি, অর্ধায়ু এবং ক্ষয় ধ্রুবকের সম্পর্ক' },

    { id: 'hsc-m1', level: 'hsc', subject: 'math', topic: 'সরলরেখা (Straight Line)', name: 'ঢাল, দূরত্ব ও ত্রিভুজের ক্ষেত্রফল', latex: 'm = \\frac{y_2 - y_1}{x_2 - x_1}, \\quad d = \\frac{|ax_1 + by_1 + c|}{\\sqrt{a^2 + b^2}}', plain: 'd = |ax₁+by₁+c|/√(a²+b²)', desc: 'বিন্দু থেকে সরলরেখার লম্ব দূরত্ব নির্ণয়' },
    { id: 'hsc-m2', level: 'hsc', subject: 'math', topic: 'ত্রিকোণমিতি (Trigonometry)', name: 'যৌগিক ও গুণিতক কোণের সূত্র', latex: '\\sin 2A = 2\\sin A\\cos A = \\frac{2\\tan A}{1+\\tan^2 A}, \\quad \\cos 2A = \\cos^2 A - \\sin^2 A', plain: 'sin2A = 2sinA cosA', desc: 'ক্যালকুলাস ও ভর্তি পরীক্ষায় সর্বাধিক ব্যবহৃত ত্রিকোণমিতিক সূত্র' },
    { id: 'hsc-m3', level: 'hsc', subject: 'math', topic: 'অন্তরীকরণ (Differentiation)', name: 'লিমিট ও মৌলিক অন্তরীকরণ সূত্র', latex: '\\lim_{x\\to 0}\\frac{\\sin x}{x} = 1, \\quad \\frac{d}{dx}(x^n) = nx^{n-1}, \\quad \\frac{d}{dx}(e^x) = e^x, \\quad \\frac{d}{dx}(\\ln x) = \\frac{1}{x}', plain: 'd/dx(xⁿ) = n xⁿ⁻¹', desc: 'ফাংশনের ঢাল ও পরিবর্তনের হার নির্ণয়' },
    { id: 'hsc-m4', level: 'hsc', subject: 'math', topic: 'যোগজীকরণ (Integration)', name: 'মৌলিক ইন্টিগ্রেশন ও বাই-পার্টস', latex: '\\int x^n dx = \\frac{x^{n+1}}{n+1}+c, \\quad \\int u\\,dv = uv - \\int v\\,du', plain: '∫ xⁿ dx = xⁿ⁺¹/(n+1) + c', desc: 'ক্ষেত্রফল ও অনির্দিষ্ট যোগজ নির্ণয়ের মূল সূত্র' },
    { id: 'hsc-m5', level: 'hsc', subject: 'math', topic: 'দ্বিঘাত সমীকরণ ও জটিল সংখ্যা', name: 'মূলের যোগফল, গুণফল ও অয়লার রূপ', latex: '\\alpha + \\beta = -\\frac{b}{a}, \\quad \\alpha\\beta = \\frac{c}{a}, \\quad e^{i\\theta} = \\cos\\theta + i\\sin\\theta', plain: 'α+β = -b/a, αβ = c/a', desc: 'ax² + bx + c = 0 সমীকরণের মূলদ্বয়ের সম্পর্ক' },
    { id: 'hsc-m6', level: 'hsc', subject: 'math', topic: 'বিন্যাস ও সমাবেশ', name: 'পারমুটেশন ও কম্বিনেশন', latex: '{}^n P_r = \\frac{n!}{(n-r)!}, \\quad {}^n C_r = \\frac{n!}{r!(n-r)!}', plain: 'nCr = n! / [r!(n-r)!]', desc: 'সাজানো (Permutation) এবং দল গঠন (Combination)' },

    { id: 'hsc-c1', level: 'hsc', subject: 'chemistry', topic: 'পরিবেশ রসায়ন (Gas Laws)', name: 'আদর্শ গ্যাস, ব্যাপন ও গতিশক্তি', latex: 'PV = nRT, \\quad \\frac{r_1}{r_2} = \\sqrt{\\frac{M_2}{M_1}}, \\quad E_k = \\frac{3}{2}nRT', plain: 'PV = nRT, r₁/r₂ = √(M₂/M₁)', desc: 'গ্রাহামের ব্যাপন সূত্র ও গ্যাসের মোট গতিশক্তি' },
    { id: 'hsc-c2', level: 'hsc', subject: 'chemistry', topic: 'রাসায়নিক সাম্যাবস্থা ও বাফার', name: 'Kp-Kc সম্পর্ক ও হেন্ডারসন সমীকরণ', latex: 'K_p = K_c(RT)^{\\Delta n}, \\quad \\text{pH} = \\text{p}K_a + \\log_{10}\\frac{[\\text{Salt}]}{[\\text{Acid}]}', plain: 'pH = pKa + log([Salt]/[Acid])', desc: 'অম্লীয় বাফার দ্রবণের pH নির্ণয়ের হেন্ডারসন-হ্যাসেলবাক সমীকরণ' },
    { id: 'hsc-c3', level: 'hsc', subject: 'chemistry', topic: 'তড়িৎ রসায়ন (Electrochemistry)', name: 'ফ্যারাডের ১ম সূত্র ও নার্নস্ট সমীকরণ', latex: 'W = ZIt = \\frac{MIt}{nF}, \\quad E_{\\text{cell}} = E^\\circ_{\\text{cell}} - \\frac{0.0592}{n}\\log_{10} Q', plain: 'W = MIt / (nF)', desc: '১ ফ্যারাডে (F) = ৯৬,৫০০ কুলম্ব (C/mol)' },
    { id: 'hsc-c4', level: 'hsc', subject: 'chemistry', topic: 'পরিমাণগত রসায়ন', name: 'টাইট্রেশন ও লঘুকরণ সূত্র', latex: 'V_1 S_1 e_1 = V_2 S_2 e_2, \\quad \\text{ppm} = S \\times M \\times 1000', plain: 'V₁S₁e₁ = V₂S₂e₂', desc: 'এসিড-ক্ষার প্রশমন ও মোলারিটি থেকে ppm রূপান্তর' },

    // ================= BCS & JOB MATH SHORTCUTS =================
    { id: 'job-m1', level: 'job', subject: 'math', topic: 'শতকরা ও লাভ-ক্ষতি (Percentage)', name: 'ক্রমিক শতকরা পরিবর্তন শর্টকাট', latex: '\\text{মোট পরিবর্তন} = \\left(A + B + \\frac{AB}{100}\\right)\\%', plain: 'A + B + (AB/100) %', desc: 'দাম বা দৈর্ঘ্য পরপর A% ও B% বাড়লে/কমলে (কমলে ঋণাত্মক চিহ্ন বসবে)' },
    { id: 'job-m2', level: 'job', subject: 'math', topic: 'সরল ও যৌগিক মুনাফা', name: 'সরল মুনাফা, চক্রবৃদ্ধি ও ২ বছরের পার্থক্য', latex: 'I = Pnr, \\quad C = P(1+r)^n, \\quad C - I_{\\text{2yr}} = P\\left(\\frac{R}{100}\\right)^2', plain: '2yr Diff = P(R/100)²', desc: 'বিসিএস ও ব্যাংক পরীক্ষায় ২ বছরের চক্রবৃদ্ধি ও সরল সুদের পার্থক্য বের করার শর্টকাট' },
    { id: 'job-m3', level: 'job', subject: 'math', topic: 'ঐকিক ও কাজ-সময় (Work & Time)', name: 'দুইজনে একত্রে কাজ শেষ করার সময়', latex: 'T = \\frac{xy}{x + y}, \\quad \\frac{M_1 D_1 H_1}{W_1} = \\frac{M_2 D_2 H_2}{W_2}', plain: 'T = xy / (x + y)', desc: 'ক ও খ পৃথকভাবে x ও y দিনে করলে একত্রে করার সময়' },
    { id: 'job-m4', level: 'job', subject: 'math', topic: 'নৌকা-স্রোত ও ট্রেন-গতিবেগ', name: 'নৌকার বেগ, স্রোতের বেগ ও গড় দ্রুতি', latex: 'u = \\frac{V_d + V_u}{2}, \\quad v = \\frac{V_d - V_u}{2}, \\quad V_{\\text{avg}} = \\frac{2xy}{x+y}', plain: 'Avg Speed = 2xy/(x+y)', desc: 'অনুকূলে বেগ Vd ও প্রতিকূলে বেগ Vu জানা থাকলে নৌকা ও স্রোতের বেগ' },
    { id: 'job-m5', level: 'job', subject: 'math', topic: 'সংখ্যা পদ্ধতি ও ধারা', name: 'স্বাভাবিক সংখ্যা, বর্গ ও ঘনের সমষ্টি', latex: '\\sum n = \\frac{n(n+1)}{2}, \\quad \\sum n^2 = \\frac{n(n+1)(2n+1)}{6}, \\quad \\sum n^3 = \\left[\\frac{n(n+1)}{2}\\right]^2', plain: '1+2+...+n = n(n+1)/2', desc: 'প্রথম n সংখ্যক স্বাভাবিক সংখ্যার সমষ্টি, বর্গের সমষ্টি ও ঘনের সমষ্টি' },
    { id: 'job-m6', level: 'job', subject: 'math', topic: 'ল.সা.গু, গ.সা.গু ও কোণ', name: 'ঘড়ির কাঁটার মধ্যবর্তী কোণ ও বহুভুজ', latex: '\\theta = \\left|\\frac{60H - 11M}{2}\\right|, \\quad \\text{কর্ণের সংখ্যা} = \\frac{n(n-3)}{2}', plain: 'Angle = |(60H - 11M)/2|', desc: 'ঘণ্টা (H) ও মিনিটের (M) কাঁটার মধ্যবর্তী কোণ (১৮০° এর বেশি হলে ৩৬০° থেকে বিয়োগ)' },
    { id: 'job-p1', level: 'job', subject: 'physics', topic: 'দৈনন্দিন বিজ্ঞান (General Science)', name: 'তাপমাত্রা স্কেল ও বিদ্যুৎ বিল হিসাব', latex: '\\frac{C}{5} = \\frac{F - 32}{9} = \\frac{K - 273}{5}, \\quad \\text{BOT Unit (kWh)} = \\frac{W \\times h}{1000}', plain: 'C/5 = (F-32)/9; Unit = Wh/1000', desc: 'বিসিএস ও প্রাইমারি বিজ্ঞানে সর্বাধিক রিপিটেড দুটি সমীকরণ' },
];

function renderLatex(expr) {
    try {
        return katex.renderToString(expr, { displayMode: false, throwOnError: false });
    } catch {
        return expr;
    }
}

const filteredFormulas = computed(() => {
    return formulas.filter((f) => {
        if (f.level !== selectedLevel.value) return false;
        if (formulaSubject.value !== 'all' && f.subject !== formulaSubject.value) return false;
        if (!formulaSearch.value.trim()) return true;
        const q = formulaSearch.value.toLowerCase();
        return (
            f.name.toLowerCase().includes(q) ||
            f.topic.toLowerCase().includes(q) ||
            f.plain.toLowerCase().includes(q) ||
            f.desc.toLowerCase().includes(q)
        );
    });
});

function toggleSelectFormula(id) {
    const idx = selectedIds.value.indexOf(id);
    if (idx >= 0) selectedIds.value.splice(idx, 1);
    else selectedIds.value.push(id);
}

function selectAllVisible() {
    const visibleIds = filteredFormulas.value.map((f) => f.id);
    const allPicked = visibleIds.every((id) => selectedIds.value.includes(id));
    if (allPicked) {
        selectedIds.value = selectedIds.value.filter((id) => !visibleIds.includes(id));
    } else {
        selectedIds.value = Array.from(new Set([...selectedIds.value, ...visibleIds]));
    }
}

function copyFormula(plain, id) {
    navigator.clipboard.writeText(plain);
    copiedId.value = id;
    setTimeout(() => { copiedId.value = null; }, 1800);
}

const exportList = computed(() => {
    if (selectedIds.value.length > 0) {
        return formulas.filter((f) => selectedIds.value.includes(f.id));
    }
    return filteredFormulas.value;
});

const levelLabels = {
    ssc: 'SSC (৯ম-১০ম শ্রেণি)',
    hsc: 'HSC ও বিশ্ববিদ্যালয় ভর্তি',
    job: 'বিসিএস ও সরকারি চাকরি শর্টকাট',
};

function buildFormulaSheetHtml() {
    const items = exportList.value;
    const rows = items.map((item, i) => `
        <div style="border:1px solid #cbd5e1;border-radius:10px;padding:10px 14px;margin-bottom:10px;page-break-inside:avoid;background:#f8fafc;">
            <div style="display:flex;justify-content:space-between;font-size:11px;color:#0284c7;font-weight:700;margin-bottom:3px;">
                <span>${i + 1}. ${item.topic} — ${item.name}</span>
                <span style="text-transform:uppercase;color:#64748b;">${item.subject}</span>
            </div>
            <div style="padding:8px 10px;background:#ffffff;border:1px solid #e2e8f0;border-radius:8px;font-size:14px;margin:4px 0;">
                ${renderLatex(item.latex)}
            </div>
            <div style="font-size:11.5px;color:#475569;">${item.desc}</div>
        </div>
    `).join('');

    return `<div>${rows}</div>`;
}

async function handleDownloadPdf() {
    if (!exportList.value.length || isGeneratingPdf.value) return;
    isGeneratingPdf.value = true;
    try {
        await downloadBrandedPdf({
            title: 'বিজ্ঞান ও গণিত সূত্রাবলি চিটশিট (Formula Sheet)',
            subtitle: `${levelLabels[selectedLevel.value]} • মোট ${exportList.value.length}টি নির্বাচিত সূত্র`,
            studentName: studentName.value.trim(),
            bodyHtml: buildFormulaSheetHtml(),
            fileName: `Sikhun_Formula_Sheet_${selectedLevel.value.toUpperCase()}.pdf`,
        });
    } finally {
        isGeneratingPdf.value = false;
    }
}

function handlePrintSheet() {
    if (!exportList.value.length) return;
    openBrandedPrintWindow({
        title: 'বিজ্ঞান ও গণিত সূত্রাবলি চিটশিট (Formula Sheet)',
        subtitle: `${levelLabels[selectedLevel.value]} • মোট ${exportList.value.length}টি সূত্র`,
        studentName: studentName.value.trim(),
        bodyHtml: buildFormulaSheetHtml(),
    });
}

// ================= INTERACTIVE FORMULA SOLVER =================
const solverType = ref('motion'); // 'motion' | 'quadratic' | 'ohm' | 'compound'
const mU = ref(10);
const mA = ref(2);
const mT = ref(5);
const motionAns = computed(() => {
    const u = Number(mU.value) || 0;
    const a = Number(mA.value) || 0;
    const t = Number(mT.value) || 0;
    const v = u + a * t;
    const s = u * t + 0.5 * a * t * t;
    return { v: v.toFixed(2), s: s.toFixed(2) };
});

const qA = ref(1);
const qB = ref(-5);
const qC = ref(6);
const quadAns = computed(() => {
    const a = Number(qA.value);
    const b = Number(qB.value) || 0;
    const c = Number(qC.value) || 0;
    if (!a) return { status: 'invalid', msg: 'a এর মান ০ হতে পারবে না' };
    const d = b * b - 4 * a * c;
    if (d < 0) return { status: 'complex', d, msg: 'নিশ্চায়ক ঋণাত্মক (জটিল মূল)' };
    const x1 = (-b + Math.sqrt(d)) / (2 * a);
    const x2 = (-b - Math.sqrt(d)) / (2 * a);
    return { status: 'real', d: d.toFixed(2), x1: x1.toFixed(3), x2: x2.toFixed(3) };
});

const cP = ref(10000);
const cR = ref(10);
const cN = ref(2);
const compoundAns = computed(() => {
    const P = Number(cP.value) || 0;
    const r = (Number(cR.value) || 0) / 100;
    const n = Number(cN.value) || 0;
    const simple = P * n * r;
    const totalC = P * Math.pow(1 + r, n);
    const compInterest = totalC - P;
    return {
        simple: simple.toFixed(2),
        compInterest: compInterest.toFixed(2),
        totalC: totalC.toFixed(2),
        diff: (compInterest - simple).toFixed(2),
    };
});

// ================= PRO UNIT CONVERTER =================
const converterType = ref('length');
const convInput = ref(1);
const convFrom = ref('m');
const convTo = ref('km');

const unitCategories = [
    { key: 'length', labelBn: 'দৈর্ঘ্য', labelEn: 'Length' },
    { key: 'weight', labelBn: 'ভর / ওজন', labelEn: 'Mass' },
    { key: 'temp', labelBn: 'তাপমাত্রা', labelEn: 'Temperature' },
    { key: 'pressure', labelBn: 'চাপ (Pressure)', labelEn: 'Pressure' },
    { key: 'energy', labelBn: 'শক্তি (Energy)', labelEn: 'Energy' },
    { key: 'land', labelBn: 'জমি ও ক্ষেত্রফল', labelEn: 'Area & Land' },
    { key: 'volume', labelBn: 'আয়তন', labelEn: 'Volume' },
    { key: 'angle', labelBn: 'কোণ', labelEn: 'Angle' },
    { key: 'speed', labelBn: 'গতিবেগ', labelEn: 'Speed' },
    { key: 'data', labelBn: 'ডিজিটাল ডাটা', labelEn: 'Data' },
];

const unitOptions = computed(() => {
    switch (converterType.value) {
        case 'length':
            return [
                { key: 'm', label: 'মিটার (m)', factor: 1 },
                { key: 'km', label: 'কিলোমিটার (km)', factor: 1000 },
                { key: 'cm', label: 'সেন্টিমিটার (cm)', factor: 0.01 },
                { key: 'mm', label: 'মিলিমিটার (mm)', factor: 0.001 },
                { key: 'nm', label: 'ন্যানোমিটার (nm)', factor: 1e-9 },
                { key: 'ft', label: 'ফুট (ft)', factor: 0.3048 },
                { key: 'inch', label: 'ইঞ্চি (in)', factor: 0.0254 },
                { key: 'mile', label: 'মাইল (mile)', factor: 1609.344 },
            ];
        case 'weight':
            return [
                { key: 'kg', label: 'কিলোগ্রাম (kg)', factor: 1 },
                { key: 'g', label: 'গ্রাম (g)', factor: 0.001 },
                { key: 'mg', label: 'মিলিগ্রাম (mg)', factor: 1e-6 },
                { key: 'lb', label: 'পাউন্ড (lb)', factor: 0.45359237 },
                { key: 'ton', label: 'মেট্রিক টন (t)', factor: 1000 },
            ];
        case 'pressure':
            return [
                { key: 'atm', label: 'অ্যাটমোস্ফিয়ার (atm)', factor: 101325 },
                { key: 'pa', label: 'প্যাসকেল (Pa)', factor: 1 },
                { key: 'kpa', label: 'কিলোপ্যাসকেল (kPa)', factor: 1000 },
                { key: 'bar', label: 'বার (bar)', factor: 100000 },
                { key: 'mmhg', label: 'মিমি পারদ (mmHg / Torr)', factor: 133.322 },
            ];
        case 'energy':
            return [
                { key: 'j', label: 'জুল (J)', factor: 1 },
                { key: 'kj', label: 'কিলোজুল (kJ)', factor: 1000 },
                { key: 'cal', label: 'ক্যালরি (cal)', factor: 4.184 },
                { key: 'kcal', label: 'কিলোক্যালরি (kcal)', factor: 4184 },
                { key: 'kwh', label: 'কিলোওয়াট-ঘণ্টা (kWh / Unit)', factor: 3600000 },
                { key: 'ev', label: 'ইলেকট্রন-ভোল্ট (eV)', factor: 1.602176634e-19 },
            ];
        case 'land':
            return [
                { key: 'decimal', label: 'শতক / শতাংশ (Decimal)', factor: 40.468564 },
                { key: 'katha', label: 'কাঠা (Katha - 720 sq.ft)', factor: 66.890188 },
                { key: 'bigha', label: 'বিঘা (Bigha - 20 Katha)', factor: 1337.80377 },
                { key: 'acre', label: 'একর (Acre - 100 Decimal)', factor: 4046.8564 },
                { key: 'sqft', label: 'বর্গফুট (sq. ft)', factor: 0.09290304 },
                { key: 'sqm', label: 'বর্গমিটার (m²)', factor: 1 },
            ];
        case 'volume':
            return [
                { key: 'l', label: 'লিটার (L / dm³)', factor: 0.001 },
                { key: 'ml', label: 'মিলিলিটার (mL / cm³)', factor: 0.000001 },
                { key: 'm3', label: 'ঘনমিটার (m³)', factor: 1 },
            ];
        case 'angle':
            return [
                { key: 'deg', label: 'ডিগ্রি (Degree °)', factor: Math.PI / 180 },
                { key: 'rad', label: 'রেডিয়ান (Radian)', factor: 1 },
            ];
        case 'speed':
            return [
                { key: 'ms', label: 'মিটার/সেকেন্ড (m/s)', factor: 1 },
                { key: 'kmh', label: 'কিমি/ঘণ্টা (km/h)', factor: 1 / 3.6 },
                { key: 'mph', label: 'মাইল/ঘণ্টা (mph)', factor: 0.44704 },
            ];
        case 'data':
            return [
                { key: 'B', label: 'বাইট (B)', factor: 1 },
                { key: 'KB', label: 'কিলোবাইট (KB)', factor: 1024 },
                { key: 'MB', label: 'মেগাবাইট (MB)', factor: 1048576 },
                { key: 'GB', label: 'গিগাবাইট (GB)', factor: 1073741824 },
            ];
        case 'temp':
            return [
                { key: 'c', label: 'সেলসিয়াস (°C)' },
                { key: 'f', label: 'ফারেনহাইট (°F)' },
                { key: 'k', label: 'কেলভিন (K)' },
            ];
        default:
            return [];
    }
});

function handleTypeChange() {
    const opts = unitOptions.value;
    if (opts.length >= 2) {
        convFrom.value = opts[0].key;
        convTo.value = opts[1].key;
    }
}

const convertedValue = computed(() => {
    const val = Number(convInput.value);
    if (isNaN(val)) return '0';
    if (converterType.value === 'temp') {
        let c = val;
        if (convFrom.value === 'f') c = (val - 32) * (5 / 9);
        else if (convFrom.value === 'k') c = val - 273.15;
        if (convTo.value === 'c') return c.toFixed(2);
        if (convTo.value === 'f') return (c * (9 / 5) + 32).toFixed(2);
        return (c + 273.15).toFixed(2);
    }
    const f = unitOptions.value.find((u) => u.key === convFrom.value);
    const t = unitOptions.value.find((u) => u.key === convTo.value);
    if (!f || !t) return '0';
    const res = (val * f.factor) / t.factor;
    if (res === 0) return '0';
    if (Math.abs(res) < 0.0001 || Math.abs(res) > 1e8) return res.toExponential(4);
    return Number(res.toFixed(6)).toString();
});

const physicalConstants = [
    { name: 'আলোর বেগ (Speed of Light, c)', value: '3.0 × 10⁸ m/s' },
    { name: 'অভিকর্ষজ ত্বরণ (Standard Gravity, g)', value: '9.8 m/s²' },
    { name: 'মহাকর্ষীয় ধ্রুবক (Gravitational Constant, G)', value: '6.674 × 10⁻¹¹ N·m²/kg²' },
    { name: 'প্ল্যাঙ্কের ধ্রুবক (Planck Constant, h)', value: '6.626 × 10⁻³⁴ J·s' },
    { name: 'অ্যাভোগাড্রো সংখ্যা (Avogadro Number, N_A)', value: '6.022 × 10²³ mol⁻¹' },
    { name: 'মোলার গ্যাস ধ্রুবক (Gas Constant, R)', value: '8.314 J/(mol·K) = 0.0821 L·atm/(mol·K)' },
    { name: 'ইলেকট্রনের চার্জ (Electron Charge, e)', value: '1.602 × 10⁻¹⁹ C' },
    { name: 'ইলেকট্রনের ভর (Electron Mass, m_e)', value: '9.109 × 10⁻³¹ kg' },
    { name: '১ ফ্যারাডে ধ্রুবক (Faraday Constant, F)', value: '96,485 C/mol' },
    { name: 'শূন্য মাধ্যমের ভেদনযোগ্যতা (ε₀)', value: '8.854 × 10⁻¹² C²/(N·m²)' },
];
</script>

<template>
    <div class="card p-5 sm:p-8 rounded-3xl border border-gray-200 dark:border-white/10 bg-white dark:bg-[#161a26] text-gray-900 dark:text-white shadow-xl space-y-6">
        <!-- Top Header & Subtabs -->
        <div class="flex flex-col lg:flex-row lg:items-center justify-between border-b border-gray-200 dark:border-white/10 pb-5 gap-4">
            <div>
                <h2 class="font-heading text-xl md:text-2xl font-extrabold flex items-center gap-2.5">
                    <CalculatorIcon class="w-6 h-6 text-cyan-500 shrink-0" />
                    <span>{{ en ? 'Science & Math Formula Bank + Unit Converter' : 'বিজ্ঞান ও গণিত সূত্র ব্যাংক এবং একক কনভার্টার' }}</span>
                </h2>
                <p class="text-xs text-gray-600 dark:text-gray-300 mt-1">
                    {{ en
                        ? 'SSC, HSC/Admission & Job Math formulas with 1-click custom A4 Cheat Sheet PDF download.'
                        : 'SSC, HSC/ভর্তি ও চাকরি পরীক্ষার অধ্যায়ভিত্তিক সূত্র, লাইভ সলভার, একক কনভার্টার এবং ১-ক্লিকে A4 PDF ডাউনলোড।'
                    }}
                </p>
            </div>

            <div class="flex flex-wrap items-center gap-2">
                <button
                    v-for="tab in [
                        { key: 'formulas', label: en ? 'Formula Bank & PDF' : 'সূত্র ব্যাংক ও PDF' },
                        { key: 'solver', label: en ? 'Live Solver' : 'সূত্র ক্যালকুলেটর' },
                        { key: 'converter', label: en ? 'Unit Converter' : 'একক কনভার্টার' },
                        { key: 'constants', label: en ? 'Constants' : 'ভৌত ধ্রুবক' },
                    ]"
                    :key="tab.key"
                    type="button"
                    class="px-3.5 py-2 rounded-xl text-xs font-bold transition border"
                    :class="subTab === tab.key ? 'bg-cyan-600 text-white border-cyan-600 shadow-md' : 'bg-gray-100 dark:bg-white/5 text-gray-700 dark:text-gray-300 border-gray-200 dark:border-white/10'"
                    @click="subTab = tab.key"
                >
                    {{ tab.label }}
                </button>
            </div>
        </div>

        <!-- ==================== 1. FORMULA BANK + PDF MAKER ==================== -->
        <div v-if="subTab === 'formulas'" class="space-y-5">
            <!-- Level Switcher + PDF Export Bar -->
            <div class="p-4 rounded-2xl bg-gradient-to-r from-cyan-500/10 via-indigo-500/5 to-amber-500/10 border border-cyan-500/20 flex flex-col lg:flex-row lg:items-center justify-between gap-4">
                <div class="flex flex-wrap items-center gap-2">
                    <button
                        v-for="lvl in [
                            { key: 'ssc', label: en ? 'SSC (9-10)' : 'SSC (৯ম-১০ম)' },
                            { key: 'hsc', label: en ? 'HSC & Admission' : 'HSC ও ভর্তি প্রস্তুতি' },
                            { key: 'job', label: en ? 'BCS & Job Shortcut' : 'বিসিএস ও জব শর্টকাট' },
                        ]"
                        :key="lvl.key"
                        type="button"
                        class="px-3.5 py-2 rounded-xl text-xs font-extrabold transition border"
                        :class="selectedLevel === lvl.key ? 'bg-slate-900 dark:bg-amber-500 text-white dark:text-slate-950 border-transparent shadow-sm' : 'bg-white dark:bg-[#0e121d] text-gray-700 dark:text-gray-300 border-gray-200 dark:border-white/10'"
                        @click="selectedLevel = lvl.key; formulaSubject = 'all'"
                    >
                        {{ lvl.label }}
                    </button>
                </div>

                <!-- Student Name + 1-Click PDF Download -->
                <div class="flex flex-wrap items-center gap-2">
                    <input
                        v-model="studentName"
                        type="text"
                        :placeholder="en ? 'Your Name (for PDF)...' : 'আপনার নাম (PDF-এর জন্য)...'"
                        class="px-3 py-2 rounded-xl bg-white dark:bg-[#0e121d] border border-gray-300 dark:border-white/15 text-xs text-gray-900 dark:text-white w-44 focus:outline-none focus:border-cyan-500"
                    />
                    <button
                        type="button"
                        :disabled="isGeneratingPdf || !exportList.length"
                        class="px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-500 disabled:opacity-50 text-white text-xs font-extrabold flex items-center gap-1.5 shadow-md transition"
                        @click="handleDownloadPdf"
                    >
                        <ArrowDownTrayIcon class="w-4 h-4" />
                        <span>
                            {{ isGeneratingPdf
                                ? (en ? 'Generating PDF...' : 'PDF তৈরি হচ্ছে...')
                                : (selectedIds.length ? `${en ? 'Download Selected' : 'নির্বাচিত'} (${selectedIds.length}) PDF` : (en ? 'Download Formula PDF' : 'সূত্র শিট PDF ডাউনলোড'))
                            }}
                        </span>
                    </button>
                    <button
                        type="button"
                        class="px-3 py-2 rounded-xl bg-white dark:bg-white/10 border border-gray-200 dark:border-white/15 text-xs font-bold flex items-center gap-1 hover:bg-gray-50 dark:hover:bg-white/15 transition"
                        @click="handlePrintSheet"
                    >
                        <PrinterIcon class="w-4 h-4" />
                        <span>{{ en ? 'Print' : 'প্রিন্ট' }}</span>
                    </button>
                </div>
            </div>

            <!-- Subject Filter & Search -->
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                <div class="flex items-center gap-2 flex-wrap">
                    <button
                        v-for="sub in [
                            { key: 'all', label: en ? 'All Subjects' : 'সকল বিষয়' },
                            { key: 'physics', label: en ? 'Physics' : 'পদার্থবিজ্ঞান' },
                            { key: 'math', label: en ? 'Mathematics' : 'গণিত' },
                            ...(selectedLevel !== 'job' ? [{ key: 'chemistry', label: en ? 'Chemistry' : 'রসায়ন' }] : []),
                        ]"
                        :key="sub.key"
                        type="button"
                        class="px-3 py-1.5 rounded-xl text-xs font-bold border transition"
                        :class="formulaSubject === sub.key ? 'bg-cyan-500/15 text-cyan-700 dark:text-cyan-300 border-cyan-500/40' : 'bg-gray-50 dark:bg-white/5 border-gray-200 dark:border-white/10 text-gray-600 dark:text-gray-400'"
                        @click="formulaSubject = sub.key"
                    >
                        {{ sub.label }}
                    </button>

                    <button
                        type="button"
                        class="text-xs font-bold text-amber-600 dark:text-amber-400 hover:underline ml-1"
                        @click="selectAllVisible"
                    >
                        {{ en ? 'Select/Unselect Visible' : 'সবগুলো মার্ক/আনমার্ক করুন' }}
                    </button>
                </div>

                <div class="relative w-full sm:w-64">
                    <MagnifyingGlassIcon class="w-4 h-4 text-gray-400 absolute left-3 top-2.5" />
                    <input
                        v-model="formulaSearch"
                        type="text"
                        :placeholder="en ? 'Search chapter or formula...' : 'অধ্যায় বা সূত্র খুঁজুন...'"
                        class="w-full pl-9 pr-3 py-2 rounded-xl bg-gray-50 dark:bg-[#0e121d] border border-gray-300 dark:border-white/10 text-xs text-gray-900 dark:text-white focus:outline-none focus:border-cyan-500"
                    />
                </div>
            </div>

            <!-- Formulas Cards Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div
                    v-for="item in filteredFormulas"
                    :key="item.id"
                    class="p-4 rounded-2xl border transition relative"
                    :class="selectedIds.includes(item.id)
                        ? 'bg-cyan-500/5 border-cyan-500/50 shadow-sm'
                        : 'bg-gray-50 dark:bg-[#0e121d] border-gray-200 dark:border-white/10 hover:border-cyan-500/30'"
                >
                    <div class="flex items-center justify-between gap-2 text-[11px] mb-1.5">
                        <span class="font-bold text-cyan-600 dark:text-cyan-400">{{ item.topic }}</span>
                        <div class="flex items-center gap-1.5">
                            <button
                                type="button"
                                class="px-2 py-0.5 rounded-lg text-[10px] font-bold border transition flex items-center gap-1"
                                :class="selectedIds.includes(item.id)
                                    ? 'bg-cyan-600 text-white border-cyan-600'
                                    : 'bg-white dark:bg-white/5 text-gray-600 dark:text-gray-300 border-gray-200 dark:border-white/10'"
                                @click="toggleSelectFormula(item.id)"
                            >
                                <BookmarkIcon class="w-3 h-3" />
                                <span>{{ selectedIds.includes(item.id) ? (en ? 'Added' : 'যুক্ত') : (en ? '+ PDF' : '+ PDF') }}</span>
                            </button>
                            <button
                                type="button"
                                class="p-1 text-gray-400 hover:text-cyan-500"
                                title="Copy"
                                @click="copyFormula(item.plain, item.id)"
                            >
                                <CheckIcon v-if="copiedId === item.id" class="w-4 h-4 text-emerald-500" />
                                <ClipboardDocumentIcon v-else class="w-4 h-4" />
                            </button>
                        </div>
                    </div>

                    <h4 class="font-bold text-gray-900 dark:text-white text-sm mb-2">{{ item.name }}</h4>

                    <!-- KaTeX Rendered Formula -->
                    <div
                        class="p-3 rounded-xl bg-white dark:bg-[#161a26] border border-gray-200 dark:border-white/5 text-slate-900 dark:text-cyan-200 overflow-x-auto text-sm"
                        v-html="renderLatex(item.latex)"
                    />

                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-2 leading-relaxed">{{ item.desc }}</p>
                </div>
            </div>
        </div>

        <!-- ==================== 2. INTERACTIVE FORMULA SOLVER ==================== -->
        <div v-else-if="subTab === 'solver'" class="space-y-5">
            <div class="flex flex-wrap gap-2">
                <button
                    v-for="s in [
                        { key: 'motion', label: en ? 'Kinematics (v, s)' : 'গতির সমীকরণ (v ও s)' },
                        { key: 'quadratic', label: en ? 'Quadratic Roots' : 'দ্বিঘাত সমীকরণ সমাধান' },
                        { key: 'compound', label: en ? 'Simple vs Compound Interest' : 'সরল ও চক্রবৃদ্ধি মুনাফা' },
                    ]"
                    :key="s.key"
                    type="button"
                    class="px-4 py-2 rounded-xl text-xs font-bold border transition"
                    :class="solverType === s.key ? 'bg-cyan-600 text-white border-cyan-600' : 'bg-gray-50 dark:bg-white/5 border-gray-200 dark:border-white/10 text-gray-700 dark:text-gray-300'"
                    @click="solverType = s.key"
                >
                    {{ s.label }}
                </button>
            </div>

            <!-- Kinematics Solver -->
            <div v-if="solverType === 'motion'" class="p-6 rounded-2xl bg-gray-50 dark:bg-[#0e121d] border border-gray-200 dark:border-white/10 space-y-4">
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-xs font-bold mb-1">{{ en ? 'Initial Velocity u (m/s)' : 'আদিবেগ u (m/s)' }}</label>
                        <input v-model.number="mU" type="number" class="w-full px-3 py-2 rounded-xl bg-white dark:bg-[#161a26] border border-gray-300 dark:border-white/10 font-mono text-sm" />
                    </div>
                    <div>
                        <label class="block text-xs font-bold mb-1">{{ en ? 'Acceleration a (m/s²)' : 'ত্বরণ a (m/s²)' }}</label>
                        <input v-model.number="mA" type="number" class="w-full px-3 py-2 rounded-xl bg-white dark:bg-[#161a26] border border-gray-300 dark:border-white/10 font-mono text-sm" />
                    </div>
                    <div>
                        <label class="block text-xs font-bold mb-1">{{ en ? 'Time t (s)' : 'সময় t (s)' }}</label>
                        <input v-model.number="mT" type="number" class="w-full px-3 py-2 rounded-xl bg-white dark:bg-[#161a26] border border-gray-300 dark:border-white/10 font-mono text-sm" />
                    </div>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-2">
                    <div class="p-4 rounded-xl bg-cyan-500/10 border border-cyan-500/20">
                        <span class="text-xs text-gray-500 block">{{ en ? 'Final Velocity (v = u + at)' : 'শেষবেগ (v = u + at)' }}</span>
                        <strong class="text-2xl font-mono text-cyan-600 dark:text-cyan-400">{{ motionAns.v }} m/s</strong>
                    </div>
                    <div class="p-4 rounded-xl bg-emerald-500/10 border border-emerald-500/20">
                        <span class="text-xs text-gray-500 block">{{ en ? 'Displacement (s = ut + ½at²)' : 'অতিক্রান্ত দূরত্ব (s = ut + ½at²)' }}</span>
                        <strong class="text-2xl font-mono text-emerald-600 dark:text-emerald-400">{{ motionAns.s }} m</strong>
                    </div>
                </div>
            </div>

            <!-- Quadratic Solver -->
            <div v-else-if="solverType === 'quadratic'" class="p-6 rounded-2xl bg-gray-50 dark:bg-[#0e121d] border border-gray-200 dark:border-white/10 space-y-4">
                <p class="text-xs text-gray-500 font-mono">ax² + bx + c = 0</p>
                <div class="grid grid-cols-3 gap-4">
                    <div>
                        <label class="block text-xs font-bold mb-1">a</label>
                        <input v-model.number="qA" type="number" class="w-full px-3 py-2 rounded-xl bg-white dark:bg-[#161a26] border border-gray-300 dark:border-white/10 font-mono text-sm" />
                    </div>
                    <div>
                        <label class="block text-xs font-bold mb-1">b</label>
                        <input v-model.number="qB" type="number" class="w-full px-3 py-2 rounded-xl bg-white dark:bg-[#161a26] border border-gray-300 dark:border-white/10 font-mono text-sm" />
                    </div>
                    <div>
                        <label class="block text-xs font-bold mb-1">c</label>
                        <input v-model.number="qC" type="number" class="w-full px-3 py-2 rounded-xl bg-white dark:bg-[#161a26] border border-gray-300 dark:border-white/10 font-mono text-sm" />
                    </div>
                </div>
                <div v-if="quadAns.status === 'real'" class="grid grid-cols-1 sm:grid-cols-3 gap-3 pt-2">
                    <div class="p-4 rounded-xl bg-white dark:bg-[#161a26] border border-gray-200 dark:border-white/10">
                        <span class="text-xs text-gray-500 block">{{ en ? 'Discriminant (D)' : 'নিশ্চায়ক (b² - 4ac)' }}</span>
                        <strong class="text-xl font-mono">{{ quadAns.d }}</strong>
                    </div>
                    <div class="p-4 rounded-xl bg-emerald-500/10 border border-emerald-500/20">
                        <span class="text-xs text-gray-500 block">x₁</span>
                        <strong class="text-xl font-mono text-emerald-600 dark:text-emerald-400">{{ quadAns.x1 }}</strong>
                    </div>
                    <div class="p-4 rounded-xl bg-cyan-500/10 border border-cyan-500/20">
                        <span class="text-xs text-gray-500 block">x₂</span>
                        <strong class="text-xl font-mono text-cyan-600 dark:text-cyan-400">{{ quadAns.x2 }}</strong>
                    </div>
                </div>
                <div v-else class="p-4 rounded-xl bg-rose-500/10 border border-rose-500/20 text-xs font-bold text-rose-600 dark:text-rose-400">
                    {{ quadAns.msg }}
                </div>
            </div>

            <!-- Interest Solver -->
            <div v-else class="p-6 rounded-2xl bg-gray-50 dark:bg-[#0e121d] border border-gray-200 dark:border-white/10 space-y-4">
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-xs font-bold mb-1">{{ en ? 'Principal P (৳)' : 'আসল P (৳)' }}</label>
                        <input v-model.number="cP" type="number" class="w-full px-3 py-2 rounded-xl bg-white dark:bg-[#161a26] border border-gray-300 dark:border-white/10 font-mono text-sm" />
                    </div>
                    <div>
                        <label class="block text-xs font-bold mb-1">{{ en ? 'Rate R (%)' : 'মুনাফার হার r (%)' }}</label>
                        <input v-model.number="cR" type="number" class="w-full px-3 py-2 rounded-xl bg-white dark:bg-[#161a26] border border-gray-300 dark:border-white/10 font-mono text-sm" />
                    </div>
                    <div>
                        <label class="block text-xs font-bold mb-1">{{ en ? 'Years (n)' : 'সময় n (বছর)' }}</label>
                        <input v-model.number="cN" type="number" class="w-full px-3 py-2 rounded-xl bg-white dark:bg-[#161a26] border border-gray-300 dark:border-white/10 font-mono text-sm" />
                    </div>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 pt-2">
                    <div class="p-4 rounded-xl bg-white dark:bg-[#161a26] border border-gray-200 dark:border-white/10">
                        <span class="text-xs text-gray-500 block">{{ en ? 'Simple Interest (I)' : 'সরল মুনাফা (I = Pnr)' }}</span>
                        <strong class="text-xl font-mono">৳{{ compoundAns.simple }}</strong>
                    </div>
                    <div class="p-4 rounded-xl bg-cyan-500/10 border border-cyan-500/20">
                        <span class="text-xs text-gray-500 block">{{ en ? 'Compound Interest' : 'চক্রবৃদ্ধি মুনাফা' }}</span>
                        <strong class="text-xl font-mono text-cyan-600 dark:text-cyan-400">৳{{ compoundAns.compInterest }}</strong>
                    </div>
                    <div class="p-4 rounded-xl bg-amber-500/10 border border-amber-500/20">
                        <span class="text-xs text-gray-500 block">{{ en ? 'Difference (CI - SI)' : 'চক্রবৃদ্ধি ও সরল মুনাফার পার্থক্য' }}</span>
                        <strong class="text-xl font-mono text-amber-600 dark:text-amber-400">৳{{ compoundAns.diff }}</strong>
                    </div>
                </div>
            </div>
        </div>

        <!-- ==================== 3. PRO UNIT CONVERTER ==================== -->
        <div v-else-if="subTab === 'converter'" class="space-y-5">
            <div class="flex items-center gap-2 flex-wrap text-xs">
                <button
                    v-for="cat in unitCategories"
                    :key="cat.key"
                    type="button"
                    class="px-3 py-1.5 rounded-xl font-bold transition border"
                    :class="converterType === cat.key ? 'bg-cyan-600 text-white border-cyan-600' : 'bg-gray-50 dark:bg-white/5 border-gray-200 dark:border-white/10 text-gray-700 dark:text-gray-300'"
                    @click="converterType = cat.key; handleTypeChange()"
                >
                    {{ en ? cat.labelEn : cat.labelBn }}
                </button>
            </div>

            <div class="p-6 rounded-2xl bg-gray-50 dark:bg-[#0e121d] border border-gray-200 dark:border-white/10 grid grid-cols-1 md:grid-cols-2 gap-6 items-center">
                <div>
                    <label class="block text-xs font-semibold text-gray-600 dark:text-gray-300 mb-1.5">
                        {{ en ? 'Input Value & Unit:' : 'প্রারম্ভিক মান ও একক:' }}
                    </label>
                    <div class="flex items-center gap-2">
                        <input
                            v-model.number="convInput"
                            type="number"
                            class="flex-1 px-4 py-2.5 rounded-xl bg-white dark:bg-[#161a26] border border-gray-300 dark:border-white/10 text-base font-mono font-bold"
                        />
                        <select
                            v-model="convFrom"
                            class="w-44 px-3 py-2.5 rounded-xl bg-white dark:bg-[#161a26] border border-gray-300 dark:border-white/10 text-xs font-semibold"
                        >
                            <option v-for="u in unitOptions" :key="u.key" :value="u.key">{{ u.label }}</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-600 dark:text-gray-300 mb-1.5">
                        {{ en ? 'Converted Result:' : 'রূপান্তরিত ফলাফল:' }}
                    </label>
                    <div class="flex items-center gap-2">
                        <div class="flex-1 px-4 py-2.5 rounded-xl bg-cyan-500/10 border border-cyan-500/20 text-base font-mono font-bold text-cyan-600 dark:text-cyan-400 overflow-x-auto whitespace-nowrap">
                            {{ convertedValue }}
                        </div>
                        <select
                            v-model="convTo"
                            class="w-44 px-3 py-2.5 rounded-xl bg-white dark:bg-[#161a26] border border-gray-300 dark:border-white/10 text-xs font-semibold"
                        >
                            <option v-for="u in unitOptions" :key="u.key" :value="u.key">{{ u.label }}</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <!-- ==================== 4. PHYSICAL CONSTANTS ==================== -->
        <div v-else class="grid grid-cols-1 md:grid-cols-2 gap-3">
            <div
                v-for="(c, idx) in physicalConstants"
                :key="idx"
                class="p-4 rounded-2xl bg-gray-50 dark:bg-[#0e121d] border border-gray-200 dark:border-white/10 flex items-center justify-between gap-3"
            >
                <span class="text-xs font-bold text-gray-800 dark:text-gray-200">{{ c.name }}</span>
                <code class="px-2.5 py-1 rounded-lg bg-cyan-500/10 text-cyan-700 dark:text-cyan-300 font-mono text-xs font-bold shrink-0">
                    {{ c.value }}
                </code>
            </div>
        </div>
    </div>
</template>
