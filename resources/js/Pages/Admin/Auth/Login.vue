<script setup>
import { Head, useForm } from '@inertiajs/vue3';
import BrandLogo from '@/Components/BrandLogo.vue';

const form = useForm({ email: '', password: '' });

function submit() {
    form.post('/admin/login', { onFinish: () => form.reset('password') });
}
</script>

<template>
    <Head title="Admin Login" />
    <div class="dark min-h-screen flex items-center justify-center bg-[var(--bg)] text-[var(--text)] px-5 relative overflow-hidden">
        <div class="absolute -top-40 -left-40 w-[480px] h-[480px] hero-glow"></div>
        <div class="absolute -bottom-40 -right-40 w-[480px] h-[480px] hero-glow"></div>
        <div class="w-full max-w-sm relative">
            <div class="flex items-center justify-center mb-8">
                <BrandLogo href="/admin" size="text-xl" suffix="Admin" suffix-class="text-sm font-normal text-[var(--text-muted)]" />
            </div>

            <form @submit.prevent="submit" class="space-y-5 bg-[var(--surface)] border border-[var(--border)] rounded-3xl p-8 shadow-[var(--shadow-lg)]">
                <div>
                    <label class="block text-sm font-medium mb-1.5 text-[var(--text-muted)]">Email</label>
                    <input v-model="form.email" type="email" required autofocus class="input" />
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1.5 text-[var(--text-muted)]">Password</label>
                    <input v-model="form.password" type="password" required class="input" />
                </div>
                <p v-if="form.errors.email" class="text-[var(--accent)] text-sm">{{ form.errors.email }}</p>
                <button type="submit" :disabled="form.processing" class="btn btn-primary w-full py-3">
                    {{ form.processing ? 'Signing in...' : 'Sign In' }}
                </button>
            </form>
        </div>
    </div>
</template>