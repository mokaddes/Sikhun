<script setup>
import { ref, computed, nextTick, watch, onMounted, onUnmounted } from 'vue';
import { usePage } from '@inertiajs/vue3';
import { useI18n } from '@/i18n';
import {
    ArrowUpIcon,
    BookOpenIcon,
    XMarkIcon,
    ArrowPathIcon,
    ArrowsRightLeftIcon,
    ArrowTopRightOnSquareIcon,
} from '@heroicons/vue/24/outline';
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
    // Optional book title for display
    bookTitle: { type: String, default: '' },
});

const emit = defineEmits(['close']);

const { t, locale } = useI18n();
const inertia = usePage();
const csrfToken = computed(() => inertia.props.csrf_token ?? '');

const BN_DIGITS = ['০', '১', '২', '৩', '৪', '৫', '৬', '৭', '৮', '৯'];
function toBengaliDigits(num) {
    if (num === null || num === undefined) return '';
    return String(num).replace(/[0-9]/g, (d) => BN_DIGITS[Number(d)]);
}

// ==================== SESSION PERSISTENCE ====================
const activeSessionId = ref(null);
const activeSessionTitle = ref('');

const messages = ref([]);
const draft = ref('');
const streaming = ref(false);
const currentReply = ref('');
const scrollBox = ref(null);
const panelRef = ref(null);

// ==================== PANEL CUSTOMIZATION (SIZE & POSITION) ====================
const DEFAULT_WIDTH = 390;
const DEFAULT_HEIGHT = 520;

const width = ref(DEFAULT_WIDTH);
const height = ref(DEFAULT_HEIGHT);
const dockSide = ref('right'); // 'right' | 'left' | 'custom'
const position = ref({ x: 0, y: 0 }); // used when dockSide === 'custom'

function loadLayoutPrefs() {
    try {
        const saved = localStorage.getItem('sikhun_reader_chat_layout');
        if (saved) {
            const parsed = JSON.parse(saved);
            if (parsed.width) width.value = Math.max(310, Math.min(parsed.width, window.innerWidth - 32));
            if (parsed.height) height.value = Math.max(340, Math.min(parsed.height, window.innerHeight - 50));
            if (parsed.dockSide) dockSide.value = parsed.dockSide;
            if (parsed.x !== undefined && parsed.y !== undefined && parsed.dockSide === 'custom') {
                position.value = {
                    x: Math.max(10, Math.min(parsed.x, window.innerWidth - width.value - 10)),
                    y: Math.max(10, Math.min(parsed.y, window.innerHeight - height.value - 10)),
                };
            }
        }
    } catch (e) {}
}

function saveLayoutPrefs() {
    try {
        localStorage.setItem(
            'sikhun_reader_chat_layout',
            JSON.stringify({
                width: width.value,
                height: height.value,
                dockSide: dockSide.value,
                x: position.value.x,
                y: position.value.y,
            })
        );
    } catch (e) {}
}

function restoreDefault() {
    width.value = DEFAULT_WIDTH;
    height.value = DEFAULT_HEIGHT;
    dockSide.value = 'right';
    position.value = { x: 0, y: 0 };
    try {
        localStorage.removeItem('sikhun_reader_chat_layout');
    } catch (e) {}
}

function toggleDockSide() {
    if (dockSide.value === 'right') {
        dockSide.value = 'left';
    } else {
        dockSide.value = 'right';
    }
    saveLayoutPrefs();
}

const panelStyle = computed(() => {
    const maxW = Math.min(width.value, window.innerWidth - 20);
    const maxH = Math.min(height.value, window.innerHeight - 30);

    const base = {
        width: `${maxW}px`,
        height: `${maxH}px`,
        zIndex: 50,
    };

    if (dockSide.value === 'custom') {
        return {
            ...base,
            position: 'fixed',
            left: `${position.value.x}px`,
            top: `${position.value.y}px`,
            right: 'auto',
            bottom: 'auto',
        };
    }

    if (dockSide.value === 'left') {
        return {
            ...base,
            position: 'fixed',
            left: '20px',
            bottom: '20px',
            right: 'auto',
            top: 'auto',
        };
    }

    // Default: dock right
    return {
        ...base,
        position: 'fixed',
        right: '20px',
        bottom: '20px',
        left: 'auto',
        top: 'auto',
    };
});

