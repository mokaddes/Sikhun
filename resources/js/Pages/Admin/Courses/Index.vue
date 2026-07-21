<script setup>
import { Head, Link } from '@inertiajs/vue3';
import AdminLayout from '@/Components/Layout/AdminLayout.vue';
import ConfirmButton from '@/Components/UI/ConfirmButton.vue';
import Pagination from '@/Components/UI/Pagination.vue';
import { useI18n } from '@/i18n';

defineProps({ courses: Object });
const { t } = useI18n();
</script>

<template>
    <Head :title="t('admin.courses.title')" />
    <AdminLayout>
        <div class="flex items-center justify-between mb-6">
            <h1 class="font-heading text-2xl font-extrabold">{{ t('admin.courses.title') }}</h1>
            <Link href="/admin/courses/create" class="px-4 py-2 rounded-lg bg-[var(--primary)] hover:bg-[var(--primary-hover)] text-white text-sm font-semibold">
                {{ t('admin.courses.new') }}
            </Link>
        </div>

        <div class="rounded-xl border border-[var(--border)] bg-[var(--surface)] overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-[var(--border)] text-left text-[var(--text-muted)]">
                        <th class="px-5 py-3 font-medium">{{ t('common.title') }}</th>
                        <th class="px-5 py-3 font-medium">{{ t('admin.courses.mentor') }}</th>
                        <th class="px-5 py-3 font-medium">{{ t('common.price') }}</th>
                        <th class="px-5 py-3 font-medium">Enrolled</th>
                        <th class="px-5 py-3 font-medium">{{ t('common.status') }}</th>
                        <th class="px-5 py-3 font-medium text-right">{{ t('common.actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="course in courses.data" :key="course.id" class="border-b border-[var(--border)] last:border-0">
                        <td class="px-5 py-3 font-medium">{{ course.title }}</td>
                        <td class="px-5 py-3 text-[var(--text-muted)]">{{ course.mentor?.name ?? '—' }}</td>
                        <td class="px-5 py-3">৳{{ course.price }}</td>
                        <td class="px-5 py-3 text-[var(--text-muted)]">{{ course.enrollments_count }}</td>
                        <td class="px-5 py-3">
                            <span class="px-2 py-0.5 rounded text-xs font-medium" :class="course.is_active ? 'bg-[var(--secondary)]/15 text-[var(--secondary)]' : 'bg-[var(--text-muted)]/15 text-[var(--text-muted)]'">
                                {{ course.is_active ? t('common.active') : t('common.inactive') }}
                            </span>
                        </td>
                        <td class="px-5 py-3 text-right space-x-3">
                            <Link :href="`/admin/courses/${course.id}/edit`" class="text-sm font-medium text-[var(--primary)] hover:underline">{{ t('common.edit') }}</Link>
                            <ConfirmButton :href="`/admin/courses/${course.id}`" method="delete" />
                        </td>
                    </tr>
                    <tr v-if="!courses.data.length"><td colspan="6" class="px-5 py-10 text-center text-[var(--text-muted)]">{{ t('common.no_results') }}</td></tr>
                </tbody>
            </table>
        </div>
        <Pagination :links="courses.links" />
    </AdminLayout>
</template>
