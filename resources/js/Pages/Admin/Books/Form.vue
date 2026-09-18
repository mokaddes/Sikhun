<script setup>
import { watch, ref } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import AdminLayout from '@/Components/Layout/AdminLayout.vue';
import { useI18n } from '@/i18n';

const props = defineProps({ book: Object, authors: Array, publications: Array, categories: Array });
const { t } = useI18n();
const isEdit = !!props.book;

const form = useForm({
    title: props.book?.title ?? '',
    slug: props.book?.slug ?? '',
    description: props.book?.description ?? '',
    author_id: props.book?.author_id ?? null,
    publication_id: props.book?.publication_id ?? null,
    category_id: props.book?.category_id ?? null,
    subject: props.book?.subject ?? '',
    level: props.book?.level ?? 'hsc',
    price: props.book?.price ?? 0,
    is_free: props.book?.is_free ?? false,
    is_published: props.book?.is_published ?? false,
    is_premium_gift: props.book?.is_premium_gift ?? false,
    chapter_purchase_enabled: props.book?.chapter_purchase_enabled ?? false,
    cover_image: null,
});

let slugTouched = isEdit;
watch(() => form.title, (val) => {
    if (!slugTouched) form.slug = val.toLowerCase().trim().replace(/[^a-z0-9]+/g, '-').replace(/(^-|-$)/g, '');
});

const coverInput = ref(null);

function submit() {
    if (isEdit) {
        form.transform((data) => ({ ...data, _method: 'put' }))
            .post(`/admin/books/${props.book.id}`, { forceFormData: true });
    } else {
        form.post('/admin/books', { forceFormData: true });
    }
}
</script>

<template>
    <Head :title="isEdit ? t('admin.books.edit') : t('admin.books.new')" />
    <AdminLayout>
        <h1 class="font-heading text-2xl font-extrabold mb-6">{{ isEdit ? t('admin.books.edit') : t('admin.books.new') }}</h1>

        <form @submit.prevent="submit" class="space-y-6 max-w-3xl">
            <div>
                <label class="block text-sm font-medium mb-1.5">{{ t('common.title') }}</label>
                <input v-model="form.title" type="text" required class="w-full px-4 py-2.5 rounded-lg bg-[var(--surface2)] border border-[var(--border)] focus:outline-none focus:ring-2 focus:ring-[var(--primary)]" />
                <p v-if="form.errors.title" class="text-[var(--accent)] text-xs mt-1">{{ form.errors.title }}</p>
            </div>

            <div>
                <label class="block text-sm font-medium mb-1.5">Slug</label>
                <input v-model="form.slug" @input="slugTouched = true" type="text" required class="w-full px-4 py-2.5 rounded-lg bg-[var(--surface2)] border border-[var(--border)] focus:outline-none focus:ring-2 focus:ring-[var(--primary)]" />
                <p v-if="form.errors.slug" class="text-[var(--accent)] text-xs mt-1">{{ form.errors.slug }}</p>
            </div>

            <div>
                <label class="block text-sm font-medium mb-1.5">{{ t('admin.books.description') }}</label>
                <textarea v-model="form.description" rows="4" class="w-full px-4 py-2.5 rounded-lg bg-[var(--surface2)] border border-[var(--border)] focus:outline-none focus:ring-2 focus:ring-[var(--primary)]"></textarea>
            </div>

            <div class="grid grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium mb-1.5">{{ t('admin.books.author') }}</label>
                    <select v-model="form.author_id" class="w-full px-4 py-2.5 rounded-lg bg-[var(--surface2)] border border-[var(--border)]">
                        <option :value="null">—</option>
                        <option v-for="a in authors" :key="a.id" :value="a.id">{{ a.name }}</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1.5">{{ t('admin.books.publication') }}</label>
                    <select v-model="form.publication_id" class="w-full px-4 py-2.5 rounded-lg bg-[var(--surface2)] border border-[var(--border)]">
                        <option :value="null">—</option>
                        <option v-for="p in publications" :key="p.id" :value="p.id">{{ p.name }}</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1.5">{{ t('admin.books.category') }}</label>
                    <select v-model="form.category_id" class="w-full px-4 py-2.5 rounded-lg bg-[var(--surface2)] border border-[var(--border)]">
                        <option :value="null">—</option>
                        <option v-for="c in categories" :key="c.id" :value="c.id">{{ c.name }}</option>
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium mb-1.5">{{ t('admin.books.subject') }}</label>
                    <input v-model="form.subject" type="text" class="w-full px-4 py-2.5 rounded-lg bg-[var(--surface2)] border border-[var(--border)]" />
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1.5">{{ t('common.level') }}</label>
                    <select v-model="form.level" class="w-full px-4 py-2.5 rounded-lg bg-[var(--surface2)] border border-[var(--border)]">
                        <option value="ssc">SSC</option>
                        <option value="hsc">HSC</option>
                        <option value="university">University</option>
                        <option value="job">Job</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1.5">{{ t('common.price') }} (৳)</label>
                    <input v-model="form.price" type="number" step="0.01" min="0" class="w-full px-4 py-2.5 rounded-lg bg-[var(--surface2)] border border-[var(--border)]" />
                </div>
            </div>

            <div class="flex flex-wrap gap-6 text-sm">
                <label class="flex items-center gap-2"><input v-model="form.is_free" type="checkbox" class="rounded" /> {{ t('admin.books.is_free') }}</label>
                <label class="flex items-center gap-2"><input v-model="form.is_published" type="checkbox" class="rounded" /> {{ t('admin.books.is_published') }}</label>
                <label class="flex items-center gap-2"><input v-model="form.is_premium_gift" type="checkbox" class="rounded" /> Premium gift book</label>
                <label class="flex items-center gap-2"><input v-model="form.chapter_purchase_enabled" type="checkbox" class="rounded" /> {{ t('admin.books.chapter_purchase') }}</label>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium mb-1.5">{{ t('admin.books.cover') }}</label>
                    <input ref="coverInput" @input="form.cover_image = coverInput.files[0]" type="file" accept="image/*" class="w-full text-sm" />
                    <img v-if="book?.cover_image_url" :src="book.cover_image_url" class="mt-2 h-24 rounded-lg object-cover" />
                </div>
                <div v-if="isEdit">
                    <label class="block text-sm font-medium mb-1.5">{{ t('admin.books.pdf') }}</label>
                    <Link :href="`/admin/books/${book.id}/pdf`" class="inline-flex items-center gap-1.5 px-3 py-2 rounded-lg border border-[var(--border)] hover:bg-[var(--surface2)] text-sm font-medium w-full text-left">
                        <span class="text-lg leading-none">↗</span>
                        {{ book.pdf_path ? 'Replace PDF / Reprocess' : 'Upload PDF' }}
                    </Link>
                    <p class="text-[10px] text-[var(--text-muted)] mt-1">PDF uploads and processing are handled on a separate page.</p>
                </div>
                <div v-else>
                    <label class="block text-sm font-medium mb-1.5">{{ t('admin.books.pdf') }}</label>
                    <p class="text-sm text-[var(--text-muted)] bg-[var(--surface2)] rounded-lg px-3 py-2.5 border border-[var(--border)]">
                        You'll upload the PDF right after creating the book.
                    </p>
                </div>
            </div>

            <button
                type="submit"
                :disabled="form.processing"
                class="px-5 py-2.5 rounded-lg bg-[var(--primary)] hover:bg-[var(--primary-hover)] text-white text-sm font-semibold disabled:opacity-60"
            >
                {{ form.processing ? t('common.saving') : t('common.save') }}
            </button>
        </form>
    </AdminLayout>
</template>
