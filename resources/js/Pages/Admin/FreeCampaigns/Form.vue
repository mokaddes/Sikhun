<script setup>
import { ref } from 'vue';
import { Head, useForm } from '@inertiajs/vue3';
import AdminLayout from '@/Components/Layout/AdminLayout.vue';
import { useI18n } from '@/i18n';

const props = defineProps({ campaign: Object, students: Array });
const { t } = useI18n();
const isEdit = !!props.campaign;

const form = useForm({
    title: props.campaign?.title ?? '',
    description: props.campaign?.description ?? '',
    scope: props.campaign?.scope ?? 'all',
    student_ids: props.campaign?.students?.map((s) => s.id) ?? [],
    starts_at: props.campaign?.starts_at ? new Date(props.campaign.starts_at).toISOString().slice(0, 16) : '',
    ends_at: props.campaign?.ends_at ? new Date(props.campaign.ends_at).toISOString().slice(0, 16) : '',
    is_active: props.campaign?.is_active ?? true,
});

const search = ref('');
const filteredStudents = ref(props.students);

function filterStudents() {
    const q = search.value.toLowerCase();
    filteredStudents.value = props.students.filter((s) => s.name.toLowerCase().includes(q) || s.email.toLowerCase().includes(q));
}
</script>

<template>
    <Head :title="isEdit ? t('common.edit') : 'New Free Campaign'" />
    <AdminLayout>
        <h1 class="font-heading text-2xl font-extrabold mb-6">{{ isEdit ? t('common.edit') : 'New Free Campaign' }}</h1>
        <form @submit.prevent="isEdit ? form.put(`/admin/free-campaigns/${props.campaign.id}`) : form.post('/admin/free-campaigns')" class="max-w-xl space-y-5 rounded-xl border border-[var(--border)] bg-[var(--surface)] p-6">
            <div>
                <label class="block text-sm font-medium mb-1.5">Title</label>
                <input v-model="form.title" type="text" required class="w-full px-4 py-2.5 rounded-lg bg-[var(--surface2)] border border-[var(--border)]" />
            </div>
            <div>
                <label class="block text-sm font-medium mb-1.5">Description</label>
                <textarea v-model="form.description" rows="2" class="w-full px-4 py-2.5 rounded-lg bg-[var(--surface2)] border border-[var(--border)]"></textarea>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium mb-1.5">Starts at</label>
                    <input v-model="form.starts_at" type="datetime-local" required class="w-full px-4 py-2.5 rounded-lg bg-[var(--surface2)] border border-[var(--border)]" />
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1.5">Ends at</label>
                    <input v-model="form.ends_at" type="datetime-local" required class="w-full px-4 py-2.5 rounded-lg bg-[var(--surface2)] border border-[var(--border)]" />
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium mb-1.5">Target</label>
                <div class="flex gap-4 mb-3">
                    <label class="flex items-center gap-2 text-sm"><input type="radio" value="all" v-model="form.scope" class="rounded-full" /> All students</label>
                    <label class="flex items-center gap-2 text-sm"><input type="radio" value="selected" v-model="form.scope" class="rounded-full" /> Selected students</label>
                </div>

                <div v-if="form.scope === 'selected'">
                    <input v-model="search" @input="filterStudents" type="text" placeholder="Search students..." class="w-full px-4 py-2.5 mb-2 rounded-lg bg-[var(--surface2)] border border-[var(--border)] text-sm" />
                    <div class="max-h-48 overflow-y-auto rounded-lg border border-[var(--border)] p-3 space-y-1">
                        <label v-for="s in filteredStudents" :key="s.id" class="flex items-center gap-2 text-sm">
                            <input type="checkbox" :value="s.id" v-model="form.student_ids" class="rounded" /> {{ s.name }} ({{ s.email }})
                        </label>
                        <p v-if="!filteredStudents.length" class="text-sm text-[var(--text-muted)]">{{ t('common.no_results') }}</p>
                    </div>
                </div>
            </div>

            <label class="flex items-center gap-2 text-sm"><input v-model="form.is_active" type="checkbox" class="rounded" /> {{ t('common.active') }}</label>
            <button type="submit" :disabled="form.processing" class="px-5 py-2.5 rounded-lg bg-[var(--primary)] hover:bg-[var(--primary-hover)] text-white text-sm font-semibold disabled:opacity-60">
                {{ form.processing ? t('common.saving') : t('common.save') }}
            </button>
        </form>
    </AdminLayout>
</template>