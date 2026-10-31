<script setup>
import { ref, nextTick } from 'vue';
import axios from 'axios';
import { useI18n } from '@/i18n';

const { t, locale } = useI18n();
const open = ref(false);
const loaded = ref(false);
const messages = ref([]);
const draft = ref('');
const sending = ref(false);
const handingOff = ref(false);
const handoffRequiresLogin = ref(false);
const handoffFailed = ref(false);
const handoffRateLimited = ref(false);
const handoffLoginUrl = ref('/login');
const ticketUrl = ref('');
const conversationId = ref(null);
const scrollBox = ref(null);

function scrollToBottom() {
    nextTick(() => {
        if (scrollBox.value) scrollBox.value.scrollTop = scrollBox.value.scrollHeight;
    });
}

function safeHref(value) {
    if (value.startsWith('/') && !value.startsWith('//') && !value.includes('\\') && !/[\x00-\x1f]/.test(value)) {
        return value;
    }

    try {
        const url = new URL(value);
        return url.protocol === 'https:' ? url.href : null;
    } catch {
        return null;
    }
}

function messageParts(message) {
    const source = String(message ?? '');
    const pattern = /\[([^\]]+)\]\((https:\/\/[^\s)]+|\/(?!\/)[^\s)]+)\)|(https:\/\/[^\s<>]+|\/(?:library|bookshelf|courses|plans|wallet|ai\/chat|exams|flashcards|essays|schedules|leaderboard|referrals|notifications|profile|access|contact|support\/tickets)(?:\/[A-Za-z0-9_-]+)*(?:\?[^\s<>]*)?)/g;
    const parts = [];
    let cursor = 0;
    let match;

    while ((match = pattern.exec(source)) !== null) {
        if (match.index > cursor) parts.push({ text: source.slice(cursor, match.index) });

        const markdownLabel = match[1];
        let href = markdownLabel ? match[2] : match[3];
        let suffix = '';
        if (!markdownLabel) {
            const trailing = href.match(/[.,!?;:]+$/)?.[0] ?? '';
            href = href.slice(0, href.length - trailing.length);
            suffix = trailing;
        }
        const safeUrl = safeHref(href);
        if (safeUrl) {
            parts.push({ text: markdownLabel ?? href, href: safeUrl, external: safeUrl.startsWith('https://') });
            if (suffix) parts.push({ text: suffix });
        } else {
            parts.push({ text: match[0] });
        }
        cursor = match.index + match[0].length;
    }

    if (cursor < source.length) parts.push({ text: source.slice(cursor) });
    return parts;
}

async function toggle() {
    open.value = !open.value;
    if (open.value && !loaded.value) {
        try {
            const { data } = await axios.get('/support/conversation');
            conversationId.value = data.conversation_id;
            messages.value = data.messages;
            loaded.value = true;
            scrollToBottom();
        } catch (error) {
            messages.value = [{
                sender_type: 'bot',
                message: locale.value === 'bn'
                    ? 'সাপোর্ট চ্যাট লোড করা যায়নি। অনুগ্রহ করে আবার খুলুন।'
                    : 'Support chat could not be loaded. Please try again.',
            }];
        }
    }
}

async function send() {
    const text = draft.value.trim();
    if (!text || sending.value || ticketUrl.value) return;

    messages.value.push({ sender_type: 'student', message: text });
    draft.value = '';
    sending.value = true;
    scrollToBottom();

    try {
        const { data } = await axios.post('/support/message', { message: text, locale: locale.value });
        messages.value = data.messages;
    } catch (error) {
        messages.value.push({
            sender_type: 'bot',
            message: error.response?.status === 429
                ? t('support.message_rate_limited')
                : t('support.send_failed'),
        });
    } finally {
        sending.value = false;
        scrollToBottom();
    }
}

async function handoff() {
    if (handingOff.value || !conversationId.value) return;

    handingOff.value = true;
    handoffRequiresLogin.value = false;
    handoffFailed.value = false;
    handoffRateLimited.value = false;

    try {
        const { data } = await axios.post('/support/conversation/ticket', {
            conversation_id: conversationId.value,
        });
        ticketUrl.value = data.redirect_url;
    } catch (error) {
        if (error.response?.status === 401) {
            handoffRequiresLogin.value = true;
            handoffLoginUrl.value = error.response.data?.login_url || '/login';
        } else if (error.response?.status === 429) {
            handoffRateLimited.value = true;
        } else {
            handoffFailed.value = true;
            console.error('Support ticket handoff failed.', error.response?.status);
        }
    } finally {
        handingOff.value = false;
    }
}
</script>

