<script setup>
import { ref } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import StudentLayout from '@/Components/Layout/StudentLayout.vue';
import { useI18n } from '@/i18n';

const props = defineProps({ sessions: Array, books: Array });
const { t } = useI18n();
const creating = ref(false);
const selectedBook = ref('');

function startNewChat() {
    creating.value = true;
    router.post('/ai/chat', { source_book_id: selectedBook.value || null }, { onFinish: () => (creating.value = false) });
}

function deleteChat(session) {
    if (confirm(t('ai_chat.delete_confirm'))) {
        router.delete(`/ai/chat/${session.id}`);
    }
}
</script>

<template>
    <Head :title="t('ai_chat.title')" />
    <StudentLayout>
        <div class="flex items-center justify-between mb-6">
            <h1 class="font-heading text-2xl font-extrabold">{{ t('ai_chat.title') }}</h1>
        </div>

        <div class="rounded-xl border border-[var(--border)] bg-[var(--surface)] p-5 mb-6 flex flex-wrap gap-3 items-center">
            <select v-model="selectedBook" class="px-4 py-2.5 rounded-lg bg-[var(--surface2)] border border-[var(--border)] text-sm flex-1 min-w-[200px]">
                <option value="">{{ t('ai_chat.no_book') }}</option>
                <option v-for="b in books" :key="b.id" :value="b.id">{{ b.title }}</option>
            </select>
            <button @click="startNewChat" :disabled="creating" class="px-5 py-2.5 rounded-lg bg-[var(--primary)] hover:bg-[var(--primary-hover)] text-white text-sm font-semibold disabled:opacity-60">
                {{ t('ai_chat.new_chat') }}
            </button>
        </div>

        <div v-if="sessions.length" class="space-y-2">
            <div v-for="s in sessions" :key="s.id" class="flex items-center justify-between rounded-xl border border-[var(--border)] bg-[var(--surface)] p-4">
                <Link :href="`/ai/chat/${s.id}`" class="flex-1">
                    <div class="font-medium text-sm">{{ s.title }}</div>
                    <div class="text-xs text-[var(--text-muted)]">{{ new Date(s.created_at).toLocaleDateString() }}</div>
                </Link>
                <button @click="deleteChat(s)" class="text-sm text-[var(--accent)] hover:underline">{{ t('common.delete') }}</button>
            </div>
        </div>
        <div v-else class="rounded-xl border border-dashed border-[var(--border)] p-12 text-center text-[var(--text-muted)]">
            {{ t('ai_chat.no_sessions') }}
        </div>
    </StudentLayout>
</template>
