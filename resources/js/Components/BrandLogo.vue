<script setup>
import { computed } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import { useI18n } from '@/i18n';

const props = defineProps({
    href: { type: String, default: '/' },
    compact: { type: Boolean, default: false },
    size: { type: String, default: 'normal' }, // 'sm', 'normal', 'lg'
    suffix: { type: String, default: '' },
    suffixClass: { type: String, default: '' },
});

const { locale } = useI18n();
const page = usePage();
const site = computed(() => page.props.site ?? {});
const isBangla = computed(() => locale.value === 'bn');
const tagline = computed(() => site.value.tagline || (isBangla.value ? 'আপনার এআই পড়ার টেবিল' : 'YOUR AI STUDY DESK'));
const logoUrl = computed(() => site.value.logo_url || '/images/sikhun-mark.svg');
</script>

<template>
    <Link :href="href" class="inline-flex items-center gap-2.5 sm:gap-3 group select-none transition-transform duration-200 hover:scale-[1.015]">
        <div class="relative flex shrink-0 items-center justify-center transition-transform duration-300 group-hover:scale-105"
             :class="size === 'sm' ? 'h-8 w-8' : (size === 'lg' ? 'h-12 w-12' : 'h-9 w-9 sm:h-10 sm:w-10')">
            <img :src="logoUrl" :alt="isBangla ? 'শিখুন লোগো' : 'Sikhun logo'" class="h-full w-full object-contain" />
        </div>

        <!-- Wordmark & Tagline -->
        <div class="flex flex-col justify-center leading-none">
            <!-- Bangla Brand Wordmark -->
            <div v-if="isBangla" class="flex flex-col">
                <span class="font-bangla-classic font-bold text-slate-900 dark:text-slate-100 transition-colors"
                      :class="size === 'sm' ? 'text-lg' : (size === 'lg' ? 'text-2xl sm:text-3xl' : 'text-xl sm:text-2xl')">
                    {{ site.name && site.name.toLowerCase() !== 'sikhun.com' ? site.name : 'শিখুন' }}
                </span>
                <span v-if="!compact" class="text-[9px] sm:text-[10px] font-semibold text-orange-600 dark:text-orange-400 mt-1 flex items-center gap-1 font-bangla">
                    <span>— ◆</span>
                    <span>{{ tagline }}</span>
                </span>
            </div>

            <!-- English Brand Wordmark -->
            <div v-else class="flex flex-col">
                <span class="font-serif-classic font-extrabold tracking-[0.18em] uppercase text-slate-900 dark:text-slate-100 transition-colors"
                      :class="size === 'sm' ? 'text-base' : (size === 'lg' ? 'text-2xl sm:text-3xl' : 'text-lg sm:text-xl')">
                    {{ (site.name || 'Sikhun.com').replace(/\.com.*$/i, '') }}<em v-if="(site.name || 'Sikhun.com').toLowerCase().includes('.com')" class="not-italic text-[var(--primary)]">.com</em>
                </span>
                <span v-if="!compact" class="text-[8px] sm:text-[9px] font-bold text-orange-600 dark:text-orange-400 mt-1 tracking-[0.14em] uppercase flex items-center gap-1 font-sans">
                    <span>— ◆</span>
                    <span>{{ tagline }}</span>
                </span>
            </div>
        </div>

        <span v-if="suffix" :class="suffixClass || 'text-xs font-normal text-slate-500 dark:text-slate-400'">{{ suffix }}</span>
    </Link>
</template>
