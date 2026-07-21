<script setup>
import { Head, Link } from '@inertiajs/vue3';
import AdminLayout from '@/Components/Layout/AdminLayout.vue';
import ConfirmButton from '@/Components/UI/ConfirmButton.vue';
import { useI18n } from '@/i18n';

defineProps({ publications: Array });
const { t } = useI18n();
</script>

<template>
    <Head :title="t('admin.publications.title')" />
    <AdminLayout>
        <div class="flex items-center justify-between mb-6">
            <h1 class="font-heading text-2xl font-extrabold">{{ t('admin.publications.title') }}</h1>
            <Link href="/admin/publications/create" class="px-4 py-2 rounded-lg bg-[var(--primary)] hover:bg-[var(--primary-hover)] text-white text-sm font-semibold">
                {{ t('admin.publications.new') }}
            </Link>
        </div>
        <div class="rounded-xl border border-[var(--border)] bg-[var(--surface)] overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-[var(--border)] text-left text-[var(--text-muted)]">
                        <th class="px-5 py-3 font-medium">{{ t('common.name') }}</th>
                        <th class="px-5 py-3 font-medium">Books</th>
                        <th class="px-5 py-3 font-medium text-right">{{ t('common.actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="p in publications" :key="p.id" class="border-b border-[var(--border)] last:border-0">
                        <td class="px-5 py-3">{{ p.name }}</td>
                        <td class="px-5 py-3 text-[var(--text-muted)]">{{ p.books_count }}</td>
                        <td class="px-5 py-3 text-right space-x-3">
                            <Link :href="`/admin/publications/${p.id}/edit`" class="text-sm font-medium text-[var(--primary)] hover:underline">{{ t('common.edit') }}</Link>
                            <ConfirmButton :href="`/admin/publications/${p.id}`" method="delete" />
                        </td>
                    </tr>
                    <tr v-if="!publications.length"><td colspan="3" class="px-5 py-10 text-center text-[var(--text-muted)]">{{ t('common.no_results') }}</td></tr>
                </tbody>
            </table>
        </div>
    </AdminLayout>
</template>
