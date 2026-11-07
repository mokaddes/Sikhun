<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import AdminLayout from '@/Components/Layout/AdminLayout.vue';
import {
    BriefcaseIcon,
    ArrowLeftIcon,
    BuildingOfficeIcon,
    CalendarDaysIcon,
    CurrencyBangladeshiIcon,
    LinkIcon,
    AcademicCapIcon,
    SparklesIcon,
} from '@heroicons/vue/24/outline';

const props = defineProps({
    job: Object,
});

const isEdit = !!props.job?.id;

const form = useForm({
    title: props.job?.title ?? '',
    slug: props.job?.slug ?? '',
    organization: props.job?.organization ?? '',
    category: props.job?.category ?? 'government',
    job_type: props.job?.job_type ?? 'Full-time',
    vacancy: props.job?.vacancy ?? '',
    salary: props.job?.salary ?? '',
    location: props.job?.location ?? 'ঢাকা, বাংলাদেশ',
    education_requirement: props.job?.education_requirement ?? '',
    experience_requirement: props.job?.experience_requirement ?? '',
    deadline: props.job?.deadline ? props.job.deadline.split('T')[0] : '',
    published_date: props.job?.published_date ? props.job.published_date.split('T')[0] : new Date().toISOString().split('T')[0],
    apply_url: props.job?.apply_url ?? '',
    source_name: props.job?.source_name ?? '',
    source_url: props.job?.source_url ?? '',
    description: props.job?.description ?? '',
    application_instructions: props.job?.application_instructions ?? '',
    is_featured: props.job?.is_featured ?? false,
});

function submit() {
    if (isEdit) {
        form.put(`/admin/jobs/${props.job.id}`);
    } else {
        form.post('/admin/jobs');
    }
}
</script>

