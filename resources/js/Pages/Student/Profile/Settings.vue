<script setup>
import { ref, computed } from 'vue';
import { useForm, Head } from '@inertiajs/vue3';
import StudentLayout from '@/Components/Layout/StudentLayout.vue';
import StudentAvatar from '@/Components/UI/StudentAvatar.vue';
import { useI18n } from '@/i18n';
import {
    UserCircleIcon,
    KeyIcon,
    AcademicCapIcon,
    PhotoIcon,
    ArrowUpTrayIcon,
    CheckCircleIcon,
    XMarkIcon,
    SparklesIcon,
} from '@heroicons/vue/24/outline';

const props = defineProps({ student: Object });
const { t, locale } = useI18n();

const filePreview = ref(null);
const fileInputRef = ref(null);

const presetAvatars = [
    { key: 'grad-cap', emoji: '🎓', label: 'শিক্ষার্থী', bg: 'from-blue-500 to-indigo-600' },
    { key: 'rocket', emoji: '🚀', label: 'গতিশীল', bg: 'from-amber-500 to-rose-600' },
    { key: 'brain', emoji: '💡', label: 'বুদ্ধিদীপ্ত', bg: 'from-yellow-400 to-amber-500' },
    { key: 'microscope', emoji: '🔬', label: 'গবেষক', bg: 'from-emerald-400 to-teal-600' },
    { key: 'book', emoji: '📚', label: 'জ্ঞানপিপাসু', bg: 'from-purple-500 to-pink-600' },
    { key: 'laptop', emoji: '💻', label: 'প্রযুক্তিবিদ', bg: 'from-cyan-500 to-blue-600' },
    { key: 'target', emoji: '🎯', label: 'লক্ষ্যভেদী', bg: 'from-red-500 to-rose-600' },
    { key: 'trophy', emoji: '🏆', label: 'চ্যাম্পিয়ন', bg: 'from-amber-400 to-yellow-600' },
];

const educationTypes = [
    {
        id: 'admission',
        title: 'বিশ্ববিদ্যালয় ভর্তি প্রস্তুতি',
        subtitle: 'DU, RU, BUET, Medical, GST',
        badge: 'Admission',
        color: 'border-blue-500/30 bg-blue-500/5 text-blue-600 dark:text-blue-400',
    },
    {
        id: 'job',
        title: 'বিসিএস ও সরকারি/ব্যাংক চাকরি প্রস্তুতি',
        subtitle: 'BCS, Bank, Primary, Govt Exam',
        badge: 'Job Exam',
        color: 'border-emerald-500/30 bg-emerald-500/5 text-emerald-600 dark:text-emerald-400',
    },
    {
        id: 'hsc',
        title: 'উচ্চ মাধ্যমিক (HSC)',
        subtitle: 'HSC Science, Arts, Commerce',
        badge: 'HSC',
        color: 'border-purple-500/30 bg-purple-500/5 text-purple-600 dark:text-purple-400',
    },
    {
        id: 'ssc',
        title: 'মাধ্যমিক (SSC)',
        subtitle: 'SSC Science, Arts, Commerce',
        badge: 'SSC',
        color: 'border-amber-500/30 bg-amber-500/5 text-amber-600 dark:text-amber-400',
    },
    {
        id: 'university',
        title: 'বিশ্ববিদ্যালয় অ্যাকাডেমিক',
        subtitle: 'Undergraduate / Semester Study',
        badge: 'University',
        color: 'border-indigo-500/30 bg-indigo-500/5 text-indigo-600 dark:text-indigo-400',
    },
];

const infoForm = useForm({
    _method: 'put',
    name: props.student.name ?? '',
    email: props.student.email ?? '',
    type: props.student.type || 'hsc',
    avatar: props.student.avatar || 'grad-cap',
    avatar_file: null,
});

const previewAvatar = computed(() => {
    if (filePreview.value) {
        return filePreview.value;
    }
    return infoForm.avatar;
});

function selectPreset(key) {
    infoForm.avatar = key;
    infoForm.avatar_file = null;
    filePreview.value = null;
    if (fileInputRef.value) {
        fileInputRef.value.value = '';
    }
}

function onFileChange(e) {
    const file = e.target.files?.[0];
    if (file) {
        infoForm.avatar_file = file;
        const reader = new FileReader();
        reader.onload = (ev) => {
            filePreview.value = ev.target.result;
        };
        reader.readAsDataURL(file);
    }
}

