<script setup>
import { Link, usePage } from '@inertiajs/vue3';

const props = defineProps({
    href: { type: String, default: '/' },
    size: { type: String, default: 'text-xl' },
    compact: { type: Boolean, default: false },
    suffix: { type: String, default: '' },
    suffixClass: { type: String, default: '' },
    imgClass: { type: String, default: '' },
});

const site = usePage().props.site;
</script>

<template>
    <Link :href="href" class="flex items-center gap-2.5 font-heading font-extrabold tracking-tight group" :class="size">
        <img
            src="/images/sikhun-icon.png"
            alt="Sikhun"
            class="shrink-0 object-contain rounded-xl shadow-[0_0_16px_rgba(0,242,254,.25)] border border-cyan-400/30 transition-transform duration-300 group-hover:scale-105"
            :class="compact ? 'h-8 w-8' : 'h-10 w-10'"
            onerror="this.src='/images/sikhun-mark.svg'"
        />
        <div class="flex flex-col leading-none">
            <span class="text-white flex items-center">
                {{ site?.name || 'Sikhun' }}<span class="text-orange-500">.</span><span class="text-cyan-400 font-bold text-xs uppercase tracking-wider ml-0.5">com</span>
            </span>
            <span v-if="!compact" class="text-[9.5px] font-semibold text-gray-400 mt-1 tracking-wide font-sans">
                স্মার্ট AI স্টাডি প্ল্যাটফর্ম
            </span>
        </div>
        <span v-if="suffix" :class="suffixClass || 'text-xs font-normal text-[var(--text-muted)]'">{{ suffix }}</span>
    </Link>
</template>
