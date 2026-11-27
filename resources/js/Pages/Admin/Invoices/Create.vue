<script setup>
import { computed, watch } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import AdminLayout from '@/Components/Layout/AdminLayout.vue';
import { useI18n } from '@/i18n';
import { ArrowLeftIcon, PlusIcon, TrashIcon } from '@heroicons/vue/24/outline';

const props = defineProps({
    students: { type: Array, default: () => [] },
    vatPercent: { type: Number, default: 0 },
    plans: { type: Array, default: () => [] },
    courses: { type: Array, default: () => [] },
    books: { type: Array, default: () => [] },
});

const { t, locale, money } = useI18n();
const todayIso = new Date().toISOString().slice(0, 10);

const form = useForm({
    student_id: '',
    type: 'one_time',
    status: 'sent',
    issued_at: todayIso,
    due_at: '',
    period_start: '',
    period_end: '',
    recurrence_interval: 'monthly',
    recurrence_days: 30,
    auto_generate_next: true,
    plan_id: '',
    course_id: '',
    book_id: '',
    notify_student: true,
    notes: '',
    discount: 0,
    vat_percent: Number(props.vatPercent ?? 0),
    items: [{ description: '', qty: 1, unit_price: 0 }],
});

const statuses = ['draft', 'sent', 'paid'];
const intervals = [
    { value: 'weekly', label: 'সাপ্তাহিক (৭ দিন)' },
    { value: 'monthly', label: 'মাসিক (১ মাস)' },
    { value: 'quarterly', label: 'ত্রৈমাসিক (৩ মাস)' },
    { value: 'yearly', label: 'বার্ষিক (১ বছর)' },
    { value: 'custom', label: 'কাস্টম দিন' },
];

watch(() => form.type, (val) => {
    if (val === 'recurring' && !form.period_start) {
        form.period_start = todayIso;
        const d = new Date();
        d.setMonth(d.getMonth() + 1);
        d.setDate(d.getDate() - 1);
        form.period_end = d.toISOString().slice(0, 10);
    }
});

watch(() => form.plan_id, (id) => {
    if (!id) return;
    const plan = props.plans.find((p) => Number(p.id) === Number(id));
    if (plan && form.items.length === 1 && !form.items[0].description) {
        form.items[0].description = `Subscription: ${plan.name}`;
        form.items[0].unit_price = Number(plan.price_total || plan.price_monthly || 0);
    }
});

watch(() => form.course_id, (id) => {
    if (!id) return;
    const course = props.courses.find((c) => Number(c.id) === Number(id));
    if (course && form.items.length === 1 && !form.items[0].description) {
        form.items[0].description = `Course Enrollment: ${course.title}`;
        form.items[0].unit_price = Number(course.price || 0);
    }
});

watch(() => form.book_id, (id) => {
    if (!id) return;
    const book = props.books.find((b) => Number(b.id) === Number(id));
    if (book && form.items.length === 1 && !form.items[0].description) {
        form.items[0].description = `Book Purchase: ${book.title}`;
        form.items[0].unit_price = Number(book.price || 0);
    }
});

const subtotal = computed(() => form.items.reduce(
    (sum, item) => sum + Math.round(Number(item.qty || 0) * Number(item.unit_price || 0) * 100) / 100,
    0,
));
const discount = computed(() => Math.min(Math.max(0, Number(form.discount || 0)), subtotal.value));
const vat = computed(() => Math.round((subtotal.value - discount.value) * Math.max(0, Number(form.vat_percent || 0)) / 100 * 100) / 100);
const total = computed(() => Math.round((subtotal.value - discount.value + vat.value) * 100) / 100);

function addItem() {
    form.items.push({ description: '', qty: 1, unit_price: 0 });
}

function removeItem(index) {
    if (form.items.length > 1) form.items.splice(index, 1);
}

function submit() {
    form.post('/admin/invoices');
}
</script>

