<script setup>
import { ref, watch } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import AdminLayout from '@/Components/Layout/AdminLayout.vue';
import Pagination from '@/Components/UI/Pagination.vue';
import { useI18n } from '@/i18n';

const props = defineProps({ students: Object, filters: Object });
const { t } = useI18n();

const search = ref(props.filters.search ?? '');
const type = ref(props.filters.type ?? '');
const status = ref(props.filters.status ?? '');

let debounce;
watch([search, type, status], () => {
    clearTimeout(debounce);
    debounce = setTimeout(() => {
        router.get('/admin/students', { search: search.value, type: type.value, status: status.value }, { preserveState: true, replace: true });
    }, 300);
});

function toggleStatus(student) {
    router.patch(`/admin/students/${student.id}/toggle-status`, {}, { preserveScroll: true });
}
</script>

<template>
    <Head :title="t('admin.students.title')" />
    <AdminLayout>
        <h1 class="font-heading text-2xl font-extrabold mb-6">{{ t('admin.students.title') }}</h1>

        <div class="flex flex-wrap gap-3 mb-5">
            <input v-model="search" type="text" :placeholder="t('common.search')" class="flex-1 min-w-[200px] px-4 py-2.5 rounded-lg bg-[var(--surface2)] border border-[var(--border)] text-sm" />
            <select v-model="type" class="px-4 py-2.5 rounded-lg bg-[var(--surface2)] border border-[var(--border)] text-sm">
                <option value="">{{ t('common.all') }} — {{ t('admin.students.type_filter') }}</option>
                <option value="ssc">SSC</option><option value="hsc">HSC</option>
                <option value="university">University</option><option value="job">Job</option>
            </select>
            <select v-model="status" class="px-4 py-2.5 rounded-lg bg-[var(--surface2)] border border-[var(--border)] text-sm">
                <option value="">{{ t('common.all') }} — {{ t('admin.students.status_filter') }}</option>
                <option value="active">{{ t('common.active') }}</option>
                <option value="inactive">{{ t('common.inactive') }}</option>
            </select>
        </div>

        <div class="rounded-xl border border-[var(--border)] bg-[var(--surface)] overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-[var(--border)] text-left text-[var(--text-muted)]">
                        <th class="px-5 py-3 font-medium">{{ t('common.name') }}</th>
                        <th class="px-5 py-3 font-medium">{{ t('common.email') }}</th>
                        <th class="px-5 py-3 font-medium">{{ t('common.type') }}</th>
                        <th class="px-5 py-3 font-medium">Wallet</th>
                        <th class="px-5 py-3 font-medium">{{ t('common.status') }}</th>
                        <th class="px-5 py-3 font-medium text-right">{{ t('common.actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="s in students.data" :key="s.id" class="border-b border-[var(--border)] last:border-0">
                        <td class="px-5 py-3">{{ s.name }}</td>
                        <td class="px-5 py-3 text-[var(--text-muted)]">{{ s.email }}</td>
                        <td class="px-5 py-3 uppercase text-xs text-[var(--text-muted)]">{{ s.type }}</td>
                        <td class="px-5 py-3">৳{{ s.wallet_balance }}</td>
                        <td class="px-5 py-3">
                            <span class="px-2 py-0.5 rounded text-xs font-medium" :class="s.status === 'active' ? 'bg-[var(--secondary)]/15 text-[var(--secondary)]' : 'bg-[var(--accent)]/15 text-[var(--accent)]'">
                                {{ s.status === 'active' ? t('common.active') : t('common.inactive') }}
                            </span>
                        </td>
                        <td class="px-5 py-3 text-right space-x-3">
                            <Link :href="`/admin/students/${s.id}`" class="text-sm font-medium text-[var(--primary)] hover:underline">{{ t('common.view') }}</Link>
                            <button @click="toggleStatus(s)" class="text-sm font-medium text-[var(--text-muted)] hover:underline">
                                {{ s.status === 'active' ? t('admin.students.deactivate') : t('admin.students.activate') }}
                            </button>
                        </td>
                    </tr>
                    <tr v-if="!students.data.length"><td colspan="6" class="px-5 py-10 text-center text-[var(--text-muted)]">{{ t('common.no_results') }}</td></tr>
                </tbody>
            </table>
        </div>
        <Pagination :links="students.links" />
    </AdminLayout>
</template>
