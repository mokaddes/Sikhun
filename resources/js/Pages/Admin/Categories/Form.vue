<script setup>
import { watch } from 'vue';
import { Head, useForm } from '@inertiajs/vue3';
import AdminLayout from '@/Components/Layout/AdminLayout.vue';
import { useI18n } from '@/i18n';

const props = defineProps({ category: Object, parents: Array });
const { t } = useI18n();
const isEdit = !!props.category;

const form = useForm({
    name: props.category?.name ?? '',
    slug: props.category?.slug ?? '',
    type: props.category?.type ?? 'academic',
    parent_id: props.category?.parent_id ?? null,
});

let slugTouched = isEdit;
watch(() => form.name, (val) => {
    if (!slugTouched) {
        form.slug = val.toLowerCase().trim().replace(/[^a-z0-9]+/g, '-').replace(/(^-|-$)/g, '');
    }
});

function submit() {
    if (isEdit) {
        form.put(`/admin/categories/${props.category.id}`);
    } else {
        form.post('/admin/categories');
    }
}
</script>

<template>
    <Head :title="isEdit ? t('common.edit') : t('admin.categories.new')" />
    <AdminLayout>
        <h1 class="font-heading text-2xl font-extrabold mb-6">{{ isEdit ? t('common.edit') : t('admin.categories.new') }}</h1>

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
            <div>
                <label class="block text-sm font-medium mb-1.5">{{ t('common.type') }}</label>
                <select v-model="form.type" class="w-full px-4 py-2.5 rounded-lg bg-[var(--surface2)] border border-[var(--border)] focus:outline-none focus:ring-2 focus:ring-[var(--primary)]">
                    <option value="academic">Academic</option>
                    <option value="non_academic">Non-academic</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium mb-1.5">{{ t('admin.categories.parent') }}</label>
                <select v-model="form.parent_id" class="w-full px-4 py-2.5 rounded-lg bg-[var(--surface2)] border border-[var(--border)] focus:outline-none focus:ring-2 focus:ring-[var(--primary)]">
                    <option :value="null">{{ t('admin.categories.none') }}</option>
                    <option v-for="p in parents" :key="p.id" :value="p.id">{{ p.name }}</option>
                </select>
            </div>
            <button type="submit" :disabled="form.processing" class="px-5 py-2.5 rounded-lg bg-[var(--primary)] hover:bg-[var(--primary-hover)] text-white text-sm font-semibold disabled:opacity-60">
                {{ form.processing ? t('common.saving') : t('common.save') }}
            </button>
        </form>
    </AdminLayout>
</template>
