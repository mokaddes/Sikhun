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
    BookOpenIcon,
    EyeIcon,
    PencilSquareIcon,
    DocumentArrowUpIcon,
    TrashIcon,
    ArrowTopRightOnSquareIcon,
} from '@heroicons/vue/24/outline';

const props = defineProps({
    books: Object,
    filters: { type: Object, default: () => ({ search: '', status: '' }) },
});

const { t, locale } = useI18n();

const search = ref(props.filters?.search ?? '');
const status = ref(props.filters?.status ?? '');

let debounceTimer = null;
watch([search, status], () => {
    clearTimeout(debounceTimer);
    debounceTimer = setTimeout(() => {
        router.get('/admin/books', {
            search: search.value || undefined,
            status: status.value || undefined,
        }, { preserveState: true, replace: true });
    }, 300);
});

function getBookActions(book) {
    return [
        { label: locale.value === 'en' ? 'View Details' : 'বিস্তারিত দেখুন', href: `/admin/books/${book.id}`, icon: EyeIcon },
        { label: t('common.edit'), href: `/admin/books/${book.id}/edit`, icon: PencilSquareIcon },
        { label: locale.value === 'en' ? 'Upload / Manage PDF' : 'PDF আপলোড / পরিচালনা', href: `/admin/books/${book.id}/pdf`, icon: DocumentArrowUpIcon },
        { divider: true },
        { label: t('common.delete'), href: `/admin/books/${book.id}`, method: 'delete', danger: true, icon: TrashIcon },
    ];
}
</script>