// ==================== DRAGGING (MOVE ANYWHERE) ====================
let isDragging = false;
let dragStartX = 0;
let dragStartY = 0;
let initialPanelLeft = 0;
let initialPanelTop = 0;

function startDrag(e) {
    if (e.target.closest('button') || e.target.closest('input') || e.target.closest('a')) {
        return; // don't drag if clicking buttons or links
    }
    isDragging = true;
    const clientX = e.clientX ?? e.touches?.[0]?.clientX ?? 0;
    const clientY = e.clientY ?? e.touches?.[0]?.clientY ?? 0;

    const rect = panelRef.value?.getBoundingClientRect();
    initialPanelLeft = rect ? rect.left : 0;
    initialPanelTop = rect ? rect.top : 0;
    dragStartX = clientX;
    dragStartY = clientY;

    dockSide.value = 'custom';
    position.value = { x: initialPanelLeft, y: initialPanelTop };

    window.addEventListener('mousemove', onDragMove);
    window.addEventListener('mouseup', endDrag);
    window.addEventListener('touchmove', onDragMove, { passive: false });
    window.addEventListener('touchend', endDrag);
}

function onDragMove(e) {
    if (!isDragging) return;
    if (e.cancelable) e.preventDefault();
    const clientX = e.clientX ?? e.touches?.[0]?.clientX ?? 0;
    const clientY = e.clientY ?? e.touches?.[0]?.clientY ?? 0;

    const deltaX = clientX - dragStartX;
    const deltaY = clientY - dragStartY;

    const newX = Math.max(10, Math.min(window.innerWidth - width.value - 10, initialPanelLeft + deltaX));
    const newY = Math.max(10, Math.min(window.innerHeight - height.value - 10, initialPanelTop + deltaY));

    position.value = { x: newX, y: newY };
}

function endDrag() {
    if (!isDragging) return;
    isDragging = false;
    window.removeEventListener('mousemove', onDragMove);
    window.removeEventListener('mouseup', endDrag);
    window.removeEventListener('touchmove', onDragMove);
    window.removeEventListener('touchend', endDrag);
    saveLayoutPrefs();
}

// ==================== RESIZING (WIDTH & HEIGHT) ====================
let isResizing = false;
let resizeDirection = '';
let resizeStartX = 0;
let resizeStartY = 0;
let initialWidth = 0;
let initialHeight = 0;
let initialX = 0;
let initialY = 0;

function startResize(e, direction) {
    e.stopPropagation();
    e.preventDefault();
    isResizing = true;
    resizeDirection = direction;

    const clientX = e.clientX ?? e.touches?.[0]?.clientX ?? 0;
    const clientY = e.clientY ?? e.touches?.[0]?.clientY ?? 0;
    resizeStartX = clientX;
    resizeStartY = clientY;

    initialWidth = width.value;
    initialHeight = height.value;

    const rect = panelRef.value?.getBoundingClientRect();
    if (rect) {
        initialX = rect.left;
        initialY = rect.top;
    }

    window.addEventListener('mousemove', onResizeMove);
    window.addEventListener('mouseup', endResize);
    window.addEventListener('touchmove', onResizeMove, { passive: false });
    window.addEventListener('touchend', endResize);
}

