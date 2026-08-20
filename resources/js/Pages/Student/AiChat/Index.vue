<script setup>
import { ref } from 'vue';
import { Head, router } from '@inertiajs/vue3';
import StudentLayout from '@/Components/Layout/StudentLayout.vue';
import { useI18n } from '@/i18n';
import ChatSidebar from './Partials/ChatSidebar.vue';
import { PlusIcon, SparklesIcon, Bars3Icon } from '@heroicons/vue/24/outline';

const props = defineProps({ sessions: Array, books: Array });

const { t } = useI18n();
const sidebarOpen = ref(false);
const creating = ref(false);

function startNewChat() {
    creating.value = true;
    router.post('/ai/chat', {}, { onFinish: () => (creating.value = false) });
}
</script>

<template>
    <Head :title="t('ai_chat.title')" />
    <StudentLayout>
        <div class="flex h-[calc(100dvh-9rem)] min-h-[440px] overflow-hidden rounded-2xl border border-[var(--border)] bg-[var(--surface)] lg:h-[calc(100dvh-7rem)]">
            <ChatSidebar v-model:open="sidebarOpen" :sessions="sessions" :active-id="null" />

            <section class="flex-1 flex flex-col min-w-0">
                <header class="flex items-center gap-3 px-4 py-3 border-b border-[var(--border)] lg:hidden">
                    <button class="icon-btn w-8 h-8" aria-label="Open history" @click="sidebarOpen = true">
                        <Bars3Icon class="w-5 h-5" />
                    </button>
                    <span class="font-heading font-bold">{{ t('ai_chat.title') }}</span>
                </header>

                <div class="flex-1 flex flex-col items-center justify-center px-6 py-10 text-center">
                    <div class="sun-disc w-16 h-16 mb-8 flex items-center justify-center text-white">
                        <SparklesIcon class="w-8 h-8" />
                    </div>
                    <h1 class="font-heading text-2xl sm:text-3xl font-extrabold mb-2">{{ t('ai_chat.greeting') }}</h1>
                    <p class="text-sm text-[var(--text-muted)] max-w-md mb-8">{{ t('ai_chat.greeting_sub') }}</p>

                    <button
                        @click="startNewChat"
                        :disabled="creating"
                        class="btn btn-primary gap-2"
                    >
                        <PlusIcon class="w-5 h-5" />
                        {{ creating ? t('ai_chat.thinking') : t('ai_chat.new_chat') }}
                    </button>
                </div>
            </section>
        </div>
    </StudentLayout>
</template>