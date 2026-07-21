<script setup>
import { Head, Link } from '@inertiajs/vue3';
import AdminLayout from '@/Components/Layout/AdminLayout.vue';
import ConfirmButton from '@/Components/UI/ConfirmButton.vue';
import Pagination from '@/Components/UI/Pagination.vue';
import { useI18n } from '@/i18n';

defineProps({ books: Object });
const { t } = useI18n();
</script>

<template>
    <Head :title="t('admin.books.title')" />
    <AdminLayout>
        <div class="flex items-center justify-between mb-6">
            <h1 class="font-heading text-2xl font-extrabold">{{ t('admin.books.title') }}</h1>
            <Link href="/admin/books/create" class="px-4 py-2 rounded-lg bg-[var(--primary)] hover:bg-[var(--primary-hover)] text-white text-sm font-semibold">
                {{ t('admin.books.new') }}
            </Link>
        </div>

        <div class="rounded-xl border border-[var(--border)] bg-[var(--surface)] overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-[var(--border)] text-left text-[var(--text-muted)]">
                        <th class="px-5 py-3 font-medium">{{ t('common.title') }}</th>
                        <th class="px-5 py-3 font-medium">{{ t('common.level') }}</th>
                        <th class="px-5 py-3 font-medium">{{ t('common.price') }}</th>
                        <th class="px-5 py-3 font-medium">{{ t('common.status') }}</th>
                        <th class="px-5 py-3 font-medium text-right">{{ t('common.actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="book in books.data" :key="book.id" class="border-b border-[var(--border)] last:border-0">
                        <td class="px-5 py-3">
                            <div class="font-medium">{{ book.title }}</div>
                            <div class="text-xs text-[var(--text-muted)]">{{ book.author?.name }}</div>
                        </td>
                        <td class="px-5 py-3 text-[var(--text-muted)] uppercase text-xs">{{ book.level }}</td>
                        <td class="px-5 py-3">{{ book.is_free ? t('common.free') : `৳${book.price}` }}</td>
                        <td class="px-5 py-3">
                            <span class="px-2 py-0.5 rounded text-xs font-medium" :class="book.is_published ? 'bg-[var(--secondary)]/15 text-[var(--secondary)]' : 'bg-[var(--text-muted)]/15 text-[var(--text-muted)]'">
                                {{ book.is_published ? t('common.published') : t('common.unpublished') }}
                            </span>
                        </td>
                        <td class="px-5 py-3 text-right space-x-3">
                            <Link :href="`/admin/books/${book.id}/edit`" class="text-sm font-medium text-[var(--primary)] hover:underline">{{ t('common.edit') }}</Link>
                            <ConfirmButton :href="`/admin/books/${book.id}`" method="delete" />
                        </td>
                    </tr>
                    <tr v-if="!books.data.length"><td colspan="5" class="px-5 py-10 text-center text-[var(--text-muted)]">{{ t('common.no_results') }}</td></tr>
                </tbody>
            </table>
        </div>
        <Pagination :links="books.links" />
    </AdminLayout>
</template>
