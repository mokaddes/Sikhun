<script setup>
import { useForm, Head } from '@inertiajs/vue3';
import StudentLayout from '@/Components/Layout/StudentLayout.vue';
import { useI18n } from '@/i18n';
import { UserCircleIcon, KeyIcon } from '@heroicons/vue/24/outline';

const props = defineProps({ student: Object });
const { t, locale } = useI18n();

const infoForm = useForm({
    name: props.student.name ?? '',
    email: props.student.email ?? '',
});
function saveInfo() {
    infoForm.put('/profile/info', { preserveScroll: true });
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
            <span class="grid h-14 w-14 shrink-0 place-items-center rounded-full bg-gradient-to-br from-[var(--secondary)] to-[var(--primary)] text-xl font-bold text-white">
                {{ student.name?.charAt(0) }}
            </span>
            <div>
                <span class="eyebrow">{{ t('profile_settings_page.title') }}</span>
                <h1 class="mt-0.5 font-heading text-2xl font-extrabold">{{ t('profile_settings_page.title') }}</h1>
                <p class="text-sm text-[var(--text-muted)]">{{ t('profile_settings_page.subtitle') }}</p>
            </div>
        </header>

        <div class="grid max-w-3xl gap-6 md:grid-cols-2">
            <!-- Profile information -->
            <form class="card anim-rise rounded-2xl p-6" @submit.prevent="saveInfo">
                <h2 class="mb-1 flex items-center gap-2 font-heading text-lg font-bold">
                    <UserCircleIcon class="h-5 w-5 text-[var(--secondary)]" /> {{ t('profile_settings_page.info_title') }}
                </h2>
                <p class="mb-5 text-sm text-[var(--text-muted)]">{{ t('profile_settings_page.info_subtitle') }}</p>

                <div class="mb-4">
                    <label class="mb-1.5 block text-sm font-medium">{{ t('profile_settings_page.name') }}</label>
                    <input v-model="infoForm.name" type="text" autocomplete="name" :class="inputClass" />
                    <p v-if="infoForm.errors.name" class="mt-1 text-xs text-[var(--accent)]">{{ infoForm.errors.name }}</p>
                </div>

                <div class="mb-5">
                    <label class="mb-1.5 block text-sm font-medium">{{ t('profile_settings_page.email') }}</label>
                    <input v-model="infoForm.email" type="email" autocomplete="email" :class="inputClass" />
                    <p v-if="infoForm.errors.email" class="mt-1 text-xs text-[var(--accent)]">{{ infoForm.errors.email }}</p>
                </div>

                <button type="submit" :disabled="infoForm.processing" class="btn btn-primary btn-sm">
                    {{ t('profile_settings_page.save_info') }}
                </button>
            </form>

            <!-- Update password -->
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

                <button type="submit" :disabled="passwordForm.processing" class="btn btn-primary btn-sm">
                    {{ t('profile_settings_page.save_password') }}
                </button>
            </form>
        </div>
    </StudentLayout>
</template>
