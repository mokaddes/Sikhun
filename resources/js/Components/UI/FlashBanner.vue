<script setup>
import { computed, ref, watch } from 'vue';
import { usePage } from '@inertiajs/vue3';

const page = usePage();
const visible = ref(false);

const success = computed(() => page.props.flash?.success);
const error = computed(() => page.props.flash?.error);

watch([success, error], ([s, e]) => {
    if (s || e) {
        visible.value = true;
        setTimeout(() => (visible.value = false), 4000);
    }
});
</script>

<template>
    <Transition
        enter-active-class="transition duration-200 ease-out"
        enter-from-class="opacity-0 -translate-y-2"
        leave-active-class="transition duration-150 ease-in"
        leave-to-class="opacity-0 -translate-y-2"
    >
        <div v-if="visible && (success || error)" class="mb-5">
            <div
                class="px-4 py-3 rounded-lg text-sm font-medium border"
                :class="success
                    ? 'bg-[var(--secondary)]/10 border-[var(--secondary)]/30 text-[var(--secondary)]'
                    : 'bg-[var(--accent)]/10 border-[var(--accent)]/30 text-[var(--accent)]'"
            >
                {{ success || error }}
            </div>
        </div>
    </Transition>
</template>
