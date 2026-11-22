<script setup>
import { ref } from 'vue';
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import AdminLayout from '@/Components/Layout/AdminLayout.vue';
import { useI18n } from '@/i18n';

const props = defineProps({
    student: Object,
    plans: Array,
    allBooks: { type: Array, default: () => [] },
    access: Object,
});
const { t } = useI18n();

const activeTab = ref('overview');

// Edit Profile
const editForm = useForm({
    name: props.student.name ?? '',
    email: props.student.email ?? '',
    phone: props.student.phone ?? '',
    type: props.student.type ?? 'hsc',
    status: props.student.status ?? 'active',
});
function submitEdit() {
    editForm.put(`/admin/students/${props.student.id}`, { preserveScroll: true });
}

// Email Verify / Soft Delete / Restore
function verifyEmail() {
    router.post(`/admin/students/${props.student.id}/verify-email`, {}, { preserveScroll: true });
}
function deleteStudent() {
    if (!confirm(`Soft-delete ${props.student.name}? The student can log back in to recover their account for 30 days.`)) return;
    router.delete(`/admin/students/${props.student.id}`);
}
function restoreStudent() {
    router.post(`/admin/students/${props.student.id}/restore`, {}, { preserveScroll: true });
}

// Wallet
const walletForm = useForm({ type: 'credit', amount: '', notes: '' });
function submitWallet() {
    walletForm.post(`/admin/students/${props.student.id}/wallet-adjust`, {
        preserveScroll: true,
        onSuccess: () => walletForm.reset('amount', 'notes'),
    });
}

// Subscription
const subForm = useForm({ plan_id: props.plans[0]?.id ?? null, months: 1 });
function submitSubscription() {
    subForm.post(`/admin/students/${props.student.id}/assign-subscription`, { preserveScroll: true });
}

// Free Access Grant
const accessForm = useForm({ name: '', notes: '', duration_days: 30 });
function submitAccess() {
    accessForm.post(`/admin/students/${props.student.id}/grant-access`, {
        preserveScroll: true,
        onSuccess: () => accessForm.reset('name', 'notes'),
    });
}

// Personal Offer (Notification + Push + Email)
const offerForm = useForm({
    title: '',
    body: '',
    action_url: '/subscription/plans',
    send_email: true,
});
function submitOffer() {
    offerForm.post(`/admin/students/${props.student.id}/send-offer`, {
        preserveScroll: true,
        onSuccess: () => offerForm.reset('title', 'body'),
    });
}

// Bookshelf Add / Remove
const shelfForm = useForm({ book_id: props.allBooks[0]?.id ?? null });
function addBookToShelf() {
    if (!shelfForm.book_id) return;
    shelfForm.post(`/admin/students/${props.student.id}/bookshelf`, { preserveScroll: true });
}
function removeBookFromShelf(bookId) {
    if (!confirm('Remove this book from student bookshelf?')) return;
    router.delete(`/admin/students/${props.student.id}/bookshelf/${bookId}`, { preserveScroll: true });
}
function deleteUploadedBook(myBookId) {
    if (!confirm('Remove this uploaded personal book?')) return;
    router.delete(`/admin/students/${props.student.id}/my-books/${myBookId}`, { preserveScroll: true });
}
</script>

