<script setup>
import { ref } from 'vue';
import { Head, router } from '@inertiajs/vue3';
import AdminLayout from '@/Components/Layout/AdminLayout.vue';
import Pagination from '@/Components/UI/Pagination.vue';
import { useI18n } from '@/i18n';

const props = defineProps({ feedback: Object, filters: Object });
const { t } = useI18n();
const rows = ref(props.feedback.data.map((item) => ({ ...item })));

function filter(params) {
    router.get('/admin/feedback', { ...props.filters, ...params }, { preserveState: true, replace: true });
}
function save(item) {
    router.patch(`/admin/feedback/${item.id}`, { status: item.status, admin_note: item.admin_note }, { preserveScroll: true });
}
</script>

<template>
    <Head :title="t('admin.support.feedback')" />
    <AdminLayout>
        <div class="space-y-5">
            <header><h1 class="font-heading text-2xl font-extrabold">{{ t('admin.support.feedback') }}</h1><p class="mt-1 text-sm text-[var(--text-muted)]">{{ t('admin.support.feedback_subtitle') }}</p></header>
            <div class="flex flex-wrap gap-2">
                <select :value="filters.status || ''" @change="filter({ status: $event.target.value })" class="rounded-lg border border-[var(--border)] bg-[var(--surface)] px-3 py-2 text-sm text-[var(--text)]"><option value="">{{ t('common.all') }}</option><option v-for="status in ['new', 'reviewed', 'resolved']" :key="status" :value="status">{{ t(`support.feedback_status_${status}`) }}</option></select>
                <select :value="filters.type || ''" @change="filter({ type: $event.target.value })" class="rounded-lg border border-[var(--border)] bg-[var(--surface)] px-3 py-2 text-sm text-[var(--text)]"><option value="">{{ t('admin.support.all_feedback_types') }}</option><option v-for="type in ['feedback', 'answer_report', 'question_report']" :key="type" :value="type">{{ t(`support.type_${type}`) }}</option></select>
            </div>
            <section class="space-y-3">
                <article v-for="item in rows" :key="item.id" class="space-y-3 rounded-xl border border-[var(--border)] bg-[var(--surface)] p-4">
                    <div class="flex flex-wrap items-start justify-between gap-2"><div><div class="font-semibold">{{ t(`support.type_${item.type}`) }}<span v-if="item.target_type"> · {{ t(`support.target_${item.target_type}`) }}</span></div><div class="mt-1 text-xs text-[var(--text-muted)]">{{ item.student?.name }} · {{ item.student?.email }} · {{ new Date(item.created_at).toLocaleString() }}</div></div><span class="rounded-full bg-[var(--surface2)] px-2 py-1 text-xs">{{ t(`support.feedback_status_${item.status}`) }}</span></div>
                    <p v-if="item.message" class="whitespace-pre-wrap text-sm">{{ item.message }}</p>
                    <details v-if="item.context?.answer" class="rounded-lg bg-[var(--surface2)] p-3"><summary class="cursor-pointer text-sm font-semibold">{{ t('admin.support.reported_answer') }}</summary><p class="mt-2 whitespace-pre-wrap text-sm">{{ item.context.answer }}</p></details>
                    <form @submit.prevent="save(item)" class="grid gap-3 sm:grid-cols-3">
                        <label class="text-xs font-semibold">{{ t('support.status') }}<select v-model="item.status" class="mt-1 w-full rounded-lg border border-[var(--border)] bg-[var(--surface2)] px-3 py-2 text-sm text-[var(--text)]"><option v-for="status in ['new', 'reviewed', 'resolved']" :key="status" :value="status">{{ t(`support.feedback_status_${status}`) }}</option></select></label>
                        <label class="text-xs font-semibold sm:col-span-2">{{ t('admin.support.admin_note') }}<textarea v-model="item.admin_note" rows="2" maxlength="5000" class="mt-1 w-full rounded-lg border border-[var(--border)] bg-[var(--surface2)] px-3 py-2 text-sm text-[var(--text)]" /></label>
                        <button class="rounded-lg bg-[var(--primary)] px-4 py-2 text-sm font-semibold text-white sm:col-start-3">{{ t('admin.support.save_feedback') }}</button>
                    </form>
                </article>
                <div v-if="!rows.length" class="py-10 text-center text-sm text-[var(--text-muted)]">{{ t('admin.support.no_feedback') }}</div>
            </section>
            <Pagination :links="feedback.links" />
        </div>
    </AdminLayout>
</template>
