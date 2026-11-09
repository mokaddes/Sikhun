<script setup>
import { ref, computed } from 'vue';
import {
    CodeBracketIcon,
    ClipboardDocumentIcon,
    CheckIcon,
    ArrowPathIcon,
    SparklesIcon,
} from '@heroicons/vue/24/outline';

const props = defineProps({
    en: { type: Boolean, default: false },
});

const language = ref('json'); // 'html' | 'css' | 'js' | 'json' | 'sql'
const indentSize = ref(2);
const codeInput = ref('');
const copied = ref(false);
const errorMsg = ref('');

const sampleSnippets = {
    json: '{"status":"success","data":{"id":101,"title":"Learn Laravel & Vue 3","tags":["php","vue","fullstack"],"price":1200,"published":true}}',
    html: '<div class="card"><header><h1>Sikhun Platform</h1></header><main><p>Learn with AI-powered books and courses.</p><button>Get Started</button></main></div>',
    css: '.container{max-width:1200px;margin:0 auto;padding:16px;}.btn{background:#4f46e5;color:#fff;border-radius:8px;padding:8px 16px;font-weight:bold;}',
    js: 'function calculateDiscount(price,percent){if(percent<=0)return price;const discount=(price*percent)/100;return Math.round(price-discount);}',
    sql: 'SELECT u.id, u.name, o.total_amount, o.created_at FROM users u INNER JOIN orders o ON u.id = o.user_id WHERE o.status = "completed" ORDER BY o.created_at DESC LIMIT 20;',
};

function loadSample() {
    codeInput.value = sampleSnippets[language.value] || '';
    beautifyCode();
}

function beautifyCode() {
    errorMsg.value = '';
    const raw = codeInput.value.trim();
    if (!raw) return;

    try {
        const indentStr = ' '.repeat(indentSize.value);

        if (language.value === 'json') {
            const parsed = JSON.parse(raw);
            codeInput.value = JSON.stringify(parsed, null, indentSize.value);
            return;
        }

        if (language.value === 'html') {
            codeInput.value = formatHtml(raw, indentStr);
            return;
        }

        if (language.value === 'css') {
            codeInput.value = formatCss(raw, indentStr);
            return;
        }

        if (language.value === 'js') {
            codeInput.value = formatJs(raw, indentStr);
            return;
        }

        if (language.value === 'sql') {
            codeInput.value = formatSql(raw, indentStr);
            return;
        }
    } catch (e) {
        errorMsg.value = (props.en ? 'Syntax error while formatting: ' : 'কোড সাজাতে সিনট্যাক্স সমস্যা: ') + e.message;
    }
}

function minifyCode() {
    errorMsg.value = '';
    const raw = codeInput.value.trim();
    if (!raw) return;

    try {
        if (language.value === 'json') {
            codeInput.value = JSON.stringify(JSON.parse(raw));
            return;
        }
        // General whitespace minification
        codeInput.value = raw
            .replace(/\/\*[\s\S]*?\*\/|([^:]|^)\/\/.*$/gm, '') // comments
            .replace(/\s+/g, ' ')
            .replace(/\s*([\{\}\:\;\,\(\)\=\+\-\*\/])\s*/g, '$1')
            .trim();
    } catch (e) {
        errorMsg.value = e.message;
    }
}

function copyCode() {
    if (!codeInput.value) return;
    navigator.clipboard.writeText(codeInput.value);
    copied.value = true;
    setTimeout(() => { copied.value = false; }, 2000);
}

function clearCode() {
    codeInput.value = '';
    errorMsg.value = '';
}

