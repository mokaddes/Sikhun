<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import { computed, onMounted } from 'vue';
import PublicLayout from '@/Components/Layout/PublicLayout.vue';
import { useI18n } from '@/i18n';
import { CheckCircleIcon, SparklesIcon } from '@heroicons/vue/24/outline';

const props = defineProps({
    plans: {
        type: Array,
        default: () => [],
    },
    defaultPlanId: [String, Number],
});

const { t, locale } = useI18n();

const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
    type: 'hsc',
    plan_id: null,
});

onMounted(() => {
    if (props.defaultPlanId) {
        form.plan_id = Number(props.defaultPlanId);
    } else if (props.plans?.length) {
        const freePlan = props.plans.find(p => p.is_free || Number(p.price_monthly) === 0);
        form.plan_id = freePlan ? freePlan.id : props.plans[0].id;
    }
});

const types = computed(() => [
    { value: 'ssc', label: t('auth.type_ssc') },
    { value: 'hsc', label: t('auth.type_hsc') },
    { value: 'admission', label: locale.value === 'bn' ? 'ভর্তি প্রস্তুতি' : 'Admission' },
    { value: 'university', label: t('auth.type_university') },
    { value: 'job', label: t('auth.type_job') },
]);

function submit() {
    form.post('/register', { onFinish: () => form.reset('password', 'password_confirmation') });
}
</script>

