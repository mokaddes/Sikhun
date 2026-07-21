<script setup>
import { Head, useForm } from '@inertiajs/vue3';

const form = useForm({ email: '', password: '' });

function submit() {
    form.post('/admin/login', { onFinish: () => form.reset('password') });
}
</script>

<template>
    <Head title="Admin Login" />
    <div class="dark min-h-screen flex items-center justify-center bg-[#09090f] text-[#e8e8f0] px-5">
        <div class="w-full max-w-sm">
            <div class="flex items-center gap-2 justify-center mb-8 font-heading font-extrabold text-xl">
                <span class="w-9 h-9 rounded-lg bg-gradient-to-br from-[#6c63ff] to-[#00d4aa] flex items-center justify-center text-white text-sm">শি</span>
                Sikhun <span class="text-sm font-normal text-[#7a7a9a]">Admin</span>
            </div>

            <form @submit.prevent="submit" class="space-y-5 bg-[#111118] border border-[#2a2a38] rounded-2xl p-8">
                <div>
                    <label class="block text-sm font-medium mb-1.5 text-[#9a9ab8]">Email</label>
                    <input v-model="form.email" type="email" required autofocus
                        class="w-full px-4 py-2.5 rounded-lg bg-[#18181f] border border-[#2a2a38] text-white focus:outline-none focus:ring-2 focus:ring-[#6c63ff]" />
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1.5 text-[#9a9ab8]">Password</label>
                    <input v-model="form.password" type="password" required
                        class="w-full px-4 py-2.5 rounded-lg bg-[#18181f] border border-[#2a2a38] text-white focus:outline-none focus:ring-2 focus:ring-[#6c63ff]" />
                </div>
                <p v-if="form.errors.email" class="text-[#ff6b6b] text-sm">{{ form.errors.email }}</p>
                <button type="submit" :disabled="form.processing"
                    class="w-full py-3 rounded-lg bg-[#6c63ff] hover:bg-[#5b53ee] text-white font-semibold transition-colors disabled:opacity-60">
                    {{ form.processing ? 'Signing in...' : 'Sign In' }}
                </button>
            </form>
        </div>
    </div>
</template>
