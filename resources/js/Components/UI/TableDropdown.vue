<script setup>
import { ref } from 'vue';
import { Link, router } from '@inertiajs/vue3';
import { onClickOutside } from '@vueuse/core';
import { EllipsisVerticalIcon } from '@heroicons/vue/24/outline';
import { useI18n } from '@/i18n';

const props = defineProps({
    items: {
        type: Array,
        default: () => [],
        // Each item: { label: String, href: String, icon: Component, onClick: Function, danger: Boolean, confirmMessage: String, method: String }
    },
    align: {
        type: String,
        default: 'right', // 'right' or 'left'
    },
});

const { t } = useI18n();
const isOpen = ref(false);
const dropdownRef = ref(null);

onClickOutside(dropdownRef, () => {
    isOpen.value = false;
});

function toggle() {
    isOpen.value = !isOpen.value;
}

function close() {
    isOpen.value = false;
}

function handleAction(item) {
    close();
    if (item.onClick) {
        item.onClick();
        return;
    }

    if (item.method && item.method.toLowerCase() === 'delete') {
        const msg = item.confirmMessage || t('common.confirm_delete');
        if (confirm(msg)) {
            router.delete(item.href, { preserveScroll: true });
        }
        return;
    }

    if (item.method && item.method.toLowerCase() === 'post') {
        router.post(item.href, {}, { preserveScroll: true });
        return;
    }

    if (item.method && item.method.toLowerCase() === 'patch') {
        router.patch(item.href, {}, { preserveScroll: true });
        return;
    }

    if (item.href) {
        router.visit(item.href);
    }
}
</script>

<template>
    <div ref="dropdownRef" class="relative inline-block text-left">
        <button
            type="button"
            @click.stop="toggle"
            class="flex h-8 w-8 items-center justify-center rounded-lg border border-transparent text-[var(--text-muted)] transition-all hover:border-[var(--border)] hover:bg-[var(--surface2)] hover:text-[var(--text)] focus:outline-hidden"
            :class="isOpen ? 'bg-[var(--surface2)] text-[var(--text)] border-[var(--border)]' : ''"
            title="Options"
            aria-label="Options"
        >
            <EllipsisVerticalIcon class="h-4.5 w-4.5" />
        </button>

        <transition
            enter-active-class="transition duration-100 ease-out"
            enter-from-class="transform scale-95 opacity-0"
            enter-to-class="transform scale-100 opacity-100"
            leave-active-class="transition duration-75 ease-in"
            leave-from-class="transform scale-100 opacity-100"
            leave-to-class="transform scale-95 opacity-0"
        >
            <div
                v-if="isOpen"
                class="absolute z-50 mt-1 min-w-[10rem] origin-top-right rounded-xl border border-[var(--border)] bg-[var(--surface)] p-1 shadow-lg ring-1 ring-black/5 focus:outline-hidden"
                :class="align === 'right' ? 'right-0' : 'left-0'"
            >
                <slot :close="close">
                    <template v-for="(item, idx) in items" :key="idx">
                        <hr v-if="item.divider" class="my-1 border-[var(--border)]" />
                        <component
                            :is="item.href && !item.method ? Link : 'button'"
                            v-else
                            :href="item.href"
                            type="button"
                            @click="handleAction(item)"
                            class="group flex w-full items-center gap-2.5 rounded-lg px-3 py-2 text-left text-xs font-medium transition-colors"
                            :class="item.danger
                                ? 'text-red-500 hover:bg-red-500/10 dark:hover:bg-red-500/15'
                                : 'text-[var(--text)] hover:bg-[var(--surface2)] hover:text-[var(--primary)]'"
                        >
                            <component
                                v-if="item.icon"
                                :is="item.icon"
                                class="h-4 w-4 shrink-0 transition-colors"
                                :class="item.danger ? 'text-red-500' : 'text-[var(--text-muted)] group-hover:text-[var(--primary)]'"
                            />
                            <span>{{ item.label }}</span>
                        </component>
                    </template>
                </slot>
            </div>
        </transition>
    </div>
</template>
