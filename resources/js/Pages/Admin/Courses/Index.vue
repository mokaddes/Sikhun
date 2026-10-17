<script setup>
import { ref, watch } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import AdminLayout from '@/Components/Layout/AdminLayout.vue';
import Pagination from '@/Components/UI/Pagination.vue';
import TableDropdown from '@/Components/UI/TableDropdown.vue';
import { useI18n } from '@/i18n';
import {
    PlusIcon,
    MagnifyingGlassIcon,
    AcademicCapIcon,
    EyeIcon,
    PencilSquareIcon,
    TrashIcon,
    UserGroupIcon,
} from '@heroicons/vue/24/outline';

const props = defineProps({
    courses: Object,
    filters: { type: Object, default: () => ({ search: '', status: '' }) },
});

const { t, locale } = useI18n();

const search = ref(props.filters?.search ?? '');
const status = ref(props.filters?.status ?? '');

let debounceTimer = null;
watch([search, status], () => {
    clearTimeout(debounceTimer);
    debounceTimer = setTimeout(() => {
        router.get('/admin/courses', {
            search: search.value || undefined,
            status: status.value || undefined,
        }, { preserveState: true, replace: true });
    }, 300);
});

function getCourseActions(course) {
    return [
        { label: t('common.edit'), href: `/admin/courses/${course.id}/edit`, icon: PencilSquareIcon },
        { label: locale.value === 'en' ? 'View Course' : 'কোর্স দেখুন', href: `/courses/${course.slug}`, icon: EyeIcon },
        { divider: true },
        { label: t('common.delete'), href: `/admin/courses/${course.id}`, method: 'delete', danger: true, icon: TrashIcon },
    ];
}
</script>

