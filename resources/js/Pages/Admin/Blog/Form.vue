<script setup>
import { ref, watch } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import AdminLayout from '@/Components/Layout/AdminLayout.vue';
import RichTextEditor from '@/Components/UI/RichTextEditor.vue';
import { useI18n } from '@/i18n';

const props = defineProps({
    post: Object,
    categories: Array,
    tags: Array,
});

const { t } = useI18n();
const isEdit = !!props.post;

const form = useForm({
    title: props.post?.title ?? '',
    slug: props.post?.slug ?? '',
    blog_category_id: props.post?.blog_category_id ?? '',
    tags: props.post?.tags?.map((tag) => tag.id) ?? [],
    excerpt: props.post?.excerpt ?? '',
    content: props.post?.content ?? '',
    featured_image: null,
    remove_featured_image: false,
    meta_title: props.post?.meta_title ?? '',
    meta_description: props.post?.meta_description ?? '',
    status: props.post?.status ?? 'draft',
    published_at: props.post?.published_at ? props.post.published_at.slice(0, 16) : '',
});

const imagePreview = ref(props.post?.featured_image_url ?? null);

let slugTouched = isEdit;
watch(() => form.title, (val) => {
    if (!slugTouched) {
        const ascii = val.toLowerCase().trim().replace(/[^a-z0-9]+/g, '-').replace(/(^-|-$)/g, '');
        form.slug = ascii || `blog-${Date.now().toString(36)}`;
    }
});

function onImageChange(event) {
    const file = event.target.files?.[0];
    if (file) {
        form.featured_image = file;
        form.remove_featured_image = false;
        imagePreview.value = URL.createObjectURL(file);
    }
}

function removeImage() {
    form.featured_image = null;
    form.remove_featured_image = true;
    imagePreview.value = null;
}

function submit() {
    if (isEdit) {
        form.transform((data) => {
            const payload = { ...data, _method: 'put' };
            if (!(payload.featured_image instanceof File)) {
                delete payload.featured_image;
            }
            return payload;
        }).post(`/admin/blog/${props.post.id}`);
    } else {
        form.post('/admin/blog');
    }
}
</script>

