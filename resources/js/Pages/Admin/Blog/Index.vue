<script setup>
import { ref } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import AdminLayout from '@/Components/Layout/AdminLayout.vue';
import Pagination from '@/Components/UI/Pagination.vue';
import ConfirmButton from '@/Components/UI/ConfirmButton.vue';
import TableDropdown from '@/Components/UI/TableDropdown.vue';
import { useI18n } from '@/i18n';
import {
    PencilSquareIcon,
    EyeIcon,
    NoSymbolIcon,
    TrashIcon,
    CheckCircleIcon,
} from '@heroicons/vue/24/outline';

const props = defineProps({
    posts: Object,
    categories: Array,
    sources: Array,
    filters: Object,
    counts: Object,
});

const { t } = useI18n();

const search = ref(props.filters?.search ?? '');
const status = ref(props.filters?.status ?? '');
const origin = ref(props.filters?.origin ?? '');
const categoryId = ref(props.filters?.category_id ?? '');
const sourceId = ref(props.filters?.source_id ?? '');

const selectedIds = ref([]);
const bulkActionType = ref('publish');

function applyFilters() {
    router.get('/admin/blog', {
        search: search.value || undefined,
        status: status.value || undefined,
        origin: origin.value || undefined,
        category_id: categoryId.value || undefined,
        source_id: sourceId.value || undefined,
    }, { preserveState: true, replace: true });
}

function toggleSelectAll(event) {
    if (event.target.checked) {
        selectedIds.value = (props.posts?.data ?? []).map((p) => p.id);
    } else {
        selectedIds.value = [];
    }
}

function runBulkAction() {
    if (!selectedIds.value.length) return;
    router.post('/admin/blog/bulk', {
        ids: selectedIds.value,
        action: bulkActionType.value,
    }, {
        onSuccess: () => {
            selectedIds.value = [];
        },
    });
}

function togglePublish(post) {
    router.post(`/admin/blog/${post.id}/toggle-publish`, {}, { preserveScroll: true });
}

function blockPost(post) {
    if (!window.confirm(t('admin.blog.confirm_block'))) return;
    router.post(`/admin/blog/${post.id}/block`, {}, { preserveScroll: true });
}

function getPostActions(post) {
    return [
        {
            label: post.status === 'published' ? t('admin.blog.unpublish') : t('admin.blog.publish_now'),
            onClick: () => togglePublish(post),
            icon: CheckCircleIcon,
        },
        ...(post.status === 'published' ? [{ label: t('common.view'), href: `/blog/${post.slug}`, icon: EyeIcon }] : []),
        { label: t('common.edit'), href: `/admin/blog/${post.id}/edit`, icon: PencilSquareIcon },
        { label: t('admin.blog.block'), onClick: () => blockPost(post), danger: true, icon: NoSymbolIcon },
        { divider: true },
        { label: t('common.delete'), href: `/admin/blog/${post.id}`, method: 'delete', danger: true, icon: TrashIcon },
    ];
}
</script>

