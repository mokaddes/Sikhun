<script setup>
import { computed } from 'vue';
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import PublicLayout from '@/Components/Layout/PublicLayout.vue';
import SeoHead from '@/Components/Seo/SeoHead.vue';
import { useI18n } from '@/i18n';
import {
    BookOpenIcon,
    BookmarkSquareIcon,
    ShoppingBagIcon,
    LockClosedIcon,
    CheckBadgeIcon,
    SparklesIcon,
    BuildingLibraryIcon,
    UserIcon,
    DocumentTextIcon,
    ChevronRightIcon,
    CreditCardIcon,
    WalletIcon,
    ArrowRightStartOnRectangleIcon,
} from '@heroicons/vue/24/outline';

const props = defineProps({
    book: Object,
    accessType: String,
    onShelf: Boolean,
    walletBalance: { type: Number, default: 0 },
    chapters: { type: Array, default: () => [] },
    relatedBooks: { type: Array, default: () => [] },
    seo: Object,
});

const { t, locale, money } = useI18n();
const page = usePage();
const isLoggedIn = computed(() => !!page.props.auth?.student);
const flashSuccess = computed(() => page.props.flash?.success);
const flashError = computed(() => page.props.flash?.error);

const loginRedirectUrl = computed(() => `/login?redirect=${encodeURIComponent(`/library/${props.book.slug}`)}`);
const registerRedirectUrl = computed(() => `/register?redirect=${encodeURIComponent(`/library/${props.book.slug}`)}`);

function requireAuthOrRun(callback) {
    if (!isLoggedIn.value) {
        router.visit(loginRedirectUrl.value);
        return;
    }
    callback();
}

function handleReadBook() {
    requireAuthOrRun(() => {
        router.visit(`/library/${props.book.id}/read`);
    });
}

function handleAddToShelf() {
    requireAuthOrRun(() => {
        router.post(`/library/${props.book.id}/shelf`, {}, { preserveScroll: true });
    });
}

function handlePurchase(method) {
    requireAuthOrRun(() => {
        router.post(`/library/${props.book.id}/purchase`, { payment_method: method });
    });
}

function handlePurchaseChapter(chapter, method) {
    requireAuthOrRun(() => {
        router.post(`/library/${props.book.id}/chapters/${chapter.id}/purchase`, { payment_method: method }, {
            preserveScroll: true,
        });
    });
}

// Top-level chapters with their sections nested for display.
const chapterTree = computed(() => {
    const tops = props.chapters.filter((c) => c.parent_id === null);
    return tops.map((top) => ({
        ...top,
        children: props.chapters.filter((c) => c.parent_id === top.id),
    }));
});

const showChapters = computed(() => props.book.chapter_purchase_enabled && chapterTree.value.length > 0);
const hasFullReadAccess = computed(() => ['free', 'granted', 'owned', 'subscription_gift'].includes(props.accessType));
</script>

