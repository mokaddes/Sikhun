<script setup>
import { Head, useForm } from '@inertiajs/vue3';
import AdminLayout from '@/Components/Layout/AdminLayout.vue';
import { useI18n } from '@/i18n';

const props = defineProps({ provider: Object });
const { t } = useI18n();
const isEdit = !!props.provider;

const useCaseOptions = [
    { value: 'book_chat', label: 'AI Chat with Books' },
    { value: 'exam_gen', label: 'Exam Generation' },
    { value: 'flashcard_gen', label: 'Flashcard Generation' },
    { value: 'essay_grade', label: 'Essay Grading' },
    { value: 'schedule_gen', label: 'Study Schedule Generation' },
    { value: 'notification_gen', label: 'Daily AI Notifications' },
    { value: 'support_bot', label: 'Support Chat Bot' },
];

const form = useForm({
    name: props.provider?.name ?? '',
    type: props.provider?.type ?? 'openai',
    api_key: '',
    model_name: props.provider?.model_name ?? '',
    api_endpoint: props.provider?.api_endpoint ?? '',
    is_active: props.provider?.is_active ?? true,
    max_tokens: props.provider?.max_tokens ?? 2000,
    temperature: props.provider?.temperature ?? 0.7,
    use_cases: props.provider?.use_case_list ?? [],
    default_use_cases: props.provider?.default_use_case_list ?? [],
});

const types = ['openai', 'gemini', 'claude', 'groq', 'deepseek', 'ollama', 'vllm', 'huggingface'];

function toggleAll() {
    form.use_cases = form.use_cases.length === useCaseOptions.length ? [] : useCaseOptions.map((u) => u.value);
}

function onUseCaseToggle(value, checked) {
    if (!checked) {
        // Un-checking a use case also removes it from "default for this use case"
        form.default_use_cases = form.default_use_cases.filter((v) => v !== value);
    }
}

function submit() {
    isEdit ? form.put(`/admin/ai-providers/${props.provider.id}`) : form.post('/admin/ai-providers');
}
</script>

<template>
    <Head :title="isEdit ? t('common.edit') : t('admin.ai_providers.new')" />
    <AdminLayout>
        <h1 class="font-heading text-2xl font-extrabold mb-6">{{ isEdit ? t('common.edit') : t('admin.ai_providers.new') }}</h1>
        <form @submit.prevent="submit" class="max-w-xl space-y-5 rounded-xl border border-[var(--border)] bg-[var(--surface)] p-6">
            <div>
                <label class="block text-sm font-medium mb-1.5">{{ t('common.name') }}</label>
                <input v-model="form.name" type="text" required placeholder="e.g. Main OpenAI Account"
                    class="w-full px-4 py-2.5 rounded-lg bg-[var(--surface2)] border border-[var(--border)]" />
                <p class="text-xs text-[var(--text-muted)] mt-1">One name per set of credentials — you'll assign use cases below, no need to duplicate this per feature.</p>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium mb-1.5">{{ t('admin.ai_providers.provider_type') }}</label>
                    <select v-model="form.type" class="w-full px-4 py-2.5 rounded-lg bg-[var(--surface2)] border border-[var(--border)]">
                        <option v-for="ty in types" :key="ty" :value="ty">{{ ty }}</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1.5">{{ t('admin.ai_providers.model_name') }}</label>
                    <input v-model="form.model_name" type="text" required placeholder="gpt-4o" class="w-full px-4 py-2.5 rounded-lg bg-[var(--surface2)] border border-[var(--border)]" />
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium mb-1.5">{{ t('admin.ai_providers.api_key') }}</label>
                <input v-model="form.api_key" type="password" :placeholder="isEdit ? 'Leave blank to keep current key' : ''" class="w-full px-4 py-2.5 rounded-lg bg-[var(--surface2)] border border-[var(--border)]" />
            </div>
            <div v-if="form.type === 'ollama' || form.type === 'vllm'">
                <label class="block text-sm font-medium mb-1.5">Local Endpoint URL</label>
                <input v-model="form.api_endpoint" type="text" placeholder="http://localhost:11434" class="w-full px-4 py-2.5 rounded-lg bg-[var(--surface2)] border border-[var(--border)]" />
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium mb-1.5">{{ t('admin.ai_providers.max_tokens') }}</label>
                    <input v-model="form.max_tokens" type="number" min="1" class="w-full px-4 py-2.5 rounded-lg bg-[var(--surface2)] border border-[var(--border)]" />
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1.5">{{ t('admin.ai_providers.temperature') }}</label>
                    <input v-model="form.temperature" type="number" step="0.1" min="0" max="2" class="w-full px-4 py-2.5 rounded-lg bg-[var(--surface2)] border border-[var(--border)]" />
                </div>
            </div>

            <!-- Multi-select use cases -->
            <div>
                <div class="flex items-center justify-between mb-2">
                    <label class="block text-sm font-medium">Use this credential for</label>
                    <button type="button" @click="toggleAll" class="text-xs text-[var(--primary)] hover:underline">
                        {{ form.use_cases.length === useCaseOptions.length ? 'Deselect all' : 'Select all' }}
                    </button>
                </div>
                <p class="text-xs text-[var(--text-muted)] mb-3">
                    Check every feature this API key should power. For each checked feature, optionally mark it as the
                    <strong>default</strong> provider — only one provider can be default per feature platform-wide.
                </p>
                <div class="space-y-2 rounded-lg border border-[var(--border)] p-3">
                    <div v-for="uc in useCaseOptions" :key="uc.value" class="flex items-center justify-between text-sm">
                        <label class="flex items-center gap-2 flex-1">
                            <input
                                type="checkbox"
                                :value="uc.value"
                                v-model="form.use_cases"
                                @change="onUseCaseToggle(uc.value, $event.target.checked)"
                                class="rounded"
                            />
                            {{ uc.label }}
                        </label>
                        <label v-if="form.use_cases.includes(uc.value)" class="flex items-center gap-1.5 text-xs text-[var(--text-muted)]">
                            <input type="checkbox" :value="uc.value" v-model="form.default_use_cases" class="rounded" />
                            default
                        </label>
                    </div>
                </div>
                <p v-if="form.errors.use_cases" class="text-[var(--accent)] text-xs mt-1">{{ form.errors.use_cases }}</p>
            </div>

            <label class="flex items-center gap-2 text-sm"><input v-model="form.is_active" type="checkbox" class="rounded" /> {{ t('common.active') }}</label>

            <button type="submit" :disabled="form.processing" class="px-5 py-2.5 rounded-lg bg-[var(--primary)] hover:bg-[var(--primary-hover)] text-white text-sm font-semibold disabled:opacity-60">
                {{ form.processing ? t('common.saving') : t('common.save') }}
            </button>
        </form>
    </AdminLayout>
</template>
