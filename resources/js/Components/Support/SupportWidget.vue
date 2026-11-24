<script setup>
import { ref, computed, nextTick } from 'vue';
import axios from 'axios';
import { useI18n } from '@/i18n';

const { t, locale } = useI18n();
const open = ref(false);
const loaded = ref(false);
const messages = ref([]);
const draft = ref('');
const sending = ref(false);
const handingOff = ref(false);
const handoffRequiresLogin = ref(false);
const handoffFailed = ref(false);
const handoffRateLimited = ref(false);
const handoffLoginUrl = ref('/login');
const ticketUrl = ref('');
const conversationId = ref(null);
const scrollBox = ref(null);

function scrollToBottom() {
    nextTick(() => {
        if (scrollBox.value) scrollBox.value.scrollTop = scrollBox.value.scrollHeight;
    });
}

function safeHref(value) {
    if (value.startsWith('/') && !value.startsWith('//') && !value.includes('\\') && !/[\x00-\x1f]/.test(value)) {
        return value;
    }

    try {
        const url = new URL(value);
        return url.protocol === 'https:' ? url.href : null;
    } catch {
        return null;
    }
}

function messageParts(message) {
    const source = String(message ?? '');
    const pattern = /\*\*([^*]+)\*\*|\[([^\]]+)\]\((https:\/\/[^\s)]+|\/(?!\/)[^\s)]+)\)|(https:\/\/[^\s<>]+|\/(?:library|bookshelf|my-books|questions|courses|plans|wallet|ai\/chat|exams|flashcards|essays|schedules|leaderboard|referrals|notifications|profile|access|contact|support\/tickets)(?:\/[A-Za-z0-9_-]+)*(?:\?[^\s<>]*)?)/g;
    const parts = [];
    let cursor = 0;
    let match;

    while ((match = pattern.exec(source)) !== null) {
        if (match.index > cursor) parts.push({ text: source.slice(cursor, match.index) });

        if (match[1] !== undefined) {
            parts.push({ text: match[1], bold: true });
        } else {
            const markdownLabel = match[2];
            let href = markdownLabel ? match[3] : match[4];
            let suffix = '';
            if (!markdownLabel) {
                const trailing = href.match(/[.,!?;:]+$/)?.[0] ?? '';
                href = href.slice(0, href.length - trailing.length);
                suffix = trailing;
            }
            const safeUrl = safeHref(href);
            if (safeUrl) {
                parts.push({ text: markdownLabel ?? href, href: safeUrl, external: safeUrl.startsWith('https://') });
                if (suffix) parts.push({ text: suffix });
            } else {
                parts.push({ text: match[0] });
            }
        }
        cursor = match.index + match[0].length;
    }

    if (cursor < source.length) parts.push({ text: source.slice(cursor) });
    return parts;
}