<template>
    <div class="fixed bottom-5 right-5 z-50">
        <div v-if="open" class="mb-3 w-80 h-96 rounded-2xl border border-[var(--border)] bg-[var(--surface)] shadow-2xl flex flex-col overflow-hidden">
            <div class="px-4 py-3 border-b border-[var(--border)] font-semibold text-sm bg-[var(--primary)] text-white">
                {{ t('support.title') }}
            </div>
            <div ref="scrollBox" class="flex-1 overflow-y-auto p-3 space-y-2">
                <div v-for="(m, i) in messages" :key="i" class="flex" :class="m.sender_type === 'student' ? 'justify-end' : 'justify-start'">
                    <div class="max-w-[80%] px-3 py-2 rounded-xl text-xs whitespace-pre-wrap"
                        :class="m.sender_type === 'student' ? 'bg-[var(--primary)] text-white' : 'bg-[var(--surface2)] text-[var(--text)]'">
                        <template v-for="(part, partIndex) in messageParts(m.message)" :key="partIndex">
                            <a v-if="part.href" :href="part.href" :target="part.external ? '_blank' : undefined"
                                :rel="part.external ? 'noopener noreferrer' : undefined"
                                class="break-all underline decoration-[var(--primary)]/50 underline-offset-2 hover:text-[var(--primary)]">
                                {{ part.text }}
                            </a>
                            <span v-else>{{ part.text }}</span>
                        </template>
                    </div>
                </div>
                <div v-if="sending" class="text-xs text-[var(--text-muted)]">{{ t('support.typing') }}</div>
            </div>
            <div v-if="ticketUrl || handoffRequiresLogin || handoffFailed || handoffRateLimited || messages.some(m => m.sender_type === 'student')" class="border-t border-[var(--border)] p-2">
                <p v-if="ticketUrl" class="mb-2 text-xs text-[var(--text-muted)]">{{ t('support.handoff_created') }}</p>
                <p v-else-if="handoffRequiresLogin" class="mb-2 text-xs text-[var(--text-muted)]">{{ t('support.handoff_login_required') }}</p>
                <p v-else-if="handoffRateLimited" class="mb-2 text-xs text-red-500">{{ t('support.handoff_rate_limited') }}</p>
                <p v-else-if="handoffFailed" class="mb-2 text-xs text-red-500">{{ t('support.handoff_failed') }}</p>
                <a v-if="ticketUrl" :href="ticketUrl" class="block rounded-lg bg-[var(--primary)] px-3 py-2 text-center text-xs font-semibold text-white">
                    {{ t('support.view_ticket') }}
                </a>
                <a v-else-if="handoffRequiresLogin" :href="handoffLoginUrl" class="block rounded-lg bg-[var(--primary)] px-3 py-2 text-center text-xs font-semibold text-white">
                    {{ t('support.login') }}
                </a>
                <button v-else type="button" @click="handoff" :disabled="handingOff || sending" class="w-full rounded-lg border border-[var(--border)] px-3 py-2 text-xs font-semibold text-[var(--text)] disabled:opacity-60">
                    {{ handingOff ? t('support.handoff_sending') : t('support.handoff') }}
                </button>
            </div>
            <form v-if="!ticketUrl" @submit.prevent="send" class="p-2 border-t border-[var(--border)] flex gap-2">
                <input v-model="draft" type="text" :placeholder="t('support.placeholder')" :disabled="sending"
                    class="flex-1 px-3 py-2 rounded-lg bg-[var(--surface2)] border border-[var(--border)] text-xs focus:outline-none" />
                <button type="submit" :disabled="sending || !draft.trim()" class="px-3 py-2 rounded-lg bg-[var(--primary)] text-white text-xs font-semibold disabled:opacity-60">
                    {{ t('ai_chat.send') }}
                </button>
            </form>
        </div>

        <button @click="toggle" class="w-14 h-14 rounded-full bg-[var(--primary)] hover:bg-[var(--primary-hover)] text-white shadow-xl flex items-center justify-center text-2xl">
            {{ open ? '✕' : '💬' }}
        </button>
    </div>
</template>
