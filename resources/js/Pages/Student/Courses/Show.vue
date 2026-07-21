<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import StudentLayout from '@/Components/Layout/StudentLayout.vue';
import SeoHead from '@/Components/Seo/SeoHead.vue';
import { useI18n } from '@/i18n';

const props = defineProps({ course: Object, enrollment: Object, isEnrolled: Boolean, seo: Object });
const { t } = useI18n();

function enroll(method) {
    router.post(`/courses/${props.course.id}/enroll`, { payment_method: method });
}
</script>

<template>
    <SeoHead :seo="seo" />
    <StudentLayout>
        <div class="grid md:grid-cols-3 gap-8 mb-8">
            <div class="rounded-xl border border-[var(--border)] bg-[var(--surface2)] aspect-video overflow-hidden flex items-center justify-center text-[var(--text-muted)] md:col-span-1">
                <img v-if="course.cover_image_url" :src="course.cover_image_url" :alt="course.title" width="480" height="270" class="w-full h-full object-cover" />
                <span v-else>{{ t('home.no_cover') }}</span>
            </div>
            <div class="md:col-span-2">
                <span class="text-xs uppercase font-medium text-[var(--text-muted)]">{{ course.level }}</span>
                <h1 class="font-heading text-3xl font-extrabold mb-3">{{ course.title }}</h1>
                <p v-if="course.mentor" class="text-[var(--text-muted)] mb-1">{{ t('admin.courses.mentor') }}: {{ course.mentor.name }} — {{ course.mentor.designation }}</p>
                <p class="mt-4 mb-6 leading-relaxed">{{ course.description }}</p>

                <div v-if="isEnrolled">
                    <div class="mb-3 text-sm">
                        {{ t('courses_page.progress') }}: <strong>{{ enrollment.progress_percentage }}%</strong>
                    </div>
                    <div class="h-2 rounded-full bg-[var(--surface2)] overflow-hidden mb-4 max-w-xs">
                        <div class="h-full bg-[var(--primary)]" :style="{ width: `${enrollment.progress_percentage}%` }"></div>
                    </div>
                    <a v-if="enrollment.certificate_path" :href="`/storage/${enrollment.certificate_path}`" target="_blank"
                        class="inline-block px-5 py-2.5 rounded-lg bg-[var(--secondary)] text-white text-sm font-semibold">
                        {{ t('courses_page.download_certificate') }}
                    </a>
                </div>
                <div v-else class="font-heading text-2xl font-extrabold mb-5">
                    <span v-if="(+course.price) === 0" class="text-[var(--secondary)]">{{ t('common.free') }}</span>
                    <span v-else>৳{{ course.price }}</span>
                </div>

                <div v-if="!isEnrolled" class="flex flex-wrap gap-3">
                    <button v-if="(+course.price) === 0" @click="enroll('wallet')" class="px-6 py-3 rounded-lg bg-[var(--primary)] hover:bg-[var(--primary-hover)] text-white font-semibold">
                        {{ t('courses_page.enroll_free') }}
                    </button>
                    <template v-else>
                        <button @click="enroll('wallet')" class="px-6 py-3 rounded-lg bg-[var(--primary)] hover:bg-[var(--primary-hover)] text-white font-semibold">
                            {{ t('book_show.pay_with_wallet') }}
                        </button>
                        <button @click="enroll('sslcommerz')" class="px-6 py-3 rounded-lg border border-[var(--border)] hover:bg-[var(--surface2)] font-semibold">
                            {{ t('book_show.pay_with_gateway') }}
                        </button>
                    </template>
                </div>
            </div>
        </div>

        <h2 class="font-heading text-xl font-bold mb-4">{{ t('admin.courses.sections') }}</h2>
        <div class="space-y-4 max-w-2xl">
            <div v-for="section in course.sections" :key="section.id" class="rounded-xl border border-[var(--border)] bg-[var(--surface)] p-5">
                <div class="font-semibold mb-3">{{ section.title }}</div>
                <ul class="space-y-1">
                    <li v-for="lesson in section.lessons" :key="lesson.id">
                        <Link v-if="isEnrolled || lesson.is_free_preview" :href="`/courses/${course.slug}/sections/${section.id}/lessons/${lesson.id}`"
                            class="flex items-center justify-between text-sm px-3 py-2 rounded-lg hover:bg-[var(--surface2)]">
                            <span>{{ lesson.title }}</span>
                            <span class="text-xs text-[var(--text-muted)]">
                                {{ lesson.is_free_preview ? t('admin.courses.free_preview') + ' · ' : '' }}{{ lesson.duration_minutes }}min
                            </span>
                        </Link>
                        <div v-else class="flex items-center justify-between text-sm px-3 py-2 rounded-lg text-[var(--text-muted)] opacity-60">
                            <span>🔒 {{ lesson.title }}</span>
                            <span class="text-xs">{{ lesson.duration_minutes }}min</span>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
    </StudentLayout>
</template>