const quickGuides = computed(() => {
    const bn = locale.value === 'bn';

    return [
        {
            id: 'chat_book',
            label: bn ? '📖 বইয়ের সাথে AI চ্যাট' : '📖 Chat with a Book',
            question: bn ? 'বইয়ের সাথে AI চ্যাট কীভাবে করব?' : 'How to chat with a book using AI?',
            answer: bn
                ? `হ্যালো!\n\nকোনো নির্দিষ্ট বই নিয়ে AI-এর সাথে চ্যাট করতে হলে প্রথমে বইটি চ্যাটে যুক্ত (Attach) করতে হবে।\n\nঅনুগ্রহ করে নিচের ধাপগুলো অনুসরণ করুন:\n\n1. **বইটি বুকশেলফে যোগ করুন:** প্রথমে [লাইব্রেরি](/library) পেজে যান এবং আপনার পছন্দের বইটি বুকশেলফে (Bookshelf) যোগ করুন।\n2. **AI চ্যাটে বই যুক্ত করুন:** এবার [AI চ্যাট](/ai/chat) ওপেন করে যে বইটি নিয়ে আলোচনা করতে চান সেটি Attach করুন।\n3. **চ্যাট শুরু করুন:** বইটি যুক্ত হয়ে গেলে বইয়ের যেকোনো টপিক নিয়ে প্রশ্ন করতে ও ব্যাখ্যা জানতে পারবেন।\n\n**বিকল্প উপায়:** যেকোনো বইয়ের **Read (পড়ুন)** পেজে গেলেও সরাসরি **পেজ-ভিত্তিক (Page-wise) AI চ্যাট** অপশন পাবেন, যেখান থেকে আপনি ঠিক যে পৃষ্ঠাটি পড়ছেন সেটির ওপর প্রশ্ন করতে পারবেন।\n\n**আরও নিখুঁত উত্তরের জন্য উপরের যেকোনো একটি পদ্ধতি অনুসরণ করুন**, যাতে AI বইয়ের সঠিক অংশকে রেফারেন্স হিসেবে ব্যবহার করতে পারে।`
                : `Hello,\n\nTo chat with AI about a specific book, you need to attach that book to the chat first.\n\nPlease follow these steps:\n\n1. **Add a Book to Your Bookshelf:** Go to the [Library](/library) and add your desired book to your [Bookshelf](/bookshelf).\n2. **Attach the Book to AI Chat:** Open [AI Chat](/ai/chat) and attach the book you want to discuss.\n3. **Start Chatting:** Once the book is attached, you can ask questions and discuss its content with AI.\n\n**Alternatively:** You can visit the **Read** page of any book. There, you'll find a **page-wise chat** option that allows you to ask questions about the specific page you're reading.\n\n**For more accurate answers, please follow one of these workflows** so the AI can use the relevant book content as context.`,
        },
        {
            id: 'how_it_works',
            label: bn ? '❓ কীভাবে কাজ করে?' : '❓ How it works',
            question: bn ? 'Sikhun.com কীভাবে কাজ করে?' : 'How does Sikhun.com work?',
            answer: bn
                ? `হ্যালো! **Sikhun.com** হলো বাংলাদেশের প্রথম AI-চালিত স্মার্ট শিক্ষা প্ল্যাটফর্ম।\n\nএখানে আপনি ৪টি সহজ ধাপে প্রস্তুতি নিতে পারবেন:\n\n1. **ডিজিটাল লাইব্রেরি:** [লাইব্রেরি](/library) থেকে ফ্রি ও পেইড বই ব্রাউজারে পড়ুন এবং পেজ-ভিত্তিক AI টিউটরের সাহায্য নিন।\n2. **প্রশ্নব্যাংক ও মডেল টেস্ট:** [প্রশ্নব্যাংক](/questions) থেকে ৩৮,০০০+ বিগত বছরের প্রশ্ন প্র্যাকটিস করুন এবং টাইমড এক্সাম দিন।\n3. **নিজের PDF আপলোড:** [My Books](/my-books)-এ নিজের ক্লাস নোট বা PDF আপলোড করে তার ওপর AI চ্যাট ও ফ্ল্যাশকার্ড তৈরি করুন।\n4. **কোর্স ও স্টুডেন্ট টুলস:** [কোর্স](/courses) করুন এবং ফ্রি স্টাডি টুলস ব্যবহার করুন।`
                : `Hello! **Sikhun.com** is Bangladesh's AI-powered learning platform.\n\nHere is how it works in 4 simple steps:\n\n1. **Digital Library:** Browse free and paid textbooks in the [Library](/library) and read with a page-aware AI Tutor.\n2. **Question Bank & Mock Tests:** Practice 38,000+ past exam MCQs in the [Question Bank](/questions) or take timed model tests.\n3. **Upload Your Own PDF:** Upload your study notes in [My Books](/my-books) to chat with AI and generate flashcards.\n4. **Courses & Smart Tools:** Join mentor-led [Courses](/courses) and download formula & routine PDFs from Student Tools.`,
        },
        {
            id: 'buy_book',
            label: bn ? '🛒 বই কীভাবে কিনব?' : '🛒 How to buy a book',
            question: bn ? 'ডিজিটাল বই বা চ্যাপ্টার কীভাবে কিনব?' : 'How to buy a book or chapter?',
            answer: bn
                ? `হ্যালো! Sikhun.com-এ আপনি **পুরো বই** অথবা শুধু **নির্দিষ্ট চ্যাপ্টার** আলাদাভাবে কিনতে পারবেন:\n\n1. **বই নির্বাচন করুন:** [লাইব্রেরি](/library) থেকে আপনার পছন্দের বইটিতে ক্লিক করুন (ফ্রি বইগুলোতে সরাসরি **পড়ুন** বাটন পাবেন)।\n2. **ক্রয় অপশন বেছে নিন:** পেইড বইয়ের ক্ষেত্রে **পুরো বই কিনুন** অথবা নির্দিষ্ট অধ্যায়ের পাশে থাকা **চ্যাপ্টার কিনুন** বাটনে ক্লিক করুন।\n3. **পেমেন্ট সম্পন্ন করুন:** আপনার [ওয়ালেট](/wallet) ব্যালেন্স অথবা অনলাইন পেমেন্ট গেটওয়ে (bKash/Nagad/Card) দিয়ে পেমেন্ট করুন।\n4. **তাৎক্ষণিক অ্যাক্সেস:** পেমেন্ট নিশ্চিত হওয়া মাত্রই বইটি আপনার [বুকশেলফে](/bookshelf) আনলক হয়ে যাবে।`
                : `Hello! On Sikhun.com, you can buy either a **full book** or **individual chapters**:\n\n1. **Pick a Book:** Open any book from the [Library](/library) (free books can be read immediately).\n2. **Choose Purchase Option:** Click **Buy Full Book** or unlock only the specific **Chapter** you need.\n3. **Complete Payment:** Pay using your [Wallet](/wallet) balance or mobile banking/card checkout.\n4. **Instant Access:** Your unlocked book or chapter immediately appears in your [Bookshelf](/bookshelf).`,
        },
        {
            id: 'plans',
            label: bn ? '💎 সাবস্ক্রিপশন প্ল্যান' : '💎 Plans & Pricing',
            question: bn ? 'সাবস্ক্রিপশন প্ল্যান ও সুবিধা কী কী?' : 'How do subscription plans work?',
            answer: bn
                ? `হ্যালো! আপনার প্রয়োজন অনুযায়ী সাশ্রয়ী AI স্টাডি প্ল্যান বেছে নিতে পারবেন:\n\n1. **প্ল্যান দেখুন:** [সাবস্ক্রিপশন প্ল্যান](/plans) পেজে গিয়ে Free, Starter, Pro, Admission Pass এবং BCS/Job Pass তুলনা করুন।\n2. **অ্যাক্টিভেট করুন:** পছন্দের প্ল্যান সিলেক্ট করে [ওয়ালেট](/wallet) বা অনলাইন পেমেন্টের মাধ্যমে সাবস্ক্রাইব করুন।\n3. **সুবিধাসমূহ:** প্ল্যান অনুযায়ী আনলিমিটেড মক টেস্ট, AI বুক চ্যাট, ফ্ল্যাশকার্ড, রচনা মূল্যায়ন এবং স্টাডি প্ল্যানার আনলক হয়ে যাবে।`
                : `Hello! You can choose an affordable AI Study Plan tailored to your exam:\n\n1. **Compare Plans:** Visit [Subscription Plans](/plans) to view Free, Starter, Pro, Admission Crash Pass, and BCS & Bank Pass.\n2. **Subscribe:** Pay via your [Wallet](/wallet) or online checkout.\n3. **Instant Activation:** Your AI Tutor minutes, mock tests, flashcards, and Mistake Review unlock immediately.`,
        },
        {
            id: 'mock_test',
            label: bn ? '📝 মডেল টেস্ট ও MCQ' : '📝 Mock Tests & MCQs',
            question: bn ? 'প্রশ্নব্যাংক ও মডেল টেস্ট কীভাবে দেব?' : 'How to practice MCQs and take mock tests?',
            answer: bn
                ? `হ্যালো! পরীক্ষা প্রস্তুতির জন্য দুইভাবে প্র্যাকটিস করতে পারবেন:\n\n1. **ফ্রি পাবলিক প্রশ্নব্যাংক:** লগইন ছাড়াই [প্রশ্নব্যাংক](/questions) পেজে বিসিএস, প্রাইমারি, ঢাবি, রাবি, গুচ্ছ ও বুয়েটের ৩৮,০০০+ প্রশ্ন ও ব্যাখ্যা প্র্যাকটিস করুন।\n2. **টাইমড মক টেস্ট:** অ্যাকাউন্টে লগইন করে [এক্সাম সেন্টার](/exams) থেকে নেগেটিভ মার্কিংসহ কাস্টম পরীক্ষা দিন এবং ভুল উত্তরগুলো **"আমার ভুলসমূহ"** সেকশনে রিভিশন করুন।`
                : `Hello! You can practice in two ways:\n\n1. **Free Public Question Bank:** Visit [Question Bank](/questions) to practice 38,000+ past BCS, Primary, DU, RU, GST & BUET MCQs with explanations — no login needed.\n2. **Timed Mock Tests:** Log in and open [Exams](/exams) to take custom timed tests with negative marking and automatic Mistake Review.`,
        },
        {
            id: 'my_pdf',
            label: bn ? '📄 নিজের PDF আপলোড' : '📄 Upload My PDF',
            question: bn ? 'নিজের নোট বা PDF আপলোড করে কীভাবে পড়ব?' : 'How to upload my own PDF notes?',
            answer: bn
                ? `হ্যালো! আপনি নিজের যেকোনো PDF বই বা ক্লাস নোট আপলোড করে AI-এর সাথে পড়তে পারবেন:\n\n1. লগইন করে [My Books](/my-books) পেজে যান।\n2. **Upload PDF** বাটনে ক্লিক করে আপনার ফাইল আপলোড করুন।\n3. আমাদের সিস্টেম অটোমেটিক OCR করে বইটি তৈরি করে দেবে—এরপর আপনি সরাসরি পেজ পড়ে AI চ্যাট ও পরীক্ষা দিতে পারবেন।`
                : `Hello! You can upload your own PDF notes or books to study with AI:\n\n1. Log in and go to [My Books](/my-books).\n2. Click **Upload PDF** and select your study file.\n3. Our system automatically processes your PDF so you can read it online, chat with AI page-by-page, and generate quizzes.`,
        },
    ];
});

