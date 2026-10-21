<script setup>
import { computed } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import AdminLayout from '@/Components/Layout/AdminLayout.vue';
import { useI18n } from '@/i18n';
import {
    SparklesIcon,
    ArrowLeftIcon,
    CheckCircleIcon,
    PlusIcon,
    TrashIcon,
} from '@heroicons/vue/24/outline';

const props = defineProps({
    provider: Object,
    providerTypes: Object,
});

const { t, locale } = useI18n();
const isEdit = !!props.provider;

const providerTypeOptions = [
    {
        key: 'openai_compatible',
        label: 'OpenAI Compatible',
        recommended: true,
        description: 'OpenAI, Groq, DeepSeek, Ollama, vLLM, OpenRouter, and other compatible APIs.',
        defaultEndpoint: 'https://api.openai.com/v1',
        placeholderEndpoint: 'https://api.openai.com/v1 (or Groq, DeepSeek, Ollama, OpenRouter)',
        defaultModel: 'gpt-4o',
        keyLabel: 'API Key (Bearer)',
        keyPlaceholder: 'sk-...',
    },
    {
        key: 'anthropic_compatible',
        label: 'Anthropic Compatible',
        recommended: false,
        description: 'Anthropic Claude API and providers implementing the Anthropic Messages API.',
        defaultEndpoint: 'https://api.anthropic.com',
        placeholderEndpoint: 'https://api.anthropic.com',
        defaultModel: 'claude-3-5-sonnet-20241022',
        keyLabel: 'Anthropic API Key (x-api-key)',
        keyPlaceholder: 'sk-ant-...',
    },
    {
        key: 'google_gemini',
        label: 'Google Gemini',
        recommended: false,
        description: 'Google Gemini API and its native request/response format.',
        defaultEndpoint: 'https://generativelanguage.googleapis.com',
        placeholderEndpoint: 'https://generativelanguage.googleapis.com',
        defaultModel: 'gemini-1.5-flash',
        keyLabel: 'Gemini API Key',
        keyPlaceholder: 'AIzaSy...',
    },
    {
        key: 'custom',
        label: 'Custom API',
        recommended: false,
        description: 'Custom base URL, authentication, headers, and API implementation.',
        defaultEndpoint: '',
        placeholderEndpoint: 'https://your-api.example.com/api/v1/chat/completions',
        defaultModel: 'custom-model',
        keyLabel: 'API Key (Optional)',
        keyPlaceholder: 'sk-... (Sent as Bearer if no Authorization header)',
    },
];

const useCaseOptions = [
    { value: 'book_chat', label: 'AI Chat with Books' },
    { value: 'exam_gen', label: 'Exam Generation' },
    { value: 'flashcard_gen', label: 'Flashcard Generation' },
    { value: 'essay_grade', label: 'Essay Grading' },
    { value: 'schedule_gen', label: 'Study Schedule Generation' },
    { value: 'notification_gen', label: 'Daily AI Notifications' },
    { value: 'support_bot', label: 'Support Chat Bot' },
    { value: 'book_ocr', label: 'Book OCR / Document Processing' },
    { value: 'book_retrieval', label: 'Book RAG / Retrieval' },
    { value: 'book_structure', label: 'Book Chapter / Structure Detection' },
    { value: 'blog_translate', label: 'Blog Translation (to Bengali)' },
    { value: 'blog_rewrite', label: 'Blog AI Rewriting & SEO' },
];

// Map legacy type names if editing an old record
function mapInitialType(type) {
    if (!type) return 'openai_compatible';
    if (['openai', 'groq', 'deepseek', 'ollama', 'vllm', 'huggingface'].includes(type)) return 'openai_compatible';
    if (type === 'claude') return 'anthropic_compatible';
    if (type === 'gemini') return 'google_gemini';
    return type;
}

const form = useForm({
    name: props.provider?.name ?? '',
    type: mapInitialType(props.provider?.type),
    api_key: '',
    model_name: props.provider?.model_name ?? '',
    api_endpoint: props.provider?.api_endpoint ?? '',
    custom_headers: props.provider?.custom_headers ?? [],
    is_active: props.provider?.is_active ?? true,
    max_tokens: props.provider?.max_tokens ?? 2000,
    temperature: props.provider?.temperature ?? 0.7,
    use_cases: props.provider?.use_case_list ?? [],
    default_use_cases: props.provider?.default_use_case_list ?? [],
});

