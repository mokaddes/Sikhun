import{o as p,g as w,d as t,a as c,b as i,m as k,t as a,c as g,f as v,v as $,r as x,n as _}from"./app-EhnHz8Mt.js";import"./jspdf.es.min-BYYHlvOU.js";import{r as T}from"./DocumentTextIcon-CjEGx5m4.js";import{r as M}from"./PrinterIcon-_khpcbn1.js";import{r as z}from"./CheckIcon-DJ7aUyaC.js";import{r as C}from"./ClipboardDocumentIcon-DUHdTPEC.js";const D={class:"card p-6 md:p-8 rounded-3xl border border-gray-200 dark:border-white/10 bg-white dark:bg-[#161a26] text-gray-900 dark:text-white shadow-xl"},L={class:"flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-gray-200 dark:border-white/10 pb-5"},N={class:"font-heading text-xl md:text-2xl font-bold flex items-center gap-2.5"},P={class:"text-xs text-gray-600 dark:text-gray-300 mt-1"},H={class:"flex items-center gap-2"},S={class:"mt-4 flex items-center justify-between gap-3 text-xs flex-wrap"},B={class:"text-gray-500"},F={class:"flex items-center gap-2"},A={class:"mt-4 grid grid-cols-1 lg:grid-cols-2 gap-4"},q={class:"rounded-2xl border border-gray-300 dark:border-white/10 bg-gray-50 dark:bg-[#0e121d] overflow-hidden flex flex-col"},E={class:"px-4 py-2 bg-gray-100 dark:bg-[#121624] border-b border-gray-200 dark:border-white/5 text-[11px] font-bold text-gray-600 dark:text-gray-400 uppercase tracking-wider"},I=["placeholder"],V={class:"rounded-2xl border border-gray-300 dark:border-white/10 bg-white dark:bg-[#0e121d] overflow-hidden flex flex-col"},W={class:"px-4 py-2 bg-gray-100 dark:bg-[#121624] border-b border-gray-200 dark:border-white/5 text-[11px] font-bold text-gray-600 dark:text-gray-400 uppercase tracking-wider"},j=["innerHTML"],Q={__name:"MarkdownEditor",props:{en:{type:Boolean,default:!1}},setup(r){const b=`# 📚 পদার্থবিজ্ঞান: আলোর প্রতিফলন (Lecture Notes)
---
**তারিখ:** ${new Date().toISOString().split("T")[0]} | **অধ্যায়:** ৮ম অধ্যায়

### ১. আলোর প্রতিফলনের সূত্রাবলি:
1. আপতিত রশ্মি, প্রতিফলিত রশ্মি এবং আপতন বিন্দুতে অঙ্কিত অভিলম্ব একই সমতলে থাকে।
2. আপতন কোণ ($i$) ও প্রতিফলন কোণ ($r$) সর্বদা সমান হয় ($i = r$)।

### ২. প্রতিবিম্বের বৈশিষ্ট্য:
* **বাস্তব প্রতিবিম্ব:** পর্দায় ফেলা যায়, সর্বদা উল্টো হয়।
* **অবাস্তব প্রতিবিম্ব:** পর্দায় ফেলা যায় না, সর্বদা সোজা হয়।

> **টিপস:** অবতল দর্পণে ফোকাস ও মেরুর মাঝে লক্ষ্যবস্তু রাখলে একমাত্র অবাস্তব ও সোজা বিম্ব গঠিত হয়।

\`\`\`
দর্পণ সমীকরণ: 1/u + 1/v = 1/f
বিবর্ধন: m = -v/u
\`\`\`
`,n=x(b),d=x(!1),l=_(()=>f(n.value));function f(o){if(!o)return"";let e=o.replace(/&/g,"&amp;").replace(/</g,"&lt;").replace(/>/g,"&gt;");return e=e.replace(/```([\s\S]*?)```/g,(s,y)=>`<pre class="p-3 my-2 rounded-xl bg-gray-900 text-gray-100 font-mono text-xs overflow-x-auto leading-relaxed border border-white/10"><code>${y.trim()}</code></pre>`),e=e.replace(/`([^`]+)`/g,'<code class="px-1.5 py-0.5 rounded bg-gray-200 dark:bg-white/10 text-rose-500 font-mono text-xs">$1</code>'),e=e.replace(/^### (.*$)/gim,'<h3 class="text-base font-bold text-gray-900 dark:text-white mt-4 mb-2">$1</h3>'),e=e.replace(/^## (.*$)/gim,'<h2 class="text-lg font-extrabold text-gray-900 dark:text-white mt-5 mb-2 border-b border-gray-200 dark:border-white/10 pb-1">$1</h2>'),e=e.replace(/^# (.*$)/gim,'<h1 class="text-xl font-extrabold text-gray-900 dark:text-white mt-2 mb-3">$1</h1>'),e=e.replace(/^---$/gim,'<hr class="my-3 border-gray-200 dark:border-white/10" />'),e=e.replace(/^\> (.*$)/gim,'<blockquote class="border-l-4 border-indigo-500 pl-3 my-2 text-xs italic text-gray-600 dark:text-gray-300 bg-indigo-500/5 py-1 rounded-r-lg">$1</blockquote>'),e=e.replace(/\*\*(.*?)\*\*/g,'<strong class="font-bold text-gray-900 dark:text-white">$1</strong>'),e=e.replace(/\*(.*?)\*/g,'<em class="italic">$1</em>'),e=e.replace(/^\s*[\*\-]\s+(.*$)/gim,'<li class="ml-4 list-disc text-gray-700 dark:text-gray-300">$1</li>'),e=e.replace(/^\s*\d+\.\s+(.*$)/gim,'<li class="ml-4 list-decimal text-gray-700 dark:text-gray-300">$1</li>'),e=e.replace(/\n/g,"<br/>"),e=e.replace(/(<\/h[1-3]>|<hr[^>]*>|<\/pre>|<\/blockquote>|<br\/>\s*<li)/g,s=>s.startsWith("<br/>")?s.replace("<br/>",""):s),e}function m(){navigator.clipboard.writeText(l.value),d.value=!0,setTimeout(()=>{d.value=!1},2e3)}function h(){const o=window.open("","_blank","width=850,height=950");if(!o){window.print();return}const e=`
        <!DOCTYPE html>
        <html lang="bn">
        <head>
            <meta charset="utf-8">
            <title>Sikhun Notes</title>
            <link rel="preconnect" href="https://fonts.googleapis.com">
            <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
            <link href="https://fonts.googleapis.com/css2?family=Anek+Bangla:wght@400;500;600;700&display=swap" rel="stylesheet">
            <style>
                @page {
                    size: A4;
                    margin: 20mm;
                }
                body {
                    font-family: 'Anek Bangla', 'Segoe UI', system-ui, -apple-system, sans-serif;
                    color: #111827;
                    background: #ffffff;
                    margin: 0;
                    padding: 15px;
                    line-height: 1.7;
                    font-size: 11pt;
                }
                h1 { font-size: 22pt; font-weight: 800; border-bottom: 2px solid #e5e7eb; padding-bottom: 6px; margin-bottom: 12px; }
                h2 { font-size: 16pt; font-weight: 700; border-bottom: 1px solid #f3f4f6; padding-bottom: 4px; margin-top: 18px; margin-bottom: 8px; }
                h3 { font-size: 13pt; font-weight: 700; margin-top: 14px; margin-bottom: 6px; }
                p { margin-bottom: 10px; }
                pre { background: #f3f4f6; border: 1px solid #e5e7eb; padding: 10px; border-radius: 6px; font-family: monospace; font-size: 9.5pt; overflow-x: auto; white-space: pre-wrap; }
                code { background: #f3f4f6; padding: 2px 5px; border-radius: 4px; font-family: monospace; font-size: 9.5pt; color: #dc2626; }
                blockquote { border-left: 4px solid #6366f1; padding-left: 12px; margin: 12px 0; color: #4b5563; font-style: italic; background: #f8fafc; padding-top: 4px; padding-bottom: 4px; }
                ul, ol { margin: 8px 0 12px 20px; }
                li { margin-bottom: 4px; }
                table { width: 100%; border-collapse: collapse; margin: 14px 0; }
                th, td { border: 1px solid #d1d5db; padding: 8px 12px; text-align: left; font-size: 10pt; }
                th { background: #f9fafb; font-weight: 700; }
                hr { border: 0; border-top: 1px solid #e5e7eb; margin: 16px 0; }
                .doc-footer { margin-top: 30px; padding-top: 10px; border-top: 1px solid #e5e7eb; font-size: 9pt; color: #9ca3af; text-align: right; }
            </style>
        </head>
        <body>
            ${l.value}
            <div class="doc-footer">Created with Sikhun Notes (sikhun.com)</div>
            <script>
                window.onload = function() {
                    window.focus();
                    window.print();
                };
            <\/script>
        </body>
        </html>
    `;o.document.open(),o.document.write(e),o.document.close()}function u(){n.value=""}return(o,e)=>(p(),w("div",D,[t("div",L,[t("div",null,[t("h2",N,[c(i(T),{class:"w-6 h-6 text-purple-500"}),k(" "+a(r.en?"Markdown Live Notes & Clean PDF Exporter":"মার্কডাউন নোট ও পরিচ্ছন্ন PDF এক্সপোর্টার"),1)]),t("p",P,a(r.en?"Write formatted study notes with headings, math formulas, and export directly as clean A4 PDF.":"হেডিং, বুলেট পয়েন্ট ও ফর্মুলা দিয়ে ক্লাসের নোট তৈরি করুন এবং পরিচ্ছন্ন A4 পেপারে PDF সেভ করুন।"),1)]),t("div",H,[t("button",{type:"button",onClick:h,class:"btn btn-primary text-xs py-2 px-4 flex items-center gap-1.5 shadow-md shadow-purple-500/25"},[c(i(M),{class:"w-4 h-4"}),t("span",null,a(r.en?"Print / Save as PDF":"প্রিন্ট / PDF হিসেবে সেভ করুন"),1)])])]),t("div",S,[t("span",B," ✏️ "+a(r.en?"Live split editor: Markdown on left, preview on right":"স্প্লিট ভিউ: বামে লিখুন, ডানে লাইভ প্রিভিউ দেখুন"),1),t("div",F,[t("button",{type:"button",onClick:m,class:"btn btn-outline text-xs py-1 px-3 flex items-center gap-1"},[d.value?(p(),g(i(z),{key:0,class:"w-3.5 h-3.5 text-emerald-500"})):(p(),g(i(C),{key:1,class:"w-3.5 h-3.5"})),t("span",null,a(d.value?r.en?"Copied HTML!":"HTML কপি হয়েছে!":r.en?"Copy HTML":"HTML কপি"),1)]),t("button",{type:"button",onClick:u,class:"text-rose-500 hover:text-rose-700 text-xs px-2 py-1"},a(r.en?"Clear":"মুছুন"),1)])]),t("div",A,[t("div",q,[t("div",E," 📝 "+a(r.en?"Markdown Input":"মার্কডাউন ইনপুট"),1),v(t("textarea",{"onUpdate:modelValue":e[0]||(e[0]=s=>n.value=s),rows:"16",placeholder:r.en?"Type markdown here...":"এখানে নোট লিখুন...",class:"w-full p-4 font-mono text-xs sm:text-sm bg-transparent border-0 text-gray-900 dark:text-white focus:outline-none focus:ring-0 leading-relaxed resize-y flex-1"},null,8,I),[[$,n.value]])]),t("div",V,[t("div",W," 👁️ "+a(r.en?"Live Formatted Preview":"লাইভ ফরম্যাটেড প্রিভিউ"),1),t("div",{innerHTML:l.value,class:"p-4 text-xs sm:text-sm text-gray-900 dark:text-gray-100 overflow-y-auto max-h-[420px] leading-relaxed select-text"},null,8,j)])])]))}};export{Q as default};
