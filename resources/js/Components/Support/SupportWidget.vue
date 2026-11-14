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
        <!-- Expanded & Enhanced Chat Drawer -->
        <div v-if="open" class="mb-3 w-[380px] sm:w-[440px] max-w-[calc(100vw-2rem)] h-[540px] sm:h-[600px] max-h-[calc(100vh-5rem)] rounded-2xl border border-[var(--border)] bg-[var(--surface)] shadow-2xl flex flex-col overflow-hidden animate-in fade-in slide-in-from-bottom-4 duration-200">
            <!-- Modern Header with Online Indicator & Close -->
            <div class="px-4 py-3.5 border-b border-[var(--border)] bg-gradient-to-r from-[var(--primary)] to-[var(--primary-hover)] text-white flex items-center justify-between shadow-xs">
                <div class="flex items-center gap-2.5">
                    <div class="relative flex h-3 w-3 shrink-0">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-300 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-3 w-3 bg-emerald-400"></span>
                    </div>
                    <div>
                        <div class="font-bold text-sm tracking-wide leading-tight">
                            {{ t('support.title') }}
                        </div>
                        <div class="text-[11px] text-white/80 leading-none mt-0.5">
                            {{ locale === 'bn' ? '২৪/৭ এআই সহায়তা ও পরামর্শ' : '24/7 AI Desk & Student Support' }}
                        </div>
                    </div>
                </div>
                <button
                    type="button"
                    @click="open = false"
                    class="h-8 w-8 rounded-lg flex items-center justify-center text-white/80 hover:text-white hover:bg-white/15 transition-colors"
                    title="Close chat"
                >
                    <span class="text-lg leading-none">✕</span>
                </button>
            </div>

            <!-- Messages Stream Area -->
            <div ref="scrollBox" class="flex-1 overflow-y-auto p-4 space-y-3">
                <div v-if="messages.length === 0 && !sending" class="py-8 text-center text-xs text-[var(--text-muted)] space-y-2">
                    <div class="text-3xl">👋</div>
                    <p class="font-medium text-[var(--text)]">
                        {{ locale === 'bn' ? 'শিখুন সাপোর্ট ডেস্কে স্বাগতম!' : 'Welcome to Sikhun Support!' }}
                    </p>
                    <p class="text-[11px] max-w-[260px] mx-auto text-[var(--text-muted)]">
                        {{ locale === 'bn' ? 'বই, কোর্স, পরীক্ষা, প্ল্যান বা যে কোনো তথ্যের জন্য প্রশ্ন করুন।' : 'Ask anything about our books, courses, AI exams, plans, or account.' }}
                    </p>
                </div>

                <div v-for="(m, i) in messages" :key="i" class="flex" :class="m.sender_type === 'student' ? 'justify-end' : 'justify-start'">
                    <div class="max-w-[85%] px-3.5 py-2.5 rounded-2xl text-[13px] leading-relaxed shadow-2xs whitespace-pre-wrap break-words"
                        :class="m.sender_type === 'student'
                            ? 'bg-[var(--primary)] text-white rounded-br-xs'
                            : 'bg-[var(--surface2)] text-[var(--text)] border border-[var(--border)] rounded-bl-xs'">
                        <template v-for="(part, partIndex) in messageParts(m.message)" :key="partIndex">
                            <a v-if="part.href" :href="part.href" :target="part.external ? '_blank' : undefined"
                                :rel="part.external ? 'noopener noreferrer' : undefined"
                                class="font-semibold underline decoration-current underline-offset-2 hover:opacity-85">
                                {{ part.text }}
                            </a>
                            <span v-else>{{ part.text }}</span>
                        </template>
                    </div>
                </div>

                <div v-if="sending" class="flex items-center gap-2 text-xs text-[var(--text-muted)] py-1">
                    <span class="flex gap-1 items-center">
                        <span class="h-2 w-2 rounded-full bg-[var(--primary)] animate-bounce" style="animation-delay: 0ms"></span>
                        <span class="h-2 w-2 rounded-full bg-[var(--primary)] animate-bounce" style="animation-delay: 150ms"></span>
                        <span class="h-2 w-2 rounded-full bg-[var(--primary)] animate-bounce" style="animation-delay: 300ms"></span>
                    </span>
                    <span class="text-[11px]">{{ t('support.typing') }}</span>
                </div>
            </div>

            <!-- Ticket / Human Handoff Notice -->
            <div v-if="ticketUrl || handoffRequiresLogin || handoffFailed || handoffRateLimited || messages.some(m => m.sender_type === 'student')" class="border-t border-[var(--border)] p-2.5 bg-[var(--surface2)]/40">
                <p v-if="ticketUrl" class="mb-2 text-xs text-emerald-600 dark:text-emerald-400 font-medium">{{ t('support.handoff_created') }}</p>
                <p v-else-if="handoffRequiresLogin" class="mb-2 text-xs text-[var(--text-muted)]">{{ t('support.handoff_login_required') }}</p>
                <p v-else-if="handoffRateLimited" class="mb-2 text-xs text-rose-500 font-medium">{{ t('support.handoff_rate_limited') }}</p>
                <p v-else-if="handoffFailed" class="mb-2 text-xs text-rose-500 font-medium">{{ t('support.handoff_failed') }}</p>
                <a v-if="ticketUrl" :href="ticketUrl" class="block rounded-xl bg-[var(--primary)] px-3 py-2 text-center text-xs font-bold text-white shadow-xs hover:bg-[var(--primary-hover)] transition-colors">
                    {{ t('support.view_ticket') }}
                </a>
                <a v-else-if="handoffRequiresLogin" :href="handoffLoginUrl" class="block rounded-xl bg-[var(--primary)] px-3 py-2 text-center text-xs font-bold text-white shadow-xs hover:bg-[var(--primary-hover)] transition-colors">
                    {{ t('support.login') }}
                </a>
                <button v-else type="button" @click="handoff" :disabled="handingOff || sending" class="w-full rounded-xl border border-[var(--border)] bg-[var(--surface)] hover:bg-[var(--surface2)] px-3 py-2 text-xs font-semibold text-[var(--text)] transition-colors disabled:opacity-60">
                    {{ handingOff ? t('support.handoff_sending') : t('support.handoff') }}
                </button>
            </div>

            <!-- Chat Input Form -->
            <form v-if="!ticketUrl" @submit.prevent="send" class="p-2.5 border-t border-[var(--border)] bg-[var(--surface)] flex gap-2 items-center">
                <input v-model="draft" type="text" :placeholder="t('support.placeholder')" :disabled="sending"
                    class="flex-1 px-3.5 py-2.5 rounded-xl bg-[var(--surface2)] border border-[var(--border)] text-xs sm:text-sm text-[var(--text)] placeholder:text-[var(--text-muted)] focus:outline-none focus:border-[var(--primary)] transition-colors" />
                <button type="submit" :disabled="sending || !draft.trim()" class="px-4 py-2.5 rounded-xl bg-[var(--primary)] hover:bg-[var(--primary-hover)] text-white text-xs sm:text-sm font-bold shadow-xs transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
                    {{ t('ai_chat.send') }}
                </button>
            </form>
        </div>

        <!-- Floating Chat Bubble Trigger -->
        <button @click="toggle" class="w-14 h-14 rounded-full bg-[var(--primary)] hover:bg-[var(--primary-hover)] text-white shadow-2xl flex items-center justify-center text-2xl transition-transform hover:scale-105 active:scale-95" :title="open ? 'Close' : 'Chat with Support'">
            {{ open ? '✕' : '💬' }}
        </button>
    </div>
</template>
