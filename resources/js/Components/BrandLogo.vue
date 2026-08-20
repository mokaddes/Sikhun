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
    <Link :href="href" class="flex items-center gap-2.5 font-heading font-extrabold tracking-tight" :class="size">
        <img
            v-if="site?.logo_url"
            :src="site.logo_url"
            :alt="site?.name || 'logo'"
            class="object-contain"
            :class="imgClass || (compact ? 'h-7 w-auto' : 'h-8 w-auto max-w-[180px]')"
        />
        <template v-else>
            <span
                class="sun-disc flex items-center justify-center text-white font-heading font-extrabold"
                :class="compact ? 'w-7 h-7 text-[11px]' : 'w-9 h-9 text-sm'"
            >শি</span>
            <span>{{ site?.name || 'Sikhun.com' }}</span>
        </template>
        <span v-if="suffix" :class="suffixClass || 'text-xs font-normal text-[var(--text-muted)]'">{{ suffix }}</span>
    </Link>
</template>