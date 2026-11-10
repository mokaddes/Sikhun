<script setup>
import { ref } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import AdminLayout from '@/Components/Layout/AdminLayout.vue';
import {
    SparklesIcon,
    ArrowLeftIcon,
    DocumentTextIcon,
    EyeIcon,
    TableCellsIcon,
    ChartBarIcon,
    QuestionMarkCircleIcon,
    ArrowsPointingOutIcon,
    CheckIcon,
    ArrowPathIcon,
    PhotoIcon,
} from '@heroicons/vue/24/outline';

const props = defineProps({
    categories: Array,
});

const topic = ref('HSC ICT Number System - সংখ্যা পদ্ধতি (বাইনারি, অক্টাল ও হেক্সাডেসিমেল রূপান্তর)');
const audience = ref('hsc');
const tone = ref('educational');
const isGenerating = ref(false);
const isEnhancing = ref(false);
const activeTab = ref('split'); // 'split', 'editor', 'preview'

const article = ref({
    title: '',
    slug: '',
    excerpt: '',
    content: '',
    featured_image_url: '',
    blog_category_id: props.categories?.[0]?.id || null,
    meta_title: '',
    meta_description: '',
    status: 'draft',
});

const exampleTopics = [
    'HSC ICT Number System - সংখ্যা পদ্ধতি',
    'এইচএসসি আইসিটি ৩য় অধ্যায়: লজিক গেট ও বুলিয়ান অ্যালজেব্রা',
    'বিসিএস প্রিলিমিনারি: সাধারণ বিজ্ঞান ও প্রযুক্তি প্রস্তুতি',
    'এসএসসি পদার্থবিজ্ঞান: গতি ও বলের সূত্রাবলীর গাণিতিক সমাধান',
    'বিশ্ববিদ্যালয় ভর্তি পরীক্ষা: সাধারণ জ্ঞান ও সাম্প্রতিক বিষয়াবলী',
];

async function generateArticle() {
    if (!topic.value.trim() || isGenerating.value) return;
    isGenerating.value = true;

    try {
        const response = await fetch('/admin/blog/ai-studio/generate', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
            },
            body: JSON.stringify({
                topic: topic.value,
                audience: audience.value,
                tone: tone.value,
            }),
        });

        const res = await response.json();
        if (res.success && res.data) {
            article.value.title = res.data.title;
            article.value.slug = res.data.slug;
            article.value.excerpt = res.data.excerpt;
            article.value.content = res.data.content;
            article.value.featured_image_url = res.data.featured_image_url;
            article.value.meta_title = res.data.meta_title;
            article.value.meta_description = res.data.meta_description;
            if (res.data.suggested_category_id) {
                article.value.blog_category_id = res.data.suggested_category_id;
            }
        }
    } catch (e) {
        console.error(e);
    } finally {
        isGenerating.value = false;
    }
}

async function enhanceArticle(action) {
    if (!article.value.content || isEnhancing.value) return;
    isEnhancing.value = true;

    try {
        const response = await fetch('/admin/blog/ai-studio/enhance', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
            },
            body: JSON.stringify({
                content: article.value.content,
                action: action,
                topic: topic.value,
            }),
        });

        const res = await response.json();
        if (res.success && res.content) {
            article.value.content = res.content;
        }
    } catch (e) {
        console.error(e);
    } finally {
        isEnhancing.value = false;
    }
}

function saveArticle(status = 'draft') {
    if (!article.value.title || !article.value.content) return;
    article.value.status = status;

    router.post('/admin/blog/ai-studio/save', article.value);
}
</script>