<template>
    <Head :title="t('admin.books.title')" />
    <AdminLayout>
        <!-- Page Header -->
        <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <div class="flex items-center gap-3">
                    <h1 class="font-heading text-2xl font-extrabold">{{ t('admin.books.title') }}</h1>
                    <span v-if="books?.total !== undefined" class="rounded-full bg-[var(--surface2)] px-2.5 py-0.5 text-xs font-semibold text-[var(--text-muted)]">
                        {{ books.total }}
                    </span>
                </div>
                <p class="mt-1 text-sm text-[var(--text-muted)]">
                    {{ locale === 'en' ? 'Manage your library catalogue, PDFs, prices, and publishing status.' : 'বইয়ের ক্যাটালগ, পিডিএফ প্রসেসিং এবং স্ট্যাটাস পরিচালনা করুন।' }}
                </p>
            </div>
            <Link href="/admin/books/create" class="btn btn-primary self-start sm:self-auto gap-2">
                <PlusIcon class="h-4 w-4" />
                {{ t('admin.books.new') }}
            </Link>
        </div>

        <!-- Datatable Filter Toolbar -->
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
                    @click="status = 'published'"
                    class="rounded-lg px-3 py-1.5 text-xs font-semibold transition-colors"
                    :class="status === 'published' ? 'bg-[var(--primary)] text-white shadow-xs' : 'text-[var(--text-muted)] hover:text-[var(--text)]'"
                >
                    {{ t('common.published') }}
                </button>
                <button
                    type="button"
                    @click="status = 'draft'"
                    class="rounded-lg px-3 py-1.5 text-xs font-semibold transition-colors"
                    :class="status === 'draft' ? 'bg-[var(--primary)] text-white shadow-xs' : 'text-[var(--text-muted)] hover:text-[var(--text)]'"
                >
                    {{ t('common.unpublished') }}
                </button>
            </div>
        </div>

        <!-- Modern Datatable Card -->
        <div class="card overflow-hidden rounded-2xl border border-[var(--border)] bg-[var(--surface)] shadow-sm">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead>
                        <tr class="border-b border-[var(--border)] bg-[var(--surface2)]/50 text-[11px] font-semibold uppercase tracking-wider text-[var(--text-muted)]">
                            <th class="px-5 py-3.5">{{ t('common.title') }}</th>
                            <th class="px-5 py-3.5">{{ t('common.level') }}</th>
                            <th class="px-5 py-3.5">{{ t('common.price') }}</th>
                            <th class="px-5 py-3.5">{{ t('common.status') }}</th>
                            <th class="px-5 py-3.5">PDF Status</th>
                            <th class="px-5 py-3.5 text-right">{{ t('common.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[var(--border)]">
                        <tr
                            v-for="book in books.data"
                            :key="book.id"
                            class="group transition-colors hover:bg-[var(--surface2)]/40"
                        >
                            <!-- Book Info -->
                            <td class="px-5 py-4">
                                <div class="flex items-center gap-3.5">
                                    <div class="flex h-12 w-9 shrink-0 items-center justify-center overflow-hidden rounded-lg border border-[var(--border)] bg-[var(--surface2)] text-base shadow-2xs">
                                        <img v-if="book.cover_image_url" :src="book.cover_image_url" :alt="book.title" class="h-full w-full object-cover" />
                                        <BookOpenIcon v-else class="h-4 w-4 text-[var(--text-muted)]" />
                                    </div>
                                    <div class="min-w-0">
                                        <Link :href="`/admin/books/${book.id}`" class="block truncate font-semibold text-[var(--text)] hover:text-[var(--primary)]">
                                            {{ book.title }}
                                        </Link>
                                        <div class="text-xs text-[var(--text-muted)]">
                                            {{ book.author?.name || '—' }} <span v-if="book.category">· {{ book.category.name }}</span>
                                        </div>
                                    </div>
                                </div>
                            </td>

                            <!-- Level -->
                            <td class="px-5 py-4">
                                <span class="rounded-md bg-[var(--surface2)] px-2 py-0.5 text-xs font-semibold uppercase tracking-wide text-[var(--text-muted)]">
                                    {{ book.level || '—' }}
                                </span>
                            </td>

                            <!-- Price -->
                            <td class="px-5 py-4">
                                <span v-if="book.is_free" class="rounded-full bg-emerald-500/10 px-2.5 py-0.5 text-xs font-semibold text-emerald-600 dark:text-emerald-400">
                                    {{ t('common.free') }}
                                </span>
                                <span v-else class="font-heading font-bold text-[var(--text)]">
                                    ৳{{ book.price }}
                                </span>
                            </td>

                            <!-- Published Status -->
                            <td class="px-5 py-4">
                                <span
                                    class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-0.5 text-xs font-semibold"
                                    :class="book.is_published
                                        ? 'bg-[var(--secondary)]/15 text-[var(--secondary)]'
                                        : 'bg-[var(--text-muted)]/15 text-[var(--text-muted)]'"
                                >
                                    <span class="h-1.5 w-1.5 rounded-full" :class="book.is_published ? 'bg-[var(--secondary)]' : 'bg-[var(--text-muted)]'"></span>
                                    {{ book.is_published ? t('common.published') : t('common.unpublished') }}
                                </span>
                            </td>

                            <!-- PDF Status -->
                            <td class="px-5 py-4">
                                <Link
                                    :href="`/admin/books/${book.id}/pdf`"
                                    class="inline-flex items-center gap-1.5 rounded-lg px-2.5 py-1 text-xs font-medium transition-colors"
                                    :class="{
                                        'bg-[var(--secondary)]/10 text-[var(--secondary)] hover:bg-[var(--secondary)]/20': book.processing_status === 'completed',
                                        'bg-red-500/10 text-red-500 hover:bg-red-500/20': book.processing_status === 'failed',
                                        'bg-amber-500/10 text-amber-500 hover:bg-amber-500/20': book.processing_status === 'processing',
                                        'bg-[var(--surface2)] text-[var(--text-muted)] hover:text-[var(--text)]': !book.pdf_path || book.processing_status === 'pending',
                                    }"
                                >
                                    <span v-if="book.processing_status === 'completed'">✓ {{ t('admin.books.status_completed') }}</span>
                                    <span v-else-if="book.processing_status === 'failed'">✕ {{ t('admin.books.status_failed') }}</span>
                                    <span v-else-if="book.processing_status === 'processing'" class="animate-pulse">⏳ {{ t('admin.books.status_processing') }}</span>
                                    <span v-else-if="book.pdf_path">{{ t('admin.books.status_pending') }}</span>
                                    <span v-else class="text-[var(--primary)] font-semibold">+ {{ locale === 'en' ? 'Upload PDF' : 'পিডিএফ আপলোড' }}</span>
                                </Link>
                            </td>

                            <!-- Actions Dropdown -->
                            <td class="px-5 py-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <Link
                                        :href="`/admin/books/${book.id}/edit`"
                                        class="hidden sm:inline-flex items-center gap-1 rounded-lg border border-transparent px-2.5 py-1 text-xs font-semibold text-[var(--primary)] hover:border-[var(--primary)]/20 hover:bg-[var(--primary)]/10 transition-colors"
                                    >
                                        {{ t('common.edit') }}
                                    </Link>
                                    <TableDropdown :items="getBookActions(book)" align="right" />
                                </div>
                            </td>
                        </tr>

                        <!-- Empty state -->
                        <tr v-if="!books.data || !books.data.length">
                            <td colspan="6" class="px-5 py-16 text-center">
                                <BookOpenIcon class="mx-auto h-10 w-10 text-[var(--text-muted)]/50" />
                                <div class="mt-3 font-heading font-semibold text-base">{{ t('common.no_results') }}</div>
                                <p class="mt-1 text-xs text-[var(--text-muted)]">
                                    {{ locale === 'en' ? 'Try adjusting your search or filters.' : 'অন্য কোনো শব্দ দিয়ে সার্চ করে দেখুন।' }}
                                </p>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Pagination -->
        <div class="mt-5">
            <Pagination :links="books.links" />
        </div>
    </AdminLayout>
</template>