<template>
    <Head :title="isEdit ? t('common.edit') : t('admin.blog.new')" />
    <AdminLayout>
        <div class="flex items-center justify-between mb-6 max-w-4xl">
            <h1 class="font-heading text-2xl font-extrabold">
                {{ isEdit ? t('common.edit') : t('admin.blog.new') }}
            </h1>
            <Link href="/admin/blog" class="text-sm text-[var(--text-muted)] hover:underline">
                ← {{ t('common.back') }}
            </Link>
        </div>

        <div v-if="post?.origin === 'auto'" class="max-w-4xl mb-5 rounded-xl border border-purple-500/30 bg-purple-500/10 p-4 text-xs space-y-1">
            <div class="font-bold text-purple-300">🤖 Auto-Scraped & AI-Rewritten Draft</div>
            <div v-if="post.source_title"><span class="text-[var(--text-muted)]">Original Title:</span> {{ post.source_title }}</div>
            <div v-if="post.source_url">
                <span class="text-[var(--text-muted)]">Source URL:</span>
                <a :href="post.source_url" target="_blank" rel="noopener" class="text-[var(--primary)] hover:underline ml-1">{{ post.source_url }}</a>
            </div>
        </div>

        <form @submit.prevent="submit" class="max-w-4xl space-y-5">
            <div class="rounded-xl border border-[var(--border)] bg-[var(--surface)] p-6 space-y-4">
                <div>
                    <label class="block text-sm font-medium mb-1.5">{{ t('common.title') }}</label>
                    <input v-model="form.title" type="text" required class="w-full px-4 py-2.5 rounded-lg bg-[var(--surface2)] border border-[var(--border)]" />
                    <p v-if="form.errors.title" class="text-xs text-[var(--accent)] mt-1">{{ form.errors.title }}</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium mb-1.5">Slug (URL)</label>
                        <input v-model="form.slug" @input="slugTouched = true" type="text" required class="w-full px-4 py-2.5 rounded-lg bg-[var(--surface2)] border border-[var(--border)] font-mono text-sm" />
                        <p class="text-xs text-[var(--text-muted)] mt-1">Public URL: /blog/{{ form.slug }}</p>
                        <p v-if="form.errors.slug" class="text-xs text-[var(--accent)] mt-1">{{ form.errors.slug }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1.5">{{ t('admin.books.category') }}</label>
                        <select v-model="form.blog_category_id" class="w-full px-4 py-2.5 rounded-lg bg-[var(--surface2)] border border-[var(--border)]">
                            <option value="">— {{ t('admin.categories.none') }} —</option>
                            <option v-for="cat in categories" :key="cat.id" :value="cat.id">{{ cat.name_bn }}</option>
                        </select>
                    </div>
                </div>

                <fieldset v-if="tags?.length" class="space-y-2">
                    <legend class="block text-sm font-medium mb-1.5">{{ t('admin.blog.tags') }}</legend>
                    <p class="text-xs text-[var(--text-muted)]">{{ t('admin.blog.tags_help') }}</p>
                    <div class="flex flex-wrap gap-2">
                        <label v-for="tag in tags" :key="tag.id" class="inline-flex items-center gap-2 rounded-full border border-[var(--border)] bg-[var(--surface2)] px-3 py-1.5 text-xs">
                            <input v-model="form.tags" type="checkbox" :value="tag.id" class="rounded" />
                            <span>{{ tag.name }}</span>
                        </label>
                    </div>
                    <p v-if="form.errors.tags || form.errors['tags.0']" class="text-xs text-[var(--accent)]">{{ form.errors.tags || form.errors['tags.0'] }}</p>
                </fieldset>

                <div>
                    <label class="block text-sm font-medium mb-1.5">{{ t('admin.blog.excerpt') }}</label>
                    <textarea v-model="form.excerpt" rows="2" class="w-full px-4 py-2.5 rounded-lg bg-[var(--surface2)] border border-[var(--border)]"></textarea>
                </div>

                <div>
                    <label class="block text-sm font-medium mb-1.5">{{ t('admin.pages.content') }}</label>
                    <RichTextEditor v-model="form.content" />
                    <p v-if="form.errors.content" class="text-xs text-[var(--accent)] mt-1">{{ form.errors.content }}</p>
                </div>
            </div>

            <!-- Featured Image & SEO -->
            <div class="rounded-xl border border-[var(--border)] bg-[var(--surface)] p-6 space-y-4">
                <div>
                    <label class="block text-sm font-medium mb-1.5">{{ t('admin.blog.featured_image') }}</label>
                    <div class="flex items-center gap-4">
                        <img v-if="imagePreview" :src="imagePreview" alt="" class="h-20 w-32 object-cover rounded-lg border border-[var(--border)]" />
                        <div class="flex-1 space-y-2">
                            <input type="file" accept=".png,.jpg,.jpeg,.webp" @change="onImageChange" class="block w-full text-sm text-[var(--text-muted)] file:mr-3 file:px-3 file:py-1.5 file:rounded-lg file:border-0 file:bg-[var(--surface2)] file:text-sm file:font-medium" />
                            <button v-if="imagePreview" type="button" @click="removeImage" class="text-xs text-[var(--accent)] hover:underline">
                                {{ t('admin.settings.remove') }}
                            </button>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 pt-2 border-t border-[var(--border)]">
                    <div>
                        <label class="block text-sm font-medium mb-1.5">SEO Meta Title</label>
                        <input v-model="form.meta_title" type="text" class="w-full px-4 py-2.5 rounded-lg bg-[var(--surface2)] border border-[var(--border)]" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1.5">SEO Meta Description</label>
                        <textarea v-model="form.meta_description" rows="2" class="w-full px-4 py-2.5 rounded-lg bg-[var(--surface2)] border border-[var(--border)]"></textarea>
                    </div>
                </div>
            </div>

            <!-- Status & Save -->
            <div class="rounded-xl border border-[var(--border)] bg-[var(--surface)] p-6 flex flex-wrap items-center justify-between gap-4">
                <div class="flex items-center gap-4">
                    <div>
                        <label class="block text-xs font-medium text-[var(--text-muted)] mb-1">{{ t('common.status') }}</label>
                        <select v-model="form.status" class="px-3.5 py-2 rounded-lg bg-[var(--surface2)] border border-[var(--border)] text-sm font-semibold">
                            <option value="draft">{{ t('admin.blog.draft') }} (Unpublished)</option>
                            <option value="published">{{ t('common.published') }} (Public)</option>
                        </select>
                    </div>
                </div>
                <button type="submit" :disabled="form.processing" class="px-6 py-2.5 rounded-lg bg-[var(--primary)] hover:bg-[var(--primary-hover)] text-white text-sm font-semibold disabled:opacity-60">
                    {{ form.processing ? t('common.saving') : t('common.save') }}
                </button>
            </div>
        </form>
    </AdminLayout>
</template>
