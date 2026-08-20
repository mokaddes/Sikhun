<script setup>
import { ref } from 'vue';
import { Head, useForm, router } from '@inertiajs/vue3';
import AdminLayout from '@/Components/Layout/AdminLayout.vue';

const props = defineProps({ conversation: Object });
const form = useForm({ message: '' });

function reply() {
    form.post(`/admin/support/${props.conversation.id}/reply`, { preserveScroll: true, onSuccess: () => form.reset() });
}
function toggleBot() {
    router.post(`/admin/support/${props.conversation.id}/toggle-bot`, {}, { preserveScroll: true });
}
function close() {
    router.post(`/admin/support/${props.conversation.id}/close`, {}, { preserveScroll: true });
}
</script>

<template>
    <Head title="Conversation" />
    <AdminLayout>
        <div class="flex items-center justify-between mb-6">
            <h1 class="font-heading text-xl font-bold">{{ conversation.student?.name ?? 'Guest visitor' }}</h1>
            <div class="flex gap-2">
                <button @click="toggleBot" class="px-3 py-1.5 rounded-lg border border-[var(--border)] text-xs font-medium">
                    Bot: {{ conversation.bot_enabled ? 'On' : 'Off' }} (toggle)
                </button>
                <button v-if="conversation.status === 'open'" @click="close" class="px-3 py-1.5 rounded-lg border border-[var(--border)] text-xs font-medium">
                    Close conversation
                </button>
            </div>
        </div>

        <div class="rounded-xl border border-[var(--border)] bg-[var(--surface)] p-5 h-96 overflow-y-auto space-y-3 mb-4">
            <div v-for="m in conversation.messages" :key="m.id" class="flex" :class="m.sender_type === 'student' ? 'justify-start' : 'justify-end'">
                <div class="max-w-[70%] px-3 py-2 rounded-xl text-sm"
                    :class="{
                        'bg-[#111a15]': m.sender_type === 'student',
                        'bg-[var(--primary)]/20 text-[var(--primary)]': m.sender_type === 'bot',
                        'bg-[var(--secondary)]/20 text-[var(--secondary)]': m.sender_type === 'admin',
                    }">
                    <div class="text-[10px] uppercase opacity-60 mb-1">{{ m.sender_type }}</div>
                    {{ m.message }}
                </div>
            </div>
        </div>

        <form @submit.prevent="reply" class="flex gap-2">
            <input v-model="form.message" type="text" placeholder="Type a manual reply..." class="flex-1 px-4 py-2.5 rounded-lg bg-[#111a15] border border-[var(--border)] text-white text-sm" />
            <button type="submit" :disabled="form.processing" class="px-5 py-2.5 rounded-lg bg-[var(--primary)] text-white text-sm font-semibold disabled:opacity-60">Send</button>
        </form>
    </AdminLayout>
</template>
