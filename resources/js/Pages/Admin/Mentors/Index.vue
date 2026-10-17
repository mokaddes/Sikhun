<script setup>
import { ref, computed } from 'vue';
import { Head, Link } from '@inertiajs/vue3';
import AdminLayout from '@/Components/Layout/AdminLayout.vue';
import TableDropdown from '@/Components/UI/TableDropdown.vue';
import { useI18n } from '@/i18n';
import {
    PlusIcon,
    MagnifyingGlassIcon,
    UserGroupIcon,
    AcademicCapIcon,
    PencilSquareIcon,
    TrashIcon,
} from '@heroicons/vue/24/outline';

const props = defineProps({ mentors: Array });
const { t, locale } = useI18n();

const search = ref('');

const filteredMentors = computed(() => {
    if (!search.value.trim()) return props.mentors || [];
    const q = search.value.toLowerCase();
    return (props.mentors || []).filter(m =>
        m.name?.toLowerCase().includes(q) ||
        m.designation?.toLowerCase().includes(q)
    );
});

function getMentorActions(mentor) {
    return [
        { label: t('common.edit'), href: `/admin/mentors/${mentor.id}/edit`, icon: PencilSquareIcon },
        { divider: true },
        { label: t('common.delete'), href: `/admin/mentors/${mentor.id}`, method: 'delete', danger: true, icon: TrashIcon },
    ];
}
</script>

<template>
    <Head :title="t('admin.mentors.title')" />
    <AdminLayout>
        <!-- Page Header -->
        <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <div class="flex items-center gap-3">
                    <h1 class="font-heading text-2xl font-extrabold">{{ t('admin.mentors.title') }}</h1>
                    <span v-if="mentors?.length !== undefined" class="rounded-full bg-[var(--surface2)] px-2.5 py-0.5 text-xs font-semibold text-[var(--text-muted)]">
                        {{ mentors.length }}
                    </span>
                </div>
                <p class="mt-1 text-sm text-[var(--text-muted)]">
                    {{ locale === 'en' ? 'Manage instructors, course authors, and mentors.' : 'কোর্স ইন্সট্রাক্টর ও মেন্টরদের প্রোফাইল পরিচালনা করুন।' }}
                </p>
            </div>
            <Link href="/admin/mentors/create" class="btn btn-primary self-start sm:self-auto gap-2">
                <PlusIcon class="h-4 w-4" />
                {{ t('admin.mentors.new') }}
            </Link>
        </div>

        <!-- Filter Toolbar -->
        <div class="mb-5 flex items-center justify-between">
            <div class="relative flex-1 max-w-sm">
                <MagnifyingGlassIcon class="pointer-events-none absolute left-3.5 top-1/2 h-4 w-4 -translate-y-1/2 text-[var(--text-muted)]" />
                <input
                    v-model="search"
                    type="text"
                    :placeholder="t('common.search') + '...'"
                    class="w-full rounded-xl border border-[var(--border)] bg-[var(--surface)] py-2 pl-9.5 pr-4 text-sm text-[var(--text)] transition-colors focus:border-[var(--primary)] focus:outline-hidden"
                />
            </div>
        </div>

        <!-- Modern Datatable -->
        <div class="card overflow-hidden rounded-2xl border border-[var(--border)] bg-[var(--surface)] shadow-sm">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead>
                        <tr class="border-b border-[var(--border)] bg-[var(--surface2)]/50 text-[11px] font-semibold uppercase tracking-wider text-[var(--text-muted)]">
                            <th class="px-5 py-3.5">{{ t('common.name') }}</th>
                            <th class="px-5 py-3.5">{{ t('admin.mentors.designation') }}</th>
                            <th class="px-5 py-3.5">Courses</th>
                            <th class="px-5 py-3.5 text-right">{{ t('common.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[var(--border)]">
                        <tr
                            v-for="m in filteredMentors"
                            :key="m.id"
                            class="group transition-colors hover:bg-[var(--surface2)]/40"
                        >
                            <td class="px-5 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-gradient-to-br from-[var(--secondary)]/20 to-[var(--primary)]/20 font-heading text-xs font-bold text-[var(--secondary)]">
                                        {{ m.name ? m.name.charAt(0).toUpperCase() : 'M' }}
                                    </div>
                                    <span class="font-semibold text-[var(--text)]">{{ m.name }}</span>
                                </div>
                            </td>

                            <td class="px-5 py-4 text-[var(--text-muted)]">
                                {{ m.designation || '—' }}
                            </td>

                            <td class="px-5 py-4">
                                <span class="inline-flex items-center gap-1.5 rounded-full bg-[var(--surface2)] px-2.5 py-0.5 text-xs font-semibold text-[var(--text-muted)]">
                                    <AcademicCapIcon class="h-3.5 w-3.5 text-[var(--secondary)]" />
                                    {{ m.courses_count }}
                                </span>
                            </td>

                            <td class="px-5 py-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <Link
                                        :href="`/admin/mentors/${m.id}/edit`"
                                        class="hidden sm:inline-flex items-center gap-1 rounded-lg border border-transparent px-2.5 py-1 text-xs font-semibold text-[var(--primary)] hover:border-[var(--primary)]/20 hover:bg-[var(--primary)]/10 transition-colors"
                                    >
                                        {{ t('common.edit') }}
                                    </Link>
                                    <TableDropdown :items="getMentorActions(m)" align="right" />
                                </div>
                            </td>
                        </tr>

                        <!-- Empty state -->
                        <tr v-if="!filteredMentors.length">
                            <td colspan="4" class="px-5 py-16 text-center">
                                <UserGroupIcon class="mx-auto h-10 w-10 text-[var(--text-muted)]/50" />
                                <div class="mt-3 font-heading font-semibold text-base">{{ t('common.no_results') }}</div>
                                <p class="mt-1 text-xs text-[var(--text-muted)]">{{ locale === 'en' ? 'No mentors found.' : 'কোনো মেন্টর পাওয়া যায়নি।' }}</p>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </AdminLayout>
</template>
