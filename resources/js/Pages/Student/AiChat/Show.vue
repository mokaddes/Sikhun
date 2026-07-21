<script setup>
import { ref, nextTick, onMounted } from 'vue';
import { Head } from '@inertiajs/vue3';
import StudentLayout from '@/Components/Layout/StudentLayout.vue';
import { useI18n } from '@/i18n';

const props = defineProps({ session: Object });
const { t } = useI18n();

const messages = ref([...(props.session.messages ?? [])]);
const draft = ref('');
const streaming = ref(false);
const currentReply = ref('');
const scrollBox = ref(null);

function scrollToBottom() {
    nextTick(() => {
        if (scrollBox.value) scrollBox.value.scrollTop = scrollBox.value.scrollHeight;
    });
}

function send() {
    const text = draft.value.trim();
    if (!text || streaming.value) return;

    messages.value.push({ role: 'user', content: text });
    draft.value = '';
    streaming.value = true;
    currentReply.value = '';
    scrollToBottom();

    const es = new EventSource(`/ai/chat/${props.session.id}/stream?message=${encodeURIComponent(text)}`);

    es.onmessage = (event) => {
        const data = JSON.parse(event.data);

        if (data.error) {
            currentReply.value = data.error;
            es.close();
            streaming.value = false;
            return;
        }

        if (data.done) {
            messages.value.push({ role: 'assistant', content: currentReply.value });
            currentReply.value = '';
            es.close();
            streaming.value = false;
            scrollToBottom();
            return;
        }

        currentReply.value += data.content;
        scrollToBottom();
    };

    es.onerror = () => {
        es.close();
        streaming.value = false;
    };
}

onMounted(scrollToBottom);
</script>

<template>
    <Head :title="session.title" />
    <StudentLayout>
        <h1 class="font-heading text-xl font-bold mb-4">{{ session.title }}</h1>

        <div ref="scrollBox" class="rounded-xl border border-[var(--border)] bg-[var(--surface)] p-5 h-[55vh] overflow-y-auto mb-4 space-y-4">
            <div v-for="(m, i) in messages" :key="i" class="flex" :class="m.role === 'user' ? 'justify-end' : 'justify-start'">
                <div class="max-w-[75%] px-4 py-2.5 rounded-2xl text-sm whitespace-pre-wrap"
                    :class="m.role === 'user' ? 'bg-[var(--primary)] text-white' : 'bg-[var(--surface2)] text-[var(--text)]'">
                    {{ m.content }}
                </div>
            </div>
            <div v-if="streaming" class="flex justify-start">
                <div class="max-w-[75%] px-4 py-2.5 rounded-2xl text-sm whitespace-pre-wrap bg-[var(--surface2)] text-[var(--text)]">
                    {{ currentReply || t('ai_chat.thinking') }}
                </div>
            </div>
        </div>

        <form @submit.prevent="send" class="flex gap-2">
            <input v-model="draft" type="text" :placeholder="t('ai_chat.type_message')" :disabled="streaming"
                class="flex-1 px-4 py-3 rounded-lg bg-[var(--surface)] border border-[var(--border)] focus:outline-none focus:ring-2 focus:ring-[var(--primary)] disabled:opacity-60" />
            <button type="submit" :disabled="streaming || !draft.trim()" class="px-6 py-3 rounded-lg bg-[var(--primary)] hover:bg-[var(--primary-hover)] text-white font-semibold disabled:opacity-60">
                {{ t('ai_chat.send') }}
            </button>
        </form>
    </StudentLayout>
</template>