<template>
    <Head :title="t('auth.register_title')" />
    <PublicLayout>
        <div class="max-w-lg mx-auto px-5 py-12 md:py-16">
            <h1 class="font-heading text-3xl font-extrabold mb-2">{{ t('auth.register_title') }}</h1>
            <p class="text-[var(--text-muted)] text-sm mb-6">{{ locale === 'en' ? 'Create your account and start with our Free Plan or choose your target plan.' : 'অ্যাকাউন্ট তৈরি করে সরাসরি ফ্রি প্ল্যানে শুরু করুন অথবা আপনার পছন্দের প্ল্যান নির্বাচন করুন।' }}</p>

            <form @submit.prevent="submit" class="space-y-4">
                <div>
                    <label class="block text-xs font-bold mb-1">{{ t('auth.full_name') }} <span class="text-rose-500">*</span></label>
                    <input v-model="form.name" type="text" required
                        class="w-full px-4 py-2.5 text-sm rounded-xl bg-[var(--surface)] border border-[var(--border)] focus:outline-none focus:ring-2 focus:ring-[var(--primary)]" />
                    <p v-if="form.errors.name" class="text-rose-500 text-xs mt-1">{{ form.errors.name }}</p>
                </div>

                <div>
                    <label class="block text-xs font-bold mb-1">{{ t('common.email') }} <span class="text-rose-500">*</span></label>
                    <input v-model="form.email" type="email" required
                        class="w-full px-4 py-2.5 text-sm rounded-xl bg-[var(--surface)] border border-[var(--border)] focus:outline-none focus:ring-2 focus:ring-[var(--primary)]" />
                    <p v-if="form.errors.email" class="text-rose-500 text-xs mt-1">{{ form.errors.email }}</p>
                </div>

                <div>
                    <label class="block text-xs font-bold mb-1">{{ t('auth.i_am_a') }}</label>
                    <div class="grid grid-cols-2 sm:grid-cols-5 gap-2">
                        <button
                            v-for="tOpt in types" :key="tOpt.value" type="button"
                            @click="form.type = tOpt.value"
                            class="px-2 py-2 rounded-xl border text-xs font-bold transition-all text-center"
                            :class="form.type === tOpt.value
                                ? 'bg-[var(--primary)] border-[var(--primary)] text-white shadow-xs'
                                : 'bg-[var(--surface)] border-[var(--border)] text-[var(--text-muted)] hover:border-[var(--primary)]/50'"
                        >
                            {{ tOpt.label }}
                        </button>
                    </div>
                </div>

                <!-- Plan Selection -->
                <div v-if="plans?.length">
                    <label class="block text-xs font-bold mb-1 flex items-center justify-between">
                        <span>{{ locale === 'en' ? 'Choose Your Plan:' : 'আপনার পছন্দের প্ল্যান নির্বাচন করুন:' }}</span>
                        <span class="text-[11px] text-emerald-600 dark:text-emerald-400 font-semibold flex items-center gap-1">
                            <CheckCircleIcon class="w-3.5 h-3.5" /> {{ locale === 'en' ? 'Default: Free Plan (৳0)' : 'ডিফল্ট: ফ্রি প্ল্যান (৳০)' }}
                        </span>
                    </label>
                    <div class="grid grid-cols-2 gap-2">
                        <button
                            v-for="p in plans"
                            :key="p.id"
                            type="button"
                            @click="form.plan_id = p.id"
                            class="p-2.5 rounded-xl border text-left transition-all relative flex flex-col justify-between"
                            :class="form.plan_id === p.id ? 'border-[var(--primary)] bg-[var(--primary)]/10 ring-2 ring-[var(--primary)]/30' : 'border-[var(--border)] bg-[var(--surface)] hover:border-[var(--primary)]/40'"
                        >
                            <div class="text-xs font-bold text-[var(--text)] flex items-center justify-between">
                                <span>{{ p.name }}</span>
                                <span class="font-extrabold text-[11px]" :class="Number(p.price_monthly) === 0 ? 'text-emerald-500' : 'text-[var(--primary)]'">
                                    {{ Number(p.price_monthly) === 0 ? (locale === 'en' ? 'Free (৳0)' : 'ফ্রি (৳০)') : `৳${p.price_monthly}` }}
                                </span>
                            </div>
                            <div class="text-[10px] text-[var(--text-muted)] mt-1 truncate">
                                {{ Number(p.price_monthly) === 0 ? (locale === 'en' ? 'Basic tools & reading' : 'বেসিক ফ্রি স্টাডি অ্যাসিস্ট্যান্ট') : `${p.ai_chat_minutes}মিনিট AI + ${p.ai_vision_limit || 0}ছবি` }}
                            </div>
                        </button>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold mb-1">{{ t('auth.password') }} <span class="text-rose-500">*</span></label>
                        <input v-model="form.password" type="password" required
                            class="w-full px-4 py-2.5 text-sm rounded-xl bg-[var(--surface)] border border-[var(--border)] focus:outline-none focus:ring-2 focus:ring-[var(--primary)]" />
                        <p v-if="form.errors.password" class="text-rose-500 text-xs mt-1">{{ form.errors.password }}</p>
                    </div>

                    <div>
                        <label class="block text-xs font-bold mb-1">{{ t('auth.confirm_password') }} <span class="text-rose-500">*</span></label>
                        <input v-model="form.password_confirmation" type="password" required
                            class="w-full px-4 py-2.5 text-sm rounded-xl bg-[var(--surface)] border border-[var(--border)] focus:outline-none focus:ring-2 focus:ring-[var(--primary)]" />
                    </div>
                </div>

                <button type="submit" :disabled="form.processing"
                    class="w-full py-3 rounded-xl bg-[var(--primary)] hover:bg-[var(--primary-hover)] text-white font-bold transition-all disabled:opacity-60 shadow-md shadow-[var(--primary)]/20 text-sm mt-2">
                    {{ form.processing ? t('auth.submitting') : (locale === 'en' ? 'Complete Registration' : 'নিবন্ধন সম্পন্ন করুন') }}
                </button>
            </form>

            <p class="text-center text-sm text-[var(--text-muted)] mt-6">
                {{ t('auth.already_have_account') }} <Link href="/login" class="text-[var(--primary)] font-bold">{{ t('auth.sign_in') }}</Link>
            </p>
        </div>
    </PublicLayout>
</template>