const currentTypeConfig = computed(() => {
    return providerTypeOptions.find(o => o.key === form.type) || providerTypeOptions[0];
});

function selectType(typeKey) {
    form.type = typeKey;
    const config = providerTypeOptions.find(o => o.key === typeKey);
    if (!form.model_name && config?.defaultModel) {
        form.model_name = config.defaultModel;
    }
}

function addHeader() {
    form.custom_headers.push({ name: '', value: '' });
}

function removeHeader(index) {
    form.custom_headers.splice(index, 1);
}

function toggleAll() {
    form.use_cases = form.use_cases.length === useCaseOptions.length ? [] : useCaseOptions.map((u) => u.value);
}

function onUseCaseToggle(value, checked) {
    if (!checked) {
        form.default_use_cases = form.default_use_cases.filter((v) => v !== value);
    }
}

function submit() {
    if (isEdit) {
        form.put(`/admin/ai-providers/${props.provider.id}`);
    } else {
        form.post('/admin/ai-providers');
    }
}
</script>

<template>
    <Head :title="isEdit ? t('common.edit') + ' — ' + form.name : t('admin.ai_providers.new')" />
    <AdminLayout>
        <div class="mb-6 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <Link href="/admin/ai-providers" class="icon-btn" title="Back to AI Providers">
                    <ArrowLeftIcon class="h-4 w-4" />
                </Link>
                <div>
                    <h1 class="font-heading text-2xl font-extrabold text-[var(--text)]">
                        {{ isEdit ? t('common.edit') + ': ' + form.name : t('admin.ai_providers.new') }}
                    </h1>
                    <p class="text-xs text-[var(--text-muted)] mt-0.5">
                        {{ locale === 'en' ? 'Configure LLM credentials, provider type, and assigned use cases.' : 'এলএলএম ক্রেডেনশিয়াল, প্রোভাইডারের ধরণ এবং ব্যবহারের ক্ষেত্রসমূহ সেট করুন।' }}
                    </p>
                </div>
            </div>
        </div>

        <form @submit.prevent="submit" class="max-w-3xl space-y-6">
            <!-- 1. Provider Type Selection (4 Canonical Types) -->
            <div class="card rounded-2xl border border-[var(--border)] bg-[var(--surface)] p-6 shadow-sm">
                <label class="block text-sm font-bold text-[var(--text)] mb-1">
                    {{ t('admin.ai_providers.provider_type') }} <span class="text-red-500">*</span>
                </label>
                <p class="text-xs text-[var(--text-muted)] mb-4">
                    {{ locale === 'en' ? 'Select one of the 4 standardized provider protocols supported by Sikhun.' : 'সিখুন প্ল্যাটফর্মে সমর্থিত ৪টি প্রোভাইডারের মধ্য থেকে একটি নির্বাচন করুন।' }}
                </p>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-3.5">
                    <div
                        v-for="opt in providerTypeOptions"
                        :key="opt.key"
                        @click="selectType(opt.key)"
                        class="relative cursor-pointer rounded-xl border p-4 transition-all"
                        :class="form.type === opt.key
                            ? 'border-[var(--primary)] bg-[var(--primary)]/5 ring-2 ring-[var(--primary)]/20 shadow-xs'
                            : 'border-[var(--border)] bg-[var(--surface2)]/40 hover:border-[var(--border-strong)] hover:bg-[var(--surface2)]/70'"
                    >
                        <div class="flex items-start justify-between gap-2">
                            <div class="flex items-center gap-2">
                                <span class="font-heading text-sm font-bold text-[var(--text)]">{{ opt.label }}</span>
                                <span v-if="opt.recommended" class="rounded-full bg-emerald-500/15 px-2 py-0.5 text-[10px] font-bold uppercase tracking-wider text-emerald-600 dark:text-emerald-400">
                                    Recommended
                                </span>
                            </div>
                            <span
                                class="flex h-5 w-5 shrink-0 items-center justify-center rounded-full border transition-colors"
                                :class="form.type === opt.key ? 'border-[var(--primary)] bg-[var(--primary)] text-white' : 'border-[var(--border)] bg-[var(--surface)]'"
                            >
                                <span v-if="form.type === opt.key" class="h-2 w-2 rounded-full bg-white"></span>
                            </span>
                        </div>
                        <p class="mt-2 text-xs text-[var(--text-muted)] leading-relaxed">
                            {{ opt.description }}
                        </p>
                    </div>
                </div>
            </div>

            <!-- 2. Primary Configuration Details -->
            <div class="card rounded-2xl border border-[var(--border)] bg-[var(--surface)] p-6 shadow-sm space-y-5">
                <h3 class="font-heading text-base font-bold text-[var(--text)] flex items-center gap-2 border-b border-[var(--border)] pb-3">
                    <SparklesIcon class="h-4.5 w-4.5 text-[var(--primary)]" />
                    {{ locale === 'en' ? 'Connection Credentials' : 'কানেকশন ও ক্রেডেনশিয়াল' }}
                </h3>

                <!-- Name & Model Name -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-[var(--text)] mb-1.5">
                            {{ t('common.name') }} <span class="text-red-500">*</span>
                        </label>
                        <input
                            v-model="form.name"
                            type="text"
                            required
                            placeholder="e.g. OpenAI Primary, DeepSeek V3, Groq Llama"
                            class="w-full rounded-xl border border-[var(--border)] bg-[var(--surface2)] px-4 py-2.5 text-sm text-[var(--text)] transition-colors focus:border-[var(--primary)] focus:outline-hidden"
                        />
                        <p class="text-[11px] text-[var(--text-muted)] mt-1">Identifies this credential set in the admin dashboard.</p>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-[var(--text)] mb-1.5">
                            {{ t('admin.ai_providers.model_name') }} <span class="text-red-500">*</span>
                        </label>
                        <input
                            v-model="form.model_name"
                            type="text"
                            required
                            :placeholder="currentTypeConfig.defaultModel || 'e.g. gpt-4o, claude-3-5-sonnet, gemini-1.5-flash'"
                            class="w-full rounded-xl border border-[var(--border)] bg-[var(--surface2)] px-4 py-2.5 text-sm font-mono text-[var(--text)] transition-colors focus:border-[var(--primary)] focus:outline-hidden"
                        />
                        <p class="text-[11px] text-[var(--text-muted)] mt-1">Exact model identifier passed to the API.</p>
                    </div>
                </div>

                <!-- API Key -->
                <div>
                    <label class="block text-xs font-semibold text-[var(--text)] mb-1.5">
                        {{ currentTypeConfig.keyLabel }}
                        <span v-if="form.type !== 'custom'" class="text-red-500">*</span>
                    </label>
                    <input
                        v-model="form.api_key"
                        type="password"
                        :placeholder="isEdit ? '•••••••••••••••• (Leave blank to keep existing key)' : currentTypeConfig.keyPlaceholder"
                        class="w-full rounded-xl border border-[var(--border)] bg-[var(--surface2)] px-4 py-2.5 text-sm font-mono text-[var(--text)] transition-colors focus:border-[var(--primary)] focus:outline-hidden"
                    />
                    <p class="text-[11px] text-[var(--text-muted)] mt-1">API key is safely encrypted at rest using AES-256.</p>
                </div>

                <!-- Endpoint URL -->
                <div>
                    <div class="flex items-center justify-between mb-1.5">
                        <label class="block text-xs font-semibold text-[var(--text)]">
                            {{ form.type === 'custom' ? 'Full API Endpoint URL' : 'Endpoint / Base URL' }}
                            <span v-if="form.type === 'custom'" class="text-red-500">*</span>
                            <span v-else class="text-[var(--text-muted)] font-normal ml-1">(Optional)</span>
                        </label>
                        <span v-if="currentTypeConfig.defaultEndpoint" class="text-[10px] text-[var(--text-muted)]">
                            Default: {{ currentTypeConfig.defaultEndpoint }}
                        </span>
                    </div>
                    <input
                        v-model="form.api_endpoint"
                        type="text"
                        :required="form.type === 'custom'"
                        :placeholder="currentTypeConfig.placeholderEndpoint"
                        class="w-full rounded-xl border border-[var(--border)] bg-[var(--surface2)] px-4 py-2.5 text-sm font-mono text-[var(--text)] transition-colors focus:border-[var(--primary)] focus:outline-hidden"
                    />
                    <p class="text-[11px] text-[var(--text-muted)] mt-1">
                        <span v-if="form.type === 'openai_compatible'">
                            Leave empty for official OpenAI, or paste the base URL for Groq (https://api.groq.com/openai/v1), DeepSeek (https://api.deepseek.com/v1), OpenRouter (https://openrouter.ai/api/v1), Ollama (http://localhost:11434/v1), or vLLM.
                        </span>
                        <span v-else-if="form.type === 'anthropic_compatible'">
                            Leave empty for official Anthropic API (https://api.anthropic.com), or enter your Anthropic Messages API proxy URL.
                        </span>
                        <span v-else-if="form.type === 'google_gemini'">
                            Leave empty for official Google Generative Language API (https://generativelanguage.googleapis.com).
                        </span>
                        <span v-else>
                            Paste the full endpoint URL that accepts POST requests (e.g. https://your-server.com/api/v1/chat/completions).
                        </span>
                    </p>
                </div>

                <!-- Custom Request Headers Builder -->
                <div class="rounded-xl border border-[var(--border)] bg-[var(--surface2)]/50 p-4">
                    <div class="flex items-center justify-between mb-2">
                        <div>
                            <label class="block text-xs font-bold text-[var(--text)]">{{ t('admin.ai_providers.custom_headers') }}</label>
                            <p class="text-[11px] text-[var(--text-muted)]">Optional request headers (e.g. x-api-key, Authorization, HTTP-Referer, etc.)</p>
                        </div>
                        <button
                            type="button"
                            @click="addHeader"
                            class="inline-flex items-center gap-1 rounded-lg border border-[var(--border)] bg-[var(--surface)] px-2.5 py-1 text-xs font-semibold text-[var(--primary)] hover:border-[var(--primary)]/30 transition-colors shadow-2xs"
                        >
                            <PlusIcon class="h-3.5 w-3.5" />
                            {{ t('admin.ai_providers.add_header') }}
                        </button>
                    </div>

                    <div class="space-y-2 mt-3">
                        <div v-for="(h, i) in form.custom_headers" :key="i" class="flex items-center gap-2">
                            <input
                                v-model="h.name"
                                type="text"
                                placeholder="Header Name (e.g. x-api-key)"
                                class="flex-1 rounded-lg border border-[var(--border)] bg-[var(--surface)] px-3 py-2 text-xs font-mono text-[var(--text)] focus:border-[var(--primary)] focus:outline-hidden"
                            />
                            <input
                                v-model="h.value"
                                type="text"
                                placeholder="Value"
                                class="flex-1 rounded-lg border border-[var(--border)] bg-[var(--surface)] px-3 py-2 text-xs font-mono text-[var(--text)] focus:border-[var(--primary)] focus:outline-hidden"
                            />
                            <button
                                type="button"
                                @click="removeHeader(i)"
                                class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg text-red-500 hover:bg-red-500/10 transition-colors"
                                title="Remove header"
                            >
                                <TrashIcon class="h-4 w-4" />
                            </button>
                        </div>
                        <p v-if="!form.custom_headers.length" class="text-xs text-[var(--text-muted)] italic">
                            {{ t('admin.ai_providers.no_headers') }}
                        </p>
                    </div>
                </div>

                <!-- Parameters: Max Tokens & Temperature -->
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-[var(--text)] mb-1.5">{{ t('admin.ai_providers.max_tokens') }}</label>
                        <input
                            v-model="form.max_tokens"
                            type="number"
                            min="1"
                            class="w-full rounded-xl border border-[var(--border)] bg-[var(--surface2)] px-4 py-2 text-sm text-[var(--text)] focus:border-[var(--primary)] focus:outline-hidden"
                        />
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-[var(--text)] mb-1.5">{{ t('admin.ai_providers.temperature') }} (0.0 - 2.0)</label>
                        <input
                            v-model="form.temperature"
                            type="number"
                            step="0.05"
                            min="0"
                            max="2"
                            class="w-full rounded-xl border border-[var(--border)] bg-[var(--surface2)] px-4 py-2 text-sm text-[var(--text)] focus:border-[var(--primary)] focus:outline-hidden"
                        />
                    </div>
                </div>
            </div>

            <!-- 3. Assigned Use Cases Multi-Select -->
            <div class="card rounded-2xl border border-[var(--border)] bg-[var(--surface)] p-6 shadow-sm space-y-4">
                <div class="flex items-center justify-between border-b border-[var(--border)] pb-3">
                    <div>
                        <h3 class="font-heading text-base font-bold text-[var(--text)]">Assigned AI Use Cases</h3>
                        <p class="text-xs text-[var(--text-muted)] mt-0.5">
                            Check each feature this provider should power. For each selected feature, you can toggle it as the <strong>platform default</strong>.
                        </p>
                    </div>
                    <button
                        type="button"
                        @click="toggleAll"
                        class="text-xs font-semibold text-[var(--primary)] hover:underline shrink-0"
                    >
                        {{ form.use_cases.length === useCaseOptions.length ? 'Deselect all' : 'Select all' }}
                    </button>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-2.5">
                    <div
                        v-for="uc in useCaseOptions"
                        :key="uc.value"
                        class="flex items-center justify-between rounded-xl border p-3 transition-colors"
                        :class="form.use_cases.includes(uc.value) ? 'border-[var(--primary)]/30 bg-[var(--primary)]/5' : 'border-[var(--border)] bg-[var(--surface2)]/30'"
                    >
                        <label class="flex items-center gap-2.5 cursor-pointer flex-1 min-w-0 pr-2">
                            <input
                                type="checkbox"
                                :value="uc.value"
                                v-model="form.use_cases"
                                @change="onUseCaseToggle(uc.value, $event.target.checked)"
                                class="rounded border-[var(--border)] text-[var(--primary)] focus:ring-[var(--primary)]"
                            />
                            <span class="text-xs font-semibold text-[var(--text)] truncate">{{ uc.label }}</span>
                        </label>

                        <label
                            v-if="form.use_cases.includes(uc.value)"
                            class="flex items-center gap-1.5 rounded-md bg-[var(--surface)] px-2 py-1 text-[11px] font-bold text-[var(--text-muted)] cursor-pointer shadow-2xs border border-[var(--border)]"
                            :class="form.default_use_cases.includes(uc.value) ? 'text-[var(--secondary)] border-[var(--secondary)]/30 bg-[var(--secondary)]/10' : ''"
                        >
                            <input
                                type="checkbox"
                                :value="uc.value"
                                v-model="form.default_use_cases"
                                class="rounded border-[var(--border)] text-[var(--secondary)] focus:ring-[var(--secondary)]"
                            />
                            <span>Default</span>
                        </label>
                    </div>
                </div>
                <p v-if="form.errors.use_cases" class="text-xs text-red-500 font-medium">{{ form.errors.use_cases }}</p>
            </div>

            <!-- 4. Active Toggle & Save -->
            <div class="card rounded-2xl border border-[var(--border)] bg-[var(--surface)] p-5 shadow-sm flex items-center justify-between">
                <label class="flex items-center gap-2.5 cursor-pointer">
                    <input
                        v-model="form.is_active"
                        type="checkbox"
                        class="rounded border-[var(--border)] text-[var(--primary)] focus:ring-[var(--primary)] h-4 w-4"
                    />
                    <div>
                        <span class="text-sm font-bold text-[var(--text)]">Provider Active</span>
                        <p class="text-xs text-[var(--text-muted)]">Enabled providers can be routed to for active AI requests.</p>
                    </div>
                </label>

                <div class="flex items-center gap-3">
                    <Link href="/admin/ai-providers" class="btn btn-outline text-xs">
                        {{ t('common.cancel') }}
                    </Link>
                    <button
                        type="submit"
                        :disabled="form.processing"
                        class="btn btn-primary text-xs"
                    >
                        {{ form.processing ? t('common.saving') : t('common.save') }}
                    </button>
                </div>
            </div>
        </form>
    </AdminLayout>
</template>
