<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import StudentLayout from '@/Components/Layout/StudentLayout.vue';
import { useI18n } from '@/i18n';

const props = defineProps({ course: Object, lesson: Object, isCompleted: Boolean, isEnrolled: Boolean });
const { t } = useI18n();

function markComplete() {
    router.post(`/courses/${props.course.slug}/sections/${props.lesson.course_section_id}/lessons/${props.lesson.id}/complete`, {}, { preserveScroll: true });
}
</script>

<template>
    <Head :title="lesson.title" />
    <StudentLayout>
        <Link :href="`/courses/${course.slug}`" class="text-sm text-[var(--text-muted)] hover:text-[var(--text)] mb-4 inline-block">
            ← {{ course.title }}
        </Link>
        <h1 class="font-heading text-xl font-bold mb-6">{{ lesson.title }}</h1>

        <div class="max-w-3xl">
            <div v-if="lesson.type === 'video'" class="aspect-video rounded-xl overflow-hidden bg-black mb-6">
                <video v-if="lesson.video_url" :src="lesson.video_url" controls class="w-full h-full"></video>
                <div v-else class="w-full h-full flex items-center justify-center text-white/60 text-sm">No video URL set for this lesson.</div>
            </div>
            <div v-else-if="lesson.type === 'text'" class="rounded-xl border border-[var(--border)] bg-[var(--surface)] p-6 mb-6 prose max-w-none" v-html="lesson.text_content"></div>
            <div v-else class="rounded-xl border border-[var(--border)] bg-[var(--surface)] p-6 mb-6 text-[var(--text-muted)]">
                PDF lesson — download not yet wired in this build.
            </div>

            <button v-if="isEnrolled" @click="markComplete" :disabled="isCompleted"
                class="px-5 py-2.5 rounded-lg font-semibold text-sm disabled:opacity-60"
                :class="isCompleted ? 'bg-[var(--secondary)] text-white' : 'bg-[var(--primary)] hover:bg-[var(--primary-hover)] text-white'">
                {{ isCompleted ? '✓ Completed' : 'Mark as Complete' }}
            </button>
        </div>
    </StudentLayout>
</template>