function clearCustomImage() {
    infoForm.avatar_file = null;
    filePreview.value = null;
    if (fileInputRef.value) {
        fileInputRef.value.value = '';
    }
    if (!infoForm.avatar || infoForm.avatar.startsWith('http') || infoForm.avatar.startsWith('/storage/')) {
        infoForm.avatar = 'grad-cap';
    }
}

function saveInfo() {
    infoForm.post('/profile/info', {
        preserveScroll: true,
        forceFormData: true,
        onSuccess: () => {
            filePreview.value = null;
            infoForm.avatar_file = null;
        },
    });
}

const passwordForm = useForm({
    current_password: '',
    password: '',
    password_confirmation: '',
});

function savePassword() {
    passwordForm.put('/profile/password', {
        preserveScroll: true,
        onSuccess: () => passwordForm.reset(),
    });
}

const inputClass = 'w-full rounded-xl border border-[var(--border)] bg-[var(--surface2)] px-3.5 py-2.5 text-sm transition-all focus:border-[var(--primary)] focus:outline-none focus:ring-2 focus:ring-[var(--primary)]/20';
</script>

<template>
    <Head :title="t('profile_settings_page.title')" />
    <StudentLayout>
        <header class="anim-rise mb-6 flex items-center gap-4">
            <StudentAvatar :avatar="student.avatar" :name="student.name" size="lg" />
            <div>
                <span class="eyebrow">{{ t('profile_settings_page.title') }}</span>
                <h1 class="mt-0.5 font-heading text-2xl font-extrabold">{{ t('profile_settings_page.title') }}</h1>
                <p class="text-sm text-[var(--text-muted)]">{{ t('profile_settings_page.subtitle') }}</p>
            </div>
        </header>

        <div class="grid max-w-5xl gap-6 lg:grid-cols-3">
            <!-- Profile information & Education Type (2 Cols) -->
            <div class="space-y-6 lg:col-span-2">
                <form class="card anim-rise rounded-2xl p-6" @submit.prevent="saveInfo">
                    <h2 class="mb-1 flex items-center gap-2 font-heading text-lg font-bold">
                        <UserCircleIcon class="h-5 w-5 text-[var(--secondary)]" /> {{ t('profile_settings_page.info_title') }}
                    </h2>
                    <p class="mb-6 text-sm text-[var(--text-muted)]">{{ t('profile_settings_page.info_subtitle') }}</p>

                    <!-- Avatar Selector Section -->
                    <div class="mb-6 rounded-2xl border border-[var(--border)] bg-[var(--surface2)]/40 p-4 sm:p-5">
                        <div class="mb-3 flex items-center justify-between">
                            <label class="block text-sm font-semibold">
                                {{ locale === 'en' ? 'Profile Avatar' : 'প্রোফাইল অ্যাভাটার' }}
                            </label>
                            <span class="text-xs text-[var(--text-muted)]">
                                {{ locale === 'en' ? 'Choose preset or upload custom image' : 'আইকন পছন্দ করুন বা ছবি আপলোড করুন' }}
                            </span>
                        </div>

                        <!-- Live Preview + Upload Button -->
                        <div class="mb-5 flex flex-wrap items-center gap-4 border-b border-[var(--border)]/60 pb-5">
                            <div class="relative">
                                <StudentAvatar :avatar="previewAvatar" :name="infoForm.name" size="xl" class="ring-4 ring-[var(--surface)] ring-offset-2 ring-offset-[var(--secondary)]/30" />
                                <span v-if="filePreview" class="absolute -bottom-1 -right-1 grid h-5 w-5 place-items-center rounded-full bg-[var(--primary)] text-white text-[10px]">
                                    ✓
                                </span>
                            </div>

                            <div class="flex-1 min-w-[200px]">
                                <p class="text-sm font-semibold">
                                    {{ filePreview ? (locale === 'en' ? 'Custom image selected' : 'কাস্টম ছবি সিলেক্ট করা হয়েছে') : (locale === 'en' ? 'Active Avatar' : 'বর্তমান অ্যাভাটার') }}
                                </p>
                                <p class="text-xs text-[var(--text-muted)] mt-0.5">
                                    {{ locale === 'en' ? 'Max 4MB (JPG, PNG, WebP)' : 'সর্বোচ্চ ৪ মেগাবাইট (JPG, PNG, WebP)' }}
                                </p>

                                <div class="mt-2.5 flex items-center gap-2">
                                    <label class="btn btn-secondary btn-sm cursor-pointer inline-flex items-center gap-1.5 rounded-xl">
                                        <ArrowUpTrayIcon class="h-4 w-4" />
                                        <span>{{ locale === 'en' ? 'Upload Image' : 'ছবি আপলোড করুন' }}</span>
                                        <input
                                            ref="fileInputRef"
                                            type="file"
                                            accept="image/png, image/jpeg, image/jpg, image/webp"
                                            class="hidden"
                                            @change="onFileChange"
                                        />
                                    </label>

                                    <button
                                        v-if="filePreview || (infoForm.avatar && (infoForm.avatar.startsWith('http') || infoForm.avatar.startsWith('/storage/')))"
                                        type="button"
                                        class="btn btn-outline btn-sm rounded-xl text-xs"
                                        @click="clearCustomImage"
                                    >
                                        <XMarkIcon class="h-3.5 w-3.5 mr-1" />
                                        {{ locale === 'en' ? 'Use Icon' : 'আইকনে ফিরুন' }}
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- 8 Preset Avatar Icons -->
                        <div>
                            <p class="mb-2.5 text-xs font-semibold uppercase tracking-wider text-[var(--text-muted)]">
                                {{ locale === 'en' ? 'Preset Icons' : 'আইকন প্রিসেটসমূহ' }}
                            </p>
                            <div class="grid grid-cols-4 sm:grid-cols-8 gap-2">
                                <button
                                    v-for="preset in presetAvatars"
                                    :key="preset.key"
                                    type="button"
                                    class="group relative flex flex-col items-center justify-center rounded-xl border p-2 transition-all hover:scale-105 active:scale-95"
                                    :class="infoForm.avatar === preset.key && !filePreview
                                        ? 'border-[var(--secondary)] bg-[var(--secondary)]/15 shadow-sm ring-2 ring-[var(--secondary)]/30'
                                        : 'border-[var(--border)] bg-[var(--surface)] hover:border-[var(--text-muted)]/40 hover:bg-[var(--surface2)]'"
                                    :title="preset.label"
                                    @click="selectPreset(preset.key)"
                                >
                                    <span class="text-2xl transition-transform group-hover:scale-110">{{ preset.emoji }}</span>
                                    <span class="mt-1 text-[10px] font-medium text-[var(--text-muted)] group-hover:text-[var(--text)] line-clamp-1">
                                        {{ preset.label }}
                                    </span>
                                    <span
                                        v-if="infoForm.avatar === preset.key && !filePreview"
                                        class="absolute -top-1 -right-1 h-3.5 w-3.5 rounded-full bg-[var(--secondary)] ring-2 ring-[var(--surface)]"
                                    />
                                </button>
                            </div>
                        </div>

                        <p v-if="infoForm.errors.avatar" class="mt-2 text-xs text-[var(--accent)]">{{ infoForm.errors.avatar }}</p>
                        <p v-if="infoForm.errors.avatar_file" class="mt-2 text-xs text-[var(--accent)]">{{ infoForm.errors.avatar_file }}</p>
                    </div>

                    <!-- Education Type Selection -->
                    <div class="mb-6 rounded-2xl border border-[var(--border)] bg-[var(--surface2)]/40 p-4 sm:p-5">
                        <div class="mb-3 flex items-center justify-between">
                            <label class="block text-sm font-semibold flex items-center gap-2">
                                <AcademicCapIcon class="h-4.5 w-4.5 text-[var(--primary)]" />
                                {{ locale === 'en' ? 'Education Stream / Target Goal' : 'প্রস্তুতি ও শিক্ষা ক্যাটাগরি' }}
                            </label>
                            <span class="text-xs text-[var(--text-muted)]">
                                {{ locale === 'en' ? 'Select your cohort' : 'আপনার লক্ষ্য বেছে নিন' }}
                            </span>
                        </div>

                        <p class="mb-3 text-xs text-[var(--text-muted)]">
                            {{ locale === 'en'
                                ? 'Your selected stream determines your cohort leaderboard and customized study material.'
                                : 'আপনার নির্বাচিত ক্যাটাগরির ওপর ভিত্তি করে আপনার লিডারবোর্ড ও প্রাসঙ্গিক কনটেন্ট সমন্বিত হবে।' }}
                        </p>

                        <div class="space-y-2">
                            <label
                                v-for="type in educationTypes"
                                :key="type.id"
                                class="flex cursor-pointer items-start gap-3 rounded-xl border p-3.5 transition-all hover:bg-[var(--surface)]"
                                :class="infoForm.type === type.id
                                    ? 'border-[var(--primary)] bg-[var(--primary)]/10 shadow-sm ring-1 ring-[var(--primary)]/30'
                                    : 'border-[var(--border)] bg-[var(--surface)]/60'"
                            >
                                <input
                                    v-model="infoForm.type"
                                    type="radio"
                                    name="student_type"
                                    :value="type.id"
                                    class="mt-1 h-4 w-4 rounded-full accent-[var(--primary)]"
                                />
                                <div class="flex-1">
                                    <div class="flex flex-wrap items-center justify-between gap-1">
                                        <span class="font-heading text-sm font-bold">{{ type.title }}</span>
                                        <span class="rounded-full px-2 py-0.5 text-[10px] font-semibold" :class="type.color">
                                            {{ type.badge }}
                                        </span>
                                    </div>
                                    <p class="mt-0.5 text-xs text-[var(--text-muted)]">{{ type.subtitle }}</p>
                                </div>
                            </label>
                        </div>
                        <p v-if="infoForm.errors.type" class="mt-2 text-xs text-[var(--accent)]">{{ infoForm.errors.type }}</p>
                    </div>

                    <!-- Personal Information -->
                    <div class="mb-4">
                        <label class="mb-1.5 block text-sm font-medium">{{ t('profile_settings_page.name') }}</label>
                        <input v-model="infoForm.name" type="text" autocomplete="name" :class="inputClass" />
                        <p v-if="infoForm.errors.name" class="mt-1 text-xs text-[var(--accent)]">{{ infoForm.errors.name }}</p>
                    </div>

                    <div class="mb-6">
                        <label class="mb-1.5 block text-sm font-medium">{{ t('profile_settings_page.email') }}</label>
                        <input v-model="infoForm.email" type="email" autocomplete="email" :class="inputClass" />
                        <p v-if="infoForm.errors.email" class="mt-1 text-xs text-[var(--accent)]">{{ infoForm.errors.email }}</p>
                    </div>

                    <button
                        type="submit"
                        :disabled="infoForm.processing"
                        class="btn btn-primary rounded-xl px-5 font-semibold shadow-sm"
                    >
                        <span v-if="infoForm.processing">{{ locale === 'en' ? 'Saving...' : 'সংরক্ষণ হচ্ছে...' }}</span>
                        <span v-else>{{ t('profile_settings_page.save_info') }}</span>
                    </button>
                </form>
            </div>

            <!-- Update password (1 Col) -->
            <div>
                <form class="card anim-rise rounded-2xl p-6" @submit.prevent="savePassword">
                    <h2 class="mb-1 flex items-center gap-2 font-heading text-lg font-bold">
                        <KeyIcon class="h-5 w-5 text-[var(--primary)]" /> {{ t('profile_settings_page.password_title') }}
                    </h2>
                    <p class="mb-5 text-sm text-[var(--text-muted)]">{{ t('profile_settings_page.password_subtitle') }}</p>

                    <div class="mb-4">
                        <label class="mb-1.5 block text-sm font-medium">{{ t('profile_settings_page.current_password') }}</label>
                        <input v-model="passwordForm.current_password" type="password" autocomplete="current-password" :class="inputClass" />
                        <p v-if="passwordForm.errors.current_password" class="mt-1 text-xs text-[var(--accent)]">{{ passwordForm.errors.current_password }}</p>
                    </div>

                    <div class="mb-4">
                        <label class="mb-1.5 block text-sm font-medium">{{ t('profile_settings_page.new_password') }}</label>
                        <input v-model="passwordForm.password" type="password" autocomplete="new-password" :class="inputClass" />
                        <p v-if="passwordForm.errors.password" class="mt-1 text-xs text-[var(--accent)]">{{ passwordForm.errors.password }}</p>
                    </div>

                    <div class="mb-5">
                        <label class="mb-1.5 block text-sm font-medium">{{ t('profile_settings_page.confirm_password') }}</label>
                        <input v-model="passwordForm.password_confirmation" type="password" autocomplete="new-password" :class="inputClass" />
                        <p v-if="passwordForm.errors.password_confirmation" class="mt-1 text-xs text-[var(--accent)]">{{ passwordForm.errors.password_confirmation }}</p>
                    </div>

                    <button type="submit" :disabled="passwordForm.processing" class="btn btn-primary btn-sm rounded-xl">
                        {{ t('profile_settings_page.save_password') }}
                    </button>
                </form>
            </div>
        </div>
    </StudentLayout>
</template>
