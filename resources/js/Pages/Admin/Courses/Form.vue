<script setup>
import { watch, ref, reactive } from 'vue';
import { Head, useForm, router } from '@inertiajs/vue3';
import AdminLayout from '@/Components/Layout/AdminLayout.vue';
import ConfirmButton from '@/Components/UI/ConfirmButton.vue';
import { useI18n } from '@/i18n';

const props = defineProps({ course: Object, mentors: Array, categories: Array });
const { t } = useI18n();
const isEdit = !!props.course;

const form = useForm({
    title: props.course?.title ?? '',
    slug: props.course?.slug ?? '',
    description: props.course?.description ?? '',
    mentor_id: props.course?.mentor_id ?? null,
    category_id: props.course?.category_id ?? null,
    level: props.course?.level ?? 'hsc',
    price: props.course?.price ?? 0,
    is_active: props.course?.is_active ?? false,
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
            .post(`/admin/courses/${props.course.id}`, { forceFormData: true, preserveScroll: true });
    } else {
        form.post('/admin/courses', { forceFormData: true });
    }
}

// --- Sections & Lessons (only shown once the course exists) ---
const newSectionTitle = ref('');
function addSection() {
    if (!newSectionTitle.value.trim()) return;
    router.post(`/admin/courses/${props.course.id}/sections`, { title: newSectionTitle.value }, {
        preserveScroll: true,
        onSuccess: () => (newSectionTitle.value = ''),
    });
}

const newLesson = reactive({});
function lessonDraft(sectionId) {
    if (!newLesson[sectionId]) {
        newLesson[sectionId] = { title: '', type: 'video', video_url: '', duration_minutes: 10, is_free_preview: false };
    }
    return newLesson[sectionId];
}
function addLesson(sectionId) {
    const draft = lessonDraft(sectionId);
    if (!draft.title.trim()) return;
    router.post(`/admin/courses/${props.course.id}/sections/${sectionId}/lessons`, draft, {
        preserveScroll: true,
        onSuccess: () => { newLesson[sectionId] = { title: '', type: 'video', video_url: '', duration_minutes: 10, is_free_preview: false }; },
    });
}
</script>