function onResizeMove(e) {
    if (!isResizing) return;
    if (e.cancelable) e.preventDefault();
    const clientX = e.clientX ?? e.touches?.[0]?.clientX ?? 0;
    const clientY = e.clientY ?? e.touches?.[0]?.clientY ?? 0;

    const deltaX = clientX - resizeStartX;
    const deltaY = clientY - resizeStartY;

    const minW = 310;
    const maxW = Math.min(800, window.innerWidth - 32);
    const minH = 340;
    const maxH = window.innerHeight - 50;

    // Height adjustment (dragging top handle upwards increases height)
    if (resizeDirection.includes('n')) {
        const newH = Math.max(minH, Math.min(maxH, initialHeight - deltaY));
        height.value = newH;
        if (dockSide.value === 'custom') {
            position.value.y = Math.max(10, initialY + deltaY);
        }
    }

    // Width adjustment
    if (dockSide.value === 'left') {
        // When docked left, dragging right handle outwards increases width
        if (resizeDirection.includes('e')) {
            const newW = Math.max(minW, Math.min(maxW, initialWidth + deltaX));
            width.value = newW;
        }
    } else {
        // When docked right or custom, dragging left handle outwards increases width
        if (resizeDirection.includes('w')) {
            const newW = Math.max(minW, Math.min(maxW, initialWidth - deltaX));
            width.value = newW;
            if (dockSide.value === 'custom') {
                position.value.x = Math.max(10, initialX + deltaX);
            }
        }
    }
}

function endResize() {
    if (!isResizing) return;
    isResizing = false;
    window.removeEventListener('mousemove', onResizeMove);
    window.removeEventListener('mouseup', endResize);
    window.removeEventListener('touchmove', onResizeMove);
    window.removeEventListener('touchend', endResize);
    saveLayoutPrefs();
}

onMounted(() => {
    loadLayoutPrefs();
});

onUnmounted(() => {
    endDrag();
    endResize();
});

watch(
    () => props.page,
    () => {
        scrollToBottom();
    }
);

function scrollToBottom() {
    nextTick(() => {
        if (scrollBox.value) scrollBox.value.scrollTop = scrollBox.value.scrollHeight;
    });
}