function triggerQuickGuide(guide) {
    if (sending.value) return;
    messages.value.push({ sender_type: 'student', message: guide.question });
    messages.value.push({ sender_type: 'bot', message: guide.answer });
    scrollToBottom();
}

async function toggle() {
    open.value = !open.value;
    if (open.value && !loaded.value) {
        try {
            const { data } = await axios.get('/support/conversation');
            conversationId.value = data.conversation_id;
            messages.value = data.messages;
            loaded.value = true;
            scrollToBottom();
        } catch (error) {
            messages.value = [{
                sender_type: 'bot',
                message: locale.value === 'bn'
                    ? 'সাপোর্ট চ্যাট লোড করা যায়নি। অনুগ্রহ করে আবার খুলুন।'
                    : 'Support chat could not be loaded. Please try again.',
            }];
        }
    }
}

async function send() {
    const text = draft.value.trim();
    if (!text || sending.value || ticketUrl.value) return;

    messages.value.push({ sender_type: 'student', message: text });
    draft.value = '';
    sending.value = true;
    scrollToBottom();

    try {
        const { data } = await axios.post('/support/message', { message: text, locale: locale.value });
        messages.value = data.messages;
    } catch (error) {
        messages.value.push({
            sender_type: 'bot',
            message: error.response?.status === 429
                ? t('support.message_rate_limited')
                : t('support.send_failed'),
        });
    } finally {
        sending.value = false;
        scrollToBottom();
    }
}