<template>
    <Head :title="isEdit ? t('admin.courses.edit') : t('admin.courses.new')" />
    <AdminLayout>
        <h1 class="font-heading text-2xl font-extrabold mb-6">{{ isEdit ? t('admin.courses.edit') : t('admin.courses.new') }}</h1>

        <form @submit.prevent="submit" class="max-w-2xl space-y-5 rounded-xl border border-[var(--border)] bg-[var(--surface)] p-6 mb-8">
            <div>
                <label class="block text-sm font-medium mb-1.5">{{ t('common.title') }}</label>
                <input v-model="form.title" type="text" required class="w-full px-4 py-2.5 rounded-lg bg-[var(--surface2)] border border-[var(--border)]" />
            </div>
            <div>
                <label class="block text-sm font-medium mb-1.5">Slug</label>
                <input v-model="form.slug" @input="slugTouched = true" type="text" required class="w-full px-4 py-2.5 rounded-lg bg-[var(--surface2)] border border-[var(--border)]" />
            </div>
            <div>
                <label class="block text-sm font-medium mb-1.5">{{ t('admin.books.description') }}</label>
                <textarea v-model="form.description" rows="3" class="w-full px-4 py-2.5 rounded-lg bg-[var(--surface2)] border border-[var(--border)]"></textarea>
            </div>
            <div class="grid grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium mb-1.5">{{ t('admin.courses.mentor') }}</label>
                    <select v-model="form.mentor_id" class="w-full px-4 py-2.5 rounded-lg bg-[var(--surface2)] border border-[var(--border)]">
                        <option :value="null">—</option>
                        <option v-for="m in mentors" :key="m.id" :value="m.id">{{ m.name }}</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1.5">{{ t('common.level') }}</label>
                    <select v-model="form.level" class="w-full px-4 py-2.5 rounded-lg bg-[var(--surface2)] border border-[var(--border)]">
                        <option value="ssc">SSC</option><option value="hsc">HSC</option>
                        <option value="university">University</option><option value="job">Job</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1.5">{{ t('common.price') }} (৳)</label>
                    <input v-model="form.price" type="number" step="0.01" min="0" class="w-full px-4 py-2.5 rounded-lg bg-[var(--surface2)] border border-[var(--border)]" />
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium mb-1.5">Cover Image</label>
                <input ref="coverInput" @input="form.cover_image = coverInput.files[0]" type="file" accept="image/*" class="w-full text-sm" />
            </div>
            <label class="flex items-center gap-2 text-sm"><input v-model="form.is_active" type="checkbox" class="rounded" /> {{ t('common.active') }}</label>

            <button type="submit" :disabled="form.processing" class="px-5 py-2.5 rounded-lg bg-[var(--primary)] hover:bg-[var(--primary-hover)] text-white text-sm font-semibold disabled:opacity-60">
                {{ form.processing ? t('common.saving') : t('common.save') }}
            </button>
        </form>

        <!-- Sections & Lessons -->
        <div v-if="isEdit" class="max-w-2xl">
            <h2 class="font-heading text-xl font-bold mb-4">{{ t('admin.courses.sections') }}</h2>

            <div v-for="section in course.sections" :key="section.id" class="rounded-xl border border-[var(--border)] bg-[var(--surface)] p-5 mb-4">
                <div class="flex items-center justify-between mb-3">
                    <div class="font-semibold">{{ section.title }}</div>
                    <ConfirmButton :href="`/admin/courses/${course.id}/sections/${section.id}`" method="delete" />
                </div>

                <ul class="space-y-2 mb-4">
                    <li v-for="lesson in section.lessons" :key="lesson.id" class="flex items-center justify-between text-sm px-3 py-2 rounded-lg bg-[var(--surface2)]">
                        <span>
                            {{ lesson.title }}
                            <span class="text-xs text-[var(--text-muted)]">· {{ lesson.type }} · {{ lesson.duration_minutes }}min</span>
                            <span v-if="lesson.is_free_preview" class="text-xs text-[var(--secondary)] ml-1">{{ t('admin.courses.free_preview') }}</span>
                        </span>
                        <ConfirmButton :href="`/admin/courses/${course.id}/sections/${section.id}/lessons/${lesson.id}`" method="delete" />
                    </li>
                    <li v-if="!section.lessons.length" class="text-xs text-[var(--text-muted)]">No lessons yet.</li>
                </ul>

                <div class="flex flex-wrap gap-2 items-center">
                    <input v-model="lessonDraft(section.id).title" type="text" placeholder="Lesson title"
                        class="flex-1 min-w-[140px] px-3 py-2 rounded-lg bg-[var(--surface2)] border border-[var(--border)] text-sm" />
                    <select v-model="lessonDraft(section.id).type" class="px-3 py-2 rounded-lg bg-[var(--surface2)] border border-[var(--border)] text-sm">
                        <option value="video">Video</option><option value="text">Text</option><option value="pdf">PDF</option>
                    </select>
                    <input v-model="lessonDraft(section.id).duration_minutes" type="number" min="0" placeholder="Min"
                        class="w-20 px-3 py-2 rounded-lg bg-[var(--surface2)] border border-[var(--border)] text-sm" />
                    <button type="button" @click="addLesson(section.id)" class="px-3 py-2 rounded-lg bg-[var(--primary)] text-white text-sm font-medium">
                        {{ t('admin.courses.add_lesson') }}
                    </button>
                </div>
            </div>

            <div class="rounded-xl border border-dashed border-[var(--border)] p-5 flex gap-2">
                <input v-model="newSectionTitle" type="text" placeholder="New section title"
                    class="flex-1 px-4 py-2.5 rounded-lg bg-[var(--surface2)] border border-[var(--border)] text-sm" />
                <button type="button" @click="addSection" class="px-4 py-2.5 rounded-lg bg-[var(--primary)] hover:bg-[var(--primary-hover)] text-white text-sm font-semibold">
                    {{ t('admin.courses.add_section') }}
                </button>
            </div>
        </div>
    </AdminLayout>
</template>
