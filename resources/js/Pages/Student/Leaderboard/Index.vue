<script setup>
import { ref, watch } from 'vue';
import { Head, router } from '@inertiajs/vue3';
import StudentLayout from '@/Components/Layout/StudentLayout.vue';
import { useI18n } from '@/i18n';
import { TrophyIcon, FireIcon } from '@heroicons/vue/24/outline';

const props = defineProps({ period: String, filters: Object, top: Array, myRank: Object, optedOut: Boolean });
const { t, locale } = useI18n();

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

const rowClass = (rank) => rank === 1
    ? 'bg-[var(--primary)]/10'
    : rank <= 3 ? 'bg-[var(--surface2)]/60' : '';
</script>

<template>
    <Head :title="t('leaderboard_page.title')" />
    <StudentLayout>
        <header class="anim-rise mb-6">
            <span class="eyebrow">{{ locale === 'en' ? 'Grow' : 'উন্নতি' }}</span>
            <h1 class="mt-1 flex items-center gap-2 font-heading text-2xl font-extrabold sm:text-3xl">
                <TrophyIcon class="h-7 w-7 text-[var(--primary)]" /> {{ t('leaderboard_page.title') }}
            </h1>
        </header>

        <div v-if="optedOut" class="mb-5 rounded-2xl border border-[var(--accent)]/30 bg-[var(--accent)]/10 px-4 py-3 text-sm">
            {{ t('leaderboard_page.opt_out_notice') }}
        </div>

        <div class="mb-6 flex flex-wrap items-center gap-3">
            <div class="flex rounded-full border border-[var(--border)] bg-[var(--surface)] p-1">
                <button
                    v-for="p in ['weekly', 'monthly', 'all_time']"
                    :key="p"
                    class="rounded-full px-4 py-1.5 text-sm font-medium transition-colors"
                    :class="period === p ? 'bg-[var(--primary)] text-white shadow-sm' : 'text-[var(--text-muted)] hover:text-[var(--text)]'"
                    @click="period = p"
                >
                    {{ t(`leaderboard_page.${p}`) }}
                </button>
            </div>
            <select v-model="type" class="input" style="width:auto">
                <option value="">{{ t('common.all') }}</option>
                <option value="ssc">SSC</option><option value="hsc">HSC</option>
                <option value="university">University</option><option value="job">Job</option>
            </select>
        </div>

        <div v-if="myRank" class="card anim-rise mb-5 flex items-center justify-between rounded-2xl border-[var(--primary)]/30 bg-[var(--primary)]/5 p-4">
            <span class="flex items-center gap-2 text-sm font-medium"><FireIcon class="h-4.5 w-4.5 text-[var(--primary)]" /> {{ t('leaderboard_page.your_rank') }}</span>
            <span class="font-heading text-lg font-bold">#{{ myRank.rank }} — {{ myRank.percentage }}%</span>
        </div>

        <div v-if="top.length" class="card overflow-hidden rounded-2xl">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-[var(--border)] text-left text-xs uppercase tracking-wide text-[var(--text-muted)]">
                            <th class="px-5 py-3 font-semibold">{{ t('leaderboard_page.rank') }}</th>
                            <th class="px-5 py-3 font-semibold">{{ t('leaderboard_page.student') }}</th>
                            <th class="px-5 py-3 font-semibold">{{ t('common.type') }}</th>
                            <th class="px-5 py-3 text-right font-semibold">{{ t('leaderboard_page.score') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="entry in top" :key="entry.student_id" class="border-b border-[var(--border)] transition-colors last:border-0 hover:bg-[var(--surface2)]/50" :class="rowClass(entry.rank)">
                            <td class="px-5 py-3 font-semibold">
                                <span class="mr-1">{{ badge(entry.rank) }}</span>#{{ entry.rank }}
                            </td>
                            <td class="px-5 py-3 font-medium">{{ entry.name }}</td>
                            <td class="px-5 py-3 text-xs uppercase text-[var(--text-muted)]">{{ entry.type }}</td>
                            <td class="px-5 py-3 text-right font-heading font-bold">{{ entry.percentage }}%</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div v-else class="card flex flex-col items-center gap-3 rounded-2xl border-dashed p-12 text-center text-[var(--text-muted)]">
            <TrophyIcon class="h-10 w-10 opacity-40" />
            {{ t('leaderboard_page.no_entries') }}
        </div>
    </StudentLayout>
</template>
