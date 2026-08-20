<script setup>
import { ref, computed, nextTick, onMounted } from 'vue';
import { Head, usePage, router } from '@inertiajs/vue3';
import StudentLayout from '@/Components/Layout/StudentLayout.vue';
import { useI18n } from '@/i18n';
import ChatSidebar from './Partials/ChatSidebar.vue';
import {
    ArrowUpIcon,
    PaperClipIcon,
    BookOpenIcon,
    XMarkIcon,
    Bars3Icon,
    SparklesIcon,
} from '@heroicons/vue/24/outline';

const props = defineProps({ session: Object, sessions: Array, books: Array });

const { t } = useI18n();
const page = usePage();
const csrfToken = computed(() => page.props.csrf_token ?? '');

const messages = ref([...(props.session.messages ?? [])]);
const draft = ref('');
const streaming = ref(false);
const currentReply = ref('');
const scrollBox = ref(null);
const composerRef = ref(null);

const sidebarOpen = ref(false);
const bookModalOpen = ref(false);
const attachingBook = ref(false);

const selectedImage = ref(null);
const imagePreview = ref(null);
const fileInput = ref(null);

const attachedBook = computed(() => props.session.source_book_id ? props.session.book : null);

const renderMessages = computed(() =>
    messages.value.map((m) => {
        if (Array.isArray(m.content)) {
            const text = m.content.filter((p) => p.type === 'text').map((p) => p.text).join('\n');
            const images = m.content
                .filter((p) => p.type === 'image')
                .map((p) => (p.path ? `/storage/${p.path}` : p.url))
                .filter(Boolean);
            return { role: m.role, text, images, imageOnly: !text };
        }
        return { role: m.role, text: m.content, images: [], imageOnly: false };
    }),
);

function scrollToBottom() {
    nextTick(() => {
        if (scrollBox.value) scrollBox.value.scrollTop = scrollBox.value.scrollHeight;
    });
}

function pickImage(event) {
    const file = event.target.files[0];
    if (!file) return;
    if (!file.type.startsWith('image/')) return;
    selectedImage.value = file;
    imagePreview.value = URL.createObjectURL(file);
    event.target.value = '';
}

function removeImage() {
    selectedImage.value = null;
    if (imagePreview.value) URL.revokeObjectURL(imagePreview.value);
    imagePreview.value = null;
}

function autogrow() {
    const el = composerRef.value;
    if (!el) return;
    el.style.height = 'auto';
    el.style.height = Math.min(el.scrollHeight, 160) + 'px';
}

function attachBook(book) {
    attachingBook.value = true;
    router.patch(`/ai/chat/${props.session.id}/book`, { book_id: book.id }, {
        preserveScroll: true,
        onSuccess: () => {
            bookModalOpen.value = false;
            attachingBook.value = false;
        },
        onError: () => (attachingBook.value = false),
    });
}