// ==================== PURE CLIENT FORMATTERS ====================
function formatHtml(html, indent) {
    let formatted = '';
    let pad = 0;
    // Normalize self-closing & newlines
    const tokens = html.replace(/>\s*</g, '><').match(/(<[^>]+>|[^<]+)/g) || [];

    tokens.forEach(node => {
        if (node.match(/^<\/\w/)) {
            pad = Math.max(0, pad - 1);
        }
        formatted += indent.repeat(pad) + node.trim() + '\n';
        if (node.match(/^<\w[^>]*[^\/]>$/) && !node.match(/^<(input|img|br|hr|meta|link)/i)) {
            pad += 1;
        }
    });
    return formatted.trim();
}

function formatCss(css, indent) {
    return css
        .replace(/\s*\{\s*/g, ' {\n' + indent)
        .replace(/\s*;\s*/g, ';\n' + indent)
        .replace(/\s*\}\s*/g, '\n}\n\n')
        .replace(/\n\s*;\n/g, ';\n')
        .trim();
}

function formatJs(js, indent) {
    let pad = 0;
    let out = '';
    const lines = js
        .replace(/\{/g, '{\n')
        .replace(/\}/g, '\n}\n')
        .replace(/;/g, ';\n')
        .split('\n')
        .map(l => l.trim())
        .filter(Boolean);

    lines.forEach(line => {
        if (line.startsWith('}')) pad = Math.max(0, pad - 1);
        out += indent.repeat(pad) + line + '\n';
        if (line.endsWith('{')) pad += 1;
    });
    return out.trim();
}

function formatSql(sql, indent) {
    const keywords = ['SELECT', 'FROM', 'WHERE', 'AND', 'OR', 'ORDER BY', 'GROUP BY', 'HAVING', 'LIMIT', 'JOIN', 'LEFT JOIN', 'RIGHT JOIN', 'INNER JOIN', 'INSERT INTO', 'VALUES', 'UPDATE', 'SET', 'DELETE'];
    let formatted = sql;
    keywords.forEach(kw => {
        const regex = new RegExp('\\b' + kw + '\\b', 'gi');
        formatted = formatted.replace(regex, '\n' + kw);
    });
    return formatted.trim();
}

const stats = computed(() => {
    const val = codeInput.value;
    const lines = val ? val.split('\n').length : 0;
    const chars = val ? val.length : 0;
    return { lines, chars };
});
</script>

