<script setup>
import { Link } from '@inertiajs/vue3';
import { useI18n } from '@/i18n';
import { PlusIcon, ChatBubbleLeftRightIcon, TrashIcon } from '@heroicons/vue/24/outline';

defineProps({
    sessions: { type: Array, default: () => [] },
    activeId: { type: Number, default: null },
    creating: { type: Boolean, default: false },
});

defineEmits(['new-chat', 'delete-chat']);

const { t } = useI18n();
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
        <Link
            v-for="s in sessions"
            :key="s.id"
            :href="`/ai/chat/${s.id}`"
            class="group flex items-center gap-2.5 rounded-xl px-3 py-2.5 text-sm transition-colors"
            :class="activeId === s.id
                ? 'bg-[var(--primary)]/10 text-[var(--primary)] font-semibold'
                : 'text-[var(--text)] hover:bg-[var(--surface2)]'"
        >
            <ChatBubbleLeftRightIcon class="w-4.5 h-4.5 shrink-0" :class="activeId === s.id ? 'text-[var(--primary)]' : 'text-[var(--text-muted)]'" />
            <span class="flex-1 min-w-0">
                <span class="block truncate">{{ s.title || t('ai_chat.title') }}</span>
                <span class="block text-[11px] font-normal" :class="activeId === s.id ? 'text-[var(--primary)]/70' : 'text-[var(--text-muted)]'">
                    {{ new Date(s.created_at).toLocaleDateString() }}
                </span>
            </span>
            <button
                @click="$emit('delete-chat', $event, s)"
                :title="t('common.delete')"
                class="opacity-0 group-hover:opacity-100 text-[var(--text-muted)] hover:text-[var(--accent)]"
            >
                <TrashIcon class="w-4 h-4" />
            </button>
        </Link>

        <div v-if="!sessions.length" class="px-3 py-8 text-center text-xs text-[var(--text-muted)]">
            {{ t('ai_chat.no_sessions') }}
        </div>
    </div>
</template>