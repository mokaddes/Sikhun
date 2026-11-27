<script setup>
import { computed, ref, watch } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import AdminLayout from '@/Components/Layout/AdminLayout.vue';
import { useI18n } from '@/i18n';

const props = defineProps({
    coupon: Object,
    students: { type: Array, default: () => [] },
    books: { type: Array, default: () => [] },
    courses: { type: Array, default: () => [] },
    plans: { type: Array, default: () => [] },
    invoices: { type: Array, default: () => [] },
});

const { t, locale } = useI18n();
const isEdit = !!props.coupon;

const initialTypes = Array.isArray(props.coupon?.applicable_types) && props.coupon.applicable_types.length
    ? props.coupon.applicable_types
    : ['all'];

const initialUsers = Array.isArray(props.coupon?.allowed_student_ids) && props.coupon.allowed_student_ids.length
    ? props.coupon.allowed_student_ids.map(Number)
    : (props.coupon?.student_id ? [Number(props.coupon.student_id)] : []);

const form = useForm({
    name: props.coupon?.name ?? '',
    description: props.coupon?.description ?? '',
    coupon_kind: props.coupon?.coupon_kind ?? 'discount',
    discount_type: props.coupon?.discount_type ?? 'percent',
    discount_value: props.coupon?.discount_value ?? 20,
    max_discount_amount: props.coupon?.max_discount_amount ?? '',
    min_order_amount: props.coupon?.min_order_amount ?? '',
    applicable_types: initialTypes,
    applicable_product_ids: {
        book: (props.coupon?.applicable_product_ids?.book ?? []).map(Number),
        course: (props.coupon?.applicable_product_ids?.course ?? []).map(Number),
        plan: (props.coupon?.applicable_product_ids?.plan ?? []).map(Number),
        invoice: (props.coupon?.applicable_product_ids?.invoice ?? []).map(Number),
    },
    student_id: props.coupon?.student_id ?? '',
    allowed_student_ids: initialUsers,
    code: props.coupon?.code ?? '',
    duration_days: props.coupon?.duration_days ?? '',
    starts_at: props.coupon?.starts_at ? new Date(props.coupon.starts_at).toISOString().slice(0, 16) : '',
    ends_at: props.coupon?.ends_at ? new Date(props.coupon.ends_at).toISOString().slice(0, 16) : '',
    max_uses: props.coupon?.max_uses ?? '',
    max_uses_per_user: props.coupon?.max_uses_per_user ?? '',
    is_active: props.coupon?.is_active ?? true,
});

// Legacy access_grant UI controls
const mode = ref(props.coupon?.student_id ? 'direct' : 'code');
const windowType = ref(props.coupon?.duration_days ? 'duration' : 'calendar');

const userScope = ref(initialUsers.length > 0 ? 'specific' : 'all');
const userSearch = ref('');

const filteredStudents = computed(() => {
    const q = userSearch.value.trim().toLowerCase();
    if (!q) return props.students.slice(0, 50);
    return props.students.filter(
        (s) => s.name.toLowerCase().includes(q) || s.email.toLowerCase().includes(q)
    ).slice(0, 50);
});

watch(mode, (val) => {
    if (form.coupon_kind === 'access_grant') {
        if (val === 'direct') form.code = '';
        if (val === 'code') form.student_id = '';
    }
});

watch(windowType, (val) => {
    if (form.coupon_kind === 'access_grant') {
        if (val === 'duration') {
            form.starts_at = '';
            form.ends_at = '';
        }
        if (val === 'calendar') form.duration_days = '';
    }
});

watch(userScope, (val) => {
    if (val === 'all') {
        form.allowed_student_ids = [];
        form.student_id = '';
    }
});

function toggleType(typeVal) {
    if (typeVal === 'all') {
        form.applicable_types = ['all'];
        return;
    }
    const next = form.applicable_types.filter((t) => t !== 'all');
    const idx = next.indexOf(typeVal);
    if (idx >= 0) {
        next.splice(idx, 1);
    } else {
        next.push(typeVal);
    }
    form.applicable_types = next.length ? next : ['all'];
}

