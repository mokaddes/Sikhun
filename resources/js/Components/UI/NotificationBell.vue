<script setup>
import { ref, onMounted } from 'vue';
import { useNotificationStore } from '@/Stores/notifications';
import { useI18n } from '@/i18n';

const store = useNotificationStore();
const { t } = useI18n();
const open = ref(false);

onMounted(() => {
    if (!store.items.length) store.fetchInitial();
});

function toggle() {
    open.value = !open.value;
}

function openItem(item) {
    if (!item.read_at) store.markRead(item.id);
}
</script>

<template>
    <div class="relative">
        <button @click="toggle" class="relative w-9 h-9 flex items-center justify-center rounded-lg border border-[var(--border)] hover:bg-[var(--surface2)]">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-4.5 h-4.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.4-1.4A2 2 0 0118 14.2V11a6 6 0 10-12 0v3.2a2 2 0 01-.6 1.4L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
            </svg>
            <span v-if="store.unreadCount > 0" class="absolute -top-1 -right-1 min-w-[16px] h-4 px-1 rounded-full bg-[var(--accent)] text-white text-[10px] font-bold flex items-center justify-center">
                {{ store.unreadCount > 9 ? '9+' : store.unreadCount }}
            </span>
        </button>

        <div v-if="open" class="absolute right-0 mt-2 w-80 max-h-96 overflow-y-auto rounded-xl border border-[var(--border)] bg-[var(--surface)] shadow-xl z-50">
            <div class="flex items-center justify-between px-4 py-3 border-b border-[var(--border)]">
                <span class="font-semibold text-sm">Notifications</span>
                <button v-if="store.unreadCount > 0" @click="store.markAllRead()" class="text-xs text-[var(--primary)] hover:underline">Mark all read</button>
            </div>
            <div v-if="!store.items.length" class="p-6 text-center text-sm text-[var(--text-muted)]">No notifications yet.</div>
            <button v-for="item in store.items" :key="item.id" @click="openItem(item)"
                class="w-full text-left px-4 py-3 border-b border-[var(--border)] last:border-0 hover:bg-[var(--surface2)]"
                :class="!item.read_at ? 'bg-[var(--primary)]/5' : ''">
                <div class="text-sm font-medium">{{ item.title }}</div>
                <div class="text-xs text-[var(--text-muted)] mt-0.5">{{ item.body }}</div>
            </button>
        </div>
    </div>
</template>
