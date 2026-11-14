<script setup>
import { ref, computed } from 'vue';
import { ShareIcon, LinkIcon, CheckIcon } from '@heroicons/vue/24/outline';

const props = defineProps({
    title: { type: String, required: true },
    description: { type: String, default: '' },
    en: { type: Boolean, default: false },
});

const copied = ref(false);
const status = computed(() => copied.value ? (props.en ? 'Link copied' : 'লিংক কপি হয়েছে') : '');

async function copyLink() {
    const url = window.location.href;
    try {
        await navigator.clipboard.writeText(url);
    } catch {
        const input = document.createElement('textarea');
        input.value = url;
        input.style.position = 'fixed';
        input.style.opacity = '0';
        document.body.appendChild(input);
        input.select();
        document.execCommand('copy');
        input.remove();
    }
    copied.value = true;
    window.setTimeout(() => { copied.value = false; }, 2200);
}

async function shareNative() {
    const payload = { title: props.title, text: props.description || props.title, url: window.location.href };
    if (navigator.share) {
        try { await navigator.share(payload); } catch (error) { if (error?.name !== 'AbortError') await copyLink(); }
    } else {
        await copyLink();
    }
}

function shareTo(service) {
    const url = window.location.href;
    const text = [props.title, props.description].filter(Boolean).join(' — ');
    const destination = service === 'facebook'
        ? `https://www.facebook.com/sharer/sharer.php?u=${encodeURIComponent(url)}`
        : `https://api.whatsapp.com/send?text=${encodeURIComponent(`${text} ${url}`)}`;
    window.open(destination, '_blank', 'noopener,noreferrer,width=680,height=600');
}
</script>

<template>
    <div class="flex flex-wrap items-center gap-2.5 rounded-2xl border border-[var(--border)] bg-[var(--surface)] p-3">
        <span class="mr-1 text-xs font-semibold text-[var(--text-muted)]">{{ en ? 'Share this page' : 'শেয়ার করুন' }}</span>
        <button type="button" @click="shareNative" class="inline-flex items-center gap-2 rounded-xl bg-[var(--primary)] px-3.5 py-2.5 text-xs font-semibold text-white transition hover:opacity-90 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[var(--primary)]">
            <ShareIcon class="h-4 w-4" />{{ en ? 'Share' : 'শেয়ার' }}
        </button>
        <button type="button" @click="shareTo('facebook')" :aria-label="en ? 'Share on Facebook' : 'ফেসবুকে শেয়ার করুন'" class="rounded-xl border border-[var(--border)] bg-[var(--surface2)] px-3.5 py-2.5 text-xs font-semibold transition hover:border-[var(--primary)]">Facebook</button>
        <button type="button" @click="shareTo('whatsapp')" :aria-label="en ? 'Share on WhatsApp' : 'হোয়াটসঅ্যাপে শেয়ার করুন'" class="rounded-xl border border-[var(--border)] bg-[var(--surface2)] px-3.5 py-2.5 text-xs font-semibold transition hover:border-[var(--primary)]">WhatsApp</button>
        <button type="button" @click="copyLink" class="inline-flex items-center gap-1.5 rounded-xl border border-[var(--border)] bg-[var(--surface2)] px-3.5 py-2.5 text-xs font-semibold transition hover:border-[var(--primary)]">
            <CheckIcon v-if="copied" class="h-4 w-4" /><LinkIcon v-else class="h-4 w-4" />{{ copied ? (en ? 'Copied' : 'কপি হয়েছে') : (en ? 'Copy link' : 'লিংক কপি') }}
        </button>
        <span v-if="status" class="sr-only" role="status">{{ status }}</span>
    </div>
</template>
