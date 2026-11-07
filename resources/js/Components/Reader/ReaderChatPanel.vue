<script setup>
import { ref, computed, nextTick, watch } from 'vue';
import { usePage } from '@inertiajs/vue3';
import { useI18n } from '@/i18n';
import { ArrowUpIcon, BookOpenIcon, XMarkIcon } from '@heroicons/vue/24/outline';
import MarkdownIt from 'markdown-it';

const md = new MarkdownIt({
    html: false, // Prevent raw HTML injection
    linkify: true,
    breaks: true,
});

function renderMarkdown(content) {
    if (!content) return '';
    return md.render(content);
}

const props = defineProps({
    // Full POST endpoint for the streaming chat, e.g. `/library/3/read/chat`
    chatUrl: { type: String, required: true },
    // Current page number in the book (emitted by FlipReader).
    page: { type: Number, default: 1 },
});

const emit = defineEmits(['close']);

const { t } = useI18n();
const inertia = usePage();
const csrfToken = computed(() => inertia.props.csrf_token ?? '');

const messages = ref([]);
const draft = ref('');
const streaming = ref(false);
const currentReply = ref('');
const scrollBox = ref(null);

watch(
    () => props.page,
    () => {
        scrollToBottom();
    },
);

function scrollToBottom() {
    nextTick(() => {
        if (scrollBox.value) scrollBox.value.scrollTop = scrollBox.value.scrollHeight;
    });
}

async function send() {
    const text = draft.value.trim();
    if (!text || streaming.value) return;

    messages.value.push({ role: 'user', content: text });
    draft.value = '';
    streaming.value = true;
    currentReply.value = '';
    scrollToBottom();

    const formData = new FormData();
    formData.append('message', text);
    formData.append('page', props.page);

    const finalize = () => {
        messages.value.push({ role: 'assistant', content: currentReply.value || '…' });
        currentReply.value = '';
        streaming.value = false;
        scrollToBottom();
    };

    try {
        const res = await fetch(props.chatUrl, {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': csrfToken.value, 'Accept': 'text/event-stream' },
            body: formData,
        });

        if (!res.ok) {
            const body = await res.text().catch(() => '');
            currentReply.value = body || ('HTTP ' + res.status);
            finalize();
            return;
        }

        const reader = res.body.getReader();
        const decoder = new TextDecoder();
        let buffer = '';

        const handleEvent = (data) => {
            if (data.error) { currentReply.value = data.error; finalize(); return true; }
            if (data.done) { finalize(); return true; }
            currentReply.value += data.content ?? '';
            scrollToBottom();
            return false;
        };

        while (true) {
            const { done, value } = await reader.read();
            if (done) break;
            buffer += decoder.decode(value, { stream: true });
            const events = buffer.split('\n\n');
            buffer = events.pop();
            for (const evt of events) {
                for (const line of evt.split('\n')) {
                    if (!line.startsWith('data:')) continue;
                    if (handleEvent(JSON.parse(line.slice(5).trim()))) return;
                }
            }
        }

        if (buffer.trim()) {
            for (const line of buffer.split('\n')) {
                if (!line.startsWith('data:')) continue;
                if (handleEvent(JSON.parse(line.slice(5).trim()))) return;
            }
        }

        finalize();
    } catch (e) {
        currentReply.value = 'Network error: ' + (e.message || 'unknown');
        finalize();
    }
}

function onComposerKeydown(e) {
    if (e.key === 'Enter' && !e.shiftKey) {
        e.preventDefault();
        send();
    }
}
</script>