<template>
    <div class="card p-6 md:p-8 rounded-3xl border border-gray-200 dark:border-white/10 bg-white dark:bg-[#161a26] text-gray-900 dark:text-white shadow-xl">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-gray-200 dark:border-white/10 pb-5">
            <div>
                <h2 class="font-heading text-xl md:text-2xl font-bold flex items-center gap-2.5">
                    <CodeBracketIcon class="w-6 h-6 text-emerald-500" />
                    {{ en ? 'Code Formatter & Beautifier' : 'কোড ফরম্যাটার ও বিউটিফায়ার (Code Formatter)' }}
                </h2>
                <p class="text-xs text-gray-600 dark:text-gray-300 mt-1">
                    {{ en ? 'Format, beautify or minify HTML, CSS, JavaScript, JSON, and SQL instantly.' : 'এইচটিএমএল, সিএসএস, জাভাস্ক্রিপ্ট, জেসন ও এসকিউএল কোড ১-ক্লিকে সুন্দর ও পরিচ্ছন্নভাবে সাজান।' }}
                </p>
            </div>

            <!-- Language Buttons -->
            <div class="flex items-center gap-1.5 flex-wrap">
                <button
                    v-for="lang in ['json', 'html', 'css', 'js', 'sql']"
                    :key="lang"
                    type="button"
                    @click="language = lang"
                    class="px-3 py-1.5 rounded-xl text-xs font-bold uppercase transition border"
                    :class="language === lang ? 'bg-emerald-500 text-white border-emerald-500' : 'bg-gray-100 dark:bg-white/5 border-gray-200 dark:border-white/10 text-gray-700 dark:text-gray-300'"
                >
                    {{ lang }}
                </button>
            </div>
        </div>

        <!-- Controls Toolbar -->
        <div class="mt-5 flex items-center justify-between gap-3 flex-wrap text-xs">
            <div class="flex items-center gap-2 flex-wrap">
                <button
                    type="button"
                    @click="beautifyCode"
                    class="btn btn-primary text-xs py-1.5 px-3.5 flex items-center gap-1.5 shadow-sm"
                >
                    <SparklesIcon class="w-4 h-4" />
                    <span>{{ en ? 'Format / Beautify' : 'কোড সাজান (Beautify)' }}</span>
                </button>
                <button
                    type="button"
                    @click="minifyCode"
                    class="btn btn-outline text-xs py-1.5 px-3"
                >
                    {{ en ? 'Minify' : 'মিনিফাই (Minify)' }}
                </button>
                <button
                    type="button"
                    @click="loadSample"
                    class="px-3 py-1.5 rounded-xl bg-gray-100 dark:bg-white/5 hover:bg-gray-200 dark:hover:bg-white/10 text-gray-700 dark:text-gray-300 transition"
                >
                    {{ en ? 'Load Sample' : 'নমুনা কোড লোড করুন' }}
                </button>
            </div>

            <div class="flex items-center gap-2">
                <span class="text-gray-500">{{ en ? 'Indent:' : 'ইন্ডেন্ট:' }}</span>
                <select
                    v-model.number="indentSize"
                    @change="beautifyCode"
                    class="bg-gray-100 dark:bg-[#0e121d] border border-gray-300 dark:border-white/10 rounded-xl px-2 py-1 text-xs text-gray-900 dark:text-white"
                >
                    <option :value="2">2 Spaces</option>
                    <option :value="4">4 Spaces</option>
                </select>

                <button
                    type="button"
                    @click="copyCode"
                    class="btn btn-outline text-xs py-1.5 px-3 flex items-center gap-1 text-emerald-600 dark:text-emerald-400 border-emerald-500/30"
                >
                    <CheckIcon v-if="copied" class="w-3.5 h-3.5" />
                    <ClipboardDocumentIcon v-else class="w-3.5 h-3.5" />
                    <span>{{ copied ? (en ? 'Copied!' : 'কপি হয়েছে!') : (en ? 'Copy' : 'কপি করুন') }}</span>
                </button>
                <button
                    type="button"
                    @click="clearCode"
                    class="text-rose-500 hover:text-rose-700 text-xs px-2 py-1"
                >
                    {{ en ? 'Clear' : 'মুছুন' }}
                </button>
            </div>
        </div>

        <!-- Error Alert -->
        <div v-if="errorMsg" class="mt-3 p-3 rounded-xl bg-rose-500/10 border border-rose-500/20 text-xs text-rose-600 dark:text-rose-400 font-mono">
            {{ errorMsg }}
        </div>

        <!-- Code Editor Area -->
        <div class="mt-4 relative rounded-2xl border border-gray-300 dark:border-white/10 overflow-hidden bg-gray-50 dark:bg-[#0e121d]">
            <textarea
                v-model="codeInput"
                rows="14"
                :placeholder="en ? 'Paste your code here and click Format...' : 'এখানে আপনার কোড পেস্ট করুন এবং \'কোড সাজান\' বাটনে চাপুন...'"
                class="w-full p-4 font-mono text-xs sm:text-sm bg-transparent border-0 text-gray-900 dark:text-white focus:outline-none focus:ring-0 leading-relaxed resize-y"
            ></textarea>

            <!-- Bottom Stats Bar -->
            <div class="px-4 py-2 bg-gray-100 dark:bg-[#121624] border-t border-gray-200 dark:border-white/5 flex items-center justify-between text-[11px] text-gray-500 font-mono">
                <span>{{ language.toUpperCase() }} MODE</span>
                <span>{{ stats.lines }} {{ en ? 'lines' : 'লাইন' }} • {{ stats.chars }} {{ en ? 'chars' : 'অক্ষর' }}</span>
            </div>
        </div>
    </div>
</template>