<template>
    <Head :title="book.title" />
    <SeoHead v-if="seo" :seo="seo" />

    <PublicLayout>
        <!-- Breadcrumb Bar -->
        <div class="border-b border-[var(--border)] bg-[var(--surface)]/70">
            <div class="mx-auto flex max-w-7xl flex-wrap items-center gap-2 px-4 py-3 text-xs text-[var(--text-muted)] sm:px-6">
                <Link href="/" class="hover:text-[var(--primary)]">{{ locale === 'en' ? 'Home' : 'হোম' }}</Link>
                <ChevronRightIcon class="h-3.5 w-3.5" />
                <Link href="/library" class="hover:text-[var(--primary)]">{{ t('nav.library') }}</Link>
                <template v-if="book.category">
                    <ChevronRightIcon class="h-3.5 w-3.5" />
                    <Link :href="`/library?category_id=${book.category.id}`" class="hover:text-[var(--primary)]">{{ book.category.name }}</Link>
                </template>
                <ChevronRightIcon class="h-3.5 w-3.5" />
                <span class="truncate font-medium text-[var(--text)]">{{ book.title }}</span>
            </div>
        </div>

        <div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:py-12">
            <!-- Flash Alerts -->
            <div v-if="flashSuccess" class="mb-6 rounded-xl border border-emerald-500/30 bg-emerald-500/10 px-4 py-3 text-sm font-medium text-emerald-600 dark:text-emerald-300">
                {{ flashSuccess }}
            </div>
            <div v-if="flashError" class="mb-6 rounded-xl border border-rose-500/30 bg-rose-500/10 px-4 py-3 text-sm font-medium text-rose-600 dark:text-rose-300">
                {{ flashError }}
            </div>

            <!-- Book Showcase Grid -->
            <div class="grid gap-8 lg:grid-cols-12">
                <!-- Left Column: Cover & Quick Info -->
                <div class="lg:col-span-4">
                    <div class="sticky top-24 space-y-5">
                        <div class="overflow-hidden rounded-2xl border border-[var(--border)] bg-[var(--surface)] p-4 shadow-lg">
                            <div class="relative aspect-[3/4] overflow-hidden rounded-xl bg-[var(--surface2)]">
                                <img
                                    v-if="book.cover_image_url"
                                    :src="book.cover_image_url"
                                    :alt="book.title"
                                    width="400"
                                    height="533"
                                    class="h-full w-full object-cover"
                                />
                                <div v-else class="flex h-full w-full flex-col items-center justify-center gap-3 bg-gradient-to-br from-slate-800 to-slate-900 p-6 text-center text-slate-300">
                                    <BookOpenIcon class="h-14 w-14 opacity-40" />
                                    <span class="font-heading text-base font-bold">{{ book.title }}</span>
                                </div>

                                <span
                                    v-if="book.is_free"
                                    class="absolute right-3 top-3 rounded-full bg-emerald-500 px-3 py-1 text-xs font-bold text-white shadow"
                                >
                                    {{ t('book_show.free_badge') }}
                                </span>
                            </div>

                            <!-- Metadata Mini Grid -->
                            <div class="mt-4 grid grid-cols-2 gap-2.5 text-xs">
                                <div v-if="book.total_pages" class="rounded-xl border border-[var(--border)] bg-[var(--surface2)]/60 p-3">
                                    <div class="text-[var(--text-muted)]">{{ locale === 'en' ? 'Total Pages' : 'মোট পৃষ্ঠা' }}</div>
                                    <div class="mt-0.5 font-heading text-sm font-bold">{{ book.total_pages }} {{ t('book_show.pages') }}</div>
                                </div>
                                <div v-if="book.level" class="rounded-xl border border-[var(--border)] bg-[var(--surface2)]/60 p-3">
                                    <div class="text-[var(--text-muted)]">{{ t('common.level') }}</div>
                                    <div class="mt-0.5 font-heading text-sm font-bold uppercase">{{ book.level }}</div>
                                </div>
                                <div v-if="book.subject" class="rounded-xl border border-[var(--border)] bg-[var(--surface2)]/60 p-3">
                                    <div class="text-[var(--text-muted)]">{{ locale === 'en' ? 'Subject' : 'বিষয়' }}</div>
                                    <div class="mt-0.5 truncate font-heading text-sm font-bold">{{ book.subject }}</div>
                                </div>
                                <div v-if="book.reading_count !== undefined" class="rounded-xl border border-[var(--border)] bg-[var(--surface2)]/60 p-3">
                                    <div class="text-[var(--text-muted)]">{{ locale === 'en' ? 'Readers' : 'পাঠক সংখ্যা' }}</div>
                                    <div class="mt-0.5 font-heading text-sm font-bold">{{ book.reading_count }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Column: Details, Actions & Chapters -->
                <div class="space-y-8 lg:col-span-8">
                    <div class="rounded-2xl border border-[var(--border)] bg-[var(--surface)] p-6 shadow-sm sm:p-8">
                        <!-- Badges -->
                        <div class="flex flex-wrap items-center gap-2">
                            <span v-if="book.category" class="rounded-full bg-[var(--primary)]/10 px-3 py-1 text-xs font-semibold text-[var(--primary)]">
                                {{ book.category.name }}
                            </span>
                            <span v-if="book.level" class="rounded-full border border-[var(--border)] bg-[var(--surface2)] px-3 py-1 text-xs font-semibold uppercase text-[var(--text-muted)]">
                                {{ book.level }}
                            </span>
                            <span v-if="accessType === 'granted'" class="rounded-full bg-emerald-500/15 px-3 py-1 text-xs font-semibold text-emerald-500">
                                {{ t('book_show.coupon_access_badge') }}
                            </span>
                            <span v-else-if="accessType === 'owned' || onShelf" class="inline-flex items-center gap-1 rounded-full bg-emerald-500/15 px-3 py-1 text-xs font-semibold text-emerald-500">
                                <CheckBadgeIcon class="h-4 w-4" /> {{ t('book_show.already_owned') }}
                            </span>
                            <span v-else-if="accessType === 'subscription_gift'" class="rounded-full bg-purple-500/15 px-3 py-1 text-xs font-semibold text-purple-400">
                                {{ t('book_show.subscription_gift_badge') }}
                            </span>
                        </div>

                        <h1 class="mt-3 font-heading text-2xl font-extrabold leading-tight sm:text-3xl lg:text-4xl">
                            {{ book.title }}
                        </h1>

                        <!-- Author & Publication -->
                        <div class="mt-4 flex flex-wrap items-center gap-5 text-sm text-[var(--text-muted)]">
                            <div v-if="book.author" class="inline-flex items-center gap-2">
                                <UserIcon class="h-4 w-4 text-[var(--primary)]" />
                                <span>{{ t('book_show.by') }}: <strong class=" text-[var(--text)]">{{ book.author.name }}</strong></span>
                            </div>
                            <div v-if="book.publication" class="inline-flex items-center gap-2">
                                <BuildingLibraryIcon class="h-4 w-4 text-[var(--secondary)]" />
                                <span>{{ t('book_show.publisher') }}: <strong class="text-[var(--text)]">{{ book.publication.name }}</strong></span>
                            </div>
                        </div>

                        <!-- Price & Action Box -->
                        <div class="mt-6 rounded-2xl border border-[var(--border)] bg-[var(--surface2)]/50 p-5 sm:p-6">
                            <div class="flex flex-wrap items-baseline justify-between gap-4 border-b border-[var(--border)] pb-4">
                                <div>
                                    <div class="text-xs font-medium uppercase tracking-wider text-[var(--text-muted)]">
                                        {{ locale === 'en' ? 'Book Access Price' : 'বইটির মূল্য' }}
                                    </div>
                                    <div class="mt-1 font-heading text-3xl font-extrabold">
                                        <span v-if="book.is_free" class="text-emerald-500">{{ t('common.free') }}</span>
                                        <span v-else>৳{{ money(book.price) }}</span>
                                    </div>
                                </div>

                                <div v-if="isLoggedIn && !book.is_free && !hasFullReadAccess" class="text-right text-xs text-[var(--text-muted)]">
                                    {{ locale === 'en' ? 'Your Wallet Balance:' : 'আপনার ওয়ালেট ব্যালেন্স:' }}
                                    <strong class="ml-1 text-sm text-[var(--text)]">৳{{ money(walletBalance, 2) }}</strong>
                                </div>
                            </div>

                            <!-- Action Buttons:
                                 For Guests (!isLoggedIn): Clicking Read, Buy, or Add to Shelf requires Auth and redirects to Login! -->
                            <div class="mt-5">
                                <!-- Guest Action Buttons (All require auth on click) -->
                                <div v-if="!isLoggedIn" class="space-y-4">
                                    <div class="flex flex-wrap gap-3">
                                        <button
                                            type="button"
                                            @click="handleReadBook"
                                            class="inline-flex items-center gap-2 rounded-xl bg-[var(--primary)] px-6 py-3.5 text-sm font-bold text-white shadow-lg shadow-orange-500/20 transition hover:bg-[var(--primary-hover)]"
                                        >
                                            <BookOpenIcon class="h-5 w-5" />
                                            <span>{{ t('book_show.read_now') }}</span>
                                        </button>

                                        <button
                                            v-if="!book.is_free"
                                            type="button"
                                            @click="handlePurchase('zinipay')"
                                            class="inline-flex items-center gap-2 rounded-xl border border-[var(--primary)]/40 bg-[var(--primary)]/10 px-6 py-3.5 text-sm font-bold text-[var(--primary)] transition hover:bg-[var(--primary)]/20"
                                        >
                                            <ShoppingBagIcon class="h-5 w-5" />
                                            <span>{{ locale === 'en' ? `Buy Book (৳${money(book.price)})` : `বইটি কিনুন (৳${money(book.price)})` }}</span>
                                        </button>

                                        <button
                                            type="button"
                                            @click="handleAddToShelf"
                                            class="inline-flex items-center gap-2 rounded-xl border border-[var(--border)] bg-[var(--surface)] px-5 py-3.5 text-sm font-semibold text-[var(--text)] transition hover:border-[var(--secondary)] hover:text-[var(--secondary)]"
                                        >
                                            <BookmarkSquareIcon class="h-5 w-5" />
                                            <span>{{ t('book_show.add_to_shelf') }}</span>
                                        </button>
                                    </div>

                                    <div class="flex flex-wrap items-center justify-between gap-3 rounded-xl border border-amber-500/25 bg-amber-500/10 px-4 py-3 text-xs text-amber-700 dark:text-amber-300">
                                        <div class="flex items-center gap-2">
                                            <LockClosedIcon class="h-4 w-4 shrink-0" />
                                            <span>
                                                {{ locale === 'en'
                                                    ? 'Sign in is required to read, buy, or save books to your personal bookshelf.'
                                                    : 'বই পড়তে, কিনতে অথবা আপনার বুকশেলফে যুক্ত করতে অনুগ্রহ করে লগইন করুন।' }}
                                            </span>
                                        </div>
                                        <div class="flex items-center gap-3 font-semibold">
                                            <Link :href="loginRedirectUrl" class="inline-flex items-center gap-1 underline hover:text-[var(--primary)]">
                                                <ArrowRightStartOnRectangleIcon class="h-4 w-4" />
                                                {{ t('auth.sign_in') }}
                                            </Link>
                                            <span>·</span>
                                            <Link :href="registerRedirectUrl" class="underline hover:text-[var(--primary)]">
                                                {{ t('auth.sign_up') }}
                                            </Link>
                                        </div>
                                    </div>
                                </div>

                                <!-- Logged-in Student with Full Read Access (Free / Owned / Granted / Subscription Gift) -->
                                <div v-else-if="hasFullReadAccess" class="flex flex-wrap items-center gap-3">
                                    <Link
                                        :href="`/library/${book.id}/read`"
                                        class="inline-flex items-center gap-2 rounded-xl bg-[var(--primary)] px-6 py-3.5 text-sm font-bold text-white shadow-lg shadow-orange-500/20 transition hover:bg-[var(--primary-hover)]"
                                    >
                                        <BookOpenIcon class="h-5 w-5" />
                                        <span>{{ t('book_show.read_now') }}</span>
                                    </Link>

                                    <button
                                        v-if="book.is_free && !onShelf"
                                        type="button"
                                        @click="handleAddToShelf"
                                        class="inline-flex items-center gap-2 rounded-xl border border-emerald-500/40 bg-emerald-500/10 px-5 py-3.5 text-sm font-semibold text-emerald-500 transition hover:bg-emerald-500/20"
                                    >
                                        <BookmarkSquareIcon class="h-5 w-5" />
                                        <span>{{ t('book_show.add_to_shelf') }}</span>
                                    </button>

                                    <span
                                        v-else-if="onShelf"
                                        class="inline-flex items-center gap-1.5 rounded-xl border border-emerald-500/30 bg-emerald-500/10 px-4 py-3 text-xs font-semibold text-emerald-500"
                                    >
                                        <CheckBadgeIcon class="h-4 w-4" />
                                        {{ locale === 'en' ? 'Saved on your Bookshelf' : 'আপনার বুকশেলফে যুক্ত আছে' }}
                                    </span>
                                </div>

                                <!-- Logged-in Student: Paid Book (Purchasable via Wallet or Gateway) -->
                                <div v-else-if="accessType === 'purchasable'" class="flex flex-wrap gap-3">
                                    <button
                                        type="button"
                                        @click="handlePurchase('wallet')"
                                        class="inline-flex items-center gap-2 rounded-xl bg-[var(--primary)] px-6 py-3.5 text-sm font-bold text-white shadow-lg shadow-orange-500/20 transition hover:bg-[var(--primary-hover)]"
                                    >
                                        <WalletIcon class="h-5 w-5" />
                                        <span>{{ t('book_show.pay_with_wallet') }} (৳{{ money(book.price) }})</span>
                                    </button>
                                    <button
                                        type="button"
                                        @click="handlePurchase('zinipay')"
                                        class="inline-flex items-center gap-2 rounded-xl border border-[var(--border)] bg-[var(--surface)] px-6 py-3.5 text-sm font-semibold transition hover:border-[var(--primary)]"
                                    >
                                        <CreditCardIcon class="h-5 w-5 text-[var(--primary)]" />
                                        <span>{{ t('book_show.pay_with_gateway') }}</span>
                                    </button>
                                </div>

                                <!-- Logged-in Student: Insufficient Wallet Balance -->
                                <div v-else class="space-y-3">
                                    <p class="text-xs font-medium text-amber-500">
                                        {{ t('book_show.insufficient_funds') }}
                                    </p>
                                    <div class="flex flex-wrap gap-3">
                                        <button
                                            type="button"
                                            @click="handlePurchase('zinipay')"
                                            class="inline-flex items-center gap-2 rounded-xl bg-[var(--primary)] px-6 py-3.5 text-sm font-bold text-white shadow-lg shadow-orange-500/20 transition hover:bg-[var(--primary-hover)]"
                                        >
                                            <CreditCardIcon class="h-5 w-5" />
                                            <span>{{ t('book_show.pay_with_gateway') }} (৳{{ money(book.price) }})</span>
                                        </button>
                                        <Link
                                            href="/wallet"
                                            class="inline-flex items-center gap-2 rounded-xl border border-[var(--border)] bg-[var(--surface)] px-5 py-3.5 text-sm font-semibold transition hover:bg-[var(--surface2)]"
                                        >
                                            <WalletIcon class="h-5 w-5 text-[var(--secondary)]" />
                                            <span>{{ t('book_show.recharge_wallet') }}</span>
                                        </Link>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Description -->
                        <div v-if="book.description" class="mt-6">
                            <h2 class="font-heading text-lg font-bold">{{ locale === 'en' ? 'About This Book' : 'বইটি সম্পর্কে' }}</h2>
                            <p class="mt-2 whitespace-pre-line text-sm leading-relaxed text-[var(--text-muted)] sm:text-base">
                                {{ book.description }}
                            </p>
                        </div>

                        <!-- AI Study Features Banner -->
                        <div class="mt-6 grid gap-3 rounded-2xl border border-indigo-500/20 bg-gradient-to-r from-indigo-500/10 via-purple-500/5 to-transparent p-4 sm:grid-cols-3">
                            <div class="flex items-start gap-2.5">
                                <SparklesIcon class="mt-0.5 h-5 w-5 shrink-0 text-indigo-400" />
                                <div>
                                    <div class="text-xs font-bold">{{ locale === 'en' ? 'AI Page Tutor' : 'AI পৃষ্ঠা টিউটর' }}</div>
                                    <div class="text-[11px] text-[var(--text-muted)]">{{ locale === 'en' ? 'Ask questions directly while reading' : 'পড়ার সময় যেকোনো লাইনের ব্যাখ্যা নিন' }}</div>
                                </div>
                            </div>
                            <div class="flex items-start gap-2.5">
                                <DocumentTextIcon class="mt-0.5 h-5 w-5 shrink-0 text-emerald-400" />
                                <div>
                                    <div class="text-xs font-bold">{{ locale === 'en' ? 'Smart Flashcards' : 'স্মার্ট ফ্ল্যাশকার্ড' }}</div>
                                    <div class="text-[11px] text-[var(--text-muted)]">{{ locale === 'en' ? 'Generate revision cards from chapters' : 'অধ্যায় থেকে তাৎক্ষণিক রিভিশন কার্ড' }}</div>
                                </div>
                            </div>
                            <div class="flex items-start gap-2.5">
                                <CheckBadgeIcon class="mt-0.5 h-5 w-5 shrink-0 text-orange-400" />
                                <div>
                                    <div class="text-xs font-bold">{{ locale === 'en' ? 'AI Exam Practice' : 'AI মডেল টেস্ট' }}</div>
                                    <div class="text-[11px] text-[var(--text-muted)]">{{ locale === 'en' ? 'Test yourself on owned chapters' : 'পঠিত অধ্যায়ের ওপর তাৎক্ষণিক পরীক্ষা' }}</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Chapter-wise Purchase / Table of Contents -->
                    <div v-if="showChapters" class="rounded-2xl border border-[var(--border)] bg-[var(--surface)] p-6 shadow-sm sm:p-8">
                        <div class="mb-4 flex flex-wrap items-center justify-between gap-2">
                            <div>
                                <h2 class="font-heading text-xl font-bold">{{ t('book_show.buy_chapters') }}</h2>
                                <p class="text-xs text-[var(--text-muted)]">
                                    {{ locale === 'en'
                                        ? 'You can unlock individual chapters or buy the full book.'
                                        : 'আপনি চাইলে পুরো বই না কিনেও শুধু প্রয়োজনীয় অধ্যায় কিনে পড়তে পারেন।' }}
                                </p>
                            </div>
                            <span class="rounded-full bg-[var(--surface2)] px-3 py-1 text-xs font-semibold">
                                {{ chapterTree.length }} {{ locale === 'en' ? 'Chapters' : 'টি অধ্যায়' }}
                            </span>
                        </div>

                        <ul class="divide-y divide-[var(--border)] overflow-hidden rounded-xl border border-[var(--border)]">
                            <li v-for="chapter in chapterTree" :key="chapter.id" class="p-4 transition hover:bg-[var(--surface2)]/40">
                                <div class="flex flex-wrap items-center justify-between gap-3">
                                    <div>
                                        <div class="font-medium text-[var(--text)]">
                                            {{ chapter.chapter_number ? chapter.chapter_number + ' — ' : '' }}{{ chapter.title }}
                                        </div>
                                        <div class="mt-0.5 text-xs text-[var(--text-muted)]">
                                            {{ t('book_show.pages') }} {{ chapter.start_page ?? '?' }}–{{ chapter.end_page ?? '?' }}
                                        </div>
                                    </div>

                                    <div v-if="chapter.owned && isLoggedIn" class="flex items-center gap-3">
                                        <span class="inline-flex items-center gap-1 text-xs font-semibold text-emerald-500">
                                            <CheckBadgeIcon class="h-4 w-4" /> {{ t('book_show.chapter_owned') }}
                                        </span>
                                        <Link
                                            :href="`/library/${book.id}/read?page=${chapter.start_page || 1}`"
                                            class="rounded-lg bg-emerald-500/15 px-3 py-1.5 text-xs font-semibold text-emerald-500 hover:bg-emerald-500/25"
                                        >
                                            {{ t('book_show.read_now') }}
                                        </Link>
                                    </div>

                                    <div v-else-if="chapter.purchasable" class="flex items-center gap-2">
                                        <span class="text-sm font-bold text-[var(--text)]">৳{{ money(chapter.price) }}</span>
                                        <button
                                            type="button"
                                            @click="handlePurchaseChapter(chapter, 'wallet')"
                                            class="inline-flex items-center gap-1.5 rounded-lg bg-[var(--primary)] px-3.5 py-1.5 text-xs font-semibold text-white transition hover:bg-[var(--primary-hover)]"
                                        >
                                            <LockClosedIcon v-if="!isLoggedIn" class="h-3.5 w-3.5" />
                                            <span>{{ t('book_show.buy_chapter') }}</span>
                                        </button>
                                        <button
                                            v-if="isLoggedIn"
                                            type="button"
                                            @click="handlePurchaseChapter(chapter, 'zinipay')"
                                            class="rounded-lg border border-[var(--border)] bg-[var(--surface)] px-3 py-1.5 text-xs font-semibold hover:border-[var(--primary)]"
                                        >
                                            {{ locale === 'en' ? 'Pay Online' : 'অনলাইন পে' }}
                                        </button>
                                    </div>
                                </div>

                                <ul v-if="chapter.children.length" class="mt-3 ml-4 space-y-1.5 border-l-2 border-[var(--border)] pl-3 text-xs text-[var(--text-muted)]">
                                    <li v-for="child in chapter.children" :key="child.id" class="flex items-center justify-between">
                                        <span>{{ child.chapter_number ? child.chapter_number + ' ' : '' }}{{ child.title }}</span>
                                        <span v-if="child.owned && isLoggedIn" class="text-emerald-500">✓</span>
                                    </li>
                                </ul>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Related Books -->
            <div v-if="relatedBooks.length" class="mt-14">
                <div class="mb-6 flex items-center justify-between">
                    <h2 class="font-heading text-2xl font-extrabold">
                        {{ locale === 'en' ? 'Related Books You May Like' : 'সম্পর্কিত আরও বই' }}
                    </h2>
                    <Link href="/library" class="text-sm font-semibold text-[var(--primary)] hover:underline">
                        {{ locale === 'en' ? 'Browse All →' : 'সব বই দেখুন →' }}
                    </Link>
                </div>

                <div class="grid grid-cols-2 gap-4 sm:gap-6 md:grid-cols-4">
                    <Link
                        v-for="rel in relatedBooks"
                        :key="rel.id"
                        :href="`/library/${rel.slug}`"
                        class="group flex flex-col overflow-hidden rounded-2xl border border-[var(--border)] bg-[var(--surface)] transition hover:-translate-y-1 hover:border-[var(--primary)]/50 hover:shadow-lg"
                    >
                        <div class="relative aspect-[3/4] overflow-hidden bg-[var(--surface2)]">
                            <img v-if="rel.cover_image_url" :src="rel.cover_image_url" :alt="rel.title" loading="lazy" class="h-full w-full object-cover transition duration-500 group-hover:scale-105" />
                            <div v-else class="flex h-full w-full items-center justify-center text-[var(--text-muted)]">
                                <BookOpenIcon class="h-10 w-10 opacity-40" />
                            </div>
                        </div>
                        <div class="p-4">
                            <h3 class="line-clamp-2 font-heading text-sm font-bold group-hover:text-[var(--primary)]">{{ rel.title }}</h3>
                            <p v-if="rel.author" class="mt-1 truncate text-xs text-[var(--text-muted)]">{{ rel.author.name }}</p>
                            <div class="mt-2 font-heading text-sm font-extrabold" :class="rel.is_free ? 'text-emerald-500' : ''">
                                {{ rel.is_free ? t('common.free') : `৳${money(rel.price)}` }}
                            </div>
                        </div>
                    </Link>
                </div>
            </div>
        </div>
    </PublicLayout>
</template>