// ==================== STREAMING CHAT ====================
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
    if (activeSessionId.value) {
        formData.append('session_id', activeSessionId.value);
    }

    const finalize = () => {
        messages.value.push({ role: 'assistant', content: currentReply.value || '…' });
        currentReply.value = '';
        streaming.value = false;
        scrollToBottom();
    };

    try {
        const res = await fetch(props.chatUrl, {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': csrfToken.value, Accept: 'text/event-stream' },
            body: formData,
        });

        if (!res.ok) {
            const body = await res.text().catch(() => '');
            currentReply.value = body || 'HTTP ' + res.status;
            finalize();
            return;
        }

        const reader = res.body.getReader();
        const decoder = new TextDecoder();
        let buffer = '';

        const handleEvent = (data) => {
            if (data.session_id) {
                activeSessionId.value = data.session_id;
            }
            if (data.session_title) {
                activeSessionTitle.value = data.session_title;
            }
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
                    try {
                        if (handleEvent(JSON.parse(line.slice(5).trim()))) return;
                    } catch (err) {}
                }
            }
        }

        if (buffer.trim()) {
            for (const line of buffer.split('\n')) {
                if (!line.startsWith('data:')) continue;
                try {
                    if (handleEvent(JSON.parse(line.slice(5).trim()))) return;
                } catch (err) {}
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
    <div
        ref="panelRef"
        :style="panelStyle"
        class="flex flex-col rounded-2xl border border-[var(--border)] bg-[var(--surface)] shadow-2xl overflow-hidden transition-[box-shadow] select-text pointer-events-auto"
    >
        <!-- Top Edge Resize Handle -->
        <div
            class="absolute top-0 inset-x-0 h-2 cursor-ns-resize hover:bg-[var(--primary)]/30 z-30 transition-colors"
            title="Resize height"
            @mousedown="startResize($event, 'n')"
            @touchstart="startResize($event, 'n')"
        ></div>

        <!-- Corner Resize Handle (Top-Left when docked right; Top-Right when docked left) -->
        <div
            v-if="dockSide !== 'left'"
            class="absolute top-0 left-0 w-4 h-4 cursor-nwse-resize z-40 group flex items-center justify-center"
            title="Drag to resize width & height"
            @mousedown="startResize($event, 'nw')"
            @touchstart="startResize($event, 'nw')"
        >
            <div class="w-2 h-2 rounded-tl-sm border-t-2 border-l-2 border-white/60 group-hover:border-white"></div>
        </div>
        <div
            v-else
            class="absolute top-0 right-0 w-4 h-4 cursor-nesw-resize z-40 group flex items-center justify-center"
            title="Drag to resize width & height"
            @mousedown="startResize($event, 'ne')"
            @touchstart="startResize($event, 'ne')"
        >
            <div class="w-2 h-2 rounded-tr-sm border-t-2 border-r-2 border-white/60 group-hover:border-white"></div>
        </div>

        <!-- Side Edge Resize Handle -->
        <div
            v-if="dockSide !== 'left'"
            class="absolute left-0 inset-y-2 w-2 cursor-ew-resize hover:bg-[var(--primary)]/30 z-30 transition-colors"
            title="Resize width"
            @mousedown="startResize($event, 'w')"
            @touchstart="startResize($event, 'w')"
        ></div>
        <div
            v-else
            class="absolute right-0 inset-y-2 w-2 cursor-ew-resize hover:bg-[var(--primary)]/30 z-30 transition-colors"
            title="Resize width"
            @mousedown="startResize($event, 'e')"
            @touchstart="startResize($event, 'e')"
        ></div>

        <!-- Panel Header (Draggable Handle) -->
        <div
            class="flex items-center justify-between gap-2 px-3.5 py-2.5 border-b border-[var(--border)] bg-[var(--primary)] text-white cursor-grab active:cursor-grabbing select-none"
            title="Drag to move anywhere"
            @mousedown="startDrag"
            @touchstart="startDrag"
        >
            <!-- Left Info -->
            <div class="flex items-center gap-2 min-w-0">
                <div class="flex flex-col gap-0.5 opacity-60 hover:opacity-100" title="Drag handle">
                    <span class="w-3.5 h-0.5 bg-white/70 rounded-full"></span>
                    <span class="w-3.5 h-0.5 bg-white/70 rounded-full"></span>
                </div>
                <BookOpenIcon class="w-4 h-4 shrink-0" />
                <span class="font-heading font-semibold text-xs sm:text-sm truncate">
                    {{ t('reader.chat_title') }}
                </span>
                <span class="shrink-0 text-[10px] font-bold px-2 py-0.5 rounded-full bg-white/20">
                    {{ locale === 'bn' ? `পৃষ্ঠা ${toBengaliDigits(props.page)}` : `Page ${props.page}` }}
                </span>
            </div>

            <!-- Controls (Move Left/Right, Restore, Close) -->
            <div class="flex items-center gap-1 shrink-0">
                <!-- Saved in AI Chat Pill Link -->
                <a
                    v-if="activeSessionId"
                    :href="`/ai/chat/${activeSessionId}`"
                    target="_blank"
                    class="hidden sm:inline-flex items-center gap-1 text-[10px] font-medium px-2 py-0.5 rounded-full bg-white/20 hover:bg-white/30 text-white transition-colors"
                    title="Open in AI Chat (নতুন ট্যাবে খুলুন)"
                >
                    <span>AI চ্যাট</span>
                    <ArrowTopRightOnSquareIcon class="w-3 h-3" />
                </a>

                <!-- Move Left / Right Button -->
                <button
                    type="button"
                    class="p-1 rounded-lg text-white/80 hover:text-white hover:bg-white/20 transition-colors"
                    :title="dockSide === 'left' ? 'ডানে সরান (Move to Right)' : 'বামে সরান (Move to Left)'"
                    @click.stop="toggleDockSide"
                >
                    <ArrowsRightLeftIcon class="w-4 h-4" />
                </button>

                <!-- Restore Default Size & Position Button -->
                <button
                    type="button"
                    class="p-1 rounded-lg text-white/80 hover:text-white hover:bg-white/20 transition-colors"
                    title="রিসেট (Restore default size & position)"
                    @click.stop="restoreDefault"
                >
                    <ArrowPathIcon class="w-4 h-4" />
                </button>

                <!-- Close Button -->
                <button
                    type="button"
                    class="p-1 rounded-lg text-white/80 hover:text-white hover:bg-white/20 transition-colors"
                    title="বন্ধ করুন (Close)"
                    @click.stop="emit('close')"
                >
                    <XMarkIcon class="w-4 h-4" />
                </button>
            </div>
        </div>

        <!-- Chat Messages -->
        <div ref="scrollBox" class="flex-1 overflow-y-auto p-3.5 space-y-3 min-h-36 custom-scrollbar">
            <!-- Saved banner if session active -->
            <div
                v-if="activeSessionId"
                class="flex items-center justify-between gap-2 p-2 rounded-xl bg-[var(--surface2)] border border-[var(--border)] text-[11px] text-[var(--text-muted)]"
            >
                <span class="truncate">
                    💾 কথোপকথন সংরক্ষিত: <strong class="text-[var(--text)]">{{ activeSessionTitle || 'বই চ্যাট' }}</strong>
                </span>
                <a
                    :href="`/ai/chat/${activeSessionId}`"
                    target="_blank"
                    class="shrink-0 text-[var(--primary)] hover:underline font-semibold flex items-center gap-0.5"
                >
                    <span>দেখুন</span>
                    <ArrowTopRightOnSquareIcon class="w-3 h-3" />
                </a>
            </div>

            <p
                v-if="!messages.length && !streaming"
                class="text-xs text-[var(--text-muted)] text-center my-6 leading-relaxed"
            >
                {{ t('reader.chat_hint') }}
            </p>

            <div
                v-for="(m, i) in messages"
                :key="i"
                class="flex"
                :class="m.role === 'user' ? 'justify-end' : 'justify-start'"
            >
                <div
                    v-if="m.role === 'user'"
                    class="max-w-[85%] px-3.5 py-2.5 rounded-2xl rounded-br-md text-xs whitespace-pre-wrap break-words bg-[var(--primary)] text-white shadow-xs"
                >
                    {{ m.content }}
                </div>
                <div
                    v-else
                    class="max-w-[90%] px-3.5 py-2.5 rounded-2xl rounded-bl-md text-xs break-words bg-[var(--surface2)] border border-[var(--border)] text-[var(--text)] chat-markdown shadow-xs"
                    v-html="renderMarkdown(m.content)"
                ></div>
            </div>

            <div v-if="streaming" class="flex justify-start">
                <div
                    class="max-w-[90%] bg-[var(--surface2)] border border-[var(--border)] rounded-2xl rounded-bl-md px-3.5 py-2.5 text-xs break-words chat-markdown shadow-xs"
                    v-html="renderMarkdown(currentReply || t('ai_chat.thinking'))"
                ></div>
            </div>
        </div>

        <!-- Chat Composer -->
        <form @submit.prevent="send" class="p-2.5 border-t border-[var(--border)] bg-[var(--surface)] flex gap-2">
            <input
                v-model="draft"
                type="text"
                :placeholder="t('ai_chat.type_message')"
                :disabled="streaming"
                class="flex-1 px-3 py-2 rounded-xl bg-[var(--surface2)] border border-[var(--border)] text-xs text-[var(--text)] focus:outline-none focus:border-[var(--primary)]"
                @keydown="onComposerKeydown"
            />
            <button
                type="submit"
                :disabled="streaming || !draft.trim()"
                class="px-3.5 py-2 rounded-xl bg-[var(--primary)] hover:bg-[var(--primary-hover)] text-white text-xs font-semibold disabled:opacity-50 transition-all flex items-center justify-center shadow-xs"
            >
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
:deep(.chat-markdown h1) {
    font-size: 0.95rem;
}
:deep(.chat-markdown h2) {
    font-size: 0.88rem;
}
:deep(.chat-markdown h3) {
    font-size: 0.82rem;
}
:deep(.chat-markdown h4) {
    font-size: 0.78rem;
}
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
