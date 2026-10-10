<script setup>
import { ref } from 'vue';
import { Head } from '@inertiajs/vue3';
import StudentLayout from '@/Components/Layout/StudentLayout.vue';
import { useI18n } from '@/i18n';
import { ClipboardDocumentIcon, CheckIcon } from '@heroicons/vue/24/outline';

const props = defineProps({ referralCode: String, referralLink: String, stats: Object, referrals: Array });
const { t, locale } = useI18n();
const copied = ref(false);

function copyLink() {
    navigator.clipboard.writeText(props.referralLink);
    copied.value = true;
    setTimeout(() => (copied.value = false), 2000);
}
</script>

<template>
    <Head :title="t('referral_page.title')" />
    <StudentLayout>
        <header class="anim-rise mb-6">
            <span class="eyebrow">{{ locale === 'en' ? 'Grow' : 'উন্নতি' }}</span>
            <h1 class="mt-1 font-heading text-2xl font-extrabold sm:text-3xl">{{ t('referral_page.title') }}</h1>
        </header>

        <div class="card anim-rise mb-6 rounded-2xl p-6">
            <label class="label">{{ t('referral_page.your_link') }}</label>
            <div class="flex flex-col gap-2 sm:flex-row">
                <input :value="referralLink" readonly class="input flex-1" />
                <button @click="copyLink" class="btn btn-primary shrink-0">
                    <CheckIcon v-if="copied" class="h-4 w-4" />
                    <ClipboardDocumentIcon v-else class="h-4 w-4" />
                    {{ copied ? t('referral_page.copied') : t('referral_page.copy') }}
                </button>
            </div>
        </div>

        <div class="stagger mb-6 grid grid-cols-3 gap-4">
            <div class="card rounded-2xl p-5">
                <div class="text-sm text-[var(--text-muted)]">{{ t('referral_page.total_referred') }}</div>
                <div class="mt-2 font-heading text-2xl font-extrabold">{{ stats.total_referred }}</div>
            </div>
            <div class="card rounded-2xl p-5">
                <div class="text-sm text-[var(--text-muted)]">{{ t('referral_page.total_converted') }}</div>
                <div class="mt-2 font-heading text-2xl font-extrabold">{{ stats.total_converted }}</div>
            </div>
            <div class="card rounded-2xl p-5">
                <div class="text-sm text-[var(--text-muted)]">{{ t('referral_page.total_earned') }}</div>
                <div class="mt-2 font-heading text-2xl font-extrabold text-[var(--secondary)]">৳{{ stats.total_earned }}</div>
            </div>
        </div>

        <h2 class="mb-3 font-heading text-lg font-bold">{{ t('referral_page.your_referrals') }}</h2>
        <div v-if="referrals.length" class="card overflow-hidden rounded-2xl">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <tbody>
                        <tr v-for="r in referrals" :key="r.id" class="border-b border-[var(--border)] last:border-0">
                            <td class="px-5 py-3.5 font-medium">{{ r.referee.name }}</td>
                            <td class="px-5 py-3.5 text-[var(--text-muted)]">{{ new Date(r.referee.created_at).toLocaleDateString() }}</td>
                            <td class="px-5 py-3.5 text-right">
                                <span class="badge" :class="r.status === 'rewarded' ? 'bg-[var(--secondary)]/15 text-[var(--secondary)]' : 'bg-[var(--text-muted)]/15 text-[var(--text-muted)]'">
                                    {{ r.status === 'rewarded' ? t('referral_page.status_rewarded') : t('referral_page.status_pending') }}
                                </span>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div v-else class="card rounded-2xl border-dashed p-8 text-center text-[var(--text-muted)]">
            {{ t('referral_page.no_referrals') }}
        </div>
    </StudentLayout>
</template>
