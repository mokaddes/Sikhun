<script setup>
import { ref } from 'vue';
import { Head, Link, useForm, router } from '@inertiajs/vue3';
import AdminLayout from '@/Components/Layout/AdminLayout.vue';
import ConfirmButton from '@/Components/UI/ConfirmButton.vue';
import { useI18n } from '@/i18n';

defineProps({
    sources: Array,
    categories: Array,
    logs: Array,
});

const { t } = useI18n();
const editingId = ref(null);
const runningSourceId = ref(null);
const runningAll = ref(false);

const form = useForm({
    name: '',
    url: '',
    type: 'rss',
    link_selector: '',
    title_selector: '',
    content_selector: '',
    default_category_id: '',
    max_per_run: 3,
    is_active: true,
});

function startEdit(src) {
    editingId.value = src.id;
    form.name = src.name;
    form.url = src.url;
    form.type = src.type;
    form.link_selector = src.link_selector ?? '';
    form.title_selector = src.title_selector ?? '';
    form.content_selector = src.content_selector ?? '';
    form.default_category_id = src.default_category_id ?? '';
    form.max_per_run = src.max_per_run ?? 3;
    form.is_active = !!src.is_active;
}

function resetForm() {
    editingId.value = null;
    form.reset();
    form.type = 'rss';
    form.max_per_run = 3;
    form.is_active = true;
}

function submit() {
    if (editingId.value) {
        form.put(`/admin/blog-sources/${editingId.value}`, {
            onSuccess: () => resetForm(),
        });
    } else {
        form.post('/admin/blog-sources', {
            onSuccess: () => resetForm(),
        });
    }
}

function runSource(src) {
    runningSourceId.value = src.id;
    router.post(`/admin/blog-sources/${src.id}/run`, {}, {
        preserveScroll: true,
        onFinish: () => { runningSourceId.value = null; },
    });
}

function runAllSources() {
    runningAll.value = true;
    router.post('/admin/blog-sources/run-all', {}, {
        preserveScroll: true,
        onFinish: () => { runningAll.value = false; },
    });
}
</script>

