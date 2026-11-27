<script setup>
import { computed } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import AdminLayout from '@/Components/Layout/AdminLayout.vue';
import { useI18n } from '@/i18n';
import { ArrowLeftIcon, PlusIcon, TrashIcon } from '@heroicons/vue/24/outline';

const props = defineProps({
    invoice: Object,
    vatPercent: { type: Number, default: 0 },
    students: { type: Array, default: () => [] },
    plans: { type: Array, default: () => [] },
    courses: { type: Array, default: () => [] },
    books: { type: Array, default: () => [] },
});

const { t, locale, money } = useI18n();

const toDateInput = (value) => {
    if (!value) return '';
    const d = new Date(value);
    if (Number.isNaN(d.getTime())) return '';
    const pad = (n) => String(n).padStart(2, '0');
    return `${d.getFullYear()}-${pad(d.getMonth() + 1)}-${pad(d.getDate())}`;
};

const meta = props.invoice.meta ?? {};

const form = useForm({
    student_id: props.invoice.student_id,
    type: props.invoice.type ?? 'one_time',
    status: props.invoice.status,
    issued_at: toDateInput(props.invoice.issued_at),
    due_at: toDateInput(props.invoice.due_at),
    period_start: toDateInput(props.invoice.period_start),
    period_end: toDateInput(props.invoice.period_end),
    recurrence_interval: meta.recurrence_interval ?? 'monthly',
    recurrence_days: meta.recurrence_days ?? 30,
    auto_generate_next: meta.auto_generate_next ?? true,
    plan_id: meta.plan_id ?? '',
    course_id: meta.course_id ?? '',
    book_id: meta.book_id ?? '',
    notify_student: false,
    notes: props.invoice.notes ?? '',
    discount: Number(props.invoice.discount ?? 0),
    vat_percent: Number(props.vatPercent ?? 0),
    items: (props.invoice.items ?? []).map((item) => ({
        description: item.description,
        qty: Number(item.qty),
        unit_price: Number(item.unit_price),
    })),
});

if (!form.items.length) {
    form.items.push({ description: '', qty: 1, unit_price: 0 });
}

const statuses = ['draft', 'sent', 'paid', 'overdue', 'void'];
const intervals = [
    { value: 'weekly', label: 'সাপ্তাহিক (৭ দিন)' },
    { value: 'monthly', label: 'মাসিক (১ মাস)' },
    { value: 'quarterly', label: 'ত্রৈমাসিক (৩ মাস)' },
    { value: 'yearly', label: 'বার্ষিক (১ বছর)' },
    { value: 'custom', label: 'কাস্টম দিন' },
];

const subtotal = computed(() =>
    form.items.reduce((sum, item) => sum + Math.round(Number(item.qty || 0) * Number(item.unit_price || 0) * 100) / 100, 0),
);
const discount = computed(() => Math.min(Math.max(0, Number(form.discount || 0)), subtotal.value));
const vat = computed(() => Math.round((subtotal.value - discount.value) * Math.max(0, Number(form.vat_percent || 0)) / 100 * 100) / 100);
const total = computed(() => Math.round((subtotal.value - discount.value + vat.value) * 100) / 100);

function addItem() {
    form.items.push({ description: '', qty: 1, unit_price: 0 });
}

function removeItem(index) {
    if (form.items.length === 1) return;
    form.items.splice(index, 1);
}

function submit() {
    form.put(`/admin/invoices/${props.invoice.id}`);
}
</script>

