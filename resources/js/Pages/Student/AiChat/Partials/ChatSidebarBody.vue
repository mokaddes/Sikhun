<script setup>
import { ref } from 'vue';
import { Link, router } from '@inertiajs/vue3';
import { useI18n } from '@/i18n';
import { PlusIcon, ChatBubbleLeftRightIcon, BookOpenIcon, PencilIcon, TrashIcon, CheckIcon, XMarkIcon } from '@heroicons/vue/24/outline';

defineProps({
    sessions: { type: Array, default: () => [] },
    activeId: { type: Number, default: null },
    creating: { type: Boolean, default: false },
});

const emit = defineEmits(['new-chat', 'delete-chat']);

const { t } = useI18n();

const editingId = ref(null);
const editingTitle = ref('');

function startRename(session, event) {
    event.preventDefault();
    event.stopPropagation();
    editingId.value = session.id;
    editingTitle.value = session.title || t('ai_chat.title');
}

function saveRename(session, event) {
    event.preventDefault();
    event.stopPropagation();
    const title = editingTitle.value.trim();
    if (title && title !== session.title) {
        router.patch(`/ai/chat/${session.id}`, { title }, {
            preserveScroll: true,
            onSuccess: () => cancelRename(),
        });
    } else {
        cancelRename();
    }
}

function cancelRename() {
    editingId.value = null;
    editingTitle.value = '';
}

function isBookChat(session) {
    return session.source_type === 'book' || session.source_type === 'upload';
}
</script>

<template>
    <div class="p-3">
        <button
            @click="$emit('new-chat')"
            :disabled="creating"
            class="w-full flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-[var(--primary)] hover:bg-[var(--primary-hover)] text-white text-sm font-semibold disabled:opacity-60"
        >
            <PlusIcon class="w-4.5 h-4.5" />
            {{ creating ? t('ai_chat.thinking') : t('ai_chat.new_chat') }}
        </button>
    </div>

    <div class="flex-1 overflow-y-auto px-2 pb-4 space-y-0.5">
        <div v-if="sessions.length" class="eyebrow px-2 mb-1.5">{{ t('nav.ai_chat') }}</div>

        <div
            v-for="s in sessions"
            :key="s.id"
            class="group rounded-xl transition-colors"
            :class="activeId === s.id ? 'bg-[var(--primary)]/10' : 'hover:bg-[var(--surface2)]'"
        >
            <!-- Inline rename -->
            <form v-if="editingId === s.id" class="flex items-center gap-1.5 px-3 py-2" @submit="saveRename(s, $event)">
                <input
                    v-model="editingTitle"
                    type="text"
                    class="flex-1 min-w-0 px-2 py-1 rounded-lg bg-[var(--surface)] border border-[var(--primary)] text-sm focus:outline-none"
                    @keydown.esc="cancelRename"
                />
                <button type="submit" class="shrink-0 text-[var(--secondary)]" :title="t('common.save')">
                    <CheckIcon class="w-4 h-4" />
                </button>
                <button type="button" class="shrink-0 text-[var(--text-muted)] hover:text-[var(--text)]" :title="t('common.cancel')" @click="cancelRename">
                    <XMarkIcon class="w-4 h-4" />
                </button>
            </form>

            <!-- Chat row -->
            <Link
                v-else
                :href="`/ai/chat/${s.id}`"
                class="flex items-center gap-2.5 rounded-xl px-3 py-2.5 text-sm transition-colors"
                :class="activeId === s.id ? 'text-[var(--primary)] font-semibold' : 'text-[var(--text)]'"
            >
                <component
                    :is="isBookChat(s) ? BookOpenIcon : ChatBubbleLeftRightIcon"
                    class="w-4.5 h-4.5 shrink-0"
                    :class="activeId === s.id ? 'text-[var(--primary)]' : isBookChat(s) ? 'text-[var(--secondary)]' : 'text-[var(--text-muted)]'"
                />
                <span class="flex-1 min-w-0">
                    <span class="block truncate">{{ s.title || t('ai_chat.title') }}</span>
                    <span class="block text-[11px] font-normal" :class="activeId === s.id ? 'text-[var(--primary)]/70' : 'text-[var(--text-muted)]'">
                        {{ isBookChat(s) ? t('ai_chat.book_chat') : new Date(s.created_at).toLocaleDateString() }}
                    </span>
                </span>
                <span class="flex items-center gap-1 opacity-0 group-hover:opacity-100">
                    <button
                        @click="startRename(s, $event)"
                        :title="t('common.edit')"
                        class="text-[var(--text-muted)] hover:text-[var(--primary)]"
                    >
                        <PencilIcon class="w-4 h-4" />
                    </button>
                    <button
                        @click="$emit('delete-chat', $event, s)"
                        :title="t('common.delete')"
                        class="text-[var(--text-muted)] hover:text-[var(--accent)]"
                    >
                        <TrashIcon class="w-4 h-4" />
                    </button>
                </span>
            </Link>
        </div>

        <div v-if="!sessions.length" class="px-3 py-8 text-center text-xs text-[var(--text-muted)]">
            {{ t('ai_chat.no_sessions') }}
        </div>
    </div>
</template>