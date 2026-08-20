<script setup>
import { ref } from 'vue';
import { router } from '@inertiajs/vue3';
import { useI18n } from '@/i18n';
import { XMarkIcon } from '@heroicons/vue/24/outline';
import ChatSidebarBody from './ChatSidebarBody.vue';

const props = defineProps({
    sessions: { type: Array, default: () => [] },
    activeId: { type: Number, default: null },
});

const open = defineModel('open', { type: Boolean, default: false });

const { t } = useI18n();
const creating = ref(false);

function startNewChat() {
    creating.value = true;
    router.post('/ai/chat', {}, {
        onFinish: () => (creating.value = false),
    });
}

function deleteChat(event, session) {
    event.preventDefault();
    event.stopPropagation();
    if (confirm(t('ai_chat.delete_confirm'))) {
        router.delete(`/ai/chat/${session.id}`, { preserveScroll: true });
    }
}
</script>

<template>
    <!-- Mobile drawer -->
    <template v-if="open">
        <div class="fixed inset-0 z-40 bg-black/40 lg:hidden" @click="open = false" />
        <aside class="fixed inset-y-0 left-0 z-50 w-72 lg:hidden flex flex-col border-r border-[var(--border)] bg-[var(--surface)] shadow-xl">
            <ChatSidebarBody :sessions="sessions" :active-id="activeId" :creating="creating" @new-chat="startNewChat" @delete-chat="deleteChat" />
            <button class="lg:hidden icon-btn w-8 h-8 self-end mr-3 mb-3" aria-label="Close" @click="open = false">
                <XMarkIcon class="w-5 h-5" />
            </button>
        </aside>
    </template>

    <!-- Desktop sidebar -->
    <aside class="hidden lg:flex w-72 xl:w-80 shrink-0 flex-col border-r border-[var(--border)] bg-[var(--surface)]">
        <ChatSidebarBody :sessions="sessions" :active-id="activeId" :creating="creating" @new-chat="startNewChat" @delete-chat="deleteChat" />
    </aside>
</template>