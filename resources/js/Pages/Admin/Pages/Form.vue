<script setup>
import { ref, watch } from 'vue';
import { Head, useForm } from '@inertiajs/vue3';
import AdminLayout from '@/Components/Layout/AdminLayout.vue';
import RichTextEditor from '@/Components/UI/RichTextEditor.vue';
import { useI18n } from '@/i18n';

const props = defineProps({ page: Object });
const { t } = useI18n();
const isEdit = !!props.page;
const activeTab = ref('bn');

const form = useForm({
    slug: props.page?.slug ?? '',
    title_bn: props.page?.title_bn ?? '',
    title_en: props.page?.title_en ?? '',
    content_bn: props.page?.content_bn ?? '',
    content_en: props.page?.content_en ?? '',
    meta_title_bn: props.page?.meta_title_bn ?? '',
    meta_title_en: props.page?.meta_title_en ?? '',
    meta_description_bn: props.page?.meta_description_bn ?? '',
    meta_description_en: props.page?.meta_description_en ?? '',
    is_published: props.page?.is_published ?? true,
});

let slugTouched = isEdit;
watch(() => form.title_bn, (val) => {
    if (!slugTouched) form.slug = val.toLowerCase().trim().replace(/[^a-z0-9]+/g, '-').replace(/(^-|-$)/g, '');
});

function submit() {
    isEdit ? form.put(`/admin/pages/${props.page.id}`) : form.post('/admin/pages');
}
</script>

<template>
    <Head :title="isEdit ? t('common.edit') : t('admin.pages.new')" />
    <AdminLayout>
        <h1 class="font-heading text-2xl font-extrabold mb-6">{{ isEdit ? t('common.edit') : t('admin.pages.new') }}</h1>

        <form @submit.prevent="submit" class="max-w-3xl space-y-5">
            <div class="rounded-xl border border-[var(--border)] bg-[var(--surface)] p-6">
                <label class="block text-sm font-medium mb-1.5">{{ t('admin.pages.slug') }}</label>
                <input v-model="form.slug" @input="slugTouched = true" type="text" required class="w-full px-4 py-2.5 rounded-lg bg-[var(--surface2)] border border-[var(--border)]" />
                <p class="text-xs text-[var(--text-muted)] mt-1">Will be available at /p/{{ form.slug }} — same URL regardless of language; content switches automatically.</p>
            </div>

            <!-- Language tabs — each language's content is fully independent -->
            <div class="rounded-xl border border-[var(--border)] bg-[var(--surface)] overflow-hidden">
                <div class="flex border-b border-[var(--border)]">
                    <button type="button" @click="activeTab = 'bn'" class="flex-1 px-4 py-3 text-sm font-semibold"
                        :class="activeTab === 'bn' ? 'bg-[var(--primary)] text-white' : 'text-[var(--text-muted)]'">
                        বাংলা (Bengali)
                    </button>
                    <button type="button" @click="activeTab = 'en'" class="flex-1 px-4 py-3 text-sm font-semibold"
                        :class="activeTab === 'en' ? 'bg-[var(--primary)] text-white' : 'text-[var(--text-muted)]'">
                        English
                    </button>
                </div>

                <div v-show="activeTab === 'bn'" class="p-6 space-y-4">
                    <div>
                        <label class="block text-sm font-medium mb-1.5">{{ t('common.title') }} (বাংলা)</label>
                        <input v-model="form.title_bn" type="text" required class="w-full px-4 py-2.5 rounded-lg bg-[var(--surface2)] border border-[var(--border)]" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1.5">{{ t('admin.pages.content') }} (বাংলা)</label>
                        <RichTextEditor v-model="form.content_bn" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1.5">{{ t('admin.pages.meta_title') }} (বাংলা)</label>
                        <input v-model="form.meta_title_bn" type="text" class="w-full px-4 py-2.5 rounded-lg bg-[var(--surface2)] border border-[var(--border)]" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1.5">{{ t('admin.pages.meta_description') }} (বাংলা)</label>
                        <textarea v-model="form.meta_description_bn" rows="2" class="w-full px-4 py-2.5 rounded-lg bg-[var(--surface2)] border border-[var(--border)]"></textarea>
                    </div>
                </div>

                <div v-show="activeTab === 'en'" class="p-6 space-y-4">
                    <div>
                        <label class="block text-sm font-medium mb-1.5">{{ t('common.title') }} (English)</label>
                        <input v-model="form.title_en" type="text" required class="w-full px-4 py-2.5 rounded-lg bg-[var(--surface2)] border border-[var(--border)]" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1.5">{{ t('admin.pages.content') }} (English)</label>
                        <RichTextEditor v-model="form.content_en" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1.5">{{ t('admin.pages.meta_title') }} (English)</label>
                        <input v-model="form.meta_title_en" type="text" class="w-full px-4 py-2.5 rounded-lg bg-[var(--surface2)] border border-[var(--border)]" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1.5">{{ t('admin.pages.meta_description') }} (English)</label>
                        <textarea v-model="form.meta_description_en" rows="2" class="w-full px-4 py-2.5 rounded-lg bg-[var(--surface2)] border border-[var(--border)]"></textarea>
                    </div>
                </div>
            </div>

            <div class="rounded-xl border border-[var(--border)] bg-[var(--surface)] p-6 flex items-center justify-between">
                <label class="flex items-center gap-2 text-sm"><input v-model="form.is_published" type="checkbox" class="rounded" /> {{ t('common.published') }}</label>
                <button type="submit" :disabled="form.processing" class="px-5 py-2.5 rounded-lg bg-[var(--primary)] hover:bg-[var(--primary-hover)] text-white text-sm font-semibold disabled:opacity-60">
                    {{ form.processing ? t('common.saving') : t('common.save') }}
                </button>
            </div>
        </form>
    </AdminLayout>
</template>
