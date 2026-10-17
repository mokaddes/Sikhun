<script setup>
import { ref, watch } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import AdminLayout from '@/Components/Layout/AdminLayout.vue';
import Pagination from '@/Components/UI/Pagination.vue';
import TableDropdown from '@/Components/UI/TableDropdown.vue';
import { useI18n } from '@/i18n';
import {
    MagnifyingGlassIcon,
    UsersIcon,
    EyeIcon,
    CheckCircleIcon,
    NoSymbolIcon,
    BanknotesIcon,
} from '@heroicons/vue/24/outline';

const props = defineProps({
    students: Object,
    filters: { type: Object, default: () => ({ search: '', type: '', status: '' }) },
});

const { t, locale } = useI18n();

const search = ref(props.filters.search ?? '');
const type = ref(props.filters.type ?? '');
const status = ref(props.filters.status ?? '');

let debounceTimer = null;
watch([search, type, status], () => {
    clearTimeout(debounceTimer);
    debounceTimer = setTimeout(() => {
        router.get('/admin/students', {
            search: search.value || undefined,
            type: type.value || undefined,
            status: status.value || undefined,
        }, { preserveState: true, replace: true });
    }, 300);
});

function toggleStatus(student) {
    router.patch(`/admin/students/${student.id}/toggle-status`, {}, { preserveScroll: true });
}

function getStudentActions(student) {
    return [
        { label: locale.value === 'en' ? 'View Profile' : 'প্রোফাইল দেখুন', href: `/admin/students/${student.id}`, icon: EyeIcon },
        {
            label: student.status === 'active' ? (locale.value === 'en' ? 'Deactivate' : 'নিষ্ক্রিয় করুন') : (locale.value === 'en' ? 'Activate' : 'সক্রিয় করুন'),
            onClick: () => toggleStatus(student),
            danger: student.status === 'active',
            icon: student.status === 'active' ? NoSymbolIcon : CheckCircleIcon,
        },
    ];
}
</script>