<template>
    <Head :title="locale === 'en' ? 'Create invoice' : 'ইনভয়েস তৈরি'" />
    <AdminLayout>
        <div class="mb-5 flex items-center justify-between gap-3">
            <Link href="/admin/invoices" class="inline-flex items-center gap-1.5 text-sm font-semibold text-[var(--text-muted)] hover:text-[var(--primary)]">
                <ArrowLeftIcon class="h-4 w-4" />
                {{ t('invoices.back') }}
            </Link>
        </div>
        <h1 class="mb-6 font-heading text-2xl font-extrabold">{{ locale === 'en' ? 'Create manual invoice' : 'ম্যানুয়াল ইনভয়েস তৈরি' }}</h1>

        <form class="grid gap-5 lg:grid-cols-3" @submit.prevent="submit">
            <div class="space-y-5 lg:col-span-2">
                <div class="card rounded-2xl p-5 space-y-4">
                    <h2 class="font-heading text-sm font-bold uppercase tracking-wider text-[var(--text-muted)]">{{ t('invoices.student') }}</h2>
                    <select v-model="form.student_id" required class="w-full rounded-lg border border-[var(--border)] bg-[var(--surface2)] px-3 py-2 text-sm">
                        <option value="" disabled>{{ locale === 'en' ? 'Select a student' : 'শিক্ষার্থী নির্বাচন করুন' }}</option>
                        <option v-for="student in students" :key="student.id" :value="student.id">
                            {{ student.name }} — {{ student.email }}
                        </option>
                    </select>
                    <p v-if="form.errors.student_id" class="mt-1 text-xs text-red-500">{{ form.errors.student_id }}</p>

                    <div class="grid sm:grid-cols-3 gap-3 pt-2 border-t border-[var(--border)]">
                        <div>
                            <label class="mb-1 block text-xs font-semibold text-[var(--text-muted)]">লিংক প্ল্যান (ঐচ্ছিক)</label>
                            <select v-model="form.plan_id" class="w-full rounded-lg border border-[var(--border)] bg-[var(--surface2)] px-2.5 py-2 text-xs">
                                <option value="">— কোনো প্ল্যান নয় —</option>
                                <option v-for="p in plans" :key="p.id" :value="p.id">{{ p.name }} (৳{{ p.price_total || p.price_monthly }})</option>
                            </select>
                        </div>
                        <div>
                            <label class="mb-1 block text-xs font-semibold text-[var(--text-muted)]">লিংক কোর্স (ঐচ্ছিক)</label>
                            <select v-model="form.course_id" class="w-full rounded-lg border border-[var(--border)] bg-[var(--surface2)] px-2.5 py-2 text-xs">
                                <option value="">— কোনো কোর্স নয় —</option>
                                <option v-for="c in courses" :key="c.id" :value="c.id">{{ c.title }} (৳{{ c.price }})</option>
                            </select>
                        </div>
                        <div>
                            <label class="mb-1 block text-xs font-semibold text-[var(--text-muted)]">লিংক বই (ঐচ্ছিক)</label>
                            <select v-model="form.book_id" class="w-full rounded-lg border border-[var(--border)] bg-[var(--surface2)] px-2.5 py-2 text-xs">
                                <option value="">— কোনো বই নয় —</option>
                                <option v-for="b in books" :key="b.id" :value="b.id">{{ b.title }} (৳{{ b.price }})</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="card rounded-2xl p-5">
                    <h2 class="mb-4 font-heading text-sm font-bold uppercase tracking-wider text-[var(--text-muted)]">{{ t('invoices.items') }}</h2>
                    <div class="space-y-3">
                        <div v-for="(item, index) in form.items" :key="index" class="grid gap-2 rounded-xl border border-[var(--border)] bg-[var(--surface2)]/40 p-3 sm:grid-cols-12">
                            <div class="sm:col-span-6">
                                <label class="mb-1 block text-[11px] font-semibold text-[var(--text-muted)]">{{ t('invoices.description') }}</label>
                                <input v-model="item.description" required maxlength="255" class="w-full rounded-lg border border-[var(--border)] bg-[var(--surface)] px-3 py-2 text-sm" />
                                <p v-if="form.errors[`items.${index}.description`]" class="mt-1 text-[11px] text-red-500">{{ form.errors[`items.${index}.description`] }}</p>
                            </div>
                            <div class="sm:col-span-2">
                                <label class="mb-1 block text-[11px] font-semibold text-[var(--text-muted)]">{{ t('invoices.qty') }}</label>
                                <input v-model="item.qty" type="number" min="0.01" max="999999" step="0.01" required class="w-full rounded-lg border border-[var(--border)] bg-[var(--surface)] px-3 py-2 text-sm" />
                            </div>
                            <div class="sm:col-span-2">
                                <label class="mb-1 block text-[11px] font-semibold text-[var(--text-muted)]">{{ t('invoices.unit_price') }}</label>
                                <input v-model="item.unit_price" type="number" min="0" max="999999999" step="0.01" required class="w-full rounded-lg border border-[var(--border)] bg-[var(--surface)] px-3 py-2 text-sm" />
                            </div>
                            <div class="flex items-end justify-between gap-2 sm:col-span-2">
                                <div class="text-right text-sm font-semibold">৳{{ money(Number(item.qty || 0) * Number(item.unit_price || 0)) }}</div>
                                <button type="button" class="rounded-lg border border-[var(--border)] p-2 text-[var(--text-muted)] hover:border-red-400 hover:text-red-500 disabled:opacity-40" :disabled="form.items.length === 1" :title="t('common.delete')" @click="removeItem(index)">
                                    <TrashIcon class="h-4 w-4" />
                                </button>
                            </div>
                        </div>
                    </div>
                    <button type="button" class="mt-3 inline-flex items-center gap-1.5 rounded-lg border border-dashed border-[var(--border)] px-3 py-2 text-xs font-semibold text-[var(--text-muted)] hover:border-[var(--primary)] hover:text-[var(--primary)]" @click="addItem">
                        <PlusIcon class="h-4 w-4" /> {{ t('invoices.add_item') }}
                    </button>
                    <p v-if="form.errors.items" class="mt-2 text-xs text-red-500">{{ form.errors.items }}</p>
                </div>

                <div class="card rounded-2xl p-5">
                    <label class="mb-1.5 block text-sm font-medium">{{ t('invoices.notes') }}</label>
                    <textarea v-model="form.notes" rows="3" maxlength="2000" class="w-full rounded-lg border border-[var(--border)] bg-[var(--surface2)] px-4 py-2.5 text-sm" :placeholder="locale === 'en' ? 'Visible to the student on the invoice' : 'শিক্ষার্থী ইনভয়েসে এটি দেখতে পাবে'"></textarea>
                </div>
            </div>

            <div class="space-y-5">
                <div class="card rounded-2xl p-5">
                    <h2 class="mb-4 font-heading text-sm font-bold uppercase tracking-wider text-[var(--text-muted)]">{{ t('invoices.invoice_details') }}</h2>
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
                                <option v-for="status in statuses" :key="status" :value="status">{{ t(`invoices.${status}`) }}</option>
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
                                    <span>মেয়াদ শেষে স্বয়ংক্রিয়ভাবে পরবর্তী ইনভয়েস তৈরি হবে</span>
                                </label>
                            </div>
                        </template>

                        <label class="flex items-center gap-2 text-xs pt-2 border-t border-[var(--border)]">
                            <input v-model="form.notify_student" type="checkbox" class="rounded" />
                            <span>তৈরি হওয়ার পর শিক্ষার্থীকে ইমেইল ও পুশ নোটিফিকেশন পাঠান</span>
                        </label>
                    </div>
                </div>

                <div class="card rounded-2xl p-5">
                    <div class="space-y-3 text-sm">
                        <div class="flex items-center justify-between"><span class="text-[var(--text-muted)]">{{ t('invoices.subtotal') }}</span><span class="font-semibold">৳{{ money(subtotal) }}</span></div>
                        <div class="flex items-center justify-between gap-3">
                            <label for="manual-invoice-discount" class="text-[var(--text-muted)]">{{ t('invoices.discount') }}</label>
                            <input id="manual-invoice-discount" v-model="form.discount" type="number" min="0" max="999999999" step="0.01" class="w-28 rounded-lg border border-[var(--border)] bg-[var(--surface2)] px-2 py-1 text-right text-sm" />
                        </div>
                        <div class="flex items-center justify-between gap-3">
                            <label for="manual-invoice-vat" class="text-[var(--text-muted)]">{{ t('invoices.vat') }} %</label>
                            <input id="manual-invoice-vat" v-model="form.vat_percent" type="number" min="0" max="100" step="0.01" class="w-28 rounded-lg border border-[var(--border)] bg-[var(--surface2)] px-2 py-1 text-right text-sm" />
                        </div>
                        <div class="flex items-center justify-between border-t border-[var(--border)] pt-3 text-base">
                            <span class="font-heading font-extrabold">{{ t('invoices.total') }}</span>
                            <span class="font-heading font-extrabold">৳{{ money(total) }}</span>
                        </div>
                    </div>
                </div>
                <div class="flex flex-col gap-2">
                    <button type="submit" :disabled="form.processing" class="btn btn-primary w-full rounded-xl py-3">{{ form.processing ? t('common.saving') : (locale === 'en' ? 'Create invoice' : 'ইনভয়েস তৈরি করুন') }}</button>
                    <Link href="/admin/invoices" class="btn w-full rounded-xl border border-[var(--border)] py-3 text-center">{{ t('common.cancel') }}</Link>
                </div>
            </div>
        </form>
    </AdminLayout>
</template>
