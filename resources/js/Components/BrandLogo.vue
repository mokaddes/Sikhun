<script setup>
import { computed } from 'vue';
import { Link } from '@inertiajs/vue3';
import { useI18n } from '@/i18n';

const props = defineProps({
    href: { type: String, default: '/' },
    compact: { type: Boolean, default: false },
    size: { type: String, default: 'normal' }, // 'sm', 'normal', 'lg'
    suffix: { type: String, default: '' },
    suffixClass: { type: String, default: '' },
});

const { locale } = useI18n();
const isBangla = computed(() => locale.value === 'bn');
</script>

<template>
    <Link :href="href" class="inline-flex items-center gap-2.5 sm:gap-3 group select-none transition-transform duration-200 hover:scale-[1.015]">
        <!-- Brand Icon: Open Book with Dual Blue & Orange Pages + AI Radiant Star -->
        <div class="relative shrink-0 flex items-center justify-center transition-transform duration-300 group-hover:scale-105"
             :class="size === 'sm' ? 'w-8 h-8' : (size === 'lg' ? 'w-12 h-12' : 'w-9 h-9 sm:w-10 sm:h-10')">
            <svg viewBox="0 0 120 120" class="w-full h-full drop-shadow-sm filter" fill="none" xmlns="http://www.w3.org/2000/svg">
                <defs>
                    <linearGradient id="bl-cyan" x1="0%" y1="0%" x2="100%" y2="100%">
                        <stop offset="0%" stop-color="#00d2ff" />
                        <stop offset="100%" stop-color="#0077b6" />
                    </linearGradient>
                    <linearGradient id="bl-orange" x1="0%" y1="0%" x2="100%" y2="100%">
                        <stop offset="0%" stop-color="#ff9e00" />
                        <stop offset="100%" stop-color="#ea580c" />
                    </linearGradient>
                    <linearGradient id="bl-gold" x1="0%" y1="0%" x2="100%" y2="100%">
                        <stop offset="0%" stop-color="#fbbf24" />
                        <stop offset="100%" stop-color="#d97706" />
                    </linearGradient>
                </defs>

                <!-- AI Neural Diamond Network -->
                <g stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round" class="text-slate-700 dark:text-slate-300">
                    <line x1="60" y1="56" x2="60" y2="28" />
                    <line x1="60" y1="56" x2="38" y2="40" />
                    <line x1="60" y1="56" x2="82" y2="40" />
                    <line x1="38" y1="40" x2="60" y2="20" />
                    <line x1="82" y1="40" x2="60" y2="20" />
                </g>

                <!-- AI Nodes -->
                <circle cx="60" cy="56" r="3.8" fill="currentColor" class="text-slate-900 dark:text-slate-100" />
                <circle cx="38" cy="40" r="4.2" fill="#00d2ff" stroke="#ffffff" stroke-width="1.2" />
                <circle cx="82" cy="40" r="4.2" fill="#ff9e00" stroke="#ffffff" stroke-width="1.2" />

                <!-- Radiant 4-pointed Knowledge Star -->
                <path d="M60 4 C60 16 64 20 76 20 C64 20 60 24 60 36 C60 24 56 20 44 20 C56 20 60 16 60 4 Z" fill="url(#bl-gold)" />

                <!-- Book Left Page (Cyan-Blue) -->
                <path d="M59 74 C44 65 30 63 12 66 C11.5 66.1 11 66.6 11 67.2 L11 96 C11 96.6 11.5 97.1 12 97 C30 94 44 96 59 104 Z" fill="url(#bl-cyan)" />
                <!-- Book Right Page (Warm Orange) -->
                <path d="M61 74 C76 65 90 63 108 66 C108.5 66.1 109 66.6 109 67.2 L109 96 C109 96.6 108.5 97.1 108 97 C90 94 76 96 61 104 Z" fill="url(#bl-orange)" />

                <!-- Dual Underlines -->
                <rect x="18" y="106" width="36" height="4" rx="2" fill="#00b4d8" />
                <rect x="66" y="106" width="36" height="4" rx="2" fill="#f77f00" />
            </svg>
        </div>

        <!-- Wordmark & Tagline -->
        <div class="flex flex-col justify-center leading-none">
            <!-- Bangla Brand Wordmark -->
            <div v-if="isBangla" class="flex flex-col">
                <span class="font-bangla-classic font-bold text-slate-900 dark:text-slate-100 transition-colors"
                      :class="size === 'sm' ? 'text-lg' : (size === 'lg' ? 'text-2xl sm:text-3xl' : 'text-xl sm:text-2xl')">
                    শিখুন
                </span>
                <span v-if="!compact" class="text-[9px] sm:text-[10px] font-semibold text-orange-600 dark:text-orange-400 mt-1 flex items-center gap-1 font-bangla">
                    <span>— ◆</span>
                    <span>আপনার এআই পড়ার টেবিল</span>
                </span>
            </div>

            <!-- English Brand Wordmark -->
            <div v-else class="flex flex-col">
                <span class="font-serif-classic font-extrabold tracking-[0.18em] uppercase text-slate-900 dark:text-slate-100 transition-colors"
                      :class="size === 'sm' ? 'text-base' : (size === 'lg' ? 'text-2xl sm:text-3xl' : 'text-lg sm:text-xl')">
                    SIKHUN
                </span>
                <span v-if="!compact" class="text-[8px] sm:text-[9px] font-bold text-orange-600 dark:text-orange-400 mt-1 tracking-[0.14em] uppercase flex items-center gap-1 font-sans">
                    <span>— ◆</span>
                    <span>YOUR AI STUDY DESK</span>
                </span>
            </div>
        </div>

        <span v-if="suffix" :class="suffixClass || 'text-xs font-normal text-slate-500 dark:text-slate-400'">{{ suffix }}</span>
    </Link>
</template>
