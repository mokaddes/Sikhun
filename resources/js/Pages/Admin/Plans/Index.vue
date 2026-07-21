<script setup>
import { Head, Link } from '@inertiajs/vue3';
import AdminLayout from '@/Components/Layout/AdminLayout.vue';
import ConfirmButton from '@/Components/UI/ConfirmButton.vue';
import { useI18n } from '@/i18n';

defineProps({ plans: Array });
const { t } = useI18n();
</script>

<template>
    <Head :title="t('admin.plans.title')" />
    <AdminLayout>
        <div class="flex items-center justify-between mb-6">
            <h1 class="font-heading text-2xl font-extrabold">{{ t('admin.plans.title') }}</h1>
            <Link href="/admin/plans/create" class="px-4 py-2 rounded-lg bg-[var(--primary)] hover:bg-[var(--primary-hover)] text-white text-sm font-semibold">
                {{ t('admin.plans.new') }}
            </Link>
        </div>

        <div class="grid md:grid-cols-3 gap-4">
            <div v-for="plan in plans" :key="plan.id" class="rounded-xl border border-[var(--border)] bg-[var(--surface)] p-6">
                <div class="flex items-center justify-between mb-2">
                    <div class="font-heading text-lg font-bold">{{ plan.name }}</div>
                    <span class="px-2 py-0.5 rounded text-xs font-medium" :class="plan.is_active ? 'bg-[var(--secondary)]/15 text-[var(--secondary)]' : 'bg-[var(--text-muted)]/15 text-[var(--text-muted)]'">
                        {{ plan.is_active ? t('common.active') : t('common.inactive') }}
                    </span>
                </div>
                <div class="text-2xl font-heading font-extrabold mb-3">৳{{ plan.price_monthly }}<span class="text-sm text-[var(--text-muted)] font-normal">/mo</span></div>
                <ul class="text-sm text-[var(--text-muted)] space-y-1 mb-4">
                    <li v-for="f in plan.features" :key="f">• {{ f }}</li>
                </ul>
                <div class="text-xs text-[var(--text-muted)] mb-4">{{ plan.subscriptions_count }} active subscribers</div>
                <div class="flex gap-3">
                    <Link :href="`/admin/plans/${plan.id}/edit`" class="text-sm font-medium text-[var(--primary)] hover:underline">{{ t('common.edit') }}</Link>
                    <ConfirmButton :href="`/admin/plans/${plan.id}`" method="delete" />
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
