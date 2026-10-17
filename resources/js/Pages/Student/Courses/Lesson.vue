<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import PublicLayout from '@/Components/Layout/PublicLayout.vue';
import { useI18n } from '@/i18n';
import {
    PlayCircleIcon,
    LockClosedIcon,
    CheckCircleIcon,
    ArrowDownTrayIcon,
    ArrowLeftIcon,
} from '@heroicons/vue/24/outline';

const props = defineProps({
    course: Object,
    lesson: Object,
    isCompleted: Boolean,
    isEnrolled: Boolean,
});

const { t, locale } = useI18n();

function markComplete() {
    router.post(`/courses/${props.course.slug}/sections/${props.lesson.course_section_id}/lessons/${props.lesson.id}/complete`, {}, { preserveScroll: true });
}
</script>

<template>
    <Head :title="`${lesson.title} — ${course.title}`" />

    <PublicLayout>
        <div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:py-10">
            <div class="mb-6 flex flex-wrap items-center justify-between gap-4">
                <div>
                    <Link :href="`/courses/${course.slug}`" class="inline-flex items-center gap-1.5 text-xs font-semibold text-[var(--primary)] hover:underline">
                        <ArrowLeftIcon class="h-4 w-4" />
                        <span>{{ course.title }}</span>
                    </Link>
                    <h1 class="mt-1 font-heading text-2xl font-extrabold sm:text-3xl">{{ lesson.title }}</h1>
                </div>

                <button
                    v-if="isEnrolled"
                    type="button"
                    @click="markComplete"
                    :disabled="isCompleted"
                    class="inline-flex items-center gap-2 rounded-xl px-5 py-2.5 text-sm font-bold transition disabled:opacity-70"
                    :class="isCompleted ? 'bg-emerald-500 text-white' : 'bg-[var(--primary)] hover:bg-[var(--primary-hover)] text-white'"
                >
                    <CheckCircleIcon class="h-5 w-5" />
                    <span>{{ isCompleted ? (locale === 'en' ? 'Completed' : 'সম্পন্ন হয়েছে') : (locale === 'en' ? 'Mark as Complete' : 'সম্পন্ন হিসেবে চিহ্নিত করুন') }}</span>
                </button>
            </div>

            <div class="grid gap-8 lg:grid-cols-12">
                <!-- Lesson Player / Content -->
                <div class="lg:col-span-8">
                    <div v-if="lesson.type === 'video'" class="aspect-video overflow-hidden rounded-2xl border border-[var(--border)] bg-black shadow-lg">
                        <video v-if="lesson.stream_url" :src="lesson.stream_url" controls controlsList="nodownload" class="h-full w-full"></video>
                        <div v-else class="flex h-full w-full items-center justify-center text-sm text-white/60">
                            {{ t('courses_page.no_video') }}
                        </div>
                    </div>

                    <div
                        v-else-if="lesson.type === 'text'"
                        class="prose max-w-none rounded-2xl border border-[var(--border)] bg-[var(--surface)] p-6 shadow-sm sm:p-8"
                        v-html="lesson.text_content"
                    ></div>

                    <div v-else class="rounded-2xl border border-[var(--border)] bg-[var(--surface)] p-8 text-center shadow-sm">
                        <a
                            v-if="lesson.download_url"
                            :href="lesson.download_url"
                            class="inline-flex items-center gap-2 rounded-xl bg-[var(--primary)] px-6 py-3.5 text-sm font-bold text-white shadow-lg transition hover:bg-[var(--primary-hover)]"
                        >
                            <ArrowDownTrayIcon class="h-5 w-5" />
                            <span>{{ t('courses_page.download_lesson_file') }}</span>
                        </a>
                        <p v-else class="text-sm text-[var(--text-muted)]">{{ t('courses_page.no_file') }}</p>
                    </div>
                </div>

                <!-- Course Curriculum Sidebar -->
                <div class="lg:col-span-4">
                    <div class="sticky top-24 overflow-hidden rounded-2xl border border-[var(--border)] bg-[var(--surface)] shadow-sm">
                        <div class="border-b border-[var(--border)] bg-[var(--surface2)]/60 px-5 py-4">
                            <h2 class="font-heading text-base font-bold">{{ locale === 'en' ? 'Course Lessons' : 'কোর্সের লেসনসমূহ' }}</h2>
                        </div>

                        <div class="max-h-[70vh] divide-y divide-[var(--border)] overflow-y-auto">
                            <div v-for="section in course.sections" :key="section.id" class="p-4">
                                <div class="mb-2 text-xs font-bold uppercase tracking-wider text-[var(--text-muted)]">
                                    {{ section.title }}
                                </div>
                                <ul class="space-y-1">
                                    <li v-for="item in section.lessons" :key="item.id">
                                        <Link
                                            v-if="isEnrolled || item.is_free_preview"
                                            :href="`/courses/${course.slug}/sections/${section.id}/lessons/${item.id}`"
                                            class="flex items-center justify-between gap-2 rounded-lg px-3 py-2 text-xs font-medium transition"
                                            :class="item.id === lesson.id
                                                ? 'bg-[var(--primary)] text-white font-bold'
                                                : 'hover:bg-[var(--surface2)] text-[var(--text)]'"
                                        >
                                            <span class="flex items-center gap-2 truncate">
                                                <PlayCircleIcon class="h-4 w-4 shrink-0" />
                                                <span class="truncate">{{ item.title }}</span>
                                            </span>
                                            <span class="shrink-0 opacity-75">{{ item.duration_minutes }}m</span>
                                        </Link>
                                        <div
                                            v-else
                                            class="flex items-center justify-between gap-2 rounded-lg px-3 py-2 text-xs text-[var(--text-muted)] opacity-60"
                                        >
                                            <span class="flex items-center gap-2 truncate">
                                                <LockClosedIcon class="h-3.5 w-3.5 shrink-0" />
                                                <span class="truncate">{{ item.title }}</span>
                                            </span>
                                            <span class="shrink-0">{{ item.duration_minutes }}m</span>
                                        </div>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </PublicLayout>
</template>
