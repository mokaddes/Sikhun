<script setup>
import { computed } from 'vue';
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import StudentLayout from '@/Components/Layout/StudentLayout.vue';
import SeoHead from '@/Components/Seo/SeoHead.vue';
import { useI18n } from '@/i18n';

const props = defineProps({ book: Object, accessType: String, chapters: { type: Array, default: () => [] }, seo: Object });
const { t } = useI18n();
const isLoggedIn = !!usePage().props.auth?.student;

function purchase(method) {
    router.post(`/library/${props.book.id}/purchase`, { payment_method: method });
}

function purchaseChapter(chapter, method) {
    router.post(`/library/${props.book.id}/chapters/${chapter.id}/purchase`, { payment_method: method }, {
        preserveScroll: true,
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
</script>

<template>
    <SeoHead :seo="seo" />
    <StudentLayout>
        <div class="grid md:grid-cols-3 gap-8">
            <div class="rounded-xl border border-[var(--border)] bg-[var(--surface2)] aspect-[3/4] overflow-hidden flex items-center justify-center text-[var(--text-muted)]">
                <img v-if="book.cover_image_url" :src="book.cover_image_url" :alt="book.title" width="400" height="533" class="w-full h-full object-cover" />
                <span v-else>{{ t('home.no_cover') }}</span>
            </div>

            <div class="md:col-span-2">
                <div class="flex items-center gap-2 mb-2">
                    <span class="text-xs uppercase font-medium text-[var(--text-muted)]">{{ book.level }}</span>
                    <span v-if="book.is_free" class="px-2 py-0.5 rounded text-xs font-semibold bg-[var(--secondary)]/15 text-[var(--secondary)]">{{ t('book_show.free_badge') }}</span>
                </div>
                <h1 class="font-heading text-3xl font-extrabold mb-3">{{ book.title }}</h1>
                <p v-if="book.author" class="text-[var(--text-muted)] mb-1">{{ t('book_show.by') }}: {{ book.author.name }}</p>
                <p v-if="book.publication" class="text-[var(--text-muted)] mb-4">{{ t('book_show.publisher') }}: {{ book.publication.name }}</p>
                <p class="text-[var(--text-muted)] mb-1" v-if="book.total_pages">{{ book.total_pages }} {{ t('book_show.pages') }}</p>

                <p class="mt-4 mb-8 leading-relaxed">{{ book.description }}</p>

                <div class="font-heading text-2xl font-extrabold mb-5">
                    <span v-if="book.is_free" class="text-[var(--secondary)]">{{ t('common.free') }}</span>
                    <span v-else>৳{{ book.price }}</span>
                </div>

                <!-- Access-type-driven CTA (REQ-LIB-06) -->
                <div v-if="['free', 'granted', 'owned', 'subscription_gift'].includes(accessType)" class="flex items-center gap-3">
                    <span v-if="accessType === 'granted'" class="px-3 py-1.5 rounded-lg text-sm font-medium bg-[var(--primary)]/15 text-[var(--primary)]">
                        {{ t('book_show.coupon_access_badge') }}
                    </span>
                    <span v-else-if="accessType !== 'free'" class="px-3 py-1.5 rounded-lg text-sm font-medium bg-[var(--secondary)]/15 text-[var(--secondary)]">
                        {{ accessType === 'owned' ? t('book_show.already_owned') : t('book_show.subscription_gift_badge') }}
                    </span>
                    <Link :href="`/library/${book.id}/read`" class="px-6 py-3 rounded-lg bg-[var(--primary)] hover:bg-[var(--primary-hover)] text-white font-semibold">
                        {{ t('book_show.read_now') }}
                    </Link>
                </div>

                <div v-else-if="accessType === 'guest'" class="flex flex-wrap gap-3">
                    <Link href="/login" class="px-6 py-3 rounded-lg bg-[var(--primary)] hover:bg-[var(--primary-hover)] text-white font-semibold">
                        {{ t('auth.sign_in') }}
                    </Link>
                    <Link href="/register" class="px-6 py-3 rounded-lg border border-[var(--border)] hover:bg-[var(--surface2)] font-semibold">
                        {{ t('auth.sign_up') }}
                    </Link>
                </div>

                <div v-else-if="accessType === 'purchasable'" class="flex flex-wrap gap-3">
                    <button @click="purchase('wallet')" class="px-6 py-3 rounded-lg bg-[var(--primary)] hover:bg-[var(--primary-hover)] text-white font-semibold">
                        {{ t('book_show.pay_with_wallet') }}
                    </button>
                    <button @click="purchase('zinipay')" class="px-6 py-3 rounded-lg border border-[var(--border)] hover:bg-[var(--surface2)] font-semibold">
                        {{ t('book_show.pay_with_gateway') }}
                    </button>
                </div>

                <div v-else class="space-y-3">
                    <p class="text-[var(--accent)] text-sm">{{ t('book_show.insufficient_funds') }}</p>
                    <div class="flex flex-wrap gap-3">
                        <Link href="/wallet" class="px-6 py-3 rounded-lg bg-[var(--primary)] hover:bg-[var(--primary-hover)] text-white font-semibold">
                            {{ t('book_show.recharge_wallet') }}
                        </Link>
                        <button @click="purchase('zinipay')" class="px-6 py-3 rounded-lg border border-[var(--border)] hover:bg-[var(--surface2)] font-semibold">
                            {{ t('book_show.pay_with_gateway') }}
                        </button>
                    </div>
                </div>

                <!-- Chapter-wise purchase -->
                <div v-if="showChapters" class="mt-8">
                    <h2 class="font-heading text-lg font-bold mb-3">{{ t('book_show.buy_chapters') }}</h2>
                    <ul class="rounded-xl border border-[var(--border)] divide-y divide-[var(--border)]">
                        <li v-for="chapter in chapterTree" :key="chapter.id" class="p-4">
                            <div class="flex items-center justify-between gap-3">
                                <div>
                                    <div class="font-medium">
                                        {{ chapter.chapter_number ? chapter.chapter_number + ' — ' : '' }}{{ chapter.title }}
                                    </div>
                                    <div class="text-xs text-[var(--text-muted)]">
                                        {{ t('book_show.pages') }} {{ chapter.start_page ?? '?' }}–{{ chapter.end_page ?? '?' }}
                                    </div>
                                </div>

                                <div v-if="chapter.owned" class="text-sm font-medium text-[var(--secondary)]">
                                    ✓ {{ t('book_show.chapter_owned') }}
                                </div>
                                <div v-else-if="chapter.purchasable && isLoggedIn" class="flex items-center gap-2">
                                    <span class="text-sm font-semibold">🔒 ৳{{ chapter.price }}</span>
                                    <button @click="purchaseChapter(chapter, 'wallet')"
                                        class="px-3 py-1.5 rounded-lg bg-[var(--primary)] hover:bg-[var(--primary-hover)] text-white text-xs font-semibold">
                                        {{ t('book_show.buy_chapter') }}
                                    </button>
                                </div>
                                <div v-else-if="chapter.purchasable && !isLoggedIn" class="text-xs">
                                    <Link href="/login" class="text-[var(--primary)] hover:underline font-medium">{{ t('auth.sign_in') }}</Link>
                                    {{ t('book_show.to_buy_chapter') }}
                                </div>
                            </div>

                            <ul v-if="chapter.children.length" class="mt-2 ml-4 space-y-1 text-sm text-[var(--text-muted)]">
                                <li v-for="child in chapter.children" :key="child.id" class="flex items-center justify-between">
                                    <span>{{ child.chapter_number ? child.chapter_number + ' ' : '' }}{{ child.title }}</span>
                                    <span v-if="child.owned" class="text-[var(--secondary)] text-xs">✓</span>
                                </li>
                            </ul>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </StudentLayout>
</template>