<template>
    <Head :title="`${t('invoices.edit')} — ${invoice.invoice_number}`" />
    <AdminLayout>
        <div class="mb-5 flex flex-wrap items-center justify-between gap-3">
            <Link :href="`/admin/invoices/${invoice.id}`" class="inline-flex items-center gap-1.5 text-sm font-semibold text-[var(--text-muted)] hover:text-[var(--primary)]">
                <ArrowLeftIcon class="h-4 w-4" />
                {{ t('invoices.back') }}
            </Link>
            <div class="font-mono text-xs text-[var(--text-muted)]">{{ invoice.invoice_number }}</div>
        </div>

        <h1 class="mb-6 font-heading text-2xl font-extrabold">{{ t('invoices.edit') }}</h1>

        <form class="grid gap-5 lg:grid-cols-3" @submit.prevent="submit">
            <div class="space-y-5 lg:col-span-2">
                <div class="card rounded-2xl p-5 space-y-4">
                    <h2 class="font-heading text-sm font-bold uppercase tracking-wider text-[var(--text-muted)]">{{ t('invoices.student') }} & লিংকিং</h2>
                    <div>
                        <label class="mb-1 block text-xs font-semibold text-[var(--text-muted)]">শিক্ষার্থী</label>
                        <select v-model="form.student_id" :disabled="invoice.status === 'paid'" class="w-full rounded-lg border border-[var(--border)] bg-[var(--surface2)] px-3 py-2 text-sm disabled:opacity-60">
                            <option v-for="s in students" :key="s.id" :value="s.id">{{ s.name }} — {{ s.email }}</option>
                        </select>
                        <p v-if="invoice.status === 'paid'" class="mt-1 text-[11px] text-[var(--text-muted)]">পরিশোধিত ইনভয়েসের শিক্ষার্থী পরিবর্তন করা যায় না।</p>
                    </div>
                    <div class="grid sm:grid-cols-3 gap-3 pt-2 border-t border-[var(--border)]">
                        <div>
                            <label class="mb-1 block text-xs font-semibold text-[var(--text-muted)]">লিংক প্ল্যান</label>
                            <select v-model="form.plan_id" class="w-full rounded-lg border border-[var(--border)] bg-[var(--surface2)] px-2.5 py-2 text-xs">
                                <option value="">— কোনো প্ল্যান নয় —</option>
                                <option v-for="p in plans" :key="p.id" :value="p.id">{{ p.name }}</option>
                            </select>
                        </div>
                        <div>
                            <label class="mb-1 block text-xs font-semibold text-[var(--text-muted)]">লিংক কোর্স</label>
                            <select v-model="form.course_id" class="w-full rounded-lg border border-[var(--border)] bg-[var(--surface2)] px-2.5 py-2 text-xs">
                                <option value="">— কোনো কোর্স নয় —</option>
                                <option v-for="c in courses" :key="c.id" :value="c.id">{{ c.title }}</option>
                            </select>
                        </div>
                        <div>
                            <label class="mb-1 block text-xs font-semibold text-[var(--text-muted)]">লিংক বই</label>
                            <select v-model="form.book_id" class="w-full rounded-lg border border-[var(--border)] bg-[var(--surface2)] px-2.5 py-2 text-xs">
                                <option value="">— কোনো বই নয় —</option>
                                <option v-for="b in books" :key="b.id" :value="b.id">{{ b.title }}</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="card rounded-2xl p-5">
                    <h2 class="mb-4 font-heading text-sm font-bold uppercase tracking-wider text-[var(--text-muted)]">
                        {{ t('invoices.items') }}
                    </h2>

                    <div class="space-y-3">
                        <div
                            v-for="(item, index) in form.items"
                            :key="index"
                            class="grid gap-2 rounded-xl border border-[var(--border)] bg-[var(--surface2)]/40 p-3 sm:grid-cols-12"
                        >
                            <div class="sm:col-span-6">
                                <label class="mb-1 block text-[11px] font-semibold text-[var(--text-muted)]">{{ t('invoices.description') }}</label>
                                <input v-model="item.description" type="text" required class="w-full rounded-lg border border-[var(--border)] bg-[var(--surface)] px-3 py-2 text-sm" />
                            </div>
                            <div class="sm:col-span-2">
                                <label class="mb-1 block text-[11px] font-semibold text-[var(--text-muted)]">{{ t('invoices.qty') }}</label>
                                <input v-model="item.qty" type="number" min="0.01" step="0.01" required class="w-full rounded-lg border border-[var(--border)] bg-[var(--surface)] px-3 py-2 text-sm" />
                            </div>
                            <div class="sm:col-span-2">
                                <label class="mb-1 block text-[11px] font-semibold text-[var(--text-muted)]">{{ t('invoices.unit_price') }}</label>
                                <input v-model="item.unit_price" type="number" min="0" step="0.01" required class="w-full rounded-lg border border-[var(--border)] bg-[var(--surface)] px-3 py-2 text-sm" />
                            </div>
                            <div class="flex items-end justify-between gap-2 sm:col-span-2">
                                <div class="text-right text-sm font-semibold">৳{{ money(Number(item.qty || 0) * Number(item.unit_price || 0)) }}</div>
                                <button
                                    type="button"
                                    class="rounded-lg border border-[var(--border)] p-2 text-[var(--text-muted)] transition hover:border-red-400 hover:text-red-500 disabled:opacity-40"
                                    :disabled="form.items.length === 1"
                                    @click="removeItem(index)"
                                >
                                    <TrashIcon class="h-4 w-4" />
                                </button>
                            </div>
                        </div>
                    </div>

                    <button
                        type="button"
                        class="mt-3 inline-flex items-center gap-1.5 rounded-lg border border-dashed border-[var(--border)] px-3 py-2 text-xs font-semibold text-[var(--text-muted)] transition hover:border-[var(--primary)] hover:text-[var(--primary)]"
                        @click="addItem"
                    >
                        <PlusIcon class="h-4 w-4" />
                        {{ t('invoices.add_item') }}
                    </button>
                </div>

                <div class="card rounded-2xl p-5">
                    <label class="mb-1.5 block text-sm font-medium">{{ t('invoices.notes') }}</label>
                    <textarea
                        v-model="form.notes"
                        rows="3"
                        class="w-full rounded-lg border border-[var(--border)] bg-[var(--surface2)] px-4 py-2.5 text-sm"
                        :placeholder="locale === 'en' ? 'Visible to the student on the invoice' : 'শিক্ষার্থী ইনভয়েসে এটি দেখতে পাবে'"
                    ></textarea>
                </div>
            </div>

            <div class="space-y-5">
                <div class="card rounded-2xl p-5">
                    <h2 class="mb-4 font-heading text-sm font-bold uppercase tracking-wider text-[var(--text-muted)]">
                        {{ t('invoices.invoice_details') }}
                    </h2>

                    <div class="space-y-4">
                        <div>
                            <label class="mb-1.5 block text-sm font-medium">ইনভয়েসের ধরন</label>
                            <div class="grid grid-cols-2 gap-2">
                                <button
                                    type="button"
                                    @click="form.type = 'one_time'"
                                    class="rounded-xl border px-3 py-2 text-xs font-bold transition cursor-pointer"
                                    :class="form.type === 'one_time' ? 'border-[var(--primary)] bg-[var(--primary)]/10 text-[var(--primary)]' : 'border-[var(--border)] text-[var(--text-muted)]'"
                                >
                                    {{ t('invoices.one_time') }}
                                </button>
                                <button
                                    type="button"
                                    @click="form.type = 'recurring'"
                                    class="rounded-xl border px-3 py-2 text-xs font-bold transition cursor-pointer"
                                    :class="form.type === 'recurring' ? 'border-[var(--primary)] bg-[var(--primary)]/10 text-[var(--primary)]' : 'border-[var(--border)] text-[var(--text-muted)]'"
                                >
                                    {{ t('invoices.recurring') }}
                                </button>
                            </div>
                        </div>

                        <div>
                            <label class="mb-1.5 block text-sm font-medium">{{ t('invoices.status') }}</label>
                            <select v-model="form.status" class="w-full rounded-lg border border-[var(--border)] bg-[var(--surface2)] px-3 py-2 text-sm">
                                <option v-for="s in statuses" :key="s" :value="s">{{ t(`invoices.${s}`) }}</option>
                            </select>
                        </div>

                        <div class="grid grid-cols-2 gap-2">
                            <div>
                                <label class="mb-1.5 block text-xs font-medium">{{ t('invoices.issued') }}</label>
                                <input v-model="form.issued_at" type="date" class="w-full rounded-lg border border-[var(--border)] bg-[var(--surface2)] px-2.5 py-2 text-xs" />
                            </div>
                            <div>
                                <label class="mb-1.5 block text-xs font-medium">{{ t('invoices.due') }}</label>
                                <input v-model="form.due_at" type="date" class="w-full rounded-lg border border-[var(--border)] bg-[var(--surface2)] px-2.5 py-2 text-xs" />
                            </div>
                        </div>

                        <template v-if="form.type === 'recurring'">
                            <div class="border-t border-[var(--border)] pt-3 space-y-3">
                                <div class="grid grid-cols-2 gap-2">
                                    <div>
                                        <label class="mb-1 block text-xs font-medium">পিরিয়ড শুরু</label>
                                        <input v-model="form.period_start" type="date" class="w-full rounded-lg border border-[var(--border)] bg-[var(--surface2)] px-2.5 py-2 text-xs" />
                                    </div>
                                    <div>
                                        <label class="mb-1 block text-xs font-medium">পিরিয়ড শেষ</label>
                                        <input v-model="form.period_end" type="date" class="w-full rounded-lg border border-[var(--border)] bg-[var(--surface2)] px-2.5 py-2 text-xs" />
                                    </div>
                                </div>
                                <div>
                                    <label class="mb-1 block text-xs font-medium">পুনরাবৃত্তির সময়কাল</label>
                                    <select v-model="form.recurrence_interval" class="w-full rounded-lg border border-[var(--border)] bg-[var(--surface2)] px-2.5 py-2 text-xs">
                                        <option v-for="iv in intervals" :key="iv.value" :value="iv.value">{{ iv.label }}</option>
                                    </select>
                                </div>
                                <div v-if="form.recurrence_interval === 'custom'">
                                    <label class="mb-1 block text-xs font-medium">কত দিন পর পর?</label>
                                    <input v-model="form.recurrence_days" type="number" min="1" max="3650" class="w-full rounded-lg border border-[var(--border)] bg-[var(--surface2)] px-2.5 py-2 text-xs" />
                                </div>
                                <label class="flex items-center gap-2 text-xs">
                                    <input v-model="form.auto_generate_next" type="checkbox" class="rounded" />
                                    <span>স্বয়ংক্রিয় পরবর্তী ইনভয়েস</span>
                                </label>
                            </div>
                        </template>

                        <label class="flex items-center gap-2 text-xs pt-2 border-t border-[var(--border)]">
                            <input v-model="form.notify_student" type="checkbox" class="rounded" />
                            <span>আপডেটের পর শিক্ষার্থীকে নোটিফাই করুন</span>
                        </label>
                    </div>
                </div>

                <div class="card rounded-2xl p-5">
                    <div class="space-y-3 text-sm">
                        <div class="flex items-center justify-between gap-3">
                            <span class="text-[var(--text-muted)]">{{ t('invoices.subtotal') }}</span>
                            <span class="font-semibold">৳{{ money(subtotal) }}</span>
                        </div>
                        <div class="flex items-center justify-between gap-3">
                            <label for="invoice-discount" class="text-[var(--text-muted)]">{{ t('invoices.discount') }}</label>
                            <input id="invoice-discount" v-model="form.discount" type="number" min="0" step="0.01" class="w-28 rounded-lg border border-[var(--border)] bg-[var(--surface2)] px-2 py-1 text-right text-sm" />
                        </div>
                        <div class="flex items-center justify-between gap-3">
                            <label for="invoice-vat" class="text-[var(--text-muted)]">{{ t('invoices.vat') }} %</label>
                            <input id="invoice-vat" v-model="form.vat_percent" type="number" min="0" max="100" step="0.01" class="w-28 rounded-lg border border-[var(--border)] bg-[var(--surface2)] px-2 py-1 text-right text-sm" />
                        </div>
                        <div class="flex items-center justify-between gap-3 border-t border-[var(--border)] pt-3 text-base">
                            <span class="font-heading font-extrabold">{{ t('invoices.total') }}</span>
                            <span class="font-heading font-extrabold">৳{{ money(total) }}</span>
                        </div>
                    </div>
                </div>

                <div class="flex flex-col gap-2">
                    <button type="submit" :disabled="form.processing" class="btn btn-primary w-full rounded-xl py-3">
                        {{ form.processing ? t('common.saving') : t('common.save') }}
                    </button>
                    <Link :href="`/admin/invoices/${invoice.id}`" class="btn w-full rounded-xl border border-[var(--border)] py-3 text-center">
                        {{ t('common.cancel') }}
                    </Link>
                </div>
            </div>
        </form>
    </AdminLayout>
</template>
