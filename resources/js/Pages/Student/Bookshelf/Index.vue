<script setup>
import { Head, Link } from '@inertiajs/vue3';
import StudentLayout from '@/Components/Layout/StudentLayout.vue';
import { useI18n } from '@/i18n';

defineProps({ shelves: Array });
const { t } = useI18n();
</script>

<template>
    <Head :title="t('bookshelf.title')" />
    <StudentLayout>
        <h1 class="font-heading text-2xl font-extrabold mb-6">{{ t('bookshelf.title') }}</h1>

        <div v-if="shelves.length" class="grid grid-cols-2 md:grid-cols-4 gap-5">
            <div v-for="shelf in shelves" :key="shelf.id" class="rounded-xl border border-[var(--border)] bg-[var(--surface)] overflow-hidden">
                <div class="aspect-[3/4] bg-[var(--surface2)] flex items-center justify-center text-[var(--text-muted)] text-xs overflow-hidden">
                    <img v-if="shelf.book.cover_image_url" :src="shelf.book.cover_image_url" class="w-full h-full object-cover" />
                    <span v-else>{{ t('home.no_cover') }}</span>
                </div>
                <div class="p-4">
                    <div class="font-medium text-sm mb-2 line-clamp-2">{{ shelf.book.title }}</div>
                    <Link :href="`/library/${shelf.book.id}/read`" class="text-sm font-medium text-[var(--primary)] hover:underline">
                        {{ t('bookshelf.continue_reading') }} →
                    </Link>
                </div>
            </div>
        </div>
        <div v-else class="rounded-xl border border-dashed border-[var(--border)] p-12 text-center text-[var(--text-muted)]">
            {{ t('bookshelf.empty') }}
            <div class="mt-4">
                <Link href="/library" class="text-[var(--primary)] font-medium">{{ t('nav.library') }} →</Link>
            </div>
        </div>
    </StudentLayout>
</template>
