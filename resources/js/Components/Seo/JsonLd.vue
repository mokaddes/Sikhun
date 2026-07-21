<script setup>
defineProps({ data: { type: [Object, Array], required: true } });
</script>

<template>
    <component :is="'script'" type="application/ld+json" v-html="JSON.stringify(data)" />
</template>
