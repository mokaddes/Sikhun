<script setup>
import { watch, ref, reactive } from 'vue';
import { Head, useForm, router } from '@inertiajs/vue3';
import axios from 'axios';
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
    delivery_type: props.course?.delivery_type ?? 'video',
    external_link: props.course?.external_link ?? '',
    link_note: props.course?.link_note ?? '',
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

// --- Chunked video upload -------------------------------------------------
// Videos are far too large for a single request, so the file is sliced into
// 5 MB chunks, uploaded one at a time, and merged server-side. Mirrors the
// book PDF uploader. `key` identifies the row so progress is tracked per form.
const CHUNK_SIZE = 5 * 1024 * 1024;
const uploading = reactive({});
const uploadError = reactive({});

async function uploadVideoChunked(file, key) {
    uploading[key] = 1;
    uploadError[key] = '';

    const totalChunks = Math.ceil(file.size / CHUNK_SIZE);
    const uploadId = crypto.randomUUID
        ? crypto.randomUUID()
        : Date.now().toString(36) + Math.random().toString(36).slice(2);

    for (let i = 0; i < totalChunks; i++) {
        const start = i * CHUNK_SIZE;
        const chunk = file.slice(start, Math.min(start + CHUNK_SIZE, file.size));

        const body = new FormData();
        body.append('file', chunk, file.name);
        body.append('upload_id', uploadId);
        body.append('chunk_index', i);
        body.append('total_chunks', totalChunks);
        body.append('filename', file.name);

        try {
            await axios.post('/admin/courses/video/chunk', body, {
                headers: { 'Content-Type': 'multipart/form-data' },
                timeout: 120000,
            });
        } catch (err) {
            uploadError[key] = err.response?.data?.message || 'Upload failed. Please try again.';
            uploading[key] = 0;
            return null;
        }

        uploading[key] = Math.round(((i + 1) / totalChunks) * 90);
    }

    try {
        const res = await axios.post('/admin/courses/video/merge', { upload_id: uploadId }, { timeout: 60000 });

        if (!res.data.ok) {
            uploadError[key] = res.data.message || 'Failed to finalize upload.';
            uploading[key] = 0;
            return null;
        }

        uploading[key] = 100;
        return res.data.temp_path;
    } catch (err) {
        uploadError[key] = err.response?.data?.message || 'Failed to finalize upload.';
        uploading[key] = 0;
        return null;
    }
}

async function pickVideo(event, draft, key) {
    const file = event.target.files?.[0];
    if (!file) return;

    draft.video_temp_path = '';
    const tempPath = await uploadVideoChunked(file, key);

    if (tempPath) {
        draft.video_temp_path = tempPath;
        draft.remove_video = false;
    }
}

// --- Sections & Lessons (video courses only) ------------------------------
const newSectionTitle = ref('');
function addSection() {
    if (!newSectionTitle.value.trim()) return;
    router.post(`/admin/courses/${props.course.id}/sections`, { title: newSectionTitle.value }, {
        preserveScroll: true,
        onSuccess: () => (newSectionTitle.value = ''),
    });
}

function blankLesson() {
    return {
        title: '', type: 'video', video_url: '', text_content: '',
        duration_minutes: 10, is_free_preview: false,
        video_temp_path: '', remove_video: false, pdf_file: null,
    };
}

const newLesson = reactive({});
function lessonDraft(sectionId) {
    if (!newLesson[sectionId]) newLesson[sectionId] = blankLesson();
    return newLesson[sectionId];
}
function addLesson(sectionId) {
    const draft = lessonDraft(sectionId);
    if (!draft.title.trim()) return;

    router.post(`/admin/courses/${props.course.id}/sections/${sectionId}/lessons`, draft, {
        forceFormData: true,
        preserveScroll: true,
        onSuccess: () => { newLesson[sectionId] = blankLesson(); },
    });
}

