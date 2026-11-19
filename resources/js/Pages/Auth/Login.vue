<script setup>
import { ref } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import PublicLayout from '@/Components/Layout/PublicLayout.vue';
import { useI18n } from '@/i18n';

const { t, locale } = useI18n();
const loginMode = ref('email');
const otpSent = ref(false);
const otpSending = ref(false);
const otpMessage = ref('');
const otpError = ref('');

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const otpForm = useForm({
    phone: '',
    code: '',
});

function submit() {
    form.post('/login', { onFinish: () => form.reset('password') });
}

async function sendOtp() {
    otpSending.value = true;
    otpError.value = '';
    otpMessage.value = '';
    try {
        const res = await fetch('/otp/send', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                'Accept': 'application/json',
            },
            body: JSON.stringify({ phone: otpForm.phone, purpose: 'login' }),
        });
        const data = await res.json();
        if (!res.ok || !data.ok) {
            otpError.value = data.message || 'Could not send OTP.';
        } else {
            otpSent.value = true;
            otpMessage.value = data.message || 'OTP sent!';
        }
    } catch {
        otpError.value = 'Network error while sending OTP.';
    } finally {
        otpSending.value = false;
    }
}

function submitOtp() {
    otpForm.post('/otp/login');
}
</script>

<template>
    <Head :title="t('auth.login_title')" />
    <PublicLayout>
        <div class="max-w-md mx-auto px-5 py-16">
            <h1 class="font-heading text-3xl font-extrabold mb-2">{{ t('auth.login_title') }}</h1>
            <p class="text-[var(--text-muted)] mb-6">{{ t('auth.login_subtitle') }}</p>

            <div v-if="$page.props.site?.sms_otp_enabled" class="grid grid-cols-2 gap-2 p-1 rounded-xl bg-[var(--surface)] border border-[var(--border)] mb-6">
                <button type="button" @click="loginMode = 'email'"
                    class="py-2 rounded-lg text-sm font-semibold transition"
                    :class="loginMode === 'email' ? 'bg-[var(--primary)] text-white' : 'text-[var(--text-muted)]'">
                    {{ locale === 'bn' ? 'ইমেইল' : 'Email' }}
                </button>
                <button type="button" @click="loginMode = 'otp'"
                    class="py-2 rounded-lg text-sm font-semibold transition"
                    :class="loginMode === 'otp' ? 'bg-[var(--primary)] text-white' : 'text-[var(--text-muted)]'">
                    {{ locale === 'bn' ? 'ফোন OTP' : 'Phone OTP' }}
                </button>
            </div>

            <form v-if="loginMode === 'otp' && $page.props.site?.sms_otp_enabled" @submit.prevent="submitOtp" class="space-y-5">
                <div>
                    <label for="login-phone" class="block text-sm font-medium mb-1.5">{{ locale === 'bn' ? 'মোবাইল নম্বর' : 'Mobile Number' }}</label>
                    <div class="flex gap-2">
                        <input id="login-phone" v-model="otpForm.phone" type="tel" required placeholder="017XXXXXXXX"
                            class="flex-1 px-4 py-2.5 rounded-lg bg-[var(--surface)] border border-[var(--border)] focus:outline-none focus:ring-2 focus:ring-[var(--primary)]" />
                        <button type="button" @click="sendOtp" :disabled="otpSending || !otpForm.phone"
                            class="px-4 py-2.5 rounded-lg bg-[var(--surface2)] border border-[var(--border)] text-sm font-semibold hover:border-[var(--primary)] disabled:opacity-50">
                            {{ otpSending ? '...' : (otpSent ? (locale === 'bn' ? 'পুনরায় পাঠান' : 'Resend') : (locale === 'bn' ? 'OTP পাঠান' : 'Send OTP')) }}
                        </button>
                    </div>
                </div>
                <div v-if="otpSent">
                    <label for="login-otp-code" class="block text-sm font-medium mb-1.5">{{ locale === 'bn' ? '৬-ডিজিটের OTP কোড' : '6-Digit OTP Code' }}</label>
                    <input id="login-otp-code" v-model="otpForm.code" type="text" maxlength="6" required placeholder="123456"
                        class="w-full px-4 py-2.5 rounded-lg bg-[var(--surface)] border border-[var(--border)] focus:outline-none focus:ring-2 focus:ring-[var(--primary)]" />
                </div>
                <p v-if="otpMessage" class="text-emerald-500 text-sm">{{ otpMessage }}</p>
                <p v-if="otpError || otpForm.errors.phone || otpForm.errors.code" class="text-[var(--accent)] text-sm">
                    {{ otpError || otpForm.errors.phone || otpForm.errors.code }}
                </p>
                <button v-if="otpSent" type="submit" :disabled="otpForm.processing"
                    class="w-full py-3 rounded-lg bg-[var(--primary)] hover:bg-[var(--primary-hover)] text-white font-semibold transition-colors disabled:opacity-60">
                    {{ otpForm.processing ? t('auth.submitting') : t('auth.sign_in') }}
                </button>
            </form>

            <form v-else @submit.prevent="submit" class="space-y-5">
                <div>
                    <label for="login-email" class="block text-sm font-medium mb-1.5">{{ t('common.email') }}</label>
                    <input id="login-email" name="email" v-model="form.email" type="email" required autofocus
                        autocomplete="username" :aria-invalid="!!form.errors.email"
                        class="w-full px-4 py-2.5 rounded-lg bg-[var(--surface)] border border-[var(--border)] focus:outline-none focus:ring-2 focus:ring-[var(--primary)]" />
                </div>

                <div>
                    <label for="login-password" class="block text-sm font-medium mb-1.5">{{ t('auth.password') }}</label>
                    <input id="login-password" name="password" v-model="form.password" type="password" required
                        autocomplete="current-password"
                        class="w-full px-4 py-2.5 rounded-lg bg-[var(--surface)] border border-[var(--border)] focus:outline-none focus:ring-2 focus:ring-[var(--primary)]" />
                </div>

                <p v-if="form.errors.email" class="text-[var(--accent)] text-sm">{{ form.errors.email }}</p>

                <div class="-mt-2 text-right">
                    <Link href="/forgot-password" class="text-xs font-medium text-[var(--primary)] hover:underline">
                        {{ t('auth.forgot_password') }}
                    </Link>
                </div>

                <label class="flex items-center gap-2 text-sm text-[var(--text-muted)]">
                    <input v-model="form.remember" type="checkbox" class="rounded" />
                    {{ t('auth.remember_me') }}
                </label>

                <button type="submit" :disabled="form.processing"
                    class="w-full py-3 rounded-lg bg-[var(--primary)] hover:bg-[var(--primary-hover)] text-white font-semibold transition-colors disabled:opacity-60">
                    {{ form.processing ? t('auth.submitting') : t('auth.sign_in') }}
                </button>
            </form>

            <div v-if="$page.props.site?.oauth_google_enabled || $page.props.site?.oauth_facebook_enabled" class="mt-6 space-y-2.5">
                <div class="relative flex py-2 items-center">
                    <div class="flex-grow border-t border-[var(--border)]"></div>
                    <span class="flex-shrink mx-3 text-xs text-[var(--text-muted)] uppercase">{{ locale === 'bn' ? 'অথবা' : 'OR' }}</span>
                    <div class="flex-grow border-t border-[var(--border)]"></div>
                </div>
                <a v-if="$page.props.site?.oauth_google_enabled" href="/auth/google/redirect"
                    class="flex w-full items-center justify-center gap-2 rounded-lg border border-[var(--border)] bg-[var(--surface)] px-4 py-2.5 text-sm font-semibold hover:border-[var(--primary)] transition">
                    <span>{{ locale === 'bn' ? 'Google দিয়ে লগইন করুন' : 'Continue with Google' }}</span>
                </a>
                <a v-if="$page.props.site?.oauth_facebook_enabled" href="/auth/facebook/redirect"
                    class="flex w-full items-center justify-center gap-2 rounded-lg border border-[var(--border)] bg-[var(--surface)] px-4 py-2.5 text-sm font-semibold hover:border-[var(--primary)] transition">
                    <span>{{ locale === 'bn' ? 'Facebook দিয়ে লগইন করুন' : 'Continue with Facebook' }}</span>
                </a>
            </div>

            <p class="text-center text-sm text-[var(--text-muted)] mt-6">
                {{ t('auth.no_account') }} <Link href="/register" class="text-[var(--primary)] font-medium">{{ t('auth.sign_up') }}</Link>
            </p>
        </div>
    </PublicLayout>
</template>
