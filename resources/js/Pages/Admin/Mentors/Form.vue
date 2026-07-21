<script setup>
import { watch } from 'vue';
import { Head, useForm } from '@inertiajs/vue3';
import AdminLayout from '@/Components/Layout/AdminLayout.vue';
import { useI18n } from '@/i18n';

const props = defineProps({ mentor: Object });
const { t } = useI18n();
const isEdit = !!props.mentor;

const form = useForm({
    name: props.mentor?.name ?? '',
    slug: props.mentor?.slug ?? '',
    designation: props.mentor?.designation ?? '',
    bio: props.mentor?.bio ?? '',
    expertise: props.mentor?.expertise ?? '',
});

let slugTouched = isEdit;
watch(() => form.name, (val) => {
    if (!slugTouched) form.slug = val.toLowerCase().trim().replace(/[^a-z0-9]+/g, '-').replace(/(^-|-$)/g, '');
});

function submit() {
    isEdit ? form.put(`/admin/mentors/${props.mentor.id}`) : form.post('/admin/mentors');
}
</script>

<template>
    <Head :title="isEdit ? t('common.edit') : t('admin.mentors.new')" />
    <AdminLayout>
        <h1 class="font-heading text-2xl font-extrabold mb-6">{{ isEdit ? t('common.edit') : t('admin.mentors.new') }}</h1>
        <form @submit.prevent="submit" class="max-w-lg space-y-5 rounded-xl border border-[var(--border)] bg-[var(--surface)] p-6">
            <div>
                <label class="block text-sm font-medium mb-1.5">{{ t('common.name') }}</label>
                <input v-model="form.name" type="text" required class="w-full px-4 py-2.5 rounded-lg bg-[var(--surface2)] border border-[var(--border)] focus:outline-none focus:ring-2 focus:ring-[var(--primary)]" />
            </div>
            <div>
                <label class="block text-sm font-medium mb-1.5">Slug</label>
                <input v-model="form.slug" @input="slugTouched = true" type="text" required class="w-full px-4 py-2.5 rounded-lg bg-[var(--surface2)] border border-[var(--border)] focus:outline-none focus:ring-2 focus:ring-[var(--primary)]" />
            </div>
            <div>
                <label class="block text-sm font-medium mb-1.5">{{ t('admin.mentors.designation') }}</label>
                <input v-model="form.designation" type="text" class="w-full px-4 py-2.5 rounded-lg bg-[var(--surface2)] border border-[var(--border)] focus:outline-none focus:ring-2 focus:ring-[var(--primary)]" />
            </div>
            <div>
                <label class="block text-sm font-medium mb-1.5">{{ t('admin.mentors.expertise') }}</label>
                <input v-model="form.expertise" type="text" placeholder="Physics, Higher Math" class="w-full px-4 py-2.5 rounded-lg bg-[var(--surface2)] border border-[var(--border)] focus:outline-none focus:ring-2 focus:ring-[var(--primary)]" />
            </div>
            <div>
                <label class="block text-sm font-medium mb-1.5">{{ t('admin.authors.bio') }}</label>
                <textarea v-model="form.bio" rows="3" class="w-full px-4 py-2.5 rounded-lg bg-[var(--surface2)] border border-[var(--border)] focus:outline-none focus:ring-2 focus:ring-[var(--primary)]"></textarea>
            </div>
            <button type="submit" :disabled="form.processing" class="px-5 py-2.5 rounded-lg bg-[var(--primary)] hover:bg-[var(--primary-hover)] text-white text-sm font-semibold disabled:opacity-60">
                {{ form.processing ? t('common.saving') : t('common.save') }}
            </button>
        </form>
    </AdminLayout>
</template>
