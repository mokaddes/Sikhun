<script setup>
import { Head, useForm } from '@inertiajs/vue3';
import StudentLayout from '@/Components/Layout/StudentLayout.vue';
import { useI18n } from '@/i18n';
import { ShieldCheckIcon, TicketIcon, SparklesIcon } from '@heroicons/vue/24/outline';

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
        <header class="anim-rise mb-6">
            <span class="eyebrow">Account</span>
            <h1 class="mt-1 flex items-center gap-2 font-heading text-2xl font-extrabold sm:text-3xl">
                <ShieldCheckIcon class="h-7 w-7 text-[var(--secondary)]" /> Access
            </h1>
            <p class="mt-1 text-sm text-[var(--text-muted)]">Redeem a coupon code or view your current free access.</p>
        </header>

        <div class="grid gap-6 md:grid-cols-2">
            <div class="card anim-rise rounded-2xl p-6">
                <h2 class="mb-4 font-heading text-lg font-bold">Active Free Access</h2>

                <div v-if="access.has_access" class="space-y-3">
                    <div v-if="access.campaign" class="rounded-xl border border-[var(--secondary)]/30 bg-[var(--secondary)]/10 p-4">
                        <div class="flex items-center gap-2 font-semibold text-[var(--secondary)]"><SparklesIcon class="h-4 w-4" /> Free Campaign</div>
                        <div class="mt-1 text-sm text-[var(--text-muted)]">{{ access.campaign.title }}</div>
                        <div class="text-sm text-[var(--text-muted)]">Until {{ fmt(access.campaign.ends_at) }}</div>
                    </div>
                    <div v-if="access.coupon" class="rounded-xl border border-[var(--primary)]/30 bg-[var(--primary)]/10 p-4">
                        <div class="flex items-center gap-2 font-semibold text-[var(--primary)]"><TicketIcon class="h-4 w-4" /> Coupon</div>
                        <div class="mt-1 text-sm text-[var(--text-muted)]">{{ access.coupon.name }}</div>
                        <div v-if="access.coupon.ends_at" class="text-sm text-[var(--text-muted)]">Until {{ fmt(access.coupon.ends_at) }}</div>
                    </div>
                    <p class="text-sm text-[var(--text-muted)]">All books, courses and AI features are unlocked.</p>
                </div>

                <div v-else class="text-sm text-[var(--text-muted)]">
                    You don't have any active coupon or free campaign access right now. Redeem a code or subscribe to a plan.
                </div>
            </div>

            <div class="card anim-rise rounded-2xl p-6">
                <h2 class="mb-4 font-heading text-lg font-bold">Redeem a Coupon Code</h2>
                <form @submit.prevent="redeem" class="space-y-3">
                    <input v-model="form.code" type="text" required placeholder="Enter coupon code" class="input uppercase" />
                    <button type="submit" :disabled="form.processing" class="btn btn-primary w-full">
                        {{ form.processing ? t('common.loading') : 'Redeem' }}
                    </button>
                </form>
            </div>
        </div>
    </StudentLayout>
</template>
