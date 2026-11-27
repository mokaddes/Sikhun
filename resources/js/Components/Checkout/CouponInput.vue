<script setup>
import { ref, watch } from 'vue';
import { useI18n } from '@/i18n';
import { TicketIcon, CheckCircleIcon, XMarkIcon } from '@heroicons/vue/24/outline';

const props = defineProps({
    targetType: { type: String, required: true },
    targetId: { type: [Number, String, null], default: null },
    subtotal: { type: [Number, null], default: null },
    months: { type: Number, default: 1 },
    chapterIds: { type: Array, default: () => [] },
    modelValue: { type: Object, default: null },
    compact: { type: Boolean, default: false },
});

const emit = defineEmits(['update:modelValue']);
const { locale } = useI18n();

const code = ref(props.modelValue?.code ?? '');
const loading = ref(false);
const error = ref('');

function getCsrfToken() {
    const meta = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    if (meta) return meta;
    const match = document.cookie.match(/XSRF-TOKEN=([^;]+)/);
    return match ? decodeURIComponent(match[1]) : '';
}

async function applyCoupon(overrideCode = null) {
    const raw = (overrideCode ?? code.value ?? '').trim().toUpperCase();
    if (!raw) {
        error.value = locale.value === 'en' ? 'Enter a coupon code first.' : 'প্রথমে একটি কুপন কোড লিখুন।';
        return;
    }

    loading.value = true;
    error.value = '';

    try {
        const res = await fetch('/coupons/apply', {
            method: 'POST',
            credentials: 'same-origin',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': getCsrfToken(),
                'X-XSRF-TOKEN': getCsrfToken(),
            },
            body: JSON.stringify({
                code: raw,
                target_type: props.targetType,
                target_id: props.targetId ?? null,
                months: props.months ?? 1,
                chapter_ids: props.chapterIds ?? [],
                subtotal: props.subtotal ?? null,
            }),
        });

        const data = await res.json().catch(() => ({}));
        if (!res.ok || !data.valid) {
            error.value = data.message || (locale.value === 'en' ? 'Invalid coupon code.' : 'কুপন কোডটি সঠিক নয়।');
            emit('update:modelValue', null);
            return;
        }

        code.value = data.coupon.code;
        emit('update:modelValue', data.coupon);
    } catch (_) {
        error.value = locale.value === 'en' ? 'Could not verify coupon right now.' : 'কুপন যাচাই করা যায়নি, আবার চেষ্টা করুন।';
    } finally {
        loading.value = false;
    }
}

function removeCoupon() {
    code.value = '';
    error.value = '';
    emit('update:modelValue', null);
}

watch(
    () => [props.targetType, props.targetId, props.months, props.subtotal, JSON.stringify(props.chapterIds)],
    () => {
        if (props.modelValue?.code) {
            applyCoupon(props.modelValue.code);
        }
    }
);
</script>

<template>
    <div :class="['rounded-2xl border border-[var(--border)] bg-[var(--surface2)]/40', compact ? 'p-3' : 'p-3.5']">
        <div v-if="!modelValue" class="space-y-2">
            <label class="flex items-center gap-1.5 text-xs font-bold text-[var(--text)]">
                <TicketIcon class="w-4 h-4 text-[var(--primary)]" />
                <span>{{ locale === 'en' ? 'Have a coupon code?' : 'কুপন কোড আছে?' }}</span>
            </label>
            <div class="flex gap-2">
                <input
                    v-model="code"
                    type="text"
                    maxlength="40"
                    @keyup.enter.prevent="applyCoupon()"
                    :placeholder="locale === 'en' ? 'Enter code (e.g. SAVE20)' : 'কুপন কোড লিখুন'"
                    class="flex-1 min-w-0 rounded-xl border border-[var(--border)] bg-[var(--surface)] px-3 py-2 text-xs font-mono uppercase text-[var(--text)] placeholder-[var(--text-muted)] focus:border-[var(--primary)] focus:outline-none"
                />
                <button
                    type="button"
                    @click="applyCoupon()"
                    :disabled="loading"
                    class="shrink-0 rounded-xl bg-[var(--primary)] hover:bg-[var(--primary-hover)] px-3.5 py-2 text-xs font-bold text-white transition disabled:opacity-60 cursor-pointer"
                >
                    {{ loading ? '...' : (locale === 'en' ? 'Apply' : 'প্রয়োগ') }}
                </button>
            </div>
            <p v-if="error" class="text-[11px] font-medium text-rose-500">{{ error }}</p>
        </div>

        <div v-else class="flex items-center justify-between gap-2 text-xs">
            <div class="flex items-start gap-2 min-w-0">
                <CheckCircleIcon class="w-4 h-4 text-emerald-500 shrink-0 mt-0.5" />
                <div class="min-w-0">
                    <div class="font-bold text-emerald-600 dark:text-emerald-400 truncate">
                        {{ modelValue.code }}
                        <span class="font-normal text-[var(--text-muted)]">
                            — {{ locale === 'en' ? `Save ৳${modelValue.discount_amount}` : `৳${modelValue.discount_amount} ছাড়` }}
                        </span>
                    </div>
                    <div class="text-[11px] text-[var(--text-muted)]">
                        {{ locale === 'en' ? 'Payable:' : 'সর্বমোট প্রদেয়:' }}
                        <strong class="text-[var(--text)]">৳{{ modelValue.final_amount }}</strong>
                        <span v-if="modelValue.is_free" class="ml-1 text-emerald-500 font-bold">
                            ({{ locale === 'en' ? '100% Free' : 'সম্পূর্ণ ফ্রি' }})
                        </span>
                    </div>
                </div>
            </div>
            <button
                type="button"
                @click="removeCoupon"
                class="shrink-0 inline-flex items-center gap-1 rounded-lg border border-[var(--border)] bg-[var(--surface)] px-2 py-1 text-[11px] font-semibold text-[var(--text-muted)] hover:text-rose-500 cursor-pointer"
            >
                <XMarkIcon class="w-3.5 h-3.5" />
                <span>{{ locale === 'en' ? 'Remove' : 'সরান' }}</span>
            </button>
        </div>
    </div>
</template>