<template>
    <div class="flex flex-col w-full max-w-sm rounded-2xl border border-[var(--border)] bg-[var(--surface)] shadow-xl overflow-hidden lg:h-[calc(100dvh-11rem)]">
        <div class="flex items-center justify-between gap-2 px-4 py-3 border-b border-[var(--border)] bg-[var(--primary)] text-white">
            <div class="flex items-center gap-2 min-w-0">
                <BookOpenIcon class="w-4.5 h-4.5 shrink-0" />
                <span class="font-heading font-semibold text-sm truncate">{{ t('reader.chat_title') }}</span>
                <span class="shrink-0 text-[11px] px-2 py-0.5 rounded-full bg-white/20">{{ t('reader.current_page', { current: props.page }) }}</span>
            </div>
            <button class="shrink-0 icon-btn w-7 h-7 !text-white hover:!bg-white/20" aria-label="Close" @click="emit('close')">
                <XMarkIcon class="w-4 h-4" />
            </button>
        </div>

        <div ref="scrollBox" class="flex-1 overflow-y-auto p-3 space-y-3 min-h-40">
            <p v-if="!messages.length && !streaming" class="text-xs text-[var(--text-muted)] text-center my-4 leading-relaxed">
                {{ t('reader.chat_hint') }}
            </p>

            <div v-for="(m, i) in messages" :key="i" class="flex" :class="m.role === 'user' ? 'justify-end' : 'justify-start'">
                <div v-if="m.role === 'user'" class="max-w-[85%] px-3.5 py-2.5 rounded-2xl rounded-br-md text-xs whitespace-pre-wrap break-words bg-[var(--primary)] text-white shadow-xs">
                    {{ m.content }}
                </div>
                <div v-else class="max-w-[88%] px-3.5 py-2.5 rounded-2xl rounded-bl-md text-xs break-words bg-[var(--surface2)] border border-[var(--border)] text-[var(--text)] chat-markdown shadow-xs"
                    v-html="renderMarkdown(m.content)">
                </div>
            </div>

            <div v-if="streaming" class="flex justify-start">
                <div class="max-w-[88%] bg-[var(--surface2)] border border-[var(--border)] rounded-2xl rounded-bl-md px-3.5 py-2.5 text-xs break-words chat-markdown shadow-xs"
                    v-html="renderMarkdown(currentReply || t('ai_chat.thinking'))">
                </div>
            </div>
        </div>

        <form @submit.prevent="send" class="p-2 border-t border-[var(--border)] flex gap-2">
            <input
                v-model="draft"
                type="text"
                :placeholder="t('ai_chat.type_message')"
                :disabled="streaming"
                class="flex-1 px-3 py-2 rounded-lg bg-[var(--surface2)] border border-[var(--border)] text-xs focus:outline-none"
                @keydown="onComposerKeydown"
            />
            <button type="submit" :disabled="streaming || !draft.trim()" class="px-3 py-2 rounded-lg bg-[var(--primary)] text-white text-xs font-semibold disabled:opacity-60">
                <ArrowUpIcon class="w-4 h-4" />
            </button>
        </form>
    </div>
</template>

<style scoped>
:deep(.chat-markdown) {
    line-height: 1.55;
}
:deep(.chat-markdown p) {
    margin-bottom: 0.5rem;
}
:deep(.chat-markdown p:last-child) {
    margin-bottom: 0;
}
:deep(.chat-markdown h1),
:deep(.chat-markdown h2),
:deep(.chat-markdown h3),
:deep(.chat-markdown h4) {
    font-weight: 700;
    color: var(--text);
    margin-top: 0.65rem;
    margin-bottom: 0.35rem;
    line-height: 1.3;
}
:deep(.chat-markdown h1) { font-size: 0.95rem; }
:deep(.chat-markdown h2) { font-size: 0.88rem; }
:deep(.chat-markdown h3) { font-size: 0.82rem; }
:deep(.chat-markdown h4) { font-size: 0.78rem; }
:deep(.chat-markdown strong) {
    font-weight: 700;
    color: var(--text);
}
:deep(.chat-markdown ul) {
    list-style-type: disc;
    margin-left: 1.1rem;
    margin-top: 0.35rem;
    margin-bottom: 0.5rem;
}
:deep(.chat-markdown ol) {
    list-style-type: decimal;
    margin-left: 1.1rem;
    margin-top: 0.35rem;
    margin-bottom: 0.5rem;
}
:deep(.chat-markdown li) {
    margin-bottom: 0.25rem;
}
:deep(.chat-markdown code) {
    font-family: monospace;
    font-size: 0.75rem;
    padding: 0.15rem 0.35rem;
    border-radius: 0.35rem;
    background-color: color-mix(in srgb, var(--text) 8%, transparent);
}
:deep(.chat-markdown pre) {
    font-family: monospace;
    font-size: 0.75rem;
    padding: 0.5rem;
    border-radius: 0.5rem;
    background-color: color-mix(in srgb, var(--text) 8%, transparent);
    overflow-x: auto;
    margin: 0.5rem 0;
}
:deep(.chat-markdown blockquote) {
    border-left: 3px solid var(--primary);
    padding-left: 0.6rem;
    color: var(--text-muted);
    margin: 0.4rem 0;
}
:deep(.chat-markdown hr) {
    border: none;
    border-top: 1px solid var(--border);
    margin: 0.5rem 0;
}
</style>