const editingId = ref(null);
const editDraft = reactive({});
function toggleEdit(lesson) {
    if (editingId.value === lesson.id) {
        editingId.value = null;
        return;
    }

    editingId.value = lesson.id;
    editDraft[lesson.id] = {
        ...blankLesson(),
        title: lesson.title,
        type: lesson.type,
        video_url: lesson.video_url ?? '',
        text_content: lesson.text_content ?? '',
        duration_minutes: lesson.duration_minutes,
        is_free_preview: lesson.is_free_preview,
    };
}
function saveLesson(sectionId, lessonId) {
    const draft = editDraft[lessonId];

    router.post(`/admin/courses/${props.course.id}/sections/${sectionId}/lessons/${lessonId}`,
        { ...draft, _method: 'put' },
        {
            forceFormData: true,
            preserveScroll: true,
            onSuccess: () => { editingId.value = null; },
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
                <p v-if="form.errors.title" class="text-xs text-red-500 mt-1">{{ form.errors.title }}</p>
            </div>
            <div>
                <label class="block text-sm font-medium mb-1.5">Slug</label>
                <input v-model="form.slug" @input="slugTouched = true" type="text" required class="w-full px-4 py-2.5 rounded-lg bg-[var(--surface2)] border border-[var(--border)]" />
                <p v-if="form.errors.slug" class="text-xs text-red-500 mt-1">{{ form.errors.slug }}</p>
            </div>
            <div>
                <label class="block text-sm font-medium mb-1.5">{{ t('admin.courses.delivery_type') }}</label>
                <select v-model="form.delivery_type" class="w-full px-4 py-2.5 rounded-lg bg-[var(--surface2)] border border-[var(--border)]">
                    <option value="video">{{ t('admin.courses.delivery_video') }}</option>
                    <option value="enrollment_link">{{ t('admin.courses.delivery_enrollment_link') }}</option>
                    <option value="file_download">{{ t('admin.courses.delivery_file_download') }}</option>
                </select>
                <p class="text-xs text-[var(--text-muted)] mt-1">{{ t('admin.courses.delivery_help') }}</p>
            </div>

            <template v-if="form.delivery_type !== 'video'">
                <div>
                    <label class="block text-sm font-medium mb-1.5">{{ t('admin.courses.external_link') }}</label>
                    <input v-model="form.external_link" type="url" placeholder="https://"
                        class="w-full px-4 py-2.5 rounded-lg bg-[var(--surface2)] border border-[var(--border)]" />
                    <p class="text-xs text-[var(--text-muted)] mt-1">{{ t('admin.courses.external_link_help') }}</p>
                    <p v-if="form.errors.external_link" class="text-xs text-red-500 mt-1">{{ form.errors.external_link }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1.5">{{ t('admin.courses.link_note') }}</label>
                    <textarea v-model="form.link_note" rows="3"
                        class="w-full px-4 py-2.5 rounded-lg bg-[var(--surface2)] border border-[var(--border)]"></textarea>
                </div>
            </template>

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

        <!-- Sections & Lessons — video courses only -->
        <div v-if="isEdit && form.delivery_type === 'video'" class="max-w-3xl">
            <h2 class="font-heading text-xl font-bold mb-4">{{ t('admin.courses.sections') }}</h2>

            <div v-for="section in course.sections" :key="section.id" class="rounded-xl border border-[var(--border)] bg-[var(--surface)] p-5 mb-4">
                <div class="flex items-center justify-between mb-3">
                    <div class="font-semibold">{{ section.title }}</div>
                    <ConfirmButton :href="`/admin/courses/${course.id}/sections/${section.id}`" method="delete" />
                </div>

                <ul class="space-y-2 mb-4">
                    <li v-for="lesson in section.lessons" :key="lesson.id" class="text-sm rounded-lg bg-[var(--surface2)]">
                        <div class="flex items-center justify-between px-3 py-2">
                            <span>
                                {{ lesson.title }}
                                <span class="text-xs text-[var(--text-muted)]">· {{ lesson.type }} · {{ lesson.duration_minutes }}min</span>
                                <span v-if="lesson.video_path" class="text-xs text-[var(--secondary)] ml-1">· {{ t('admin.courses.video_attached') }}</span>
                                <span v-else-if="lesson.pdf_path" class="text-xs text-[var(--secondary)] ml-1">· {{ t('admin.courses.pdf_attached') }}</span>
                                <span v-if="lesson.is_free_preview" class="text-xs text-[var(--secondary)] ml-1">· {{ t('admin.courses.free_preview') }}</span>
                            </span>
                            <span class="flex items-center gap-2">
                                <button type="button" @click="toggleEdit(lesson)" class="text-xs px-2 py-1 rounded border border-[var(--border)] hover:bg-[var(--surface)]">
                                    {{ editingId === lesson.id ? t('common.cancel') : t('common.edit') }}
                                </button>
                                <ConfirmButton :href="`/admin/courses/${course.id}/sections/${section.id}/lessons/${lesson.id}`" method="delete" />
                            </span>
                        </div>

                        <!-- Inline lesson editor: title, media, preview flag -->
                        <div v-if="editingId === lesson.id" class="border-t border-[var(--border)] px-3 py-3 space-y-3">
                            <input v-model="editDraft[lesson.id].title" type="text" :placeholder="t('common.title')"
                                class="w-full px-3 py-2 rounded-lg bg-[var(--surface)] border border-[var(--border)] text-sm" />
                            <div class="flex flex-wrap gap-2">
                                <select v-model="editDraft[lesson.id].type" class="px-3 py-2 rounded-lg bg-[var(--surface)] border border-[var(--border)] text-sm">
                                    <option value="video">Video</option><option value="text">Text</option><option value="pdf">PDF</option>
                                </select>
                                <input v-model="editDraft[lesson.id].duration_minutes" type="number" min="0" placeholder="Min"
                                    class="w-24 px-3 py-2 rounded-lg bg-[var(--surface)] border border-[var(--border)] text-sm" />
                                <label class="flex items-center gap-2 text-sm px-2">
                                    <input v-model="editDraft[lesson.id].is_free_preview" type="checkbox" class="rounded" />
                                    {{ t('admin.courses.free_preview') }}
                                </label>
                            </div>

                            <template v-if="editDraft[lesson.id].type === 'video'">
                                <div>
                                    <label class="block text-xs font-medium mb-1">{{ t('admin.courses.upload_video') }}</label>
                                    <input type="file" accept="video/mp4,video/webm,video/quicktime"
                                        @change="pickVideo($event, editDraft[lesson.id], `edit-${lesson.id}`)" class="w-full text-sm" />
                                    <div v-if="uploading[`edit-${lesson.id}`]" class="mt-1">
                                        <div class="h-1.5 rounded-full bg-[var(--surface)] overflow-hidden">
                                            <div class="h-full bg-[var(--primary)]" :style="{ width: `${uploading[`edit-${lesson.id}`]}%` }"></div>
                                        </div>
                                        <p class="text-xs text-[var(--text-muted)] mt-1">{{ t('admin.courses.uploading') }} {{ uploading[`edit-${lesson.id}`] }}%</p>
                                    </div>
                                    <p v-if="editDraft[lesson.id].video_temp_path" class="text-xs text-[var(--secondary)] mt-1">{{ t('admin.courses.video_ready') }}</p>
                                    <p v-if="uploadError[`edit-${lesson.id}`]" class="text-xs text-red-500 mt-1">{{ uploadError[`edit-${lesson.id}`] }}</p>
                                    <label v-if="lesson.video_path" class="flex items-center gap-2 text-xs mt-2">
                                        <input v-model="editDraft[lesson.id].remove_video" type="checkbox" class="rounded" />
                                        {{ t('admin.courses.remove_video') }}
                                    </label>
                                </div>
                                <div>
                                    <label class="block text-xs font-medium mb-1">{{ t('admin.courses.or_video_url') }}</label>
                                    <input v-model="editDraft[lesson.id].video_url" type="text" placeholder="https://..."
                                        class="w-full px-3 py-2 rounded-lg bg-[var(--surface)] border border-[var(--border)] text-sm" />
                                </div>
                            </template>

                            <template v-if="editDraft[lesson.id].type === 'pdf'">
                                <div>
                                    <label class="block text-xs font-medium mb-1">{{ t('admin.courses.upload_pdf') }}</label>
                                    <input type="file" accept="application/pdf"
                                        @change="editDraft[lesson.id].pdf_file = $event.target.files[0]" class="w-full text-sm" />
                                    <p v-if="lesson.pdf_path" class="text-xs text-[var(--text-muted)] mt-1">{{ t('admin.courses.pdf_replace_hint') }}</p>
                                </div>
                            </template>

                            <template v-if="editDraft[lesson.id].type === 'text'">
                                <textarea v-model="editDraft[lesson.id].text_content" rows="4" class="w-full px-3 py-2 rounded-lg bg-[var(--surface)] border border-[var(--border)] text-sm"></textarea>
                            </template>

                            <button type="button" @click="saveLesson(section.id, lesson.id)"
                                class="px-4 py-2 rounded-lg bg-[var(--primary)] text-white text-sm font-semibold">
                                {{ t('common.save') }}
                            </button>
                        </div>
                    </li>
                    <li v-if="!section.lessons.length" class="text-xs text-[var(--text-muted)]">{{ t('admin.courses.no_lessons') }}</li>
                </ul>

                <!-- Add lesson -->
                <div class="space-y-2">
                    <div class="flex flex-wrap gap-2 items-center">
                        <input v-model="lessonDraft(section.id).title" type="text" :placeholder="t('common.title')"
                            class="flex-1 min-w-[140px] px-3 py-2 rounded-lg bg-[var(--surface2)] border border-[var(--border)] text-sm" />
                        <select v-model="lessonDraft(section.id).type" class="px-3 py-2 rounded-lg bg-[var(--surface2)] border border-[var(--border)] text-sm">
                            <option value="video">Video</option><option value="text">Text</option><option value="pdf">PDF</option>
                        </select>
                        <input v-model="lessonDraft(section.id).duration_minutes" type="number" min="0" placeholder="Min"
                            class="w-20 px-3 py-2 rounded-lg bg-[var(--surface2)] border border-[var(--border)] text-sm" />
                        <label class="flex items-center gap-2 text-sm">
                            <input v-model="lessonDraft(section.id).is_free_preview" type="checkbox" class="rounded" />
                            {{ t('admin.courses.free_preview') }}
                        </label>
                    </div>
                    <div v-if="lessonDraft(section.id).type === 'video'" class="flex flex-wrap gap-2 items-center">
                        <input type="file" accept="video/mp4,video/webm,video/quicktime"
                            @change="pickVideo($event, lessonDraft(section.id), `new-${section.id}`)" class="text-sm" />
                        <input v-model="lessonDraft(section.id).video_url" type="text" placeholder="…or paste a video URL"
                            class="flex-1 min-w-[160px] px-3 py-2 rounded-lg bg-[var(--surface2)] border border-[var(--border)] text-sm" />
                    </div>
                    <div v-else-if="lessonDraft(section.id).type === 'pdf'">
                        <input type="file" accept="application/pdf"
                            @change="lessonDraft(section.id).pdf_file = $event.target.files[0]" class="text-sm" />
                    </div>
                    <div v-else>
                        <textarea v-model="lessonDraft(section.id).text_content" rows="2" placeholder="Lesson content"
                            class="w-full px-3 py-2 rounded-lg bg-[var(--surface2)] border border-[var(--border)] text-sm"></textarea>
                    </div>

                    <div v-if="uploading[`new-${section.id}`]" class="max-w-sm">
                        <div class="h-1.5 rounded-full bg-[var(--surface2)] overflow-hidden">
                            <div class="h-full bg-[var(--primary)]" :style="{ width: `${uploading[`new-${section.id}`]}%` }"></div>
                        </div>
                        <p class="text-xs text-[var(--text-muted)] mt-1">{{ t('admin.courses.uploading') }} {{ uploading[`new-${section.id}`] }}%</p>
                    </div>
                    <p v-if="lessonDraft(section.id).video_temp_path" class="text-xs text-[var(--secondary)]">{{ t('admin.courses.video_ready') }}</p>
                    <p v-if="uploadError[`new-${section.id}`]" class="text-xs text-red-500">{{ uploadError[`new-${section.id}`] }}</p>

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

        <p v-else-if="isEdit" class="max-w-2xl text-sm text-[var(--text-muted)] rounded-xl border border-dashed border-[var(--border)] p-5">
            {{ t('admin.courses.link_course_hint') }}
        </p>
    </AdminLayout>
</template>
