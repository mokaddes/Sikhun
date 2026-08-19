<script setup>
import { Head, useForm } from '@inertiajs/vue3';
import StudentLayout from '@/Components/Layout/StudentLayout.vue';
import { useI18n } from '@/i18n';

const props = defineProps({ access: Object });
const { t } = useI18n();

const form = useForm({ code: '' });
function redeem() {
    form.post('/access/redeem', { preserveScroll: true, onSuccess: () => form.reset() });
}

function fmt(iso) {
    return new Date(iso).toLocaleDateString();
}
</script>

<template>
    <Head title="Access" />
    <StudentLayout>
        <h1 class="font-heading text-2xl font-extrabold mb-1">Access</h1>
        <p class="text-[var(--text-muted)] mb-8">Redeem a coupon code or view your current free access.</p>

        <div class="grid md:grid-cols-2 gap-6">
            <div class="rounded-xl border border-[var(--border)] bg-[var(--surface)] p-6">
                <h2 class="font-heading text-lg font-bold mb-4">Active Free Access</h2>

                <div v-if="access.has_access" class="space-y-3">
                    <div v-if="access.campaign" class="rounded-lg bg-[var(--secondary)]/10 border border-[var(--secondary)]/30 p-4">
                        <div class="font-semibold text-[var(--secondary)]">Free Campaign</div>
                        <div class="text-sm text-[var(--text-muted)]">{{ access.campaign.title }}</div>
                        <div class="text-sm text-[var(--text-muted)]">Until {{ fmt(access.campaign.ends_at) }}</div>
                    </div>
                    <div v-if="access.coupon" class="rounded-lg bg-[var(--primary)]/10 border border-[var(--primary)]/30 p-4">
                        <div class="font-semibold text-[var(--primary)]">Coupon</div>
                        <div class="text-sm text-[var(--text-muted)]">{{ access.coupon.name }}</div>
                        <div v-if="access.coupon.ends_at" class="text-sm text-[var(--text-muted)]">Until {{ fmt(access.coupon.ends_at) }}</div>
                    </div>
                    <p class="text-sm text-[var(--text-muted)]">All books, courses and AI features are unlocked.</p>
                </div>

                <div v-else class="text-[var(--text-muted)] text-sm">
                    You don't have any active coupon or free campaign access right now. Redeem a code below or subscribe to a plan.
                </div>
            </div>

            <div class="rounded-xl border border-[var(--border)] bg-[var(--surface)] p-6">
                <h2 class="font-heading text-lg font-bold mb-4">Redeem a Coupon Code</h2>
                <form @submit.prevent="redeem" class="space-y-3">
                    <input v-model="form.code" type="text" required placeholder="Enter coupon code" class="w-full px-4 py-2.5 rounded-lg bg-[var(--surface2)] border border-[var(--border)] uppercase" />
                    <button type="submit" :disabled="form.processing" class="px-5 py-2.5 rounded-lg bg-[var(--primary)] hover:bg-[var(--primary-hover)] text-white text-sm font-semibold disabled:opacity-60">
                        {{ form.processing ? t('common.loading') : 'Redeem' }}
                    </button>
                </form>
            </div>
        </div>
    </StudentLayout>
</template>