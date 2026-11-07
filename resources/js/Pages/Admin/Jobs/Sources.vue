<script setup>
import { ref } from 'vue';
import { Head, Link, useForm, router } from '@inertiajs/vue3';
import AdminLayout from '@/Components/Layout/AdminLayout.vue';
import ConfirmButton from '@/Components/UI/ConfirmButton.vue';
import {
    BoltIcon,
    ArrowPathIcon,
    PencilSquareIcon,
    TrashIcon,
    PlusIcon,
    ArrowLeftIcon,
    CheckCircleIcon,
    ExclamationCircleIcon,
    GlobeAltIcon,
    RssIcon,
} from '@heroicons/vue/24/outline';

const props = defineProps({
    sources: Array,
});

const editingId = ref(null);
const runningSourceId = ref(null);
const runningAll = ref(false);

const form = useForm({
    name: '',
    url: '',
    feed_url: '',
    type: 'rss',
    default_category: 'government',
    notes: '',
    is_active: true,
});

function startEdit(src) {
    editingId.value = src.id;
    form.name = src.name;
    form.url = src.url;
    form.feed_url = src.feed_url ?? '';
    form.type = src.type;
    form.default_category = src.default_category ?? 'government';
    form.notes = src.notes ?? '';
    form.is_active = !!src.is_active;
}

function resetForm() {
    editingId.value = null;
    form.reset();
    form.type = 'rss';
    form.default_category = 'government';
    form.is_active = true;
}

function submit() {
    if (editingId.value) {
        form.put(`/admin/job-sources/${editingId.value}`, {
            onSuccess: () => resetForm(),
        });
    } else {
        form.post('/admin/job-sources', {
            onSuccess: () => resetForm(),
        });
    }
}

function runSource(src) {
    runningSourceId.value = src.id;
    router.post(`/admin/job-sources/${src.id}/run`, {}, {
        preserveScroll: true,
        onFinish: () => { runningSourceId.value = null; },
    });
}

function runAllSources() {
    runningAll.value = true;
    router.post('/admin/job-sources/run-all', {}, {
        preserveScroll: true,
        onFinish: () => { runningAll.value = false; },
    });
}
</script>