function showCategoryProducts(cat) {
    return form.applicable_types.includes('all') || form.applicable_types.includes(cat);
}

function toggleItem(list, id) {
    const numId = Number(id);
    const idx = list.indexOf(numId);
    if (idx >= 0) list.splice(idx, 1);
    else list.push(numId);
}

function submit() {
    if (form.coupon_kind === 'access_grant') {
        if (windowType.value === 'duration') {
            form.starts_at = '';
            form.ends_at = '';
        } else {
            form.duration_days = '';
        }
    } else {
        form.code = (form.code || '').trim().toUpperCase();
        if (userScope.value === 'all') {
            form.allowed_student_ids = [];
            form.student_id = '';
        }
    }

    isEdit ? form.put(`/admin/coupons/${props.coupon.id}`) : form.post('/admin/coupons');
}
</script>

<template>
    <Head :title="isEdit ? 'কুপন এডিট' : 'নতুন কুপন তৈরি'" />
    <AdminLayout>
        <div class="mb-6 flex items-center justify-between gap-4">
            <div>
                <h1 class="font-heading text-2xl font-extrabold">{{ isEdit ? 'কুপন সম্পাদনা' : 'নতুন কুপন তৈরি করুন' }}</h1>
                <p class="mt-1 text-sm text-[var(--text-muted)]">
                    ডিসকাউন্ট কুপন (বই, কোর্স, প্ল্যান বা ইনভয়েস) অথবা ফুল-অ্যাক্সেস গ্রান্ট কনফিগার করুন।
                </p>
            </div>
            <Link href="/admin/coupons" class="btn btn-outline text-xs">তালিকায় ফিরুন</Link>
        </div>

        <form @submit.prevent="submit" class="max-w-4xl space-y-6 rounded-2xl border border-[var(--border)] bg-[var(--surface)] p-6 shadow-sm">
            <!-- Coupon Kind -->
            <div>
                <label class="block text-sm font-bold mb-2">কুপনের ধরন</label>
                <div class="grid sm:grid-cols-2 gap-3">
                    <button
                        type="button"
                        @click="form.coupon_kind = 'discount'"
                        class="p-3.5 rounded-xl border text-left transition cursor-pointer"
                        :class="form.coupon_kind === 'discount' ? 'border-[var(--primary)] bg-[var(--primary)]/10 ring-1 ring-[var(--primary)]' : 'border-[var(--border)] bg-[var(--surface2)]/40'"
                    >
                        <div class="font-bold text-sm">ডিসকাউন্ট কুপন (% বা নির্দিষ্ট টাকা ছাড়)</div>
                        <div class="text-xs text-[var(--text-muted)] mt-0.5">কোর্স, বই, প্ল্যান বা ইনভয়েস পরিশোধে কোড প্রয়োগ করে ছাড় পাওয়া যাবে।</div>
                    </button>
                    <button
                        type="button"
                        @click="form.coupon_kind = 'access_grant'"
                        class="p-3.5 rounded-xl border text-left transition cursor-pointer"
                        :class="form.coupon_kind === 'access_grant' ? 'border-[var(--primary)] bg-[var(--primary)]/10 ring-1 ring-[var(--primary)]' : 'border-[var(--border)] bg-[var(--surface2)]/40'"
                    >
                        <div class="font-bold text-sm">ফুল-অ্যাক্সেস গ্রান্ট কুপন</div>
                        <div class="text-xs text-[var(--text-muted)] mt-0.5">সাবস্ক্রিপশন ছাড়াই নির্দিষ্ট দিনের জন্য সম্পূর্ণ প্ল্যাটফর্ম আনলক।</div>
                    </button>
                </div>
            </div>

            <!-- Basic Info -->
            <div class="grid sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium mb-1.5">কুপনের নাম *</label>
                    <input v-model="form.name" type="text" required placeholder="যেমন: Eid Special 30% Off" class="w-full px-4 py-2.5 rounded-lg bg-[var(--surface2)] border border-[var(--border)]" />
                    <p v-if="form.errors.name" class="text-xs text-rose-500 mt-1">{{ form.errors.name }}</p>
                </div>
                <div v-if="form.coupon_kind === 'discount' || mode === 'code'">
                    <label class="block text-sm font-medium mb-1.5">কুপন কোড {{ form.coupon_kind === 'discount' ? '*' : '' }}</label>
                    <input v-model="form.code" type="text" :required="form.coupon_kind === 'discount'" maxlength="40" placeholder="যেমন: EID30" class="w-full px-4 py-2.5 rounded-lg bg-[var(--surface2)] border border-[var(--border)] font-mono uppercase" />
                    <p v-if="form.errors.code" class="text-xs text-rose-500 mt-1">{{ form.errors.code }}</p>
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium mb-1.5">বিবরণ (ঐচ্ছিক)</label>
                <textarea v-model="form.description" rows="2" class="w-full px-4 py-2.5 rounded-lg bg-[var(--surface2)] border border-[var(--border)]"></textarea>
            </div>

            <!-- DISCOUNT COUPON SETTINGS -->
            <template v-if="form.coupon_kind === 'discount'">
                <div class="grid sm:grid-cols-4 gap-4 border-t border-[var(--border)] pt-5">
                    <div>
                        <label class="block text-sm font-medium mb-1.5">ছাড়ের ধরন</label>
                        <select v-model="form.discount_type" class="w-full px-3 py-2.5 rounded-lg bg-[var(--surface2)] border border-[var(--border)]">
                            <option value="percent">শতাংশ (%)</option>
                            <option value="fixed">নির্দিষ্ট টাকা (৳)</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1.5">ছাড়ের পরিমাণ *</label>
                        <input v-model="form.discount_value" type="number" step="0.01" min="0" required class="w-full px-3 py-2.5 rounded-lg bg-[var(--surface2)] border border-[var(--border)]" />
                        <p v-if="form.errors.discount_value" class="text-xs text-rose-500 mt-1">{{ form.errors.discount_value }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1.5">সর্বোচ্চ ছাড় (৳, ঐচ্ছিক)</label>
                        <input v-model="form.max_discount_amount" type="number" step="0.01" min="0" placeholder="যেমন: 500" class="w-full px-3 py-2.5 rounded-lg bg-[var(--surface2)] border border-[var(--border)]" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1.5">ন্যূনতম অর্ডার (৳, ঐচ্ছিক)</label>
                        <input v-model="form.min_order_amount" type="number" step="0.01" min="0" placeholder="যেমন: 100" class="w-full px-3 py-2.5 rounded-lg bg-[var(--surface2)] border border-[var(--border)]" />
                    </div>
                </div>

                <!-- Type-wise restriction -->
                <div class="border-t border-[var(--border)] pt-5 space-y-3">
                    <label class="block text-sm font-bold">কোন ধরনের প্রোডাক্টে প্রযোজ্য হবে? (Type-wise)</label>
                    <div class="flex flex-wrap gap-2">
                        <button
                            v-for="opt in [
                                { id: 'all', label: 'সকল ধরন (All)' },
                                { id: 'course', label: 'কোর্স (Courses)' },
                                { id: 'book', label: 'বই ও অধ্যায় (Books)' },
                                { id: 'plan', label: 'সাবস্ক্রিপশন প্ল্যান (Plans)' },
                                { id: 'invoice', label: 'ইনভয়েস (Invoices)' },
                            ]"
                            :key="opt.id"
                            type="button"
                            @click="toggleType(opt.id)"
                            class="px-3.5 py-1.5 rounded-full text-xs font-bold border transition cursor-pointer"
                            :class="form.applicable_types.includes(opt.id)
                                ? 'bg-[var(--primary)] text-white border-[var(--primary)]'
                                : 'bg-[var(--surface2)] text-[var(--text-muted)] border-[var(--border)]'"
                        >
                            {{ opt.label }}
                        </button>
                    </div>
                </div>

                <!-- Product-wise restriction -->
                <div class="border-t border-[var(--border)] pt-5 space-y-4">
                    <div>
                        <label class="block text-sm font-bold">নির্দিষ্ট প্রোডাক্ট নির্বাচন (Product-wise — খালি রাখলে ক্যাটাগরির সব প্রোডাক্টে প্রযোজ্য)</label>
                    </div>
                    <div class="grid sm:grid-cols-2 gap-4">
                        <div v-if="showCategoryProducts('course')" class="rounded-xl border border-[var(--border)] bg-[var(--surface2)]/30 p-3">
                            <div class="text-xs font-bold mb-2">নির্দিষ্ট কোর্স ({{ form.applicable_product_ids.course.length }} নির্বাচিত)</div>
                            <div class="max-h-36 overflow-y-auto space-y-1 text-xs">
                                <label v-for="c in courses" :key="c.id" class="flex items-center gap-2 py-0.5 cursor-pointer">
                                    <input type="checkbox" :checked="form.applicable_product_ids.course.includes(Number(c.id))" @change="toggleItem(form.applicable_product_ids.course, c.id)" class="rounded" />
                                    <span class="truncate">{{ c.title }} (৳{{ c.price }})</span>
                                </label>
                            </div>
                        </div>

                        <div v-if="showCategoryProducts('book')" class="rounded-xl border border-[var(--border)] bg-[var(--surface2)]/30 p-3">
                            <div class="text-xs font-bold mb-2">নির্দিষ্ট বই ({{ form.applicable_product_ids.book.length }} নির্বাচিত)</div>
                            <div class="max-h-36 overflow-y-auto space-y-1 text-xs">
                                <label v-for="b in books" :key="b.id" class="flex items-center gap-2 py-0.5 cursor-pointer">
                                    <input type="checkbox" :checked="form.applicable_product_ids.book.includes(Number(b.id))" @change="toggleItem(form.applicable_product_ids.book, b.id)" class="rounded" />
                                    <span class="truncate">{{ b.title }} (৳{{ b.price }})</span>
                                </label>
                            </div>
                        </div>

                        <div v-if="showCategoryProducts('plan')" class="rounded-xl border border-[var(--border)] bg-[var(--surface2)]/30 p-3">
                            <div class="text-xs font-bold mb-2">নির্দিষ্ট প্ল্যান ({{ form.applicable_product_ids.plan.length }} নির্বাচিত)</div>
                            <div class="max-h-36 overflow-y-auto space-y-1 text-xs">
                                <label v-for="p in plans" :key="p.id" class="flex items-center gap-2 py-0.5 cursor-pointer">
                                    <input type="checkbox" :checked="form.applicable_product_ids.plan.includes(Number(p.id))" @change="toggleItem(form.applicable_product_ids.plan, p.id)" class="rounded" />
                                    <span class="truncate">{{ p.name }} (৳{{ p.price_total || p.price_monthly }})</span>
                                </label>
                            </div>
                        </div>

                        <div v-if="showCategoryProducts('invoice')" class="rounded-xl border border-[var(--border)] bg-[var(--surface2)]/30 p-3">
                            <div class="text-xs font-bold mb-2">নির্দিষ্ট বকেয়া ইনভয়েস ({{ form.applicable_product_ids.invoice.length }} নির্বাচিত)</div>
                            <div class="max-h-36 overflow-y-auto space-y-1 text-xs">
                                <label v-for="inv in invoices" :key="inv.id" class="flex items-center gap-2 py-0.5 cursor-pointer">
                                    <input type="checkbox" :checked="form.applicable_product_ids.invoice.includes(Number(inv.id))" @change="toggleItem(form.applicable_product_ids.invoice, inv.id)" class="rounded" />
                                    <span class="truncate">{{ inv.invoice_number }} — {{ inv.student?.name }} (৳{{ inv.total }})</span>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- User-wise restriction -->
                <div class="border-t border-[var(--border)] pt-5 space-y-3">
                    <label class="block text-sm font-bold">ব্যবহারকারী সীমাবদ্ধতা (User-wise)</label>
                    <div class="flex flex-wrap gap-4 text-sm">
                        <label class="flex items-center gap-2"><input type="radio" value="all" v-model="userScope" /> যেকোনো শিক্ষার্থী ব্যবহার করতে পারবে</label>
                        <label class="flex items-center gap-2"><input type="radio" value="specific" v-model="userScope" /> শুধুমাত্র নির্ধারিত শিক্ষার্থী ({{ form.allowed_student_ids.length }} নির্বাচিত)</label>
                    </div>
                    <div v-if="userScope === 'specific'" class="rounded-xl border border-[var(--border)] bg-[var(--surface2)]/30 p-3 space-y-2">
                        <input v-model="userSearch" type="text" placeholder="নাম বা ইমেইল দিয়ে খুঁজুন..." class="w-full px-3 py-1.5 rounded-lg bg-[var(--surface)] border border-[var(--border)] text-xs" />
                        <div class="max-h-40 overflow-y-auto grid sm:grid-cols-2 gap-1 text-xs">
                            <label v-for="s in filteredStudents" :key="s.id" class="flex items-center gap-2 py-0.5 cursor-pointer">
                                <input type="checkbox" :checked="form.allowed_student_ids.includes(Number(s.id))" @change="toggleItem(form.allowed_student_ids, s.id)" class="rounded" />
                                <span class="truncate">{{ s.name }} ({{ s.email }})</span>
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Usage limits & schedule -->
                <div class="grid sm:grid-cols-4 gap-4 border-t border-[var(--border)] pt-5">
                    <div>
                        <label class="block text-sm font-medium mb-1.5">সর্বমোট ব্যবহার সীমা</label>
                        <input v-model="form.max_uses" type="number" min="1" placeholder="খালি = সীমাহীন" class="w-full px-3 py-2.5 rounded-lg bg-[var(--surface2)] border border-[var(--border)]" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1.5">প্রতি জনে ব্যবহার সীমা</label>
                        <input v-model="form.max_uses_per_user" type="number" min="1" placeholder="যেমন: 1" class="w-full px-3 py-2.5 rounded-lg bg-[var(--surface2)] border border-[var(--border)]" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1.5">শুরুর তারিখ (ঐচ্ছিক)</label>
                        <input v-model="form.starts_at" type="datetime-local" class="w-full px-3 py-2.5 rounded-lg bg-[var(--surface2)] border border-[var(--border)]" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1.5">মেয়াদ শেষ (ঐচ্ছিক)</label>
                        <input v-model="form.ends_at" type="datetime-local" class="w-full px-3 py-2.5 rounded-lg bg-[var(--surface2)] border border-[var(--border)]" />
                    </div>
                </div>
            </template>

            <!-- LEGACY ACCESS GRANT SETTINGS -->
            <template v-else>
                <div>
                    <label class="block text-sm font-medium mb-1.5">Issuance</label>
                    <div class="flex gap-4 mb-3">
                        <label class="flex items-center gap-2 text-sm"><input type="radio" value="code" v-model="mode" class="rounded-full" /> Public code (any student can redeem)</label>
                        <label class="flex items-center gap-2 text-sm"><input type="radio" value="direct" v-model="mode" class="rounded-full" /> Direct assign to a student</label>
                    </div>

                    <div v-if="mode === 'code'" class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium mb-1.5">Max uses (blank = unlimited)</label>
                            <input v-model="form.max_uses" type="number" min="1" class="w-full px-4 py-2.5 rounded-lg bg-[var(--surface2)] border border-[var(--border)]" />
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-1.5">Per user limit</label>
                            <input v-model="form.max_uses_per_user" type="number" min="1" class="w-full px-4 py-2.5 rounded-lg bg-[var(--surface2)] border border-[var(--border)]" />
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
            </template>

            <label class="flex items-center gap-2 text-sm"><input v-model="form.is_active" type="checkbox" class="rounded" /> {{ t('common.active') }}</label>
            <button type="submit" :disabled="form.processing" class="px-6 py-2.5 rounded-lg bg-[var(--primary)] hover:bg-[var(--primary-hover)] text-white text-sm font-semibold disabled:opacity-60">
                {{ form.processing ? t('common.saving') : t('common.save') }}
            </button>
        </form>
    </AdminLayout>
</template>
