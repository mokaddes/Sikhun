<script setup>
import { ref, watch } from 'vue';
import { Head, useForm } from '@inertiajs/vue3';
import AdminLayout from '@/Components/Layout/AdminLayout.vue';
import { useI18n } from '@/i18n';

const props = defineProps({ coupon: Object, students: Array });
const { t } = useI18n();
const isEdit = !!props.coupon;

const form = useForm({
    name: props.coupon?.name ?? '',
    description: props.coupon?.description ?? '',
    student_id: props.coupon?.student_id ?? '',
    code: props.coupon?.code ?? '',
    duration_days: props.coupon?.duration_days ?? '',
    starts_at: props.coupon?.starts_at ? new Date(props.coupon.starts_at).toISOString().slice(0, 16) : '',
    ends_at: props.coupon?.ends_at ? new Date(props.coupon.ends_at).toISOString().slice(0, 16) : '',
    max_uses: props.coupon?.max_uses ?? '',
    is_active: props.coupon?.is_active ?? true,
});

const mode = ref(props.coupon?.student_id ? 'direct' : 'code');
const windowType = ref(props.coupon?.duration_days ? 'duration' : 'calendar');

watch(mode, (val) => {
    if (val === 'direct') form.code = '';
    if (val === 'code') form.student_id = '';
});

watch(windowType, (val) => {
    if (val === 'duration') { form.starts_at = ''; form.ends_at = ''; }
    if (val === 'calendar') form.duration_days = '';
});

function submit() {
    const payload = { ...form };
    if (mode.value === 'code') payload.code = payload.code || undefined;
    if (windowType.value === 'duration') { payload.starts_at = undefined; payload.ends_at = undefined; }
    if (windowType.value === 'calendar') payload.duration_days = undefined;
    isEdit ? form.put(`/admin/coupons/${props.coupon.id}`) : form.post('/admin/coupons');
}
</script>

<template>
    <Head :title="isEdit ? t('common.edit') : 'New Coupon'" />
    <AdminLayout>
        <h1 class="font-heading text-2xl font-extrabold mb-6">{{ isEdit ? t('common.edit') : 'New Coupon' }}</h1>
        <form @submit.prevent="submit" class="max-w-xl space-y-5 rounded-xl border border-[var(--border)] bg-[var(--surface)] p-6">
            <div>
                <label class="block text-sm font-medium mb-1.5">Name</label>
                <input v-model="form.name" type="text" required class="w-full px-4 py-2.5 rounded-lg bg-[var(--surface2)] border border-[var(--border)]" />
            </div>
            <div>
                <label class="block text-sm font-medium mb-1.5">Description</label>
                <textarea v-model="form.description" rows="2" class="w-full px-4 py-2.5 rounded-lg bg-[var(--surface2)] border border-[var(--border)]"></textarea>
            </div>

            <div>
                <label class="block text-sm font-medium mb-1.5">Issuance</label>
                <div class="flex gap-4 mb-3">
                    <label class="flex items-center gap-2 text-sm"><input type="radio" value="code" v-model="mode" class="rounded-full" /> Public code (any student can redeem)</label>
                    <label class="flex items-center gap-2 text-sm"><input type="radio" value="direct" v-model="mode" class="rounded-full" /> Direct assign to a student</label>
                </div>

                <div v-if="mode === 'code'" class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium mb-1.5">Coupon code</label>
                        <input v-model="form.code" type="text" maxlength="40" placeholder="e.g. FREEMONTH" class="w-full px-4 py-2.5 rounded-lg bg-[var(--surface2)] border border-[var(--border)]" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1.5">Max uses (blank = unlimited)</label>
                        <input v-model="form.max_uses" type="number" min="1" class="w-full px-4 py-2.5 rounded-lg bg-[var(--surface2)] border border-[var(--border)]" />
                    </div>
                </div>

                <div v-else>
                    <label class="block text-sm font-medium mb-1.5">Student</label>
                    <select v-model="form.student_id" required class="w-full px-4 py-2.5 rounded-lg bg-[var(--surface2)] border border-[var(--border)]">
                        <option value="" disabled>Select a student</option>
                        <option v-for="s in students" :key="s.id" :value="s.id">{{ s.name }} ({{ s.email }})</option>
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium mb-1.5">Access window</label>
                <div class="flex gap-4 mb-3">
                    <label class="flex items-center gap-2 text-sm"><input type="radio" value="duration" v-model="windowType" class="rounded-full" /> N days from grant/redeem</label>
                    <label class="flex items-center gap-2 text-sm"><input type="radio" value="calendar" v-model="windowType" class="rounded-full" /> Calendar dates</label>
                </div>

                <div v-if="windowType === 'duration'">
                    <label class="block text-sm font-medium mb-1.5">Days</label>
                    <input v-model="form.duration_days" type="number" min="1" max="3650" required class="w-full px-4 py-2.5 rounded-lg bg-[var(--surface2)] border border-[var(--border)]" />
                </div>

                <div v-else class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium mb-1.5">Starts at</label>
                        <input v-model="form.starts_at" type="datetime-local" class="w-full px-4 py-2.5 rounded-lg bg-[var(--surface2)] border border-[var(--border)]" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1.5">Ends at</label>
                        <input v-model="form.ends_at" type="datetime-local" class="w-full px-4 py-2.5 rounded-lg bg-[var(--surface2)] border border-[var(--border)]" />
                    </div>
                </div>
            </div>

            <label class="flex items-center gap-2 text-sm"><input v-model="form.is_active" type="checkbox" class="rounded" /> {{ t('common.active') }}</label>
            <button type="submit" :disabled="form.processing" class="px-5 py-2.5 rounded-lg bg-[var(--primary)] hover:bg-[var(--primary-hover)] text-white text-sm font-semibold disabled:opacity-60">
                {{ form.processing ? t('common.saving') : t('common.save') }}
            </button>
        </form>
    </AdminLayout>
</template>