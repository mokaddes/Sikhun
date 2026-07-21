<script setup>
import { ref, watch } from 'vue';
import { Head, router } from '@inertiajs/vue3';
import StudentLayout from '@/Components/Layout/StudentLayout.vue';
import { useI18n } from '@/i18n';

const props = defineProps({ period: String, filters: Object, top: Array, myRank: Object, optedOut: Boolean });
const { t } = useI18n();

const period = ref(props.period);
const type = ref(props.filters.type ?? '');

watch([period, type], () => {
    router.get('/leaderboard', { period: period.value, type: type.value }, { preserveState: true, replace: true });
});

function badge(rank) {
    if (rank === 1) return '🥇';
    if (rank === 2) return '🥈';
    if (rank === 3) return '🥉';
    if (rank <= 10) return '🔥';
    if (rank <= 100) return '⭐';
    return '';
}
</script>

<template>
    <Head :title="t('leaderboard_page.title')" />
    <StudentLayout>
        <h1 class="font-heading text-2xl font-extrabold mb-6">{{ t('leaderboard_page.title') }}</h1>

        <div v-if="optedOut" class="mb-5 px-4 py-3 rounded-lg bg-[var(--accent)]/10 border border-[var(--accent)]/30 text-sm">
            {{ t('leaderboard_page.opt_out_notice') }}
        </div>

        <div class="flex flex-wrap gap-3 mb-6">
            <div class="flex rounded-lg border border-[var(--border)] overflow-hidden">
                <button v-for="p in ['weekly', 'monthly', 'all_time']" :key="p" @click="period = p"
                    class="px-4 py-2 text-sm font-medium"
                    :class="period === p ? 'bg-[var(--primary)] text-white' : 'bg-[var(--surface)] text-[var(--text-muted)]'">
                    {{ t(`leaderboard_page.${p}`) }}
                </button>
            </div>
            <select v-model="type" class="px-4 py-2 rounded-lg bg-[var(--surface)] border border-[var(--border)] text-sm">
                <option value="">{{ t('common.all') }}</option>
                <option value="ssc">SSC</option><option value="hsc">HSC</option>
                <option value="university">University</option><option value="job">Job</option>
            </select>
        </div>

        <div v-if="myRank" class="mb-5 rounded-xl border border-[var(--primary)]/30 bg-[var(--primary)]/5 p-4 flex items-center justify-between">
            <span class="text-sm font-medium">{{ t('leaderboard_page.your_rank') }}</span>
            <span class="font-heading font-bold">#{{ myRank.rank }} — {{ myRank.percentage }}%</span>
        </div>

        <div v-if="top.length" class="rounded-xl border border-[var(--border)] bg-[var(--surface)] overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-[var(--border)] text-left text-[var(--text-muted)]">
                        <th class="px-5 py-3 font-medium">{{ t('leaderboard_page.rank') }}</th>
                        <th class="px-5 py-3 font-medium">{{ t('leaderboard_page.student') }}</th>
                        <th class="px-5 py-3 font-medium">{{ t('common.type') }}</th>
                        <th class="px-5 py-3 font-medium text-right">{{ t('leaderboard_page.score') }}</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="entry in top" :key="entry.student_id" class="border-b border-[var(--border)] last:border-0">
                        <td class="px-5 py-3">{{ badge(entry.rank) }} #{{ entry.rank }}</td>
                        <td class="px-5 py-3 font-medium">{{ entry.name }}</td>
                        <td class="px-5 py-3 uppercase text-xs text-[var(--text-muted)]">{{ entry.type }}</td>
                        <td class="px-5 py-3 text-right font-semibold">{{ entry.percentage }}%</td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div v-else class="rounded-xl border border-dashed border-[var(--border)] p-12 text-center text-[var(--text-muted)]">
            {{ t('leaderboard_page.no_entries') }}
        </div>
    </StudentLayout>
</template>