<template>
    <Head title="চাকরির স্ক্র্যাপিং সোর্স সমূহ" />

    <AdminLayout>
        <!-- Header -->
        <header class="anim-rise mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <Link
                    href="/admin/jobs"
                    class="inline-flex items-center gap-1.5 text-xs text-[var(--text-muted)] hover:text-[var(--text)] transition mb-2"
                >
                    <ArrowLeftIcon class="w-3.5 h-3.5" /> সার্কুলার তালিকায় ফিরে যান
                </Link>
                <h1 class="flex items-center gap-2 font-heading text-2xl font-extrabold sm:text-3xl text-[var(--text)]">
                    <BoltIcon class="h-8 w-8 text-[var(--primary)]" />
                    চাকরির স্ক্র্যাপিং সোর্স
                </h1>
                <p class="mt-1 text-xs sm:text-sm text-[var(--text-muted)]">
                    যেসব ওয়েবসাইট ও RSS ফিড থেকে স্বয়ংক্রিয়ভাবে চাকরির বিজ্ঞপ্তি সংগৃহীত ও সিঙ্ক হবে তা পরিচালনা করুন।
                </p>
            </div>

            <div class="flex items-center gap-2">
                <button
                    type="button"
                    @click="runAllSources"
                    :disabled="runningAll"
                    class="btn btn-primary text-xs py-2.5 px-4 flex items-center gap-2 font-bold shadow-md shadow-[var(--primary)]/20"
                >
                    <ArrowPathIcon class="w-4 h-4" :class="{ 'animate-spin': runningAll }" />
                    {{ runningAll ? 'সকল সোর্স স্ক্র্যাপ হচ্ছে...' : 'সকল সক্রিয় সোর্স স্ক্র্যাপ করুন' }}
                </button>
            </div>
        </header>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Left: Sources List Table (2 cols) -->
            <div class="lg:col-span-2 space-y-4">
                <div class="card overflow-hidden rounded-3xl border border-[var(--border)] bg-[var(--surface)] shadow-xs">
                    <div class="p-4 border-b border-[var(--border)] bg-[var(--surface2)]/40 flex items-center justify-between">
                        <span class="font-heading font-bold text-sm text-[var(--text)]">কনফিগার করা সোর্সসমূহ ({{ sources.length }})</span>
                    </div>

                    <div class="divide-y divide-[var(--border)]">
                        <div
                            v-for="src in sources"
                            :key="src.id"
                            class="p-4 hover:bg-[var(--surface2)]/30 transition flex flex-col sm:flex-row sm:items-center justify-between gap-4"
                            :class="{ 'opacity-60': !src.is_active }"
                        >
                            <div class="space-y-1.5 flex-1 min-w-0">
                                <div class="flex items-center gap-2 flex-wrap">
                                    <span class="font-heading font-bold text-sm text-[var(--text)]">{{ src.name }}</span>
                                    <span
                                        class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider"
                                        :class="src.is_active ? 'bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-500/20' : 'bg-rose-500/10 text-rose-500 border border-rose-500/20'"
                                    >
                                        {{ src.is_active ? 'সক্রিয়' : 'নিষ্ক্রিয়' }}
                                    </span>
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold bg-[var(--surface2)] text-[var(--text-muted)] border border-[var(--border)]">
                                        {{ src.type.toUpperCase() }}
                                    </span>
                                </div>

                                <div class="text-xs text-[var(--text-muted)] flex items-center gap-2 flex-wrap">
                                    <a :href="src.url" target="_blank" class="hover:text-[var(--primary)] transition flex items-center gap-1 truncate max-w-xs">
                                        <GlobeAltIcon class="w-3.5 h-3.5 shrink-0" />
                                        {{ src.url }}
                                    </a>
                                    <span v-if="src.feed_url">•</span>
                                    <a v-if="src.feed_url" :href="src.feed_url" target="_blank" class="text-amber-500 hover:underline flex items-center gap-1 truncate max-w-xs">
                                        <RssIcon class="w-3.5 h-3.5 shrink-0" />
                                        RSS Feed
                                    </a>
                                </div>

                                <div class="text-[11px] text-[var(--text-muted)] flex items-center gap-3 pt-1">
                                    <span>ক্যাটাগরি: <strong>{{ src.default_category }}</strong></span>
                                    <span>আমদানি: <strong>{{ src.jobs_imported_count }}টি</strong></span>
                                    <span v-if="src.last_scraped_at">
                                        সর্বশেষ: {{ new Date(src.last_scraped_at).toLocaleDateString() }} {{ new Date(src.last_scraped_at).toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'}) }}
                                    </span>
                                </div>
                            </div>

                            <div class="flex items-center gap-2 shrink-0">
                                <button
                                    type="button"
                                    @click="runSource(src)"
                                    :disabled="runningSourceId === src.id"
                                    class="btn btn-outline text-xs py-1.5 px-3 flex items-center gap-1.5 font-bold hover:border-[var(--primary)]"
                                >
                                    <ArrowPathIcon class="w-3.5 h-3.5" :class="{ 'animate-spin': runningSourceId === src.id }" />
                                    {{ runningSourceId === src.id ? 'চলছে...' : 'এখনই চালান' }}
                                </button>

                                <button
                                    type="button"
                                    @click="startEdit(src)"
                                    class="p-2 rounded-lg border border-[var(--border)] hover:bg-[var(--surface2)] text-[var(--text)] transition"
                                    title="এডিট করুন"
                                >
                                    <PencilSquareIcon class="w-4 h-4" />
                                </button>

                                <ConfirmButton
                                    @confirm="router.delete(`/admin/job-sources/${src.id}`, { preserveScroll: true })"
                                    message="আপনি কি নিশ্চিতভাবে এই স্ক্র্যাপিং সোর্সটি ডিলিট করতে চান?"
                                    class="p-2 rounded-lg border border-rose-500/30 text-rose-500 hover:bg-rose-500/10 transition"
                                    title="ডিলিট করুন"
                                >
                                    <TrashIcon class="w-4 h-4" />
                                </ConfirmButton>
                            </div>
                        </div>

                        <div v-if="!sources.length" class="p-8 text-center text-xs text-[var(--text-muted)]">
                            কোনো সোর্স কনফিগার করা নেই। ডান পাশের ফর্ম থেকে সোর্স যুক্ত করুন।
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right: Add / Edit Form (1 col) -->
            <div class="space-y-4">
                <div class="card p-5 rounded-3xl border border-[var(--border)] bg-[var(--surface)] shadow-xs">
                    <h2 class="font-heading font-bold text-sm text-[var(--text)] mb-3 pb-2 border-b border-[var(--border)] flex items-center justify-between">
                        <span>{{ editingId ? 'সোর্স এডিট করুন' : 'নতুন স্ক্র্যাপিং সোর্স যোগ' }}</span>
                        <button
                            v-if="editingId"
                            type="button"
                            @click="resetForm"
                            class="text-xs text-[var(--text-muted)] hover:text-[var(--text)]"
                        >
                            বাতিল
                        </button>
                    </h2>

                    <form @submit.prevent="submit" class="space-y-3.5">
                        <div>
                            <label class="block text-xs font-bold text-[var(--text)] mb-1">সোর্সের নাম <span class="text-rose-500">*</span></label>
                            <input
                                v-model="form.name"
                                type="text"
                                placeholder="যেমন: Teletalk Alljobs"
                                class="input w-full text-xs py-2"
                                required
                            />
                            <div v-if="form.errors.name" class="text-xs text-rose-500 mt-1">{{ form.errors.name }}</div>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-[var(--text)] mb-1">মূল ওয়েবসাইট URL <span class="text-rose-500">*</span></label>
                            <input
                                v-model="form.url"
                                type="url"
                                placeholder="https://alljobs.teletalk.com.bd"
                                class="input w-full text-xs py-2 font-mono"
                                required
                            />
                            <div v-if="form.errors.url" class="text-xs text-rose-500 mt-1">{{ form.errors.url }}</div>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-[var(--text)] mb-1">RSS / XML ফিড URL (ঐচ্ছিক)</label>
                            <input
                                v-model="form.feed_url"
                                type="url"
                                placeholder="https://example.com/rss.xml"
                                class="input w-full text-xs py-2 font-mono"
                            />
                        </div>

                        <div class="grid grid-cols-2 gap-2">
                            <div>
                                <label class="block text-xs font-bold text-[var(--text)] mb-1">ধরণ</label>
                                <select v-model="form.type" class="input w-full text-xs py-2">
                                    <option value="rss">RSS ফিড</option>
                                    <option value="web">ওয়েব পোর্টাল</option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-[var(--text)] mb-1">ডিফল্ট ক্যাটাগরি</label>
                                <select v-model="form.default_category" class="input w-full text-xs py-2">
                                    <option value="government">সরকারি</option>
                                    <option value="bank">ব্যাংক</option>
                                    <option value="ngo">এনজিও</option>
                                    <option value="private">প্রাইভেট</option>
                                    <option value="it_tech">আইটি</option>
                                    <option value="education">শিক্ষা</option>
                                </select>
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-[var(--text)] mb-1">নোট / বিবরণী</label>
                            <textarea
                                v-model="form.notes"
                                rows="2"
                                placeholder="সোর্স সম্পর্কিত কোনো নোট..."
                                class="input w-full text-xs py-2"
                            ></textarea>
                        </div>

                        <div class="pt-1">
                            <label class="flex items-center gap-2 cursor-pointer select-none">
                                <input
                                    v-model="form.is_active"
                                    type="checkbox"
                                    class="rounded border-[var(--border)] text-[var(--primary)] focus:ring-[var(--primary)] h-4 w-4"
                                />
                                <span class="text-xs font-bold text-[var(--text)]">সোর্সটি সক্রিয় রাখুন</span>
                            </label>
                        </div>

                        <div class="pt-2 flex items-center justify-end gap-2">
                            <button
                                type="submit"
                                :disabled="form.processing"
                                class="btn btn-primary text-xs py-2 px-4 w-full font-bold shadow-md shadow-[var(--primary)]/20"
                            >
                                {{ form.processing ? 'সংরক্ষণ হচ্ছে...' : (editingId ? 'আপডেট করুন' : 'সোর্স সংরক্ষণ করুন') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
