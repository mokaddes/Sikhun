<script setup>
import { ref, computed, onMounted, onBeforeUnmount } from 'vue';

const props = defineProps({ durationMinutes: { type: Number, required: true } });
const emit = defineEmits(['expired']);

const secondsLeft = ref(props.durationMinutes * 60);
let interval;

const display = computed(() => {
    const m = Math.floor(secondsLeft.value / 60);
    const s = secondsLeft.value % 60;
    return `${m}:${s.toString().padStart(2, '0')}`;
});

const urgent = computed(() => secondsLeft.value <= props.durationMinutes * 60 * 0.2);

onMounted(() => {
    interval = setInterval(() => {
        secondsLeft.value--;
        if (secondsLeft.value <= 0) {
            clearInterval(interval);
            emit('expired');
        }
    }, 1000);
});
onBeforeUnmount(() => clearInterval(interval));
</script>

<template>
    <div class="px-4 py-2 rounded-lg font-mono font-bold text-lg" :class="urgent ? 'bg-[var(--accent)]/15 text-[var(--accent)]' : 'bg-[var(--surface2)] text-[var(--text)]'">
        {{ display }}
    </div>
</template>
