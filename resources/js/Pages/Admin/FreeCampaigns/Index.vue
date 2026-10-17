<script setup>
import { Head, Link } from '@inertiajs/vue3';
import AdminLayout from '@/Components/Layout/AdminLayout.vue';
import TableDropdown from '@/Components/UI/TableDropdown.vue';
import { useI18n } from '@/i18n';
import {
    PlusIcon,
    MegaphoneIcon,
    CalendarDaysIcon,
    UsersIcon,
    PencilSquareIcon,
    TrashIcon,
} from '@heroicons/vue/24/outline';

defineProps({ campaigns: Object });
const { t, locale } = useI18n();

function isLive(c) {
    const now = Date.now();
    return c.is_active && new Date(c.starts_at).getTime() <= now && new Date(c.ends_at).getTime() > now;
}

function getCampaignActions(c) {
    return [
        { label: t('common.edit'), href: `/admin/free-campaigns/${c.id}/edit`, icon: PencilSquareIcon },
        { divider: true },
        { label: t('common.delete'), href: `/admin/free-campaigns/${c.id}`, method: 'delete', danger: true, icon: TrashIcon },
    ];
}
</script>

<template>
    <Head title="Free Campaigns" />
    <AdminLayout>
        <!-- Page Header -->
        <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <div class="flex items-center gap-3">
                    <h1 class="font-heading text-2xl font-extrabold">Free Campaigns</h1>
                    <span v-if="campaigns?.data?.length !== undefined" class="rounded-full bg-[var(--surface2)] px-2.5 py-0.5 text-xs font-semibold text-[var(--text-muted)]">
                        {{ campaigns.data.length }}
                    </span>
                </div>
                <p class="mt-1 text-sm text-[var(--text-muted)]">
                    {{ locale === 'en' ? 'Open free access to all library books & courses for a limited time window.' : 'নির্দিষ্ট সময়ের জন্য বিনামূল্যে সকল সামগ্রী ব্যবহারের ক্যাম্পেইন পরিচালনা করুন।' }}
                </p>
            </div>
            <Link href="/admin/free-campaigns/create" class="btn btn-primary self-start sm:self-auto gap-2">
                <PlusIcon class="h-4 w-4" />
                {{ t('common.create') }}
            </Link>
        </div>

        <!-- Campaign Cards Grid -->
        <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
            <div
                v-for="c in campaigns.data"
                :key="c.id"
                class="card card-hover relative flex flex-col justify-between overflow-hidden rounded-2xl border border-[var(--border)] p-6 transition-all"
            >
                <div>
                    <div class="flex items-start justify-between gap-3">
                        <div class="min-w-0">
                            <h3 class="font-heading text-lg font-bold text-[var(--text)] truncate">{{ c.title }}</h3>
                            <div class="mt-1 flex items-center gap-1.5 text-xs text-[var(--text-muted)]">
                                <UsersIcon class="h-3.5 w-3.5 text-[var(--secondary)]" />
                                <span>{{ c.scope === 'all' ? (locale === 'en' ? 'All students' : 'সকল শিক্ষার্থী') : `${c.students_count} ${locale === 'en' ? 'students' : 'জন শিক্ষার্থী'}` }}</span>
                            </div>
                        </div>
                        <div class="flex items-center gap-2">
                            <span
                                class="inline-flex items-center gap-1 rounded-full px-2.5 py-0.5 text-xs font-semibold uppercase tracking-wide"
                                :class="isLive(c) ? 'bg-emerald-500/15 text-emerald-600 dark:text-emerald-400' : c.is_active ? 'bg-[var(--secondary)]/15 text-[var(--secondary)]' : 'bg-[var(--text-muted)]/15 text-[var(--text-muted)]'"
                            >
                                <span class="h-1.5 w-1.5 rounded-full" :class="isLive(c) ? 'bg-emerald-500 animate-pulse' : c.is_active ? 'bg-[var(--secondary)]' : 'bg-[var(--text-muted)]'"></span>
                                {{ isLive(c) ? 'LIVE' : c.is_active ? 'Scheduled' : t('common.inactive') }}
                            </span>
                            <TableDropdown :items="getCampaignActions(c)" align="right" />
                        </div>
                    </div>

                    <div class="my-4 rounded-xl bg-[var(--surface2)]/50 p-3 text-xs text-[var(--text-muted)] flex items-center gap-2 border border-[var(--border)]">
                        <CalendarDaysIcon class="h-4 w-4 text-[var(--primary)] shrink-0" />
                        <span class="truncate">{{ new Date(c.starts_at).toLocaleDateString() }} → {{ new Date(c.ends_at).toLocaleDateString() }}</span>
                    </div>

                    <p v-if="c.description" class="text-xs text-[var(--text-muted)] line-clamp-3 mb-4 leading-relaxed">{{ c.description }}</p>
                </div>

                <div class="mt-4 pt-3 border-t border-[var(--border)] flex items-center justify-between">
                    <Link
                        :href="`/admin/free-campaigns/${c.id}/edit`"
                        class="text-xs font-semibold text-[var(--primary)] hover:underline inline-flex items-center gap-1"
                    >
                        <PencilSquareIcon class="h-3.5 w-3.5" />
                        {{ t('common.edit') }}
                    </Link>
                </div>
            </div>
        </div>

        <div v-if="!campaigns.data || !campaigns.data.length" class="card rounded-2xl border border-dashed border-[var(--border)] p-12 text-center">
            <MegaphoneIcon class="mx-auto h-12 w-12 text-[var(--text-muted)]/40" />
            <div class="mt-3 font-heading font-semibold text-lg">{{ t('common.no_results') }}</div>
            <p class="mt-1 text-sm text-[var(--text-muted)]">
                {{ locale === 'en' ? 'No free campaigns scheduled.' : 'কোনো ফ্রি ক্যাম্পেইন পাওয়া যায়নি।' }}
            </p>
        </div>
    </AdminLayout>
</template>