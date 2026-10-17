<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import AdminLayout from '@/Components/Layout/AdminLayout.vue';
import Pagination from '@/Components/UI/Pagination.vue';
import ConfirmButton from '@/Components/UI/ConfirmButton.vue';
import { useI18n } from '@/i18n';

defineProps({
    entries: Object,
    filters: Object,
});

const { t } = useI18n();

const form = useForm({
    source_url: '',
    source_title: '',
    reason: 'Manually blocked URL',
});

function submit() {
    form.post('/admin/blog-blocklist', {
        onSuccess: () => form.reset('source_url', 'source_title'),
    });
}
</script>

<template>
    <Head :title="t('admin.nav.blog_blocklist')" />
    <AdminLayout>
        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="font-heading text-2xl font-extrabold">{{ t('admin.nav.blog_blocklist') }}</h1>
                <p class="text-xs text-[var(--text-muted)] mt-1">
                    Any blocked URL, content hash, or title fingerprint is permanently skipped during duplicate search & daily scraping.
                </p>
            </div>
            <Link href="/admin/blog" class="text-sm text-[var(--text-muted)] hover:underline">← {{ t('admin.blog.title') }}</Link>
        </div>

        <form @submit.prevent="submit" class="rounded-xl border border-[var(--border)] bg-[var(--surface)] p-4 mb-6 grid grid-cols-1 md:grid-cols-4 gap-3">
            <input v-model="form.source_url" type="url" required placeholder="https://example.com/article-to-block" class="md:col-span-2 px-3.5 py-2 rounded-lg bg-[var(--surface2)] border border-[var(--border)] text-sm font-mono" />
            <input v-model="form.source_title" type="text" placeholder="Article title (optional)" class="px-3.5 py-2 rounded-lg bg-[var(--surface2)] border border-[var(--border)] text-sm" />
            <button type="submit" :disabled="form.processing" class="px-4 py-2 rounded-lg bg-rose-600 hover:bg-rose-700 text-white text-sm font-semibold">
                + Block URL
            </button>
        </form>

        <div class="rounded-xl border border-[var(--border)] bg-[var(--surface)] overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-[var(--border)] text-left text-[var(--text-muted)]">
                        <th class="px-4 py-3 font-medium">Title / URL</th>
                        <th class="px-4 py-3 font-medium">Reason</th>
                        <th class="px-4 py-3 font-medium">Date</th>
                        <th class="px-4 py-3 font-medium text-right">{{ t('common.actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="entry in entries.data" :key="entry.id" class="border-b border-[var(--border)] last:border-0">
                        <td class="px-4 py-3 max-w-lg">
                            <div class="font-medium truncate">{{ entry.source_title || '—' }}</div>
                            <div class="text-xs font-mono text-[var(--text-muted)] truncate">{{ entry.source_url || entry.source_url_hash }}</div>
                        </td>
                        <td class="px-4 py-3 text-xs text-[var(--text-muted)]">{{ entry.reason || 'Blocked' }}</td>
                        <td class="px-4 py-3 text-xs text-[var(--text-muted)]">{{ new Date(entry.created_at).toLocaleDateString() }}</td>
                        <td class="px-4 py-3 text-right">
                            <ConfirmButton :href="`/admin/blog-blocklist/${entry.id}`" method="delete" :label="t('admin.blog.unblock')" />
                        </td>
                    </tr>
                    <tr v-if="!entries.data?.length">
                        <td colspan="4" class="px-4 py-10 text-center text-[var(--text-muted)]">No blocked blog entries.</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <Pagination :links="entries.links" />
    </AdminLayout>
</template>