<template>
    <Head :title="`${student.name} — Student Management`" />
    <AdminLayout>
        <!-- Deleted Banner -->
        <div v-if="student.is_trashed" class="mb-6 flex flex-wrap items-center justify-between gap-3 rounded-xl border border-red-500/30 bg-red-500/10 p-4 text-sm text-red-400">
            <div>
                <strong>Account Scheduled for Deletion:</strong>
                This student was soft-deleted and will be permanently purged in <strong>{{ student.days_until_purge }} day(s)</strong> (30-day recovery policy).
            </div>
            <button type="button" @click="restoreStudent" class="rounded-lg bg-emerald-600 px-4 py-2 text-xs font-bold text-white hover:bg-emerald-700">
                Restore Account Now
            </button>
        </div>

        <!-- Header -->
        <div class="mb-6 flex flex-wrap items-start justify-between gap-4">
            <div>
                <div class="flex flex-wrap items-center gap-2.5">
                    <h1 class="font-heading text-2xl font-extrabold">{{ student.name }}</h1>
                    <span class="rounded-full px-2.5 py-0.5 text-xs font-bold uppercase" :class="student.status === 'active' ? 'bg-emerald-500/15 text-emerald-500' : 'bg-red-500/15 text-red-500'">
                        {{ student.status }}
                    </span>
                    <span v-if="student.email_verified_at" class="rounded-full bg-emerald-500/15 px-2.5 py-0.5 text-xs font-semibold text-emerald-500">
                        ✓ Email Verified
                    </span>
                    <span v-else class="rounded-full bg-amber-500/15 px-2.5 py-0.5 text-xs font-semibold text-amber-500">
                        ⚠ Email Unverified
                    </span>
                </div>
                <p class="mt-1 text-sm text-[var(--text-muted)]">
                    {{ student.email || 'No email' }} · {{ student.phone || 'No phone' }} · {{ student.type?.toUpperCase() }} · Wallet: <strong>৳{{ student.wallet_balance }}</strong>
                </p>
            </div>
            <div class="flex flex-wrap items-center gap-2">
                <button v-if="!student.email_verified_at && !student.is_trashed" type="button" @click="verifyEmail" class="rounded-lg bg-emerald-600 px-3.5 py-2 text-xs font-bold text-white hover:bg-emerald-700">
                    ✓ Approve / Verify Email
                </button>
                <button v-if="!student.is_trashed" type="button" @click="deleteStudent" class="rounded-lg border border-red-500/40 bg-red-500/10 px-3.5 py-2 text-xs font-bold text-red-500 hover:bg-red-500/20">
                    Delete Student (30-day recovery)
                </button>
                <Link href="/admin/students" class="rounded-lg border border-[var(--border)] px-3.5 py-2 text-xs font-semibold text-[var(--text-muted)] hover:text-[var(--text)]">
                    ← Back
                </Link>
            </div>
        </div>

        <!-- Tabs -->
        <div class="mb-6 flex flex-wrap gap-2 border-b border-[var(--border)] pb-3">
            <button
                v-for="tab in [
                    { id: 'overview', label: 'Overview & Edit' },
                    { id: 'transactions', label: `Transactions & Purchases (${(student.wallet_transactions?.length || 0) + (student.orders?.length || 0)})` },
                    { id: 'books', label: `Bookshelf (${student.book_shelf?.length || 0}) & Uploads (${student.my_books?.length || 0})` },
                    { id: 'offer', label: 'Send Offer (Push + Email)' },
                ]"
                :key="tab.id"
                type="button"
                @click="activeTab = tab.id"
                class="rounded-xl px-4 py-2 text-xs font-bold transition-colors"
                :class="activeTab === tab.id ? 'bg-[var(--primary)] text-white' : 'bg-[var(--surface)] text-[var(--text-muted)] hover:text-[var(--text)]'"
            >
                {{ tab.label }}
            </button>
        </div>

        <!-- TAB 1: Overview, Edit, Wallet, Plan, Access -->
        <div v-show="activeTab === 'overview'" class="grid md:grid-cols-2 gap-6">
            <!-- Edit Student Info -->
            <div class="rounded-xl border border-[var(--border)] bg-[var(--surface)] p-6">
                <h2 class="font-heading text-lg font-bold mb-4">Edit Student Profile</h2>
                <form @submit.prevent="submitEdit" class="space-y-3">
                    <input v-model="editForm.name" type="text" required placeholder="Full Name" class="w-full px-3 py-2 rounded-lg bg-[var(--surface2)] border border-[var(--border)] text-sm" />
                    <input v-model="editForm.email" type="email" placeholder="Email Address" class="w-full px-3 py-2 rounded-lg bg-[var(--surface2)] border border-[var(--border)] text-sm" />
                    <input v-model="editForm.phone" type="text" placeholder="Phone Number" class="w-full px-3 py-2 rounded-lg bg-[var(--surface2)] border border-[var(--border)] text-sm" />
                    <div class="grid grid-cols-2 gap-2">
                        <select v-model="editForm.type" class="px-3 py-2 rounded-lg bg-[var(--surface2)] border border-[var(--border)] text-sm">
                            <option value="ssc">SSC</option>
                            <option value="hsc">HSC</option>
                            <option value="admission">Admission</option>
                            <option value="university">University</option>
                            <option value="job">Job</option>
                        </select>
                        <select v-model="editForm.status" class="px-3 py-2 rounded-lg bg-[var(--surface2)] border border-[var(--border)] text-sm">
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>
                    <button type="submit" :disabled="editForm.processing" class="px-4 py-2 rounded-lg bg-[var(--primary)] text-white text-sm font-semibold disabled:opacity-60">
                        {{ editForm.processing ? 'Saving...' : 'Save Profile Changes' }}
                    </button>
                </form>
            </div>

            <!-- Wallet -->
            <div class="rounded-xl border border-[var(--border)] bg-[var(--surface)] p-6">
                <h2 class="font-heading text-lg font-bold mb-4">Wallet — ৳{{ student.wallet_balance }}</h2>
                <form @submit.prevent="submitWallet" class="space-y-3">
                    <div class="flex gap-2">
                        <select v-model="walletForm.type" class="px-3 py-2 rounded-lg bg-[var(--surface2)] border border-[var(--border)] text-sm">
                            <option value="credit">{{ t('admin.students.credit') }}</option>
                            <option value="debit">{{ t('admin.students.debit') }}</option>
                        </select>
                        <input v-model="walletForm.amount" type="number" step="0.01" min="0.01" :placeholder="t('admin.students.amount')" class="flex-1 px-3 py-2 rounded-lg bg-[var(--surface2)] border border-[var(--border)] text-sm" />
                    </div>
                    <input v-model="walletForm.notes" type="text" :placeholder="t('admin.students.reason')" class="w-full px-3 py-2 rounded-lg bg-[var(--surface2)] border border-[var(--border)] text-sm" />
                    <button type="submit" :disabled="walletForm.processing" class="px-4 py-2 rounded-lg bg-[var(--primary)] text-white text-sm font-semibold disabled:opacity-60">
                        {{ t('admin.students.wallet_adjust') }}
                    </button>
                </form>
            </div>

            <!-- Subscription -->
            <div class="rounded-xl border border-[var(--border)] bg-[var(--surface)] p-6">
                <h2 class="font-heading text-lg font-bold mb-4">Plan &amp; Subscription</h2>
                <div v-if="student.active_subscription" class="mb-4 rounded-lg bg-[var(--surface2)]/60 p-3 text-sm">
                    <div class="font-bold">{{ student.active_subscription.plan?.name }}</div>
                    <div class="text-xs text-[var(--text-muted)]">Expires: {{ new Date(student.active_subscription.expires_at).toLocaleDateString() }} · AI chat mins left: {{ student.active_subscription.ai_chat_minutes_remaining }}</div>
                </div>
                <div v-else class="mb-4 text-sm text-[var(--text-muted)]">No active subscription.</div>

                <form @submit.prevent="submitSubscription" class="space-y-3 mb-4">
                    <select v-model="subForm.plan_id" class="w-full px-3 py-2 rounded-lg bg-[var(--surface2)] border border-[var(--border)] text-sm">
                        <option v-for="p in plans" :key="p.id" :value="p.id">{{ p.name }} — ৳{{ p.price_monthly }}/mo</option>
                    </select>
                    <input v-model="subForm.months" type="number" min="1" max="24" placeholder="Months" class="w-full px-3 py-2 rounded-lg bg-[var(--surface2)] border border-[var(--border)] text-sm" />
                    <button type="submit" :disabled="subForm.processing" class="px-4 py-2 rounded-lg bg-[var(--primary)] text-white text-sm font-semibold disabled:opacity-60">
                        {{ t('admin.students.assign_subscription') }}
                    </button>
                </form>

                <div v-if="student.subscriptions?.length" class="border-t border-[var(--border)] pt-3">
                    <div class="text-xs font-bold uppercase text-[var(--text-muted)] mb-2">Subscription History</div>
                    <ul class="space-y-1 text-xs max-h-36 overflow-y-auto">
                        <li v-for="sub in student.subscriptions" :key="sub.id" class="flex justify-between">
                            <span>{{ sub.plan?.name || 'Plan' }} ({{ sub.status }})</span>
                            <span class="text-[var(--text-muted)]">{{ sub.expires_at ? new Date(sub.expires_at).toLocaleDateString() : '—' }}</span>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Free Access Grant -->
            <div class="rounded-xl border border-[var(--border)] bg-[var(--surface)] p-6">
                <h2 class="font-heading text-lg font-bold mb-4">Free Access Grant (Coupon / Campaign)</h2>
                <div v-if="access?.has_access" class="mb-4 text-sm space-y-1">
                    <div v-if="access.campaign" class="text-[var(--secondary)]">Campaign: {{ access.campaign.title }}</div>
                    <div v-if="access.coupon" class="text-[var(--primary)]">Coupon: {{ access.coupon.name }}</div>
                </div>
                <form @submit.prevent="submitAccess" class="space-y-3">
                    <input v-model="accessForm.name" type="text" placeholder="Label (e.g. Support grant)" class="w-full px-3 py-2 rounded-lg bg-[var(--surface2)] border border-[var(--border)] text-sm" />
                    <input v-model="accessForm.duration_days" type="number" min="1" max="3650" placeholder="Duration (days)" class="w-full px-3 py-2 rounded-lg bg-[var(--surface2)] border border-[var(--border)] text-sm" />
                    <input v-model="accessForm.notes" type="text" placeholder="Notes (optional)" class="w-full px-3 py-2 rounded-lg bg-[var(--surface2)] border border-[var(--border)] text-sm" />
                    <button type="submit" :disabled="accessForm.processing" class="px-4 py-2 rounded-lg bg-[var(--primary)] text-white text-sm font-semibold disabled:opacity-60">
                        Grant Full Access
                    </button>
                </form>
            </div>
        </div>

        <!-- TAB 2: Transactions, Orders, Invoices & Course/Chapter Purchases -->
        <div v-show="activeTab === 'transactions'" class="grid md:grid-cols-2 gap-6">
            <div class="rounded-xl border border-[var(--border)] bg-[var(--surface)] p-6">
                <h2 class="font-heading text-lg font-bold mb-3">Wallet Transactions</h2>
                <div class="max-h-80 overflow-y-auto divide-y divide-[var(--border)] text-xs">
                    <div v-for="tx in student.wallet_transactions" :key="tx.id" class="py-2.5 flex justify-between gap-2">
                        <div>
                            <div class="font-semibold">{{ tx.category }}</div>
                            <div class="text-[var(--text-muted)]">{{ tx.notes || tx.reference || new Date(tx.created_at).toLocaleString() }}</div>
                        </div>
                        <div class="text-right font-bold" :class="tx.type === 'credit' ? 'text-emerald-500' : 'text-red-500'">
                            {{ tx.type === 'credit' ? '+' : '-' }}৳{{ tx.amount }}
                            <div class="text-[10px] font-normal text-[var(--text-muted)]">Bal: ৳{{ tx.balance_after }}</div>
                        </div>
                    </div>
                    <div v-if="!student.wallet_transactions?.length" class="py-4 text-[var(--text-muted)]">No wallet transactions yet.</div>
                </div>
            </div>

            <div class="rounded-xl border border-[var(--border)] bg-[var(--surface)] p-6">
                <h2 class="font-heading text-lg font-bold mb-3">Orders &amp; Purchase History</h2>
                <div class="max-h-80 overflow-y-auto divide-y divide-[var(--border)] text-xs">
                    <div v-for="o in student.orders" :key="o.id" class="py-2.5 flex justify-between gap-2">
                        <div>
                            <div class="font-semibold">{{ o.order_number }} · {{ o.payment_method }}</div>
                            <div class="text-[var(--text-muted)]">{{ o.orderable_type?.split('\\').pop() }} #{{ o.orderable_id }} · {{ new Date(o.created_at).toLocaleDateString() }}</div>
                        </div>
                        <div class="text-right">
                            <div class="font-bold">৳{{ o.amount }}</div>
                            <span class="uppercase text-[10px] font-bold" :class="o.status === 'paid' || o.status === 'completed' ? 'text-emerald-500' : 'text-amber-500'">{{ o.status }}</span>
                        </div>
                    </div>
                    <div v-if="!student.orders?.length" class="py-4 text-[var(--text-muted)]">No orders yet.</div>
                </div>
            </div>

            <div class="rounded-xl border border-[var(--border)] bg-[var(--surface)] p-6">
                <h2 class="font-heading text-lg font-bold mb-3">Invoices</h2>
                <div class="max-h-64 overflow-y-auto divide-y divide-[var(--border)] text-xs">
                    <div v-for="inv in student.invoices" :key="inv.id" class="py-2.5 flex justify-between">
                        <div>
                            <div class="font-semibold">{{ inv.invoice_number }}</div>
                            <div class="text-[var(--text-muted)]">Due: {{ inv.due_at ? new Date(inv.due_at).toLocaleDateString() : '—' }}</div>
                        </div>
                        <div class="text-right">
                            <div class="font-bold">৳{{ inv.total }}</div>
                            <span class="uppercase text-[10px] font-bold" :class="inv.status === 'paid' ? 'text-emerald-500' : 'text-amber-500'">{{ inv.status }}</span>
                        </div>
                    </div>
                    <div v-if="!student.invoices?.length" class="py-4 text-[var(--text-muted)]">No invoices yet.</div>
                </div>
            </div>

            <div class="rounded-xl border border-[var(--border)] bg-[var(--surface)] p-6">
                <h2 class="font-heading text-lg font-bold mb-3">Enrolled Courses &amp; Owned Chapters</h2>
                <div class="space-y-3 text-xs">
                    <div>
                        <div class="font-bold text-[var(--text-muted)] mb-1">Courses ({{ student.course_enrollments?.length || 0 }})</div>
                        <ul class="space-y-1">
                            <li v-for="ce in student.course_enrollments" :key="ce.id">• {{ ce.course?.title || 'Course #' + ce.course_id }}</li>
                            <li v-if="!student.course_enrollments?.length" class="text-[var(--text-muted)]">None</li>
                        </ul>
                    </div>
                    <div>
                        <div class="font-bold text-[var(--text-muted)] mb-1">Purchased Chapters ({{ student.owned_chapters?.length || 0 }})</div>
                        <ul class="space-y-1 max-h-36 overflow-y-auto">
                            <li v-for="oc in student.owned_chapters" :key="oc.id">• {{ oc.chapter?.book?.title }} — {{ oc.chapter?.title }}</li>
                            <li v-if="!student.owned_chapters?.length" class="text-[var(--text-muted)]">None</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <!-- TAB 3: Bookshelf Management & Book Upload Tracking -->
        <div v-show="activeTab === 'books'" class="grid md:grid-cols-2 gap-6">
            <div class="rounded-xl border border-[var(--border)] bg-[var(--surface)] p-6">
                <h2 class="font-heading text-lg font-bold mb-4">Manage Student Bookshelf</h2>
                <form @submit.prevent="addBookToShelf" class="flex gap-2 mb-5">
                    <select v-model="shelfForm.book_id" class="flex-1 px-3 py-2 rounded-lg bg-[var(--surface2)] border border-[var(--border)] text-xs">
                        <option v-for="b in allBooks" :key="b.id" :value="b.id">{{ b.title }} (৳{{ b.price || 0 }})</option>
                    </select>
                    <button type="submit" :disabled="shelfForm.processing" class="px-4 py-2 rounded-lg bg-[var(--primary)] text-white text-xs font-bold">
                        + Add Book
                    </button>
                </form>

                <div class="divide-y divide-[var(--border)] text-xs max-h-96 overflow-y-auto">
                    <div v-for="item in student.book_shelf" :key="item.id" class="py-2.5 flex items-center justify-between gap-2">
                        <div>
                            <div class="font-semibold">{{ item.book?.title || 'Book #' + item.book_id }}</div>
                            <div class="text-[var(--text-muted)]">Source: {{ item.source || 'shelf' }}</div>
                        </div>
                        <button type="button" @click="removeBookFromShelf(item.book_id)" class="text-red-500 hover:underline font-semibold">Remove</button>
                    </div>
                    <div v-if="!student.book_shelf?.length" class="py-4 text-[var(--text-muted)]">No books on bookshelf.</div>
                </div>
            </div>

            <div class="rounded-xl border border-[var(--border)] bg-[var(--surface)] p-6">
                <h2 class="font-heading text-lg font-bold mb-4">Student Uploaded Books Tracking (MyBooks)</h2>
                <div class="mb-3 text-xs text-[var(--text-muted)]">
                    Uploads this month ({{ student.trial_reuploads_month || 'N/A' }}): <strong>{{ student.trial_reuploads_count || 0 }}</strong> · Total stored: <strong>{{ student.my_books?.length || 0 }}</strong>
                </div>
                <div class="divide-y divide-[var(--border)] text-xs max-h-96 overflow-y-auto">
                    <div v-for="mb in student.my_books" :key="mb.id" class="py-2.5 flex items-center justify-between gap-2">
                        <div>
                            <div class="font-semibold">{{ mb.title }}</div>
                            <div class="text-[var(--text-muted)]">{{ mb.total_pages || 0 }} pages · Status: {{ mb.status || 'ready' }} · {{ new Date(mb.created_at).toLocaleDateString() }}</div>
                        </div>
                        <button type="button" @click="deleteUploadedBook(mb.id)" class="text-red-500 hover:underline font-semibold">Delete</button>
                    </div>
                    <div v-if="!student.my_books?.length" class="py-4 text-[var(--text-muted)]">No personal PDF books uploaded by this student.</div>
                </div>
            </div>
        </div>

        <!-- TAB 4: Send User-Specific Offer -->
        <div v-show="activeTab === 'offer'" class="max-w-xl rounded-xl border border-[var(--border)] bg-[var(--surface)] p-6">
            <h2 class="font-heading text-lg font-bold mb-1">Send Personal Offer / Notification</h2>
            <p class="text-xs text-[var(--text-muted)] mb-5">Sends directly to {{ student.name }} via In-App Notification, Google Firebase Push Notification, and Email.</p>
            <form @submit.prevent="submitOffer" class="space-y-4">
                <div>
                    <label class="block text-xs font-bold mb-1">Offer Title</label>
                    <input v-model="offerForm.title" type="text" required placeholder="e.g. আপনার জন্য ৩০% স্পেশাল ডিসকাউন্ট!" class="w-full px-3.5 py-2.5 rounded-lg bg-[var(--surface2)] border border-[var(--border)] text-sm" />
                </div>
                <div>
                    <label class="block text-xs font-bold mb-1">Message</label>
                    <textarea v-model="offerForm.body" rows="4" required placeholder="Write personal offer details..." class="w-full px-3.5 py-2.5 rounded-lg bg-[var(--surface2)] border border-[var(--border)] text-sm"></textarea>
                </div>
                <div>
                    <label class="block text-xs font-bold mb-1">Clickable Link (Optional)</label>
                    <input v-model="offerForm.action_url" type="text" placeholder="/subscription/plans or https://sikhun.com/..." class="w-full px-3.5 py-2.5 rounded-lg bg-[var(--surface2)] border border-[var(--border)] text-sm" />
                </div>
                <label class="flex items-center gap-2 text-sm cursor-pointer">
                    <input v-model="offerForm.send_email" type="checkbox" class="rounded" />
                    <span>Also send Email to {{ student.email || '(no email)' }}</span>
                </label>
                <button type="submit" :disabled="offerForm.processing" class="px-5 py-2.5 rounded-lg bg-[var(--primary)] text-white text-sm font-bold disabled:opacity-60">
                    {{ offerForm.processing ? 'Sending...' : 'Send Personal Offer Now' }}
                </button>
            </form>
        </div>
    </AdminLayout>
</template>
