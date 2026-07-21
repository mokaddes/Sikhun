<script setup>
import { Head, Link } from '@inertiajs/vue3';
import AdminLayout from '@/Components/Layout/AdminLayout.vue';
import ConfirmButton from '@/Components/UI/ConfirmButton.vue';
import { useI18n } from '@/i18n';

defineProps({ pages: Array });
const { t } = useI18n();
</script>

<template>
    <Head :title="t('admin.pages.title')" />
    <AdminLayout>
        <div class="flex items-center justify-between mb-6">
            <h1 class="font-heading text-2xl font-extrabold">{{ t('admin.pages.title') }}</h1>
            <Link href="/admin/pages/create" class="px-4 py-2 rounded-lg bg-[var(--primary)] hover:bg-[var(--primary-hover)] text-white text-sm font-semibold">
                {{ t('admin.pages.new') }}
            </Link>
        </div>
        <div class="rounded-xl border border-[var(--border)] bg-[var(--surface)] overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-[var(--border)] text-left text-[var(--text-muted)]">
                        <th class="px-5 py-3 font-medium">Title (BN)</th>
                        <th class="px-5 py-3 font-medium">Title (EN)</th>
                        <th class="px-5 py-3 font-medium">{{ t('admin.pages.slug') }}</th>
                        <th class="px-5 py-3 font-medium">{{ t('common.status') }}</th>
                        <th class="px-5 py-3 font-medium text-right">{{ t('common.actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="p in pages" :key="p.id" class="border-b border-[var(--border)] last:border-0">
                        <td class="px-5 py-3">{{ p.title_bn }}</td>
                        <td class="px-5 py-3 text-[var(--text-muted)]">{{ p.title_en }}</td>
                        <td class="px-5 py-3 text-[var(--text-muted)] font-mono text-xs">/p/{{ p.slug }}</td>
                        <td class="px-5 py-3">
                            <span class="px-2 py-0.5 rounded text-xs font-medium" :class="p.is_published ? 'bg-[var(--secondary)]/15 text-[var(--secondary)]' : 'bg-[var(--text-muted)]/15 text-[var(--text-muted)]'">
                                {{ p.is_published ? t('common.published') : t('common.unpublished') }}
                            </span>
                        </td>
                        <td class="px-5 py-3 text-right space-x-3">
                            <Link :href="`/admin/pages/${p.id}/edit`" class="text-sm font-medium text-[var(--primary)] hover:underline">{{ t('common.edit') }}</Link>
                            <ConfirmButton :href="`/admin/pages/${p.id}`" method="delete" />
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </AdminLayout>
</template>