<template>
    <Head :title="t('admin.blog.title')" />
    <AdminLayout>
        <div class="flex flex-wrap items-center justify-between gap-4 mb-6">
            <div>
                <h1 class="font-heading text-2xl font-extrabold">{{ t('admin.blog.title') }}</h1>
                <p class="text-xs text-[var(--text-muted)] mt-1">
                    {{ t('admin.blog.subtitle') }}
                </p>
            </div>
            <div class="flex flex-wrap items-center gap-2">
                <Link href="/admin/blog-categories" class="px-3.5 py-2 rounded-lg border border-[var(--border)] bg-[var(--surface)] text-xs font-semibold hover:bg-[var(--surface2)]">
                    {{ t('admin.nav.blog_categories') }}
                </Link>
                <Link href="/admin/blog-sources" class="px-3.5 py-2 rounded-lg border border-[var(--border)] bg-[var(--surface)] text-xs font-semibold hover:bg-[var(--surface2)]">
                    {{ t('admin.nav.blog_sources') }}
                </Link>
                <Link href="/admin/blog-blocklist" class="px-3.5 py-2 rounded-lg border border-[var(--border)] bg-[var(--surface)] text-xs font-semibold hover:bg-[var(--surface2)]">
                    {{ t('admin.nav.blog_blocklist') }} ({{ counts?.blocked ?? 0 }})
                </Link>
                <Link href="/admin/blog/create" class="px-4 py-2 rounded-lg bg-[var(--primary)] hover:bg-[var(--primary-hover)] text-white text-sm font-semibold">
                    + {{ t('admin.blog.new') }}
                </Link>
            </div>
        </div>

        <!-- Summary status tabs -->
        <div class="grid grid-cols-3 gap-4 mb-5 max-w-xl">
            <button type="button" @click="status = 'draft'; applyFilters()" class="rounded-xl border p-3.5 text-left transition"
                :class="status === 'draft' ? 'border-[var(--primary)] bg-[var(--primary)]/10' : 'border-[var(--border)] bg-[var(--surface)]'">
                <div class="text-xs text-[var(--text-muted)]">{{ t('admin.blog.drafts') }}</div>
                <div class="text-xl font-extrabold text-amber-500 mt-0.5">{{ counts?.draft ?? 0 }}</div>
            </button>
            <button type="button" @click="status = 'published'; applyFilters()" class="rounded-xl border p-3.5 text-left transition"
                :class="status === 'published' ? 'border-[var(--primary)] bg-[var(--primary)]/10' : 'border-[var(--border)] bg-[var(--surface)]'">
                <div class="text-xs text-[var(--text-muted)]">{{ t('common.published') }}</div>
                <div class="text-xl font-extrabold text-emerald-500 mt-0.5">{{ counts?.published ?? 0 }}</div>
            </button>
            <Link href="/admin/blog-blocklist" class="rounded-xl border border-[var(--border)] bg-[var(--surface)] p-3.5 text-left hover:border-[var(--primary)]/40 transition">
                <div class="text-xs text-[var(--text-muted)]">{{ t('admin.blog.blocked') }}</div>
                <div class="text-xl font-extrabold text-rose-500 mt-0.5">{{ counts?.blocked ?? 0 }}</div>
            </Link>
        </div>

        <!-- Filters -->
        <form @submit.prevent="applyFilters" class="rounded-xl border border-[var(--border)] bg-[var(--surface)] p-4 mb-5 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-6 gap-3">
            <input v-model="search" type="search" :placeholder="t('common.search')" class="lg:col-span-2 px-3.5 py-2 rounded-lg bg-[var(--surface2)] border border-[var(--border)] text-sm" />
            <select v-model="status" @change="applyFilters" class="px-3 py-2 rounded-lg bg-[var(--surface2)] border border-[var(--border)] text-sm">
                <option value="">{{ t('common.status') }}: {{ t('common.all') }}</option>
                <option value="draft">{{ t('admin.blog.draft') }}</option>
                <option value="published">{{ t('common.published') }}</option>
            </select>
            <select v-model="origin" @change="applyFilters" class="px-3 py-2 rounded-lg bg-[var(--surface2)] border border-[var(--border)] text-sm">
                <option value="">{{ t('common.type') }}: {{ t('common.all') }}</option>
                <option value="auto">{{ t('admin.blog.origin_auto') }}</option>
                <option value="manual">{{ t('admin.blog.origin_manual') }}</option>
            </select>
            <select v-model="categoryId" @change="applyFilters" class="px-3 py-2 rounded-lg bg-[var(--surface2)] border border-[var(--border)] text-sm">
                <option value="">{{ t('admin.books.category') }}: {{ t('common.all') }}</option>
                <option v-for="cat in categories" :key="cat.id" :value="cat.id">{{ cat.name_bn }}</option>
            </select>
            <button type="submit" class="px-4 py-2 rounded-lg bg-[var(--secondary)] text-white text-sm font-semibold">
                {{ t('common.filter') }}
            </button>
        </form>

        <!-- Bulk action bar -->
        <div v-if="selectedIds.length" class="mb-4 flex items-center justify-between rounded-xl border border-[var(--primary)]/40 bg-[var(--primary)]/10 px-4 py-2.5 text-sm">
            <span class="font-medium">{{ selectedIds.length }} selected</span>
            <div class="flex items-center gap-2">
                <select v-model="bulkActionType" class="px-3 py-1.5 rounded-lg bg-[var(--surface)] border border-[var(--border)] text-xs">
                    <option value="publish">{{ t('admin.blog.publish_now') }}</option>
                    <option value="draft">{{ t('admin.blog.move_to_draft') }}</option>
                    <option value="block">{{ t('admin.blog.block') }}</option>
                    <option value="delete">{{ t('common.delete') }}</option>
                </select>
                <button type="button" @click="runBulkAction" class="px-3.5 py-1.5 rounded-lg bg-[var(--primary)] text-white text-xs font-semibold">
                    Apply
                </button>
            </div>
        </div>

        <div class="rounded-xl border border-[var(--border)] bg-[var(--surface)] overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-[var(--border)] text-left text-[var(--text-muted)]">
                        <th class="px-4 py-3 w-8">
                            <input type="checkbox" @change="toggleSelectAll" :checked="posts.data?.length && selectedIds.length === posts.data.length" class="rounded" />
                        </th>
                        <th class="px-4 py-3 font-medium">{{ t('common.title') }}</th>
                        <th class="px-4 py-3 font-medium">{{ t('admin.books.category') }}</th>
                        <th class="px-4 py-3 font-medium">{{ t('common.type') }}</th>
                        <th class="px-4 py-3 font-medium">{{ t('common.status') }}</th>
                        <th class="px-4 py-3 font-medium text-right">{{ t('common.actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="post in posts.data" :key="post.id" class="border-b border-[var(--border)] last:border-0">
                        <td class="px-4 py-3">
                            <input type="checkbox" :value="post.id" v-model="selectedIds" class="rounded" />
                        </td>
                        <td class="px-4 py-3 max-w-md">
                            <div class="font-semibold line-clamp-1">{{ post.title }}</div>
                            <div class="text-xs text-[var(--text-muted)] font-mono truncate">/blog/{{ post.slug }}</div>
                            <a v-if="post.source_url" :href="post.source_url" target="_blank" rel="noopener" class="text-[11px] text-[var(--primary)] hover:underline truncate block max-w-xs">
                                ↗ {{ post.source?.name || post.source_url }}
                            </a>
                        </td>
                        <td class="px-4 py-3 text-[var(--text-muted)]">
                            {{ post.category?.name_bn ?? '—' }}
                        </td>
                        <td class="px-4 py-3">
                            <span class="px-2 py-0.5 rounded text-xs font-medium"
                                :class="post.origin === 'auto' ? 'bg-purple-500/15 text-purple-400' : 'bg-blue-500/15 text-blue-400'">
                                {{ post.origin === 'auto' ? t('admin.blog.origin_auto') : t('admin.blog.origin_manual') }}
                            </span>
                        </td>
                        <td class="px-4 py-3">
                            <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold"
                                :class="post.status === 'published' ? 'bg-emerald-500/15 text-emerald-500' : 'bg-amber-500/15 text-amber-500'">
                                {{ post.status === 'published' ? t('common.published') : t('admin.blog.draft') }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <Link
                                    :href="`/admin/blog/${post.id}/edit`"
                                    class="hidden sm:inline-flex items-center gap-1 rounded-lg border border-transparent px-2.5 py-1 text-xs font-semibold text-[var(--primary)] hover:border-[var(--primary)]/20 hover:bg-[var(--primary)]/10 transition-colors"
                                >
                                    {{ t('common.edit') }}
                                </Link>
                                <TableDropdown :items="getPostActions(post)" align="right" />
                            </div>
                        </td>
                    </tr>
                    <tr v-if="!posts.data?.length">
                        <td colspan="6" class="px-5 py-10 text-center text-[var(--text-muted)]">
                            {{ t('common.no_results') }}
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <Pagination :links="posts.links" />
    </AdminLayout>
</template>