async function send() {
    const text = draft.value.trim();
    if ((!text && !selectedImage.value) || streaming.value) return;

    const formData = new FormData();
    formData.append('message', text);
    if (selectedImage.value) formData.append('image', selectedImage.value);

    messages.value.push({
        role: 'user',
        content: [{ type: 'text', text }, { type: 'image', path: null, url: imagePreview.value }],
    });

    draft.value = '';
    if (composerRef.value) composerRef.value.style.height = 'auto';
    removeImage();
    streaming.value = true;
    currentReply.value = '';
    scrollToBottom();

    let finished = false;
    const finalize = () => {
        if (finished) return;
        finished = true;
        messages.value.push({ role: 'assistant', content: currentReply.value || '…' });
        currentReply.value = '';
        streaming.value = false;
        scrollToBottom();
    };

    try {
        const res = await fetch(`/ai/chat/${props.session.id}/stream`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken.value,
                'Accept': 'text/event-stream',
            },
            body: formData,
        });

        if (!res.ok) {
            currentReply.value = 'HTTP ' + res.status;
            finalize();
            return;
        }

        const reader = res.body.getReader();
        const decoder = new TextDecoder();
        let buffer = '';

        const handleEvent = (data) => {
            if (data.error) {
                currentReply.value = data.error;
                finalize();
                return true;
            }
            if (data.done) {
                finalize();
                return true;
            }
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

onMounted(scrollToBottom);
</script>

<template>
    <Head :title="session.title || t('ai_chat.title')" />
    <StudentLayout>
        <div class="flex h-[calc(100dvh-9rem)] min-h-[440px] overflow-hidden rounded-2xl border border-[var(--border)] bg-[var(--surface)] lg:h-[calc(100dvh-7rem)]">
            <ChatSidebar v-model:open="sidebarOpen" :sessions="sessions" :active-id="session.id" />

            <section class="flex-1 flex flex-col min-w-0">
                <!-- Chat header -->
                <header class="flex items-center gap-3 px-4 py-3 border-b border-[var(--border)]">
                    <button class="lg:hidden icon-btn w-8 h-8" aria-label="Open history" @click="sidebarOpen = true">
                        <Bars3Icon class="w-5 h-5" />
                    </button>
                    <div class="flex-1 min-w-0">
                        <h1 class="font-heading font-bold truncate">{{ session.title || t('ai_chat.title') }}</h1>
                        <div v-if="attachedBook" class="flex items-center gap-1.5 text-xs text-[var(--secondary)]">
                            <BookOpenIcon class="w-3.5 h-3.5" />
                            <span class="truncate">{{ t('ai_chat.attached_book') }}: {{ attachedBook.title }}</span>
                        </div>
                    </div>
                </header>

                <!-- Messages -->
                <div ref="scrollBox" class="flex-1 overflow-y-auto px-4 sm:px-6 py-5 space-y-4">
                    <div v-if="!renderMessages.length && !streaming" class="h-full flex flex-col items-center justify-center text-center px-6">
                        <div class="sun-disc w-14 h-14 mb-4 flex items-center justify-center text-white">
                            <SparklesIcon class="w-7 h-7" />
                        </div>
                        <h2 class="font-heading text-lg font-bold mb-1">{{ t('ai_chat.greeting') }}</h2>
                        <p class="text-sm text-[var(--text-muted)] max-w-sm">{{ t('ai_chat.greeting_sub') }}</p>
                    </div>

                    <div v-for="(m, i) in renderMessages" :key="i" class="flex" :class="m.role === 'user' ? 'justify-end' : 'justify-start'">
                        <div class="max-w-[85%] sm:max-w-[75%] space-y-2"
                            :class="m.role === 'user' ? 'bg-[var(--primary)] text-white rounded-2xl rounded-br-md px-4 py-2.5' : 'text-[var(--text)]'">
                            <img v-for="(img, j) in m.images" :key="j" :src="img" alt="uploaded" class="rounded-lg max-h-56 w-auto object-contain bg-[var(--surface2)]" />
                            <div v-if="m.text" class="text-sm whitespace-pre-wrap break-words">{{ m.text }}</div>
                            <div v-else-if="m.imageOnly && !m.images.length" class="text-xs text-[var(--text-muted)]">{{ t('ai_chat.image') }}</div>
                        </div>
                    </div>

                    <div v-if="streaming" class="flex justify-start">
                        <div class="max-w-[85%] sm:max-w-[75%] bg-[var(--surface2)] border border-[var(--border)] rounded-2xl rounded-bl-md px-4 py-2.5 text-sm whitespace-pre-wrap break-words">
                            {{ currentReply || t('ai_chat.thinking') }}
                            <span v-if="!currentReply" class="inline-flex gap-1 ml-1 align-middle">
                                <span class="inline-block w-1 h-1 rounded-full bg-current animate-bounce" style="animation-delay:0ms">.</span>
                                <span class="inline-block w-1 h-1 rounded-full bg-current animate-bounce" style="animation-delay:150ms">.</span>
                                <span class="inline-block w-1 h-1 rounded-full bg-current animate-bounce" style="animation-delay:300ms">.</span>
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Composer -->
                <div class="border-t border-[var(--border)] p-3 sm:p-4">
                    <div class="max-w-3xl mx-auto">
                        <div v-if="imagePreview" class="flex items-center gap-3 mb-2 px-1">
                            <div class="relative">
                                <img :src="imagePreview" alt="preview" class="h-20 w-20 object-cover rounded-xl border border-[var(--border)]" />
                                <button
                                    class="absolute -top-2 -right-2 w-5 h-5 rounded-full bg-[var(--surface2)] border border-[var(--border)] flex items-center justify-center text-[var(--text-muted)] hover:text-[var(--accent)]"
                                    type="button"
                                    :title="t('ai_chat.remove_image')"
                                    @click="removeImage"
                                >
                                    <XMarkIcon class="w-3 h-3" />
                                </button>
                            </div>
                        </div>

                        <div class="flex items-end gap-2 rounded-2xl border border-[var(--border)] bg-[var(--surface2)] px-3 py-2 focus-within:ring-2 focus-within:ring-[var(--primary)]/40">
                            <button
                                type="button"
                                class="icon-btn w-9 h-9 shrink-0"
                                :title="t('ai_chat.image')"
                                :disabled="streaming"
                                @click="fileInput.click()"
                            >
                                <PaperClipIcon class="w-5 h-5" />
                            </button>
                            <input ref="fileInput" type="file" accept="image/*" class="hidden" @change="pickImage" />

                            <button
                                type="button"
                                class="flex items-center gap-1.5 shrink-0 px-3 py-2 rounded-xl text-xs font-medium border transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                                :class="attachedBook
                                    ? 'border-[var(--secondary)]/30 bg-[var(--secondary)]/10 text-[var(--secondary)]'
                                    : 'border-[var(--border)] bg-[var(--surface)] text-[var(--text)] hover:border-[var(--primary)]'"
                                :disabled="!!attachedBook"
                                :title="attachedBook ? `${t('ai_chat.attached_book')}: ${attachedBook.title}` : t('ai_chat.attach_book_hint')"
                                @click="bookModalOpen = true"
                            >
                                <BookOpenIcon class="w-4 h-4" />
                                {{ attachedBook ? attachedBook.title : t('ai_chat.attach_book') }}
                            </button>

                            <textarea
                                ref="composerRef"
                                v-model="draft"
                                rows="1"
                                class="flex-1 min-w-0 bg-transparent resize-none outline-none py-1.5 max-h-40 text-sm"
                                :placeholder="t('ai_chat.type_message')"
                                :disabled="streaming"
                                @input="autogrow"
                                @keydown="onComposerKeydown"
                            ></textarea>

                            <button
                                type="button"
                                class="w-9 h-9 shrink-0 rounded-full bg-[var(--primary)] hover:bg-[var(--primary-hover)] text-white flex items-center justify-center disabled:opacity-50"
                                :disabled="streaming || (!draft.trim() && !selectedImage)"
                                :title="t('ai_chat.send')"
                                @click="send"
                            >
                                <ArrowUpIcon class="w-5 h-5" />
                            </button>
                        </div>
                        <p class="text-[11px] text-[var(--text-muted)] mt-1.5 px-1">{{ t('ai_chat.image_hint') }}</p>
                    </div>
                </div>
            </section>
        </div>

        <!-- Attach book modal -->
        <div v-if="bookModalOpen" class="fixed inset-0 z-50 flex items-center justify-center p-4">
            <div class="absolute inset-0 bg-black/50" @click="bookModalOpen = false" />
            <div class="relative w-full max-w-md rounded-2xl border border-[var(--border)] bg-[var(--surface)] p-5 shadow-2xl">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="font-heading font-bold">{{ t('ai_chat.attach_book') }}</h3>
                    <button class="icon-btn w-8 h-8" @click="bookModalOpen = false">
                        <XMarkIcon class="w-5 h-5" />
                    </button>
                </div>

                <div class="space-y-2 max-h-72 overflow-y-auto">
                    <button
                        v-for="book in books"
                        :key="book.id"
                        type="button"
                        class="w-full flex items-center gap-3 rounded-xl border border-[var(--border)] bg-[var(--surface2)] hover:border-[var(--primary)] p-3 text-left disabled:opacity-50"
                        :disabled="attachingBook"
                        @click="attachBook(book)"
                    >
                        <img v-if="book.cover_image_url" :src="book.cover_image_url" alt="" class="h-12 w-9 object-cover rounded-md bg-[var(--surface)]" />
                        <span v-else class="h-12 w-9 rounded-md bg-[var(--surface)] flex items-center justify-center text-lg">📘</span>
                        <span class="flex-1 min-w-0">
                            <span class="block text-sm font-medium truncate">{{ book.title }}</span>
                            <span class="block text-xs text-[var(--text-muted)]">{{ t('ai_chat.attach_book_hint') }}</span>
                        </span>
                    </button>

                    <div v-if="!books.length" class="py-10 text-center text-sm text-[var(--text-muted)]">
                        {{ t('ai_chat.no_books_to_attach') }}
                    </div>
                </div>
            </div>
        </div>
    </StudentLayout>
</template>