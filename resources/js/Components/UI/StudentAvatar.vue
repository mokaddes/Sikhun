<script setup>
import { computed } from 'vue';

const props = defineProps({
    avatar: {
        type: String,
        default: null,
    },
    name: {
        type: String,
        default: 'Student',
    },
    size: {
        type: String,
        default: 'md', // xs, sm, md, lg, xl
    },
});

const presetMap = {
    'grad-cap': '🎓',
    'rocket': '🚀',
    'brain': '💡',
    'microscope': '🔬',
    'book': '📚',
    'laptop': '💻',
    'target': '🎯',
    'trophy': '🏆',
    'star': '⭐',
    'lion': '🦁',
};

const isImageUrl = computed(() => {
    if (!props.avatar) return false;
    const a = props.avatar.trim();
    return a.startsWith('http://') || a.startsWith('https://') || a.startsWith('/storage/') || a.startsWith('data:image/');
});

const emojiIcon = computed(() => {
    if (!props.avatar) return null;
    const a = props.avatar.trim();
    if (presetMap[a]) {
        return presetMap[a];
    }
    // Check if it's already an emoji (length <= 4 and contains non-ascii)
    if (!isImageUrl.value && /[\p{Emoji}\u200d]+/u.test(a)) {
        return a;
    }
    return null;
});

const initial = computed(() => {
    return (props.name || 'S').trim().charAt(0).toUpperCase();
});

const sizeClasses = computed(() => {
    switch (props.size) {
        case 'xs': return 'h-6 w-6 text-xs';
        case 'sm': return 'h-8 w-8 text-sm';
        case 'md': return 'h-10 w-10 text-base';
        case 'lg': return 'h-14 w-14 text-xl';
        case 'xl': return 'h-20 w-20 text-3xl';
        default: return props.size;
    }
});
</script>

<template>
    <div
        class="relative inline-flex shrink-0 select-none items-center justify-center overflow-hidden rounded-full font-bold shadow-sm transition-transform"
        :class="[sizeClasses, !isImageUrl ? 'bg-gradient-to-br from-[var(--secondary)] to-[var(--primary)] text-white' : 'bg-[var(--surface2)]']"
    >
        <img
            v-if="isImageUrl"
            :src="avatar"
            :alt="name"
            class="h-full w-full object-cover"
            loading="lazy"
        />
        <span v-else-if="emojiIcon" class="leading-none">
            {{ emojiIcon }}
        </span>
        <span v-else class="leading-none font-heading uppercase text-white">
            {{ initial }}
        </span>
    </div>
</template>
