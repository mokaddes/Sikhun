<script setup>
import { ref } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import AdminLayout from '@/Components/Layout/AdminLayout.vue';
import ConfirmButton from '@/Components/UI/ConfirmButton.vue';
import { useI18n } from '@/i18n';

defineProps({ categories: Array });
const { t } = useI18n();

const editingId = ref(null);
const form = useForm({
    name_bn: '',
    name_en: '',
    slug: '',
    sort_order: 0,
    is_active: true,
});

function startEdit(cat) {
    editingId.value = cat.id;
    form.name_bn = cat.name_bn;
    form.name_en = cat.name_en ?? '';
    form.slug = cat.slug;
    form.sort_order = cat.sort_order ?? 0;
    form.is_active = !!cat.is_active;
}

function resetForm() {
    editingId.value = null;
    form.reset();
    form.is_active = true;
}

function submit() {
    if (editingId.value) {
        form.put(`/admin/blog-categories/${editingId.value}`, {
            onSuccess: () => resetForm(),
        });
    } else {
        form.post('/admin/blog-categories', {
            onSuccess: () => resetForm(),
        });
    }
}
</script>

<template>
    <Head :title="t('admin.nav.blog_categories')" />
    <AdminLayout>
        <div class="flex items-center justify-between mb-6">
            <h1 class="font-heading text-2xl font-extrabold">{{ t('admin.nav.blog_categories') }}</h1>
            <Link href="/admin/blog" class="text-sm text-[var(--text-muted)] hover:underline">← {{ t('admin.blog.title') }}</Link>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <form @submit.prevent="submit" class="rounded-xl border border-[var(--border)] bg-[var(--surface)] p-5 space-y-4 h-fit">
                <h2 class="font-heading font-bold text-base">{{ editingId ? t('common.edit') : t('common.create') }}</h2>
                <div>
                    <label class="block text-xs font-medium mb-1">Name (বাংলা) *</label>
                    <input v-model="form.name_bn" type="text" required class="w-full px-3.5 py-2 rounded-lg bg-[var(--surface2)] border border-[var(--border)] text-sm" />
                </div>
                <div>
                    <label class="block text-xs font-medium mb-1">Name (English)</label>
                    <input v-model="form.name_en" type="text" class="w-full px-3.5 py-2 rounded-lg bg-[var(--surface2)] border border-[var(--border)] text-sm" />
                </div>
                <div>
                    <label class="block text-xs font-medium mb-1">Slug</label>
                    <input v-model="form.slug" type="text" placeholder="auto-generated" class="w-full px-3.5 py-2 rounded-lg bg-[var(--surface2)] border border-[var(--border)] text-sm font-mono" />
                </div>
                <div>
                    <label class="block text-xs font-medium mb-1">Sort Order</label>
                    <input v-model="form.sort_order" type="number" min="0" class="w-full px-3.5 py-2 rounded-lg bg-[var(--surface2)] border border-[var(--border)] text-sm" />
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

            <div class="lg:col-span-2 rounded-xl border border-[var(--border)] bg-[var(--surface)] overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-[var(--border)] text-left text-[var(--text-muted)]">
                            <th class="px-4 py-3 font-medium">বাংলা নাম</th>
                            <th class="px-4 py-3 font-medium">English</th>
                            <th class="px-4 py-3 font-medium">Slug</th>
                            <th class="px-4 py-3 font-medium">Posts</th>
                            <th class="px-4 py-3 font-medium text-right">{{ t('common.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="cat in categories" :key="cat.id" class="border-b border-[var(--border)] last:border-0">
                            <td class="px-4 py-3 font-medium">{{ cat.name_bn }}</td>
                            <td class="px-4 py-3 text-[var(--text-muted)]">{{ cat.name_en || '—' }}</td>
                            <td class="px-4 py-3 font-mono text-xs text-[var(--text-muted)]">{{ cat.slug }}</td>
                            <td class="px-4 py-3">{{ cat.posts_count ?? 0 }}</td>
                            <td class="px-4 py-3 text-right space-x-3">
                                <button type="button" @click="startEdit(cat)" class="text-xs font-medium text-[var(--primary)] hover:underline">
                                    {{ t('common.edit') }}
                                </button>
                                <ConfirmButton :href="`/admin/blog-categories/${cat.id}`" method="delete" />
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </AdminLayout>
</template>
