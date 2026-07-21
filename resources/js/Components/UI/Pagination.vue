<script setup>
import { Link } from '@inertiajs/vue3';

defineProps({ links: { type: Array, default: () => [] } });
</script>

<template>
    <div v-if="links.length > 3" class="flex flex-wrap gap-1 mt-5">
        <Link
            v-for="(link, i) in links"
            :key="i"
            :href="link.url || '#'"
            v-html="link.label"
            preserve-scroll
            class="min-w-[36px] text-center px-3 py-1.5 rounded-lg text-sm border transition-colors"
            :class="[
                link.active
                    ? 'bg-[var(--primary)] text-white border-[var(--primary)]'
                    : 'border-[var(--border)] text-[var(--text-muted)] hover:bg-[var(--surface2)]',
                !link.url ? 'opacity-40 pointer-events-none' : '',
            ]"
        />
    </div>
</template>
