<script setup>
import { ref } from 'vue';
import { Head } from '@inertiajs/vue3';
import StudentLayout from '@/Components/Layout/StudentLayout.vue';
import { useI18n } from '@/i18n';

const props = defineProps({ referralCode: String, referralLink: String, stats: Object, referrals: Array });
const { t } = useI18n();
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
        <h1 class="font-heading text-2xl font-extrabold mb-6">{{ t('referral_page.title') }}</h1>

        <div class="rounded-xl border border-[var(--border)] bg-[var(--surface)] p-6 mb-6">
            <label class="block text-sm font-medium mb-2">{{ t('referral_page.your_link') }}</label>
            <div class="flex gap-2">
                <input :value="referralLink" readonly class="flex-1 px-4 py-2.5 rounded-lg bg-[var(--surface2)] border border-[var(--border)] text-sm" />
                <button @click="copyLink" class="px-5 py-2.5 rounded-lg bg-[var(--primary)] hover:bg-[var(--primary-hover)] text-white text-sm font-semibold whitespace-nowrap">
                    {{ copied ? t('referral_page.copied') : t('referral_page.copy') }}
                </button>
            </div>
        </div>

        <div class="grid grid-cols-3 gap-4 mb-6">
            <div class="rounded-xl border border-[var(--border)] bg-[var(--surface)] p-5">
                <div class="text-sm text-[var(--text-muted)] mb-1">{{ t('referral_page.total_referred') }}</div>
                <div class="text-2xl font-heading font-extrabold">{{ stats.total_referred }}</div>
            </div>
            <div class="rounded-xl border border-[var(--border)] bg-[var(--surface)] p-5">
                <div class="text-sm text-[var(--text-muted)] mb-1">{{ t('referral_page.total_converted') }}</div>
                <div class="text-2xl font-heading font-extrabold">{{ stats.total_converted }}</div>
            </div>
            <div class="rounded-xl border border-[var(--border)] bg-[var(--surface)] p-5">
                <div class="text-sm text-[var(--text-muted)] mb-1">{{ t('referral_page.total_earned') }}</div>
                <div class="text-2xl font-heading font-extrabold">৳{{ stats.total_earned }}</div>
            </div>
        </div>

        <h2 class="font-heading text-lg font-bold mb-3">{{ t('referral_page.your_referrals') }}</h2>
        <div v-if="referrals.length" class="rounded-xl border border-[var(--border)] bg-[var(--surface)] overflow-x-auto">
            <table class="w-full text-sm">
                <tbody>
                    <tr v-for="r in referrals" :key="r.id" class="border-b border-[var(--border)] last:border-0">
                        <td class="px-5 py-3">{{ r.referee.name }}</td>
                        <td class="px-5 py-3 text-[var(--text-muted)]">{{ new Date(r.referee.created_at).toLocaleDateString() }}</td>
                        <td class="px-5 py-3 text-right">
                            <span class="px-2 py-0.5 rounded text-xs font-medium" :class="r.status === 'rewarded' ? 'bg-[var(--secondary)]/15 text-[var(--secondary)]' : 'bg-[var(--text-muted)]/15 text-[var(--text-muted)]'">
                                {{ r.status === 'rewarded' ? t('referral_page.status_rewarded') : t('referral_page.status_pending') }}
                            </span>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div v-else class="rounded-xl border border-dashed border-[var(--border)] p-8 text-center text-[var(--text-muted)]">
            {{ t('referral_page.no_referrals') }}
        </div>
    </StudentLayout>
</template>