<template>
    <Head :title="t('admin.students.title')" />
    <AdminLayout>
        <!-- Page Header -->
        <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <div class="flex items-center gap-3">
                    <h1 class="font-heading text-2xl font-extrabold">{{ t('admin.students.title') }}</h1>
                    <span v-if="students?.total !== undefined" class="rounded-full bg-[var(--surface2)] px-2.5 py-0.5 text-xs font-semibold text-[var(--text-muted)]">
                        {{ students.total }}
                    </span>
                </div>
                <p class="mt-1 text-sm text-[var(--text-muted)]">
                    {{ locale === 'en' ? 'Manage registered students, subscriptions, and wallet balances.' : 'নিবন্ধিত শিক্ষার্থী, সাবস্ক্রিপশন এবং ওয়ালেট পরিচালনা করুন।' }}
                </p>
            </div>
        </div>

        <!-- Filter Toolbar -->
        <div class="mb-5 flex flex-wrap items-center gap-3">
            <div class="relative flex-1 min-w-[220px]">
                <MagnifyingGlassIcon class="pointer-events-none absolute left-3.5 top-1/2 h-4 w-4 -translate-y-1/2 text-[var(--text-muted)]" />
                <input
                    v-model="search"
                    type="text"
                    :placeholder="t('common.search') + ' (name, email)...'"
                    class="w-full rounded-xl border border-[var(--border)] bg-[var(--surface)] py-2 pl-9.5 pr-4 text-sm text-[var(--text)] transition-colors focus:border-[var(--primary)] focus:outline-hidden"
                />
            </div>

            <select
                v-model="type"
                class="rounded-xl border border-[var(--border)] bg-[var(--surface)] px-3.5 py-2 text-xs font-medium text-[var(--text)] transition-colors focus:border-[var(--primary)] focus:outline-hidden"
            >
                <option value="">{{ t('common.all') }} — {{ t('admin.students.type_filter') }}</option>
                <option value="ssc">SSC</option>
                <option value="hsc">HSC</option>
                <option value="university">University</option>
                <option value="job">Job</option>
            </select>

            <select
                v-model="status"
                class="rounded-xl border border-[var(--border)] bg-[var(--surface)] px-3.5 py-2 text-xs font-medium text-[var(--text)] transition-colors focus:border-[var(--primary)] focus:outline-hidden"
            >
                <option value="">{{ t('common.all') }} — {{ t('admin.students.status_filter') }}</option>
                <option value="active">{{ t('common.active') }}</option>
                <option value="inactive">{{ t('common.inactive') }}</option>
            </select>
        </div>

        <!-- Modern Datatable -->
        <div class="card overflow-hidden rounded-2xl border border-[var(--border)] bg-[var(--surface)] shadow-sm">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead>
                        <tr class="border-b border-[var(--border)] bg-[var(--surface2)]/50 text-[11px] font-semibold uppercase tracking-wider text-[var(--text-muted)]">
                            <th class="px-5 py-3.5">{{ t('common.name') }}</th>
                            <th class="px-5 py-3.5">{{ t('common.email') }}</th>
                            <th class="px-5 py-3.5">{{ t('common.type') }}</th>
                            <th class="px-5 py-3.5">Wallet</th>
                            <th class="px-5 py-3.5">{{ t('common.status') }}</th>
                            <th class="px-5 py-3.5 text-right">{{ t('common.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[var(--border)]">
                        <tr
                            v-for="s in students.data"
                            :key="s.id"
                            class="group transition-colors hover:bg-[var(--surface2)]/40"
                        >
                            <td class="px-5 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-gradient-to-br from-[var(--secondary)]/20 to-[var(--primary)]/20 font-heading text-xs font-bold text-[var(--secondary)]">
                                        {{ s.name ? s.name.charAt(0).toUpperCase() : 'S' }}
                                    </div>
                                    <div class="min-w-0">
                                        <Link :href="`/admin/students/${s.id}`" class="block font-semibold text-[var(--text)] hover:text-[var(--primary)]">
                                            {{ s.name }}
                                        </Link>
                                        <div v-if="s.phone" class="text-xs text-[var(--text-muted)]">{{ s.phone }}</div>
                                    </div>
                                </div>
                            </td>

                            <td class="px-5 py-4 text-[var(--text-muted)]">
                                {{ s.email }}
                            </td>

                            <td class="px-5 py-4">
                                <span class="rounded-md bg-[var(--surface2)] px-2 py-0.5 text-xs font-semibold uppercase tracking-wide text-[var(--text-muted)]">
                                    {{ s.type || '—' }}
                                </span>
                            </td>

                            <td class="px-5 py-4">
                                <span class="font-heading font-bold text-[var(--text)]">
                                    ৳{{ s.wallet_balance }}
                                </span>
                            </td>

                            <td class="px-5 py-4">
                                <span
                                    class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-0.5 text-xs font-semibold"
                                    :class="s.status === 'active'
                                        ? 'bg-[var(--secondary)]/15 text-[var(--secondary)]'
                                        : 'bg-red-500/15 text-red-500'"
                                >
                                    <span class="h-1.5 w-1.5 rounded-full" :class="s.status === 'active' ? 'bg-[var(--secondary)]' : 'bg-red-500'"></span>
                                    {{ s.status === 'active' ? t('common.active') : t('common.inactive') }}
                                </span>
                            </td>

                            <td class="px-5 py-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <Link
                                        :href="`/admin/students/${s.id}`"
                                        class="hidden sm:inline-flex items-center gap-1 rounded-lg border border-transparent px-2.5 py-1 text-xs font-semibold text-[var(--primary)] hover:border-[var(--primary)]/20 hover:bg-[var(--primary)]/10 transition-colors"
                                    >
                                        {{ t('common.view') }}
                                    </Link>
                                    <TableDropdown :items="getStudentActions(s)" align="right" />
                                </div>
                            </td>
                        </tr>

                        <!-- Empty state -->
                        <tr v-if="!students.data || !students.data.length">
                            <td colspan="6" class="px-5 py-16 text-center">
                                <UsersIcon class="mx-auto h-10 w-10 text-[var(--text-muted)]/50" />
                                <div class="mt-3 font-heading font-semibold text-base">{{ t('common.no_results') }}</div>
                                <p class="mt-1 text-xs text-[var(--text-muted)]">
                                    {{ locale === 'en' ? 'No students match your filter criteria.' : 'কোনো শিক্ষার্থী পাওয়া যায়নি।' }}
                                </p>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Pagination -->
        <div class="mt-5">
            <Pagination :links="students.links" />
        </div>
    </AdminLayout>
</template>