async function handoff() {
    if (handingOff.value || !conversationId.value) return;

    handingOff.value = true;
    handoffRequiresLogin.value = false;
    handoffFailed.value = false;
    handoffRateLimited.value = false;

    try {
        const { data } = await axios.post('/support/conversation/ticket', {
            conversation_id: conversationId.value,
        });
        ticketUrl.value = data.redirect_url;
    } catch (error) {
        if (error.response?.status === 401) {
            handoffRequiresLogin.value = true;
            handoffLoginUrl.value = error.response.data?.login_url || '/login';
        } else if (error.response?.status === 429) {
            handoffRateLimited.value = true;
        } else {
            handoffFailed.value = true;
            console.error('Support ticket handoff failed.', error.response?.status);
        }
    } finally {
        handingOff.value = false;
    }
}
</script>

<template>
    <div class="fixed bottom-5 right-5 z-50">
        <!-- Expanded & Enhanced Chat Drawer -->
        <div v-if="open" class="mb-3 w-[380px] sm:w-[440px] max-w-[calc(100vw-2rem)] h-[540px] sm:h-[600px] max-h-[calc(100vh-5rem)] rounded-2xl border border-[var(--border)] bg-[var(--surface)] shadow-2xl flex flex-col overflow-hidden animate-in fade-in slide-in-from-bottom-4 duration-200">
            <!-- Modern Header with Online Indicator & Close -->
            <div class="px-4 py-3.5 border-b border-[var(--border)] bg-gradient-to-r from-[var(--primary)] to-[var(--primary-hover)] text-white flex items-center justify-between shadow-xs">
                <div class="flex items-center gap-2.5">
                    <div class="relative flex h-3 w-3 shrink-0">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-300 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-3 w-3 bg-emerald-400"></span>
                    </div>
                    <div>
                        <div class="font-bold text-sm tracking-wide leading-tight">
                            {{ t('support.title') }}
                        </div>
                        <div class="text-[11px] text-white/80 leading-none mt-0.5">
                            {{ locale === 'bn' ? '২৪/৭ এআই সহায়তা ও পরামর্শ' : '24/7 AI Desk & Student Support' }}
                        </div>
                    </div>
                </div>
                <button
                    type="button"
                    @click="open = false"
                    class="h-8 w-8 rounded-lg flex items-center justify-center text-white/80 hover:text-white hover:bg-white/15 transition-colors"
                    title="Close chat"
                >
                    <span class="text-lg leading-none">✕</span>
                </button>
            </div>

            <!-- Messages Stream Area -->
            <div ref="scrollBox" class="flex-1 overflow-y-auto p-4 space-y-3">
                <div v-if="messages.length === 0 && !sending" class="py-8 text-center text-xs text-[var(--text-muted)] space-y-2">
                    <div class="text-3xl">👋</div>
                    <p class="font-medium text-[var(--text)]">
                        {{ locale === 'bn' ? 'শিখুন সাপোর্ট ডেস্কে স্বাগতম!' : 'Welcome to Sikhun Support!' }}
                    </p>
                    <p class="text-[11px] max-w-[260px] mx-auto text-[var(--text-muted)]">
                        {{ locale === 'bn' ? 'বই, কোর্স, পরীক্ষা, প্ল্যান বা যে কোনো তথ্যের জন্য প্রশ্ন করুন।' : 'Ask anything about our books, courses, AI exams, plans, or account.' }}
                    </p>
                </div>

                <div v-for="(m, i) in messages" :key="i" class="flex" :class="m.sender_type === 'student' ? 'justify-end' : 'justify-start'">
                    <div class="max-w-[85%] px-3.5 py-2.5 rounded-2xl text-[13px] leading-relaxed shadow-2xs whitespace-pre-wrap break-words"
                        :class="m.sender_type === 'student'
                            ? 'bg-[var(--primary)] text-white rounded-br-xs'
                            : 'bg-[var(--surface2)] text-[var(--text)] border border-[var(--border)] rounded-bl-xs'">
                        <template v-for="(part, partIndex) in messageParts(m.message)" :key="partIndex">
                            <a v-if="part.href" :href="part.href" :target="part.external ? '_blank' : undefined"
                                :rel="part.external ? 'noopener noreferrer' : undefined"
                                class="font-semibold underline decoration-current underline-offset-2 hover:opacity-85">
                                {{ part.text }}
                            </a>
                            <strong v-else-if="part.bold" class="font-bold">{{ part.text }}</strong>
                            <span v-else>{{ part.text }}</span>
                        </template>
                    </div>
                </div>

                <div v-if="sending" class="flex items-center gap-2 text-xs text-[var(--text-muted)] py-1">
                    <span class="flex gap-1 items-center">
                        <span class="h-2 w-2 rounded-full bg-[var(--primary)] animate-bounce" style="animation-delay: 0ms"></span>
                        <span class="h-2 w-2 rounded-full bg-[var(--primary)] animate-bounce" style="animation-delay: 150ms"></span>
                        <span class="h-2 w-2 rounded-full bg-[var(--primary)] animate-bounce" style="animation-delay: 300ms"></span>
                    </span>
                    <span class="text-[11px]">{{ t('support.typing') }}</span>
                </div>
            </div>

            <!-- Ticket / Human Handoff Notice -->
            <div v-if="ticketUrl || handoffRequiresLogin || handoffFailed || handoffRateLimited || messages.some(m => m.sender_type === 'student')" class="border-t border-[var(--border)] p-2.5 bg-[var(--surface2)]/40">
                <p v-if="ticketUrl" class="mb-2 text-xs text-emerald-600 dark:text-emerald-400 font-medium">{{ t('support.handoff_created') }}</p>
                <p v-else-if="handoffRequiresLogin" class="mb-2 text-xs text-[var(--text-muted)]">{{ t('support.handoff_login_required') }}</p>
                <p v-else-if="handoffRateLimited" class="mb-2 text-xs text-rose-500 font-medium">{{ t('support.handoff_rate_limited') }}</p>
                <p v-else-if="handoffFailed" class="mb-2 text-xs text-rose-500 font-medium">{{ t('support.handoff_failed') }}</p>
                <a v-if="ticketUrl" :href="ticketUrl" class="block rounded-xl bg-[var(--primary)] px-3 py-2 text-center text-xs font-bold text-white shadow-xs hover:bg-[var(--primary-hover)] transition-colors">
                    {{ t('support.view_ticket') }}
                </a>
                <a v-else-if="handoffRequiresLogin" :href="handoffLoginUrl" class="block rounded-xl bg-[var(--primary)] px-3 py-2 text-center text-xs font-bold text-white shadow-xs hover:bg-[var(--primary-hover)] transition-colors">
                    {{ t('support.login') }}
                </a>
                <button v-else type="button" @click="handoff" :disabled="handingOff || sending" class="w-full rounded-xl border border-[var(--border)] bg-[var(--surface)] hover:bg-[var(--surface2)] px-3 py-2 text-xs font-semibold text-[var(--text)] transition-colors disabled:opacity-60">
                    {{ handingOff ? t('support.handoff_sending') : t('support.handoff') }}
                </button>
            </div>

            <!-- Quick Help Action Chips (Instant Guided Answers) -->
            <div v-if="!ticketUrl" class="border-t border-[var(--border)] bg-[var(--surface)] px-2.5 pt-2 pb-1">
                <div class="flex items-center gap-1.5 overflow-x-auto pb-1 scrollbar-none">
                    <button
                        v-for="guide in quickGuides"
                        :key="guide.id"
                        type="button"
                        :disabled="sending"
                        class="shrink-0 px-2.5 py-1 rounded-full text-[11px] font-semibold border border-[var(--border)] bg-[var(--surface2)] text-[var(--text)] hover:border-[var(--primary)] hover:text-[var(--primary)] transition-colors disabled:opacity-50"
                        @click="triggerQuickGuide(guide)"
                    >
                        {{ guide.label }}
                    </button>
                </div>
            </div>

            <!-- Chat Input Form -->
            <form v-if="!ticketUrl" @submit.prevent="send" class="p-2.5 border-t border-[var(--border)] bg-[var(--surface)] flex gap-2 items-center">
                <input v-model="draft" type="text" :placeholder="t('support.placeholder')" :disabled="sending"
                    class="flex-1 px-3.5 py-2.5 rounded-xl bg-[var(--surface2)] border border-[var(--border)] text-xs sm:text-sm text-[var(--text)] placeholder:text-[var(--text-muted)] focus:outline-none focus:border-[var(--primary)] transition-colors" />
                <button type="submit" :disabled="sending || !draft.trim()" class="px-4 py-2.5 rounded-xl bg-[var(--primary)] hover:bg-[var(--primary-hover)] text-white text-xs sm:text-sm font-bold shadow-xs transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
                    {{ t('ai_chat.send') }}
                </button>
            </form>
        </div>

        <!-- Floating Chat Bubble Trigger -->
        <button @click="toggle" class="w-14 h-14 rounded-full bg-[var(--primary)] hover:bg-[var(--primary-hover)] text-white shadow-2xl flex items-center justify-center text-2xl transition-transform hover:scale-105 active:scale-95" :title="open ? 'Close' : 'Chat with Support'">
            {{ open ? '✕' : '💬' }}
        </button>
    </div>
</template>
