<script setup>
import { computed } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import AdminLayout from '@/Components/Layout/AdminLayout.vue';
import {
    AcademicCapIcon,
    ArrowLeftIcon,
    CheckIcon,
} from '@heroicons/vue/24/outline';

const props = defineProps({
    notice: Object,
});

const isEdit = computed(() => !!props.notice);

const form = useForm({
    title: props.notice?.title ?? '',
    institution_name: props.notice?.institution_name ?? '',
    university_type: props.notice?.university_type ?? 'public',
    academic_year: props.notice?.academic_year ?? '2026-2027',
    application_start: props.notice?.application_start ? props.notice.application_start.substring(0, 16) : '',
    application_start_text: props.notice?.application_start_text ?? '',
    application_deadline: props.notice?.application_deadline ? props.notice.application_deadline.substring(0, 16) : '',
    application_deadline_text: props.notice?.application_deadline_text ?? '',
    admit_card_date_text: props.notice?.admit_card_date_text ?? '',
    minimum_gpa_ssc: props.notice?.minimum_gpa_ssc ?? '',
    minimum_gpa_hsc: props.notice?.minimum_gpa_hsc ?? '',
    total_gpa_required: props.notice?.total_gpa_required ?? '',
    application_fee: props.notice?.application_fee ?? '',
    apply_url: props.notice?.apply_url ?? '',
    official_website_url: props.notice?.official_website_url ?? '',
    notice_image_url: props.notice?.notice_image_url ?? '',
    featured_image_url: props.notice?.featured_image_url ?? '',
    description: props.notice?.description ?? '',
    eligibility_criteria: props.notice?.eligibility_criteria ?? '',
    marks_distribution: props.notice?.marks_distribution ?? '',
    status: props.notice?.status ?? 'draft',
    is_featured: props.notice?.is_featured ?? false,
    meta_title: props.notice?.meta_title ?? '',
    meta_description: props.notice?.meta_description ?? '',
});

function submit() {
    if (isEdit.value) {
        form.put(`/admin/admissions/${props.notice.id}`);
    } else {
        form.post('/admin/admissions');
    }
}
</script>