<template>
    <Head :title="t('admin.courses.title')" />
    <AdminLayout>
        <!-- Page Header -->
        <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <div class="flex items-center gap-3">
                    <h1 class="font-heading text-2xl font-extrabold">{{ t('admin.courses.title') }}</h1>
                    <span v-if="courses?.total !== undefined" class="rounded-full bg-[var(--surface2)] px-2.5 py-0.5 text-xs font-semibold text-[var(--text-muted)]">
                        {{ courses.total }}
                    </span>
                </div>
                <p class="mt-1 text-sm text-[var(--text-muted)]">
                    {{ locale === 'en' ? 'Manage curriculum, lessons, pricing, and instructors.' : 'কোর্স কারিকুলাম, লেসন, মূল্য এবং ইন্সট্রাক্টর পরিচালনা করুন।' }}
                </p>
            </div>
            <Link href="/admin/courses/create" class="btn btn-primary self-start sm:self-auto gap-2">
                <PlusIcon class="h-4 w-4" />
                {{ t('admin.courses.new') }}
            </Link>
        </div>

        <!-- Filter Toolbar -->
        <div class="mb-5 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div class="relative flex-1 max-w-md">
                <MagnifyingGlassIcon class="pointer-events-none absolute left-3.5 top-1/2 h-4 w-4 -translate-y-1/2 text-[var(--text-muted)]" />
                <input
                    v-model="search"
                    type="text"
                    :placeholder="t('common.search') + '...'"
                    class="w-full rounded-xl border border-[var(--border)] bg-[var(--surface)] py-2 pl-9.5 pr-4 text-sm text-[var(--text)] transition-colors focus:border-[var(--primary)] focus:outline-hidden"
                />
            </div>

            <!-- Status Tabs -->
            <div class="flex items-center gap-1.5 rounded-xl border border-[var(--border)] bg-[var(--surface)] p-1">
                <button
                    type="button"
                    @click="status = ''"
                    class="rounded-lg px-3 py-1.5 text-xs font-semibold transition-colors"
                    :class="!status ? 'bg-[var(--primary)] text-white shadow-xs' : 'text-[var(--text-muted)] hover:text-[var(--text)]'"
                >
                    {{ t('common.all') }}
                </button>
                <button
                    type="button"
                    @click="status = 'active'"
                    class="rounded-lg px-3 py-1.5 text-xs font-semibold transition-colors"
                    :class="status === 'active' ? 'bg-[var(--primary)] text-white shadow-xs' : 'text-[var(--text-muted)] hover:text-[var(--text)]'"
                >
                    {{ t('common.active') }}
                </button>
                <button
                    type="button"
                    @click="status = 'inactive'"
                    class="rounded-lg px-3 py-1.5 text-xs font-semibold transition-colors"
                    :class="status === 'inactive' ? 'bg-[var(--primary)] text-white shadow-xs' : 'text-[var(--text-muted)] hover:text-[var(--text)]'"
                >
                    {{ t('common.inactive') }}
                </button>
            </div>
        </div>

        <!-- Modern Datatable -->
        <div class="card overflow-hidden rounded-2xl border border-[var(--border)] bg-[var(--surface)] shadow-sm">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead>
                        <tr class="border-b border-[var(--border)] bg-[var(--surface2)]/50 text-[11px] font-semibold uppercase tracking-wider text-[var(--text-muted)]">
                            <th class="px-5 py-3.5">{{ t('common.title') }}</th>
                            <th class="px-5 py-3.5">{{ t('admin.courses.mentor') }}</th>
                            <th class="px-5 py-3.5">{{ t('common.price') }}</th>
                            <th class="px-5 py-3.5">Enrolled</th>
                            <th class="px-5 py-3.5">{{ t('common.status') }}</th>
                            <th class="px-5 py-3.5 text-right">{{ t('common.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[var(--border)]">
                        <tr
                            v-for="course in courses.data"
                            :key="course.id"
                            class="group transition-colors hover:bg-[var(--surface2)]/40"
                        >
                            <td class="px-5 py-4">
                                <div class="flex items-center gap-3.5">
                                    <div class="flex h-11 w-11 shrink-0 items-center justify-center overflow-hidden rounded-xl border border-[var(--border)] bg-[var(--surface2)] text-base shadow-2xs">
                                        <img v-if="course.cover_image_url" :src="course.cover_image_url" :alt="course.title" class="h-full w-full object-cover" />
                                        <AcademicCapIcon v-else class="h-5 w-5 text-[var(--text-muted)]" />
                                    </div>
                                    <div class="min-w-0">
                                        <Link :href="`/admin/courses/${course.id}/edit`" class="block truncate font-semibold text-[var(--text)] hover:text-[var(--primary)]">
                                            {{ course.title }}
                                        </Link>
                                        <div class="text-xs text-[var(--text-muted)]">
                                            {{ course.level ? course.level.toUpperCase() : '' }}
                                        </div>
                                    </div>
                                </div>
                            </td>

                            <td class="px-5 py-4 text-[var(--text-muted)]">
                                {{ course.mentor?.name ?? '—' }}
                            </td>

                            <td class="px-5 py-4">
                                <span v-if="!course.price || course.price == 0" class="rounded-full bg-emerald-500/10 px-2.5 py-0.5 text-xs font-semibold text-emerald-600 dark:text-emerald-400">
                                    {{ t('common.free') }}
                                </span>
                                <span v-else class="font-heading font-bold text-[var(--text)]">
                                    ৳{{ course.price }}
                                </span>
                            </td>

                            <td class="px-5 py-4">
                                <span class="inline-flex items-center gap-1.5 text-xs text-[var(--text-muted)] font-medium">
                                    <UserGroupIcon class="h-3.5 w-3.5 text-[var(--secondary)]" />
                                    {{ course.enrollments_count }}
                                </span>
                            </td>

                            <td class="px-5 py-4">
                                <span
                                    class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-0.5 text-xs font-semibold"
                                    :class="course.is_active
                                        ? 'bg-[var(--secondary)]/15 text-[var(--secondary)]'
                                        : 'bg-[var(--text-muted)]/15 text-[var(--text-muted)]'"
                                >
                                    <span class="h-1.5 w-1.5 rounded-full" :class="course.is_active ? 'bg-[var(--secondary)]' : 'bg-[var(--text-muted)]'"></span>
                                    {{ course.is_active ? t('common.active') : t('common.inactive') }}
                                </span>
                            </td>

                            <td class="px-5 py-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <Link
                                        :href="`/admin/courses/${course.id}/edit`"
                                        class="hidden sm:inline-flex items-center gap-1 rounded-lg border border-transparent px-2.5 py-1 text-xs font-semibold text-[var(--primary)] hover:border-[var(--primary)]/20 hover:bg-[var(--primary)]/10 transition-colors"
                                    >
                                        {{ t('common.edit') }}
                                    </Link>
                                    <TableDropdown :items="getCourseActions(course)" align="right" />
                                </div>
                            </td>
                        </tr>

                        <!-- Empty state -->
                        <tr v-if="!courses.data || !courses.data.length">
                            <td colspan="6" class="px-5 py-16 text-center">
                                <AcademicCapIcon class="mx-auto h-10 w-10 text-[var(--text-muted)]/50" />
                                <div class="mt-3 font-heading font-semibold text-base">{{ t('common.no_results') }}</div>
                                <p class="mt-1 text-xs text-[var(--text-muted)]">
                                    {{ locale === 'en' ? 'No courses match your criteria.' : 'কোনো কোর্স পাওয়া যায়নি।' }}
                                </p>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Pagination -->
        <div class="mt-5">
            <Pagination :links="courses.links" />
        </div>
    </AdminLayout>
</template>