<template>
    <AdminLayout>
        <Head title="AI Blog Studio - Admin" />

        <div class="space-y-6">
            <!-- Header Bar -->
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div class="flex items-center gap-3">
                    <Link href="/admin/blog" class="btn btn-ghost p-2">
                        <ArrowLeftIcon class="w-5 h-5" />
                    </Link>
                    <div>
                        <h1 class="text-2xl font-bold tracking-tight text-white flex items-center gap-2">
                            <SparklesIcon class="w-7 h-7 text-orange-500" />
                            <span>AI Blog Writing Studio</span>
                        </h1>
                        <p class="text-xs text-gray-400 mt-0.5">
                            টপিক অনুযায়ী স্বয়ংক্রিয় ব্লগ আর্টিকেল রচনা, ফ্লোচার্ট ও টেবিল তৈরি এবং প্রি-পাবলিশ প্রিভিউ।
                        </p>
                    </div>
                </div>

                <!-- View Modes (Split / Editor / Preview) -->
                <div class="flex items-center gap-2 bg-[#121620] p-1 rounded-xl border border-white/5">
                    <button
                        @click="activeTab = 'split'"
                        :class="['px-3 py-1.5 rounded-lg text-xs font-semibold transition', activeTab === 'split' ? 'bg-orange-500 text-white' : 'text-gray-400 hover:text-white']"
                    >
                        স্প্লিট ভিউ (Split)
                    </button>
                    <button
                        @click="activeTab = 'editor'"
                        :class="['px-3 py-1.5 rounded-lg text-xs font-semibold transition', activeTab === 'editor' ? 'bg-orange-500 text-white' : 'text-gray-400 hover:text-white']"
                    >
                        এডিটর (Editor)
                    </button>
                    <button
                        @click="activeTab = 'preview'"
                        :class="['px-3 py-1.5 rounded-lg text-xs font-semibold transition', activeTab === 'preview' ? 'bg-orange-500 text-white' : 'text-gray-400 hover:text-white']"
                    >
                        লাইভ প্রিভিউ (Preview)
                    </button>
                </div>
            </div>

            <!-- Topic Generator Card -->
            <div class="card p-6 border-orange-500/20 bg-gradient-to-r from-orange-500/5 via-transparent to-transparent space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-12 gap-3 items-end">
                    <div class="md:col-span-6">
                        <label class="block text-xs font-bold text-gray-300 uppercase mb-1">ব্লগের বিষয় / টপিক লিখুন</label>
                        <input
                            v-model="topic"
                            type="text"
                            placeholder="যেমন: HSC ICT Number System - সংখ্যা পদ্ধতি"
                            class="input w-full"
                            @keyup.enter="generateArticle"
                        />
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-xs font-bold text-gray-300 uppercase mb-1">টার্গেট অডিয়েন্স</label>
                        <select v-model="audience" class="input w-full text-xs">
                            <option value="hsc">এইচএসসি (HSC)</option>
                            <option value="ssc">এসএসসি (SSC)</option>
                            <option value="admission">বিশ্ববিদ্যালয় ভর্তি</option>
                            <option value="job_seeker">চাকরি ও বিসিএস</option>
                            <option value="general">সাধারণ প্রযুক্তি</option>
                        </select>
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-xs font-bold text-gray-300 uppercase mb-1">লেখার ধরন (Tone)</label>
                        <select v-model="tone" class="input w-full text-xs">
                            <option value="educational">শিক্ষামূলক ও বিস্তারিত</option>
                            <option value="exam_guide">পরীক্ষার শর্টকাট গাইড</option>
                            <option value="step_by_step">ধাপে ধাপে টিউটোরিয়াল</option>
                        </select>
                    </div>
                    <div class="md:col-span-2">
                        <button
                            @click="generateArticle"
                            :disabled="isGenerating || !topic.trim()"
                            class="btn btn-primary w-full h-[42px] inline-flex items-center justify-center gap-2 text-xs"
                        >
                            <SparklesIcon class="w-4 h-4" :class="{ 'animate-spin': isGenerating }" />
                            <span>{{ isGenerating ? 'AI তৈরি করছে...' : 'আর্টিকেল লিখুন' }}</span>
                        </button>
                    </div>
                </div>

                <!-- Quick Examples Chips -->
                <div class="flex flex-wrap items-center gap-2 pt-2 text-xs">
                    <span class="text-gray-400">উদাহরণ টপিক:</span>
                    <button
                        v-for="(t, idx) in exampleTopics"
                        :key="idx"
                        @click="topic = t; generateArticle()"
                        class="px-2.5 py-1 rounded-full bg-white/5 hover:bg-orange-500/20 text-gray-300 hover:text-orange-400 border border-white/5 transition text-[11px]"
                    >
                        {{ t }}
                    </button>
                </div>
            </div>

            <!-- Workspace Layout -->
            <div class="grid grid-cols-1 gap-6" :class="activeTab === 'split' ? 'lg:grid-cols-12' : ''">
                <!-- Left Panel: Editor & AI Enhancers (6 cols in split view) -->
                <div v-show="activeTab === 'split' || activeTab === 'editor'" :class="activeTab === 'split' ? 'lg:col-span-6' : ''" class="space-y-4">
                    <div class="card p-6 space-y-4">
                        <div class="flex items-center justify-between border-b border-white/5 pb-3">
                            <h2 class="text-sm font-bold text-white flex items-center gap-2">
                                <DocumentTextIcon class="w-4 h-4 text-orange-400" />
                                <span>ব্লগ কন্টেন্ট এডিটর</span>
                            </h2>
                            <span v-if="article.title" class="text-[11px] text-emerald-400 font-semibold">আর্টিকেল প্রস্তুত</span>
                        </div>

                        <!-- 1-Click AI Enhancement Toolbar -->
                        <div v-if="article.content" class="p-3 bg-white/5 rounded-2xl border border-white/5 space-y-2">
                            <span class="text-[11px] font-bold text-orange-400 uppercase tracking-wider block">
                                ১-ক্লিক এআই এনহ্যান্সার (AI Enhancement Options)
                            </span>
                            <div class="flex flex-wrap gap-2">
                                <button
                                    @click="enhanceArticle('expand')"
                                    :disabled="isEnhancing"
                                    class="px-3 py-1.5 rounded-lg bg-white/5 hover:bg-white/10 text-xs font-semibold text-gray-200 border border-white/10 transition flex items-center gap-1.5"
                                >
                                    <ArrowsPointingOutIcon class="w-3.5 h-3.5 text-blue-400" />
                                    <span>আরও বিস্তারিত করুন</span>
                                </button>
                                <button
                                    @click="enhanceArticle('add_flowchart')"
                                    :disabled="isEnhancing"
                                    class="px-3 py-1.5 rounded-lg bg-white/5 hover:bg-white/10 text-xs font-semibold text-gray-200 border border-white/10 transition flex items-center gap-1.5"
                                >
                                    <ChartBarIcon class="w-3.5 h-3.5 text-purple-400" />
                                    <span>ফ্লোচার্ট যুক্ত করুন</span>
                                </button>
                                <button
                                    @click="enhanceArticle('add_table')"
                                    :disabled="isEnhancing"
                                    class="px-3 py-1.5 rounded-lg bg-white/5 hover:bg-white/10 text-xs font-semibold text-gray-200 border border-white/10 transition flex items-center gap-1.5"
                                >
                                    <TableCellsIcon class="w-3.5 h-3.5 text-emerald-400" />
                                    <span>তুলনামূলক টেবিল যোগ</span>
                                </button>
                                <button
                                    @click="enhanceArticle('add_quiz')"
                                    :disabled="isEnhancing"
                                    class="px-3 py-1.5 rounded-lg bg-white/5 hover:bg-white/10 text-xs font-semibold text-gray-200 border border-white/10 transition flex items-center gap-1.5"
                                >
                                    <QuestionMarkCircleIcon class="w-3.5 h-3.5 text-amber-400" />
                                    <span>মডেল টেস্ট প্রশ্ন যোগ</span>
                                </button>
                            </div>
                        </div>

                        <!-- Post Fields -->
                        <div class="space-y-3">
                            <div>
                                <label class="block text-xs font-bold text-gray-300 uppercase mb-1">ব্লগের শিরোনাম</label>
                                <input v-model="article.title" type="text" placeholder="ব্লগের আকর্ষণীয় শিরোনাম" class="input w-full text-sm font-semibold" />
                            </div>

                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-xs text-gray-400 mb-1">ইউআরএল স্লাগ (Slug)</label>
                                    <input v-model="article.slug" type="text" placeholder="post-slug" class="input w-full text-xs font-mono" />
                                </div>
                                <div>
                                    <label class="block text-xs text-gray-400 mb-1">ক্যাটাগরি</label>
                                    <select v-model="article.blog_category_id" class="input w-full text-xs">
                                        <option v-for="cat in categories" :key="cat.id" :value="cat.id">{{ cat.name_bn }}</option>
                                    </select>
                                </div>
                            </div>

                            <div>
                                <label class="block text-xs text-gray-400 mb-1">ফিচার্ড কভার ইমেজ URL</label>
                                <input v-model="article.featured_image_url" type="text" placeholder="https://images.unsplash.com/..." class="input w-full text-xs font-mono" />
                            </div>

                            <div>
                                <label class="block text-xs text-gray-400 mb-1">সংক্ষিপ্ত সারাংশ (Excerpt)</label>
                                <textarea v-model="article.excerpt" rows="2" placeholder="২-৩ বাক্যে ব্লগের মূল বিষয়..." class="input w-full text-xs"></textarea>
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-gray-300 uppercase mb-1">মূল কন্টেন্ট (HTML / Semantic Body)</label>
                                <textarea
                                    v-model="article.content"
                                    rows="16"
                                    placeholder="কন্টেন্ট এখানে প্রদর্শিত হবে..."
                                    class="input w-full font-mono text-xs leading-relaxed"
                                ></textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Panel: Live Student View Preview (6 cols in split view) -->
                <div v-show="activeTab === 'split' || activeTab === 'preview'" :class="activeTab === 'split' ? 'lg:col-span-6' : ''" class="space-y-4">
                    <div class="card p-6 md:p-8 space-y-6 bg-[#0f131d] border-white/10 sticky top-20">
                        <div class="flex items-center justify-between border-b border-white/5 pb-3">
                            <h2 class="text-sm font-bold text-white flex items-center gap-2">
                                <EyeIcon class="w-4 h-4 text-emerald-400" />
                                <span>লাইভ প্রি-পাবলিশ প্রিভিউ (Student View)</span>
                            </h2>
                            <span class="text-xs text-gray-400">sikhun.com/blog/preview</span>
                        </div>

                        <!-- Mock Blog Article Wrapper -->
                        <div v-if="article.title" class="space-y-4 text-left">
                            <div v-if="article.featured_image_url" class="rounded-2xl overflow-hidden max-h-56 border border-white/10">
                                <img :src="article.featured_image_url" alt="Cover" class="w-full h-full object-cover" />
                            </div>

                            <div>
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-orange-500/10 text-orange-400 border border-orange-500/20">
                                    {{ categories?.find(c => c.id === article.blog_category_id)?.name_bn || 'শিক্ষা ও তথ্যপ্রযুক্তি' }}
                                </span>
                                <h1 class="text-xl md:text-2xl font-bold text-white mt-2 leading-tight">
                                    {{ article.title }}
                                </h1>
                                <p class="text-xs text-gray-400 mt-2 leading-relaxed">
                                    {{ article.excerpt }}
                                </p>
                            </div>

                            <!-- Live Rendered HTML Content -->
                            <div
                                class="prose prose-invert max-w-none text-xs leading-relaxed border-t border-white/5 pt-4 space-y-3"
                                v-html="article.content"
                            />
                        </div>

                        <!-- Empty State in Preview -->
                        <div v-else class="py-24 text-center text-gray-500 text-xs">
                            <SparklesIcon class="w-10 h-10 text-gray-600 mx-auto mb-2" />
                            <span>উপরের টপিক লিখে "আর্টিকেল লিখুন" বাটনে ক্লিক করলে এখানে লাইভ প্রিভিউ প্রদর্শিত হবে।</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Bottom Floating Action Bar -->
            <div class="card p-4 flex flex-col sm:flex-row items-center justify-between gap-4 sticky bottom-4 z-20 shadow-2xl bg-[#171c28]/95 backdrop-blur-md">
                <div class="text-xs text-gray-400">
                    <span v-if="article.title">ব্লগ প্রস্তুত: <strong>{{ article.title }}</strong></span>
                    <span v-else>কোনো আর্টিকেল ড্রাফট প্রস্তুত নেই</span>
                </div>
                <div class="flex items-center gap-3 w-full sm:w-auto">
                    <button
                        @click="saveArticle('draft')"
                        :disabled="!article.title || !article.content"
                        class="btn btn-secondary text-xs flex-1 sm:flex-initial"
                    >
                        ড্রাফট হিসেবে সংরক্ষণ (Save Draft)
                    </button>
                    <button
                        @click="saveArticle('published')"
                        :disabled="!article.title || !article.content"
                        class="btn btn-primary text-xs inline-flex items-center justify-center gap-1.5 flex-1 sm:flex-initial"
                    >
                        <CheckIcon class="w-4 h-4" />
                        <span>১-ক্লিকে প্রকাশ করুন (Publish Now)</span>
                    </button>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
