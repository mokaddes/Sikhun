<script setup>
import { Head, Link } from '@inertiajs/vue3';
import AdminLayout from '@/Components/Layout/AdminLayout.vue';
import ConfirmButton from '@/Components/UI/ConfirmButton.vue';
import { useI18n } from '@/i18n';

defineProps({ categories: Array });
const { t } = useI18n();
</script>

<template>
    <Head :title="t('admin.categories.title')" />
    <AdminLayout>
        <div class="flex items-center justify-between mb-6">
            <h1 class="font-heading text-2xl font-extrabold">{{ t('admin.categories.title') }}</h1>
            <Link href="/admin/categories/create" class="px-4 py-2 rounded-lg bg-[var(--primary)] hover:bg-[var(--primary-hover)] text-white text-sm font-semibold">
                {{ t('admin.categories.new') }}
            </Link>
        </div>

        <div class="rounded-xl border border-[var(--border)] bg-[var(--surface)] overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-[var(--border)] text-left text-[var(--text-muted)]">
                        <th class="px-5 py-3 font-medium">{{ t('common.name') }}</th>
                        <th class="px-5 py-3 font-medium">{{ t('common.type') }}</th>
                        <th class="px-5 py-3 font-medium">{{ t('admin.categories.parent') }}</th>
                        <th class="px-5 py-3 font-medium text-right">{{ t('common.actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="cat in categories" :key="cat.id" class="border-b border-[var(--border)] last:border-0">
                        <td class="px-5 py-3">{{ cat.name }}</td>
                        <td class="px-5 py-3 text-[var(--text-muted)]">{{ cat.type }}</td>
                        <td class="px-5 py-3 text-[var(--text-muted)]">{{ cat.parent?.name ?? '—' }}</td>
                        <td class="px-5 py-3 text-right space-x-3">
                            <Link :href="`/admin/categories/${cat.id}/edit`" class="text-sm font-medium text-[var(--primary)] hover:underline">{{ t('common.edit') }}</Link>
                            <ConfirmButton :href="`/admin/categories/${cat.id}`" method="delete" />
                        </td>
                    </tr>
                    <tr v-if="!categories.length">
                        <td colspan="4" class="px-5 py-10 text-center text-[var(--text-muted)]">{{ t('common.no_results') }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </AdminLayout>
</template>
