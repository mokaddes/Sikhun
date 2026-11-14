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
const brandName = computed(() => isBangla.value
    ? (site.value.name_bn || 'শিখুন')
    : (site.value.name_en || site.value.name || 'Sikhun.com'));
const tagline = computed(() => isBangla.value
    ? (site.value.tagline_bn || 'আপনার এআই পড়ার টেবিল')
    : (site.value.tagline_en || site.value.tagline || 'YOUR AI STUDY DESK'));
const logoUrl = computed(() => site.value.logo_url || '/images/sikhun-mark.svg');
</script>

<template>
    <Link :href="href" class="inline-flex items-center gap-3 sm:gap-3.5 group select-none transition-transform duration-200 hover:scale-[1.015]">
        <div class="relative flex shrink-0 items-center justify-center transition-transform duration-300 group-hover:scale-105"
             :class="size === 'sm' ? 'h-9 w-9 sm:h-10 sm:w-10' : (size === 'lg' ? 'h-14 w-14 sm:h-16 sm:w-16' : 'h-11 w-11 sm:h-12 sm:w-12')">
            <img :src="logoUrl" :alt="isBangla ? 'শিখুন লোগো' : 'Sikhun logo'" class="h-full w-full object-contain" />
        </div>

        <!-- Wordmark & Tagline -->
        <div class="flex flex-col justify-center leading-none">
            <!-- Bangla Brand Wordmark -->
            <div v-if="isBangla" class="flex flex-col">
                <span class="font-bangla-classic font-bold text-slate-900 dark:text-slate-100 transition-colors"
                      :class="size === 'sm' ? 'text-lg sm:text-xl' : (size === 'lg' ? 'text-2xl sm:text-3xl' : 'text-xl sm:text-2xl')">
                    {{ brandName }}
                </span>
                <span v-if="!compact" class="text-[9.5px] sm:text-[10.5px] font-semibold text-orange-600 dark:text-orange-400 mt-1 flex items-center gap-1 font-bangla">
                    <span>— ◆</span>
                    <span>{{ tagline }}</span>
                </span>
            </div>

            <!-- English Brand Wordmark -->
            <div v-else class="flex flex-col">
                <span class="font-serif-classic font-extrabold tracking-[0.18em] uppercase text-slate-900 dark:text-slate-100 transition-colors"
                      :class="size === 'sm' ? 'text-base sm:text-lg' : (size === 'lg' ? 'text-2xl sm:text-3xl' : 'text-lg sm:text-xl')">
                    {{ brandName.replace(/\.com.*$/i, '') }}<em v-if="brandName.toLowerCase().includes('.com')" class="not-italic text-[var(--primary)]">.com</em>
                </span>
                <span v-if="!compact" class="text-[8.5px] sm:text-[9.5px] font-bold text-orange-600 dark:text-orange-400 mt-1 tracking-[0.14em] uppercase flex items-center gap-1 font-sans">
                    <span>— ◆</span>
                    <span>{{ tagline }}</span>
                </span>
            </div>
        </div>

        <span v-if="suffix" :class="suffixClass || 'text-xs font-normal text-slate-500 dark:text-slate-400'">{{ suffix }}</span>
    </Link>
</template>