<template>
    <Head :title="t('admin.nav.blog_sources')" />
    <AdminLayout>
        <div class="flex flex-wrap items-center justify-between gap-4 mb-6">
            <div>
                <h1 class="font-heading text-2xl font-extrabold">{{ t('admin.nav.blog_sources') }}</h1>
                <p class="text-xs text-[var(--text-muted)] mt-1">
                    Configure RSS, Sitemap, or HTML list URLs for daily automatic scraping (runs daily at 03:00 AM).
                </p>
            </div>
            <div class="flex items-center gap-3">
                <button type="button" :disabled="runningAll" @click="runAllSources"
                    class="px-4 py-2 rounded-lg bg-[var(--secondary)] text-white text-xs font-semibold disabled:opacity-60">
                    {{ runningAll ? 'Scraping...' : '⚡ Run All Active Sources Now' }}
                </button>
                <Link href="/admin/blog" class="text-sm text-[var(--text-muted)] hover:underline">← {{ t('admin.blog.title') }}</Link>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
            <form @submit.prevent="submit" class="rounded-xl border border-[var(--border)] bg-[var(--surface)] p-5 space-y-4 h-fit">
                <h2 class="font-heading font-bold text-base">{{ editingId ? t('common.edit') : 'Add Scrape Source' }}</h2>
                <div>
                    <label class="block text-xs font-medium mb-1">Source Name *</label>
                    <input v-model="form.name" type="text" required placeholder="e.g. NASA Science News" class="w-full px-3.5 py-2 rounded-lg bg-[var(--surface2)] border border-[var(--border)] text-sm" />
                </div>
                <div>
                    <label class="block text-xs font-medium mb-1">Feed / Listing URL *</label>
                    <input v-model="form.url" type="url" required placeholder="https://example.com/feed.xml" class="w-full px-3.5 py-2 rounded-lg bg-[var(--surface2)] border border-[var(--border)] text-sm font-mono" />
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-medium mb-1">Source Type</label>
                        <select v-model="form.type" class="w-full px-3 py-2 rounded-lg bg-[var(--surface2)] border border-[var(--border)] text-sm">
                            <option value="rss">RSS / Atom Feed</option>
                            <option value="sitemap">XML Sitemap</option>
                            <option value="html_list">HTML List Page</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-medium mb-1">Max Drafts / Run</label>
                        <input v-model="form.max_per_run" type="number" min="1" max="30" class="w-full px-3 py-2 rounded-lg bg-[var(--surface2)] border border-[var(--border)] text-sm" />
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-medium mb-1">Default Category</label>
                    <select v-model="form.default_category_id" class="w-full px-3 py-2 rounded-lg bg-[var(--surface2)] border border-[var(--border)] text-sm">
                        <option value="">— None —</option>
                        <option v-for="cat in categories" :key="cat.id" :value="cat.id">{{ cat.name_bn }}</option>
                    </select>
                </div>

                <div v-if="form.type === 'html_list'" class="space-y-3 pt-2 border-t border-[var(--border)]">
                    <div>
                        <label class="block text-xs font-medium mb-1">Link CSS Selector</label>
                        <input v-model="form.link_selector" type="text" placeholder="article h2 a" class="w-full px-3 py-1.5 rounded-lg bg-[var(--surface2)] border border-[var(--border)] text-xs font-mono" />
                    </div>
                    <div>
                        <label class="block text-xs font-medium mb-1">Article Title Selector (optional)</label>
                        <input v-model="form.title_selector" type="text" placeholder="h1.entry-title" class="w-full px-3 py-1.5 rounded-lg bg-[var(--surface2)] border border-[var(--border)] text-xs font-mono" />
                    </div>
                    <div>
                        <label class="block text-xs font-medium mb-1">Article Content Selector (optional)</label>
                        <input v-model="form.content_selector" type="text" placeholder="article .entry-content" class="w-full px-3 py-1.5 rounded-lg bg-[var(--surface2)] border border-[var(--border)] text-xs font-mono" />
                    </div>
                </div>

                <label class="flex items-center gap-2 text-sm">
                    <input v-model="form.is_active" type="checkbox" class="rounded" /> {{ t('common.active') }}
                </label>

                <div class="flex items-center gap-2 pt-2">
                    <button type="submit" :disabled="form.processing" class="px-4 py-2 rounded-lg bg-[var(--primary)] text-white text-sm font-semibold">
                        {{ editingId ? t('common.update') : t('common.save') }}
                    </button>
                    <button v-if="editingId" type="button" @click="resetForm" class="px-3 py-2 rounded-lg bg-[var(--surface2)] text-xs">
                        {{ t('common.cancel') }}
                    </button>
                </div>
            </form>

            <div class="lg:col-span-2 rounded-xl border border-[var(--border)] bg-[var(--surface)] overflow-x-auto h-fit">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-[var(--border)] text-left text-[var(--text-muted)]">
                            <th class="px-4 py-3 font-medium">Source</th>
                            <th class="px-4 py-3 font-medium">Type</th>
                            <th class="px-4 py-3 font-medium">Max/Run</th>
                            <th class="px-4 py-3 font-medium">Last Run</th>
                            <th class="px-4 py-3 font-medium text-right">{{ t('common.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="src in sources" :key="src.id" class="border-b border-[var(--border)] last:border-0">
                            <td class="px-4 py-3">
                                <div class="font-semibold">{{ src.name }}</div>
                                <div class="text-xs font-mono text-[var(--text-muted)] truncate max-w-xs">{{ src.url }}</div>
                                <div v-if="src.default_category" class="text-[11px] text-[var(--primary)] mt-0.5">{{ src.default_category.name_bn }}</div>
                            </td>
                            <td class="px-4 py-3 uppercase text-xs font-mono">{{ src.type }}</td>
                            <td class="px-4 py-3">{{ src.max_per_run }}</td>
                            <td class="px-4 py-3 text-xs text-[var(--text-muted)]">
                                <div v-if="src.last_run_at">{{ new Date(src.last_run_at).toLocaleString() }}</div>
                                <span v-if="src.last_status" class="px-2 py-0.5 rounded text-[10px] font-semibold"
                                    :class="src.last_status === 'completed' ? 'bg-emerald-500/15 text-emerald-500' : 'bg-rose-500/15 text-rose-500'">
                                    {{ src.last_status }}
                                </span>
                                <span v-else>Never</span>
                            </td>
                            <td class="px-4 py-3 text-right whitespace-nowrap space-x-2.5">
                                <button type="button" :disabled="runningSourceId === src.id" @click="runSource(src)"
                                    class="px-2.5 py-1 rounded-lg bg-[var(--primary)]/15 text-[var(--primary)] text-xs font-semibold hover:bg-[var(--primary)]/25 disabled:opacity-50">
                                    {{ runningSourceId === src.id ? 'Running...' : '▶ Run Now' }}
                                </button>
                                <button type="button" @click="startEdit(src)" class="text-xs font-medium text-[var(--primary)] hover:underline">
                                    {{ t('common.edit') }}
                                </button>
                                <ConfirmButton :href="`/admin/blog-sources/${src.id}`" method="delete" />
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Recent Scrape Logs -->
        <div class="rounded-xl border border-[var(--border)] bg-[var(--surface)] overflow-x-auto">
            <div class="px-5 py-3.5 border-b border-[var(--border)] font-heading font-bold text-sm">
                Recent Scrape Logs
            </div>
            <table class="w-full text-xs">
                <thead>
                    <tr class="border-b border-[var(--border)] text-left text-[var(--text-muted)]">
                        <th class="px-4 py-2.5">Source</th>
                        <th class="px-4 py-2.5">Found</th>
                        <th class="px-4 py-2.5">Drafted</th>
                        <th class="px-4 py-2.5">Duplicates</th>
                        <th class="px-4 py-2.5">Blocked</th>
                        <th class="px-4 py-2.5">Failed</th>
                        <th class="px-4 py-2.5">Time</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="log in logs" :key="log.id" class="border-b border-[var(--border)] last:border-0">
                        <td class="px-4 py-2.5 font-medium">
                            {{ log.source?.name ?? 'Deleted Source' }}
                            <div v-if="log.error" class="text-[11px] text-rose-400 truncate max-w-md" :title="log.error">{{ log.error }}</div>
                        </td>
                        <td class="px-4 py-2.5">{{ log.found }}</td>
                        <td class="px-4 py-2.5 font-bold text-emerald-500">{{ log.drafted }}</td>
                        <td class="px-4 py-2.5 text-[var(--text-muted)]">{{ log.duplicates }}</td>
                        <td class="px-4 py-2.5 text-amber-500">{{ log.blocked }}</td>
                        <td class="px-4 py-2.5 text-rose-500">{{ log.failed }}</td>
                        <td class="px-4 py-2.5 text-[var(--text-muted)]">{{ log.started_at ? new Date(log.started_at).toLocaleString() : '—' }}</td>
                    </tr>
                    <tr v-if="!logs?.length">
                        <td colspan="7" class="px-4 py-6 text-center text-[var(--text-muted)]">No scrape logs yet.</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </AdminLayout>
</template>
