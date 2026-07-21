<script setup>
import { computed } from 'vue';
import { useThemeStore } from '@/Stores/theme';

const theme = useThemeStore();

const icon = computed(() => {
    if (theme.mode === 'light') return 'sun';
    if (theme.mode === 'dark') return 'moon';
    return 'system';
});

const label = computed(() => {
    if (theme.mode === 'light') return 'Light mode';
    if (theme.mode === 'dark') return 'Dark mode';
    return 'System default';
});
</script>

<template>
    <button
        type="button"
        @click="theme.cycle()"
        :title="label"
        class="flex items-center justify-center w-9 h-9 rounded-lg
               bg-[var(--surface2)] hover:bg-[var(--border)]
               border border-[var(--border)]
               text-[var(--text)] transition-colors duration-150"
    >
        <!-- Sun -->
        <svg v-if="icon === 'sun'" xmlns="http://www.w3.org/2000/svg" class="w-4.5 h-4.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <circle cx="12" cy="12" r="4" />
            <path d="M12 2v2M12 20v2M4.93 4.93l1.41 1.41M17.66 17.66l1.41 1.41M2 12h2M20 12h2M6.34 17.66l-1.41 1.41M19.07 4.93l-1.41 1.41" />
        </svg>
        <!-- Moon -->
        <svg v-else-if="icon === 'moon'" xmlns="http://www.w3.org/2000/svg" class="w-4.5 h-4.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M21 12.79A9 9 0 1111.21 3 7 7 0 0021 12.79z" />
        </svg>
        <!-- System (monitor) -->
        <svg v-else xmlns="http://www.w3.org/2000/svg" class="w-4.5 h-4.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <rect x="2" y="3" width="20" height="14" rx="2" />
            <path d="M8 21h8M12 17v4" />
        </svg>
    </button>
</template>
