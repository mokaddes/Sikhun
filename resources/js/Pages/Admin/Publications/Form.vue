<script setup>
import { watch } from 'vue';
import { Head, useForm } from '@inertiajs/vue3';
import AdminLayout from '@/Components/Layout/AdminLayout.vue';
import { useI18n } from '@/i18n';

const props = defineProps({ publication: Object });
const { t } = useI18n();
const isEdit = !!props.publication;

const form = useForm({
    name: props.publication?.name ?? '',
    slug: props.publication?.slug ?? '',
});

let slugTouched = isEdit;
watch(() => form.name, (val) => {
    if (!slugTouched) form.slug = val.toLowerCase().trim().replace(/[^a-z0-9]+/g, '-').replace(/(^-|-$)/g, '');
});

function submit() {
    isEdit ? form.put(`/admin/publications/${props.publication.id}`) : form.post('/admin/publications');
}
</script>

<template>
    <Head :title="isEdit ? t('common.edit') : t('admin.publications.new')" />
    <AdminLayout>
        <h1 class="font-heading text-2xl font-extrabold mb-6">{{ isEdit ? t('common.edit') : t('admin.publications.new') }}</h1>
        <form @submit.prevent="submit" class="max-w-lg space-y-5 rounded-xl border border-[var(--border)] bg-[var(--surface)] p-6">
            <div>
                <label class="block text-sm font-medium mb-1.5">{{ t('common.name') }}</label>
                <input v-model="form.name" type="text" required class="w-full px-4 py-2.5 rounded-lg bg-[var(--surface2)] border border-[var(--border)] focus:outline-none focus:ring-2 focus:ring-[var(--primary)]" />
            </div>
            <div>
                <label class="block text-sm font-medium mb-1.5">Slug</label>
                <input v-model="form.slug" @input="slugTouched = true" type="text" required class="w-full px-4 py-2.5 rounded-lg bg-[var(--surface2)] border border-[var(--border)] focus:outline-none focus:ring-2 focus:ring-[var(--primary)]" />
                <p v-if="form.errors.slug" class="text-[var(--accent)] text-xs mt-1">{{ form.errors.slug }}</p>
            </div>
            <button type="submit" :disabled="form.processing" class="px-5 py-2.5 rounded-lg bg-[var(--primary)] hover:bg-[var(--primary-hover)] text-white text-sm font-semibold disabled:opacity-60">
                {{ form.processing ? t('common.saving') : t('common.save') }}
            </button>
        </form>
    </AdminLayout>
</template>
