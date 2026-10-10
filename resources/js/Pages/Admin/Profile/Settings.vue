<script setup>
import { useForm, Head } from '@inertiajs/vue3';
import AdminLayout from '@/Components/Layout/AdminLayout.vue';
import { UserCircleIcon, KeyIcon, ShieldCheckIcon } from '@heroicons/vue/24/outline';

const props = defineProps({ admin: Object });

const infoForm = useForm({
    name: props.admin.name ?? '',
    email: props.admin.email ?? '',
});
function saveInfo() {
    infoForm.put('/admin/profile/info', { preserveScroll: true });
}

const passwordForm = useForm({
    current_password: '',
    password: '',
    password_confirmation: '',
});
function savePassword() {
    passwordForm.put('/admin/profile/password', {
        preserveScroll: true,
        onSuccess: () => passwordForm.reset(),
    });
}

const inputClass = 'input';
const roleLabel = (role) => String(role ?? '').replace(/_/g, ' ').replace(/\b\w/g, (c) => c.toUpperCase());
</script>

<template>
    <Head title="Admin Profile" />
    <AdminLayout>
        <header class="anim-rise mb-6 flex items-center gap-4">
            <span class="grid h-14 w-14 shrink-0 place-items-center rounded-full bg-gradient-to-br from-[var(--secondary)] to-[var(--primary)] text-xl font-bold text-white">
                {{ admin.name?.charAt(0) ?? 'A' }}
            </span>
            <div>
                <span class="eyebrow">Admin Account</span>
                <h1 class="mt-0.5 font-heading text-2xl font-extrabold">Profile Settings</h1>
                <p class="flex items-center gap-1.5 text-sm text-[var(--text-muted)]">
                    <ShieldCheckIcon class="h-4 w-4" /> {{ roleLabel(admin.role) }}
                </p>
            </div>
        </header>

        <div class="grid max-w-3xl gap-6 md:grid-cols-2">
            <!-- Profile information -->
            <form class="card anim-rise rounded-2xl p-6" @submit.prevent="saveInfo">
                <h2 class="mb-1 flex items-center gap-2 font-heading text-lg font-bold">
                    <UserCircleIcon class="h-5 w-5 text-[var(--secondary)]" /> Profile Information
                </h2>
                <p class="mb-5 text-sm text-[var(--text-muted)]">Update your name and email address.</p>

                <div class="mb-4">
                    <label class="label">Name</label>
                    <input v-model="infoForm.name" type="text" autocomplete="name" :class="inputClass" />
                    <p v-if="infoForm.errors.name" class="mt-1 text-xs text-[var(--accent)]">{{ infoForm.errors.name }}</p>
                </div>

                <div class="mb-5">
                    <label class="label">Email address</label>
                    <input v-model="infoForm.email" type="email" autocomplete="email" :class="inputClass" />
                    <p v-if="infoForm.errors.email" class="mt-1 text-xs text-[var(--accent)]">{{ infoForm.errors.email }}</p>
                </div>

                <button type="submit" :disabled="infoForm.processing" class="btn btn-primary btn-sm">
                    Save Changes
                </button>
            </form>

            <!-- Update password -->
            <form class="card anim-rise rounded-2xl p-6" @submit.prevent="savePassword">
                <h2 class="mb-1 flex items-center gap-2 font-heading text-lg font-bold">
                    <KeyIcon class="h-5 w-5 text-[var(--primary)]" /> Update Password
                </h2>
                <p class="mb-5 text-sm text-[var(--text-muted)]">Use a long, random password to stay secure.</p>

                <div class="mb-4">
                    <label class="label">Current Password</label>
                    <input v-model="passwordForm.current_password" type="password" autocomplete="current-password" :class="inputClass" />
                    <p v-if="passwordForm.errors.current_password" class="mt-1 text-xs text-[var(--accent)]">{{ passwordForm.errors.current_password }}</p>
                </div>

                <div class="mb-4">
                    <label class="label">New Password</label>
                    <input v-model="passwordForm.password" type="password" autocomplete="new-password" :class="inputClass" />
                    <p v-if="passwordForm.errors.password" class="mt-1 text-xs text-[var(--accent)]">{{ passwordForm.errors.password }}</p>
                </div>

                <div class="mb-5">
                    <label class="label">Confirm Password</label>
                    <input v-model="passwordForm.password_confirmation" type="password" autocomplete="new-password" :class="inputClass" />
                    <p v-if="passwordForm.errors.password_confirmation" class="mt-1 text-xs text-[var(--accent)]">{{ passwordForm.errors.password_confirmation }}</p>
                </div>

                <button type="submit" :disabled="passwordForm.processing" class="btn btn-primary btn-sm">
                    Update Password
                </button>
            </form>
        </div>
    </AdminLayout>
</template>