<template>
    <AdminLayout>
        <Head :title="isEdit ? 'ভর্তি বিজ্ঞপ্তি সম্পাদনা - Admin' : 'নতুন ভর্তি বিজ্ঞপ্তি - Admin'" />

        <div class="max-w-4xl mx-auto space-y-6">
            <!-- Header -->
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <Link href="/admin/admissions" class="btn btn-ghost p-2">
                        <ArrowLeftIcon class="w-5 h-5" />
                    </Link>
                    <div>
                        <h1 class="text-2xl font-bold tracking-tight text-white flex items-center gap-2">
                            <AcademicCapIcon class="w-7 h-7 text-orange-500" />
                            <span>{{ isEdit ? 'ভর্তি বিজ্ঞপ্তি সম্পাদনা' : 'নতুন ভর্তি বিজ্ঞপ্তি যুক্ত করুন' }}</span>
                        </h1>
                        <p class="text-xs text-gray-400 mt-0.5">
                            বিশ্ববিদ্যালয়ের ভর্তি বিজ্ঞপ্তি বিবরণ, যোগ্যতা ও অনলাইন আবেদন তথ্য দিন।
                        </p>
                    </div>
                </div>
            </div>

            <!-- Form -->
            <form @submit.prevent="submit" class="card p-6 md:p-8 space-y-6">
                <!-- Basic Info -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="md:col-span-2">
                        <label class="block text-xs font-bold text-gray-300 uppercase mb-1">বিজ্ঞপ্তির শিরোনাম *</label>
                        <input
                            v-model="form.title"
                            type="text"
                            placeholder="যেমন: ঢাকা বিশ্ববিদ্যালয় আন্ডারগ্র্যাজুয়েট ভর্তি বিজ্ঞপ্তি ২০২৬-২০২৭"
                            class="input w-full"
                            required
                        />
                        <span v-if="form.errors.title" class="text-xs text-rose-400 mt-1 block">{{ form.errors.title }}</span>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-300 uppercase mb-1">বিশ্ববিদ্যালয়ের নাম *</label>
                        <input
                            v-model="form.institution_name"
                            type="text"
                            placeholder="যেমন: ঢাকা বিশ্ববিদ্যালয় (DU)"
                            class="input w-full"
                            required
                        />
                        <span v-if="form.errors.institution_name" class="text-xs text-rose-400 mt-1 block">{{ form.errors.institution_name }}</span>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-300 uppercase mb-1">ক্যাটাগরি *</label>
                        <select v-model="form.university_type" class="input w-full" required>
                            <option value="public">পাবলিক বিশ্ববিদ্যালয় (Public)</option>
                            <option value="guccho">গুচ্ছ সমন্বিত বিশ্ববিদ্যালয় (Guccho Cluster)</option>
                            <option value="private">বেসরকারি বিশ্ববিদ্যালয় (Private)</option>
                        </select>
                        <span v-if="form.errors.university_type" class="text-xs text-rose-400 mt-1 block">{{ form.errors.university_type }}</span>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-300 uppercase mb-1">শিক্ষাবর্ষ / সেশন *</label>
                        <input
                            v-model="form.academic_year"
                            type="text"
                            placeholder="যেমন: 2026-2027"
                            class="input w-full"
                            required
                        />
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-300 uppercase mb-1">আবেদন ফি</label>
                        <input
                            v-model="form.application_fee"
                            type="text"
                            placeholder="যেমন: ১,০৫০ টাকা (প্রতি ইউনিট)"
                            class="input w-full"
                        />
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-300 uppercase mb-1">আবেদন শুরু (Datetime)</label>
                        <input
                            v-model="form.application_start"
                            type="datetime-local"
                            class="input w-full text-xs"
                        />
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-300 uppercase mb-1">আবেদন শুরু লিখিত বিবরণ (Text)</label>
                        <input
                            v-model="form.application_start_text"
                            type="text"
                            placeholder="যেমন: ১১ নভেম্বর ২০২৬, বুধবার • বেলা ১২:০০টা"
                            class="input w-full text-xs"
                        />
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-300 uppercase mb-1">আবেদনের শেষ তারিখ (Datetime)</label>
                        <input
                            v-model="form.application_deadline"
                            type="datetime-local"
                            class="input w-full text-xs"
                        />
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-300 uppercase mb-1">আবেদনের শেষ তারিখ লিখিত বিবরণ (Text)</label>
                        <input
                            v-model="form.application_deadline_text"
                            type="text"
                            placeholder="যেমন: ২৫ নভেম্বর ২০২৬, বুধবার • রাত ১১:৫৯টা"
                            class="input w-full text-xs"
                        />
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-xs font-bold text-gray-300 uppercase mb-1">প্রবেশপত্র ডাউনলোডের বিবরণ / সময়সূচি</label>
                        <input
                            v-model="form.admit_card_date_text"
                            type="text"
                            placeholder="যেমন: সংশ্লিষ্ট ইউনিটের পরীক্ষার দিন সকাল পর্যন্ত"
                            class="input w-full text-xs"
                        />
                    </div>
                </div>

                <!-- GPA Requirements -->
                <div class="pt-4 border-t border-white/5">
                    <h3 class="text-sm font-bold text-white mb-3">জিপিএ যোগ্যতা</h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-xs text-gray-400 mb-1">এসএসসি ন্যূনতম জিপিএ</label>
                            <input v-model="form.minimum_gpa_ssc" type="text" placeholder="যেমন: 3.50" class="input w-full" />
                        </div>
                        <div>
                            <label class="block text-xs text-gray-400 mb-1">এইচএসসি ন্যূনতম জিপিএ</label>
                            <input v-model="form.minimum_gpa_hsc" type="text" placeholder="যেমন: 3.50" class="input w-full" />
                        </div>
                        <div>
                            <label class="block text-xs text-gray-400 mb-1">মোট ন্যূনতম জিপিএ</label>
                            <input v-model="form.total_gpa_required" type="text" placeholder="যেমন: ৮.০০ (বিজ্ঞান) / ৭.৫০" class="input w-full" />
                        </div>
                    </div>
                </div>

                <!-- Links & Notice Image -->
                <div class="pt-4 border-t border-white/5 space-y-4">
                    <h3 class="text-sm font-bold text-white mb-1">লিংক ও অফিসিয়াল নোটিশ ইমেজ</h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs text-gray-400 mb-1">অনলাইন আবেদন পোর্টাল লিংক (Apply URL)</label>
                            <input v-model="form.apply_url" type="url" placeholder="https://admission.eis.du.ac.bd" class="input w-full" />
                        </div>
                        <div>
                            <label class="block text-xs text-gray-400 mb-1">অফিসিয়াল ওয়েবসাইট লিংক</label>
                            <input v-model="form.official_website_url" type="url" placeholder="https://du.ac.bd" class="input w-full" />
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs text-gray-400 mb-1">অফিসিয়াল সার্কুলার ইমেজ বা স্ক্যান URL</label>
                        <input v-model="form.notice_image_url" type="text" placeholder="https://example.com/circular.jpg" class="input w-full" />
                        <div v-if="form.notice_image_url" class="mt-2 p-2 bg-white/5 rounded-xl text-center">
                            <img :src="form.notice_image_url" alt="Preview" class="max-h-40 mx-auto rounded object-contain" />
                        </div>
                    </div>
                </div>

                <!-- Rich Description -->
                <div class="pt-4 border-t border-white/5">
                    <label class="block text-xs font-bold text-gray-300 uppercase mb-1">সার্কুলার বিবরণ ও নির্দেশিকা (HTML/Markdown) *</label>
                    <textarea
                        v-model="form.description"
                        rows="12"
                        placeholder="ভর্তি সংক্রান্ত সম্পূর্ণ ব্লগ পোস্ট বিবরণ লিখুন (টেবিল, পরীক্ষার নিয়মাবলী, ইউনিট পরিচিতি)..."
                        class="input w-full font-mono text-xs"
                        required
                    ></textarea>
                    <span v-if="form.errors.description" class="text-xs text-rose-400 mt-1 block">{{ form.errors.description }}</span>
                </div>

                <!-- Status & Publish -->
                <div class="pt-4 border-t border-white/5 flex flex-wrap items-center justify-between gap-4">
                    <div class="flex items-center gap-4">
                        <label class="flex items-center gap-2 cursor-pointer text-xs text-gray-300">
                            <input type="checkbox" v-model="form.is_featured" class="checkbox" />
                            <span>ফিচার্ড সার্কুলার</span>
                        </label>
                        <div>
                            <select v-model="form.status" class="input text-xs">
                                <option value="draft">ড্রাফট (Draft)</option>
                                <option value="published">প্রকাশিত (Published)</option>
                            </select>
                        </div>
                    </div>

                    <div class="flex items-center gap-3">
                        <Link href="/admin/admissions" class="btn btn-ghost text-xs">বাতিল</Link>
                        <button type="submit" :disabled="form.processing" class="btn btn-primary text-xs inline-flex items-center gap-2">
                            <CheckIcon class="w-4 h-4" />
                            <span>{{ isEdit ? 'আপডেট করুন' : 'সংরক্ষণ করুন' }}</span>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </AdminLayout>
</template>