<template>
    <Head :title="isEdit ? 'সার্কুলার এডিট করুন' : 'নতুন সার্কুলার যুক্ত করুন'" />

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
                    <BriefcaseIcon class="h-8 w-8 text-[var(--primary)]" />
                    {{ isEdit ? 'সার্কুলার সম্পাদন করুন' : 'নতুন সার্কুলার তৈরি করুন' }}
                </h1>
                <p class="mt-1 text-xs sm:text-sm text-[var(--text-muted)]">
                    চাকরির সকল তথ্য, যোগ্যতা, আবেদনের নিয়ম ও অফিসিয়াল লিংক প্রদান করুন।
                </p>
            </div>
        </header>

        <form @submit.prevent="submit" class="space-y-6 max-w-5xl">
            <!-- Basic Information Card -->
            <div class="card p-6 rounded-3xl border border-[var(--border)] bg-[var(--surface)] shadow-xs space-y-4">
                <h2 class="font-heading text-base font-bold text-[var(--text)] flex items-center gap-2 border-b border-[var(--border)] pb-3">
                    <BuildingOfficeIcon class="w-5 h-5 text-[var(--primary)]" />
                    প্রাথমিক তথ্য
                </h2>

                <div class="space-y-4">
                    <div>
                        <label class="block text-xs font-bold text-[var(--text)] mb-1">সার্কুলারের শিরোনাম <span class="text-rose-500">*</span></label>
                        <input
                            v-model="form.title"
                            type="text"
                            placeholder="যেমন: বাংলাদেশ ব্যাংক সহকারী পরিচালক (জেনারেল) নিয়োগ বিজ্ঞপ্তি ২০২৬"
                            class="input w-full text-sm py-2"
                            required
                        />
                        <div v-if="form.errors.title" class="text-xs text-rose-500 mt-1">{{ form.errors.title }}</div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-[var(--text)] mb-1">প্রতিষ্ঠান / কোম্পানি <span class="text-rose-500">*</span></label>
                            <input
                                v-model="form.organization"
                                type="text"
                                placeholder="যেমন: বাংলাদেশ ব্যাংক"
                                class="input w-full text-sm py-2"
                                required
                            />
                            <div v-if="form.errors.organization" class="text-xs text-rose-500 mt-1">{{ form.errors.organization }}</div>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-[var(--text)] mb-1">কাস্টম স্লাগ (ঐচ্ছিক)</label>
                            <input
                                v-model="form.slug"
                                type="text"
                                placeholder="ফাঁকা রাখলে স্বয়ংক্রিয়ভাবে তৈরি হবে"
                                class="input w-full text-sm py-2 font-mono"
                            />
                            <div v-if="form.errors.slug" class="text-xs text-rose-500 mt-1">{{ form.errors.slug }}</div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-[var(--text)] mb-1">ক্যাটাগরি <span class="text-rose-500">*</span></label>
                            <select v-model="form.category" class="input w-full text-sm py-2" required>
                                <option value="government">সরকারি চাকরি (Govt)</option>
                                <option value="bank">ব্যাংক ও আর্থিক প্রতিষ্ঠান (Bank)</option>
                                <option value="ngo">এনজিও ও উন্নয়ন সংস্থা (NGO)</option>
                                <option value="private">প্রাইভেট কোম্পানি (Private)</option>
                                <option value="it_tech">আইটি ও সফটওয়্যার (IT)</option>
                                <option value="education">শিক্ষা ও শিক্ষকতা (Education)</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-[var(--text)] mb-1">চাকরির ধরণ</label>
                            <select v-model="form.job_type" class="input w-full text-sm py-2">
                                <option value="Full-time">Full-time (স্থায়ী)</option>
                                <option value="Contractual">Contractual (চুক্তিভিত্তিক)</option>
                                <option value="Part-time">Part-time (খন্ডকালীন)</option>
                                <option value="Internship">Internship (ইন্টার্নশিপ)</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-[var(--text)] mb-1">কর্মস্থল / লোকেশন</label>
                            <input
                                v-model="form.location"
                                type="text"
                                placeholder="ঢাকা, বাংলাদেশ"
                                class="input w-full text-sm py-2"
                            />
                        </div>
                    </div>
                </div>
            </div>

            <!-- Vacancy & Eligibility Card -->
            <div class="card p-6 rounded-3xl border border-[var(--border)] bg-[var(--surface)] shadow-xs space-y-4">
                <h2 class="font-heading text-base font-bold text-[var(--text)] flex items-center gap-2 border-b border-[var(--border)] pb-3">
                    <AcademicCapIcon class="w-5 h-5 text-emerald-500" />
                    পদসংখ্যা, বেতন ও যোগ্যতা
                </h2>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-[var(--text)] mb-1">পদসংখ্যা / শূন্যপদ</label>
                        <input
                            v-model="form.vacancy"
                            type="text"
                            placeholder="যেমন: ১০০ জন অথবা বিভিন্ন পদ"
                            class="input w-full text-sm py-2"
                        />
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-[var(--text)] mb-1">বেতন স্কেল</label>
                        <input
                            v-model="form.salary"
                            type="text"
                            placeholder="যেমন: ২২,০০০ - ৫৩,০৬০ টাকা (গ্রেড-৯)"
                            class="input w-full text-sm py-2"
                        />
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-[var(--text)] mb-1">শিক্ষাগত যোগ্যতা</label>
                        <textarea
                            v-model="form.education_requirement"
                            rows="2"
                            placeholder="যেমন: যেকোনো বিষয়ে ৪ বছর মেয়াদি স্নাতক বা সমমান ডিগ্রি..."
                            class="input w-full text-sm py-2"
                        ></textarea>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-[var(--text)] mb-1">কাজের অভিজ্ঞতা</label>
                        <textarea
                            v-model="form.experience_requirement"
                            rows="2"
                            placeholder="যেমন: প্রয়োজন নেই / সংশ্লিষ্ট বিষয়ে ২ বছরের অভিজ্ঞতা..."
                            class="input w-full text-sm py-2"
                        ></textarea>
                    </div>
                </div>
            </div>

            <!-- Dates & Application Link Card -->
            <div class="card p-6 rounded-3xl border border-[var(--border)] bg-[var(--surface)] shadow-xs space-y-4">
                <h2 class="font-heading text-base font-bold text-[var(--text)] flex items-center gap-2 border-b border-[var(--border)] pb-3">
                    <CalendarDaysIcon class="w-5 h-5 text-amber-500" />
                    সময়সীমা ও আবেদন লিংক
                </h2>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-[var(--text)] mb-1">আবেদনের শেষ তারিখ (Deadline) <span class="text-rose-500">*</span></label>
                        <input
                            v-model="form.deadline"
                            type="date"
                            class="input w-full text-sm py-2"
                            required
                        />
                        <div v-if="form.errors.deadline" class="text-xs text-rose-500 mt-1">{{ form.errors.deadline }}</div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-[var(--text)] mb-1">প্রকাশের তারিখ (Published Date)</label>
                        <input
                            v-model="form.published_date"
                            type="date"
                            class="input w-full text-sm py-2"
                        />
                    </div>
                </div>

                <div class="space-y-4 pt-2">
                    <div>
                        <label class="block text-xs font-bold text-[var(--text)] mb-1 flex items-center gap-1.5">
                            <LinkIcon class="w-3.5 h-3.5 text-[var(--primary)]" />
                            অফিসিয়াল অনলাইন আবেদনের লিঙ্ক (Apply URL)
                        </label>
                        <input
                            v-model="form.apply_url"
                            type="url"
                            placeholder="https://...teletalk.com.bd অথবা https://erecruitment.bb.org.bd"
                            class="input w-full text-sm py-2 font-mono"
                        />
                        <span class="text-[11px] text-[var(--text-muted)] mt-0.5 block">শিক্ষার্থীরা এই লিঙ্কে ক্লিক করে সরাসরি সরকারি পোর্টালে গিয়ে আবেদন করতে পারবেন।</span>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-[var(--text)] mb-1">উৎস বা সার্কুলার প্রোভাইডার</label>
                            <input
                                v-model="form.source_name"
                                type="text"
                                placeholder="যেমন: Teletalk Alljobs / Students.bd"
                                class="input w-full text-sm py-2"
                            />
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-[var(--text)] mb-1">উৎস ওয়েব লিংক</label>
                            <input
                                v-model="form.source_url"
                                type="url"
                                placeholder="https://..."
                                class="input w-full text-sm py-2 font-mono"
                            />
                        </div>
                    </div>
                </div>
            </div>

            <!-- Full Description & Instructions Card -->
            <div class="card p-6 rounded-3xl border border-[var(--border)] bg-[var(--surface)] shadow-xs space-y-4">
                <h2 class="font-heading text-base font-bold text-[var(--text)] flex items-center gap-2 border-b border-[var(--border)] pb-3">
                    <SparklesIcon class="w-5 h-5 text-purple-500" />
                    বিস্তারিত বিবরণ ও নিয়মাবলী
                </h2>

                <div class="space-y-4">
                    <div>
                        <label class="block text-xs font-bold text-[var(--text)] mb-1">সার্কুলারের সম্পূর্ণ বিবরণী <span class="text-rose-500">*</span></label>
                        <textarea
                            v-model="form.description"
                            rows="9"
                            placeholder="পদের বিবরণ, শর্তাবলী, শিক্ষাগত যোগ্যতার খুঁটিনাটি..."
                            class="input w-full text-sm py-2.5 font-sans leading-relaxed"
                            required
                        ></textarea>
                        <div v-if="form.errors.description" class="text-xs text-rose-500 mt-1">{{ form.errors.description }}</div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-[var(--text)] mb-1">আবেদনের বিশেষ নিয়ম বা SMS নির্দেশিকা</label>
                        <textarea
                            v-model="form.application_instructions"
                            rows="4"
                            placeholder="যেমন: আবেদনের ৭২ ঘণ্টার মধ্যে টেলিটক প্রিপেইড সিম থেকে এসএমএস করে ফি জমা দিতে হবে..."
                            class="input w-full text-sm py-2"
                        ></textarea>
                    </div>

                    <div class="pt-2">
                        <label class="flex items-center gap-2 cursor-pointer select-none">
                            <input
                                v-model="form.is_featured"
                                type="checkbox"
                                class="rounded border-[var(--border)] text-[var(--primary)] focus:ring-[var(--primary)] h-4 w-4"
                            />
                            <span class="text-xs font-bold text-[var(--text)]">এই সার্কুলারটিকে হোমপেজে ফিচার্ড হিসেবে প্রদর্শন করুন</span>
                        </label>
                    </div>
                </div>
            </div>

            <!-- Submit Button Bar -->
            <div class="flex items-center justify-end gap-3 pt-4">
                <Link
                    href="/admin/jobs"
                    class="btn btn-outline text-xs py-2.5 px-5 font-bold"
                >
                    বাতিল
                </Link>
                <button
                    type="submit"
                    :disabled="form.processing"
                    class="btn btn-primary text-xs py-2.5 px-6 font-bold shadow-md shadow-[var(--primary)]/20"
                >
                    {{ form.processing ? 'সংরক্ষণ হচ্ছে...' : (isEdit ? 'আপডেট করুন' : 'সার্কুলার প্রকাশ করুন') }}
                </button>
            </div>
        </form>
    </AdminLayout>
</template>
