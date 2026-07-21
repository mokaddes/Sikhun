<script setup>
import { router } from '@inertiajs/vue3';
import { useI18n } from '@/i18n';

const props = defineProps({
    href: { type: String, required: true },
    method: { type: String, default: 'delete' },
    label: { type: String, default: null },
    confirmMessage: { type: String, default: null },
});

const { t } = useI18n();

function handle() {
    if (confirm(props.confirmMessage || t('common.confirm_delete'))) {
        router[props.method](props.href, {}, { preserveScroll: true });
    }
}
</script>

<template>
    <button
        type="button"
        @click="handle"
        class="text-sm font-medium text-[var(--accent)] hover:underline"
    >
        {{ label || t('common.delete') }}
    </button>
</template>
