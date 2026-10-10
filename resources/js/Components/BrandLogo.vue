<script setup>
import { Link, usePage } from '@inertiajs/vue3';

const props = defineProps({
    href: { type: String, default: '/' },
    // Text size for the wordmark (used when no logo image is set).
    size: { type: String, default: 'text-xl' },
    // Compact footer variant (smaller mark + wordmark).
    compact: { type: Boolean, default: false },
    // Optional suffix rendered after the wordmark (e.g. "Admin").
    suffix: { type: String, default: '' },
    suffixClass: { type: String, default: '' },
    imgClass: { type: String, default: '' },
});

const site = usePage().props.site;
</script>

<template>
    <Link :href="href" class="flex items-center gap-0.5 font-heading font-extrabold tracking-tight" :class="size">
        <img
            src="/images/sikhun-mark.svg"
            alt=""
            class="shrink-0 object-contain drop-shadow-[0_0_12px_rgba(0,217,242,.18)]"
            :class="compact ? 'h-8 w-8' : 'h-10 w-10'"
        />
        <span>{{ site?.name || 'Sikhun.com' }}</span>
        <span v-if="suffix" :class="suffixClass || 'text-xs font-normal text-[var(--text-muted)]'">{{ suffix }}</span>
    </Link>
</template>
