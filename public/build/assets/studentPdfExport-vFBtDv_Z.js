const __vite__mapDeps=(i,m=__vite__mapDeps,d=(m.f||(m.f=["assets/jspdf.es.min-DRG5-79N.js","assets/app-WkSfhyXj.js","assets/app-CR-44Afx.css"])))=>i.map(i=>d[i]);
import{_ as h}from"./app-WkSfhyXj.js";async function S({title:d="Sikhun Study Sheet",subtitle:e="",studentName:o="",bodyHtml:r="",fileName:i="Sikhun_Study_Sheet.pdf"}){const[{default:p},{jsPDF:y}]=await Promise.all([h(()=>import("./html2canvas.esm-QH1iLAAe.js"),[]),h(()=>import("./jspdf.es.min-DRG5-79N.js").then(n=>n.j),__vite__mapDeps([0,1,2]))]),v=new Date().toLocaleDateString("bn-BD",{year:"numeric",month:"long",day:"numeric"}),t=document.createElement("div");t.style.position="fixed",t.style.left="-9999px",t.style.top="0",t.style.width="794px",t.style.background="#ffffff",t.style.color="#0f172a",t.style.padding="36px 40px",t.style.fontFamily="'Hind Siliguri', 'Noto Sans Bengali', 'Plus Jakarta Sans', sans-serif",t.style.lineHeight="1.55",t.style.boxSizing="border-box",t.innerHTML=`
        <div style="display:flex;justify-content:space-between;align-items:flex-start;border-bottom:2.5px solid #d97706;padding-bottom:14px;margin-bottom:20px;">
            <div>
                <div style="font-size:11px;font-weight:800;letter-spacing:0.12em;color:#d97706;text-transform:uppercase;">SIKHUN.COM • SMART STUDENT DESK</div>
                <h1 style="margin:4px 0 0;font-size:22px;font-weight:800;color:#0f172a;">${d}</h1>
                ${e?`<div style="margin-top:4px;font-size:13px;color:#475569;font-weight:600;">${e}</div>`:""}
            </div>
            <div style="text-align:right;font-size:12px;color:#475569;">
                <div><strong>তারিখ:</strong> ${v}</div>
                ${o?`<div style="margin-top:3px;color:#0f172a;font-weight:700;">শিক্ষার্থী: ${o}</div>`:""}
                <div style="margin-top:3px;color:#059669;font-weight:700;">https://sikhun.com/tools</div>
            </div>
        </div>

        <div style="font-size:13px;color:#1e293b;">
            ${r}
        </div>

        <div style="margin-top:26px;padding-top:12px;border-top:1px solid #cbd5e1;display:flex;justify-content:space-between;align-items:center;font-size:11px;color:#64748b;">
            <div><strong>Sikhun.com (শিখুন)</strong> — ৩৮,০০০+ ফ্রি MCQ প্রশ্নব্যাংক, ডিজিটাল বই ও AI টিউটর</div>
            <div>ফ্রি প্র্যাকটিস: <strong>sikhun.com/questions</strong></div>
        </div>
    `,document.body.appendChild(t);try{await new Promise(w=>setTimeout(w,120));const n=await p(t,{scale:2,useCORS:!0,backgroundColor:"#ffffff",logging:!1}),g=n.toDataURL("image/jpeg",.95),s=new y({orientation:"portrait",unit:"mm",format:"a4"}),x=210,m=297,c=x,a=n.height*c/n.width;let l=a,f=0;for(s.addImage(g,"JPEG",0,f,c,a,void 0,"FAST"),l-=m;l>4;)f=l-a,s.addPage(),s.addImage(g,"JPEG",0,f,c,a,void 0,"FAST"),l-=m;const u=i.endsWith(".pdf")?i:`${i}.pdf`;s.save(u)}finally{document.body.removeChild(t)}}function k({title:d="Sikhun Study Sheet",subtitle:e="",studentName:o="",bodyHtml:r=""}){const i=window.open("","_blank","width=900,height=950");if(!i){window.print();return}const p=new Date().toLocaleDateString("bn-BD",{year:"numeric",month:"long",day:"numeric"});i.document.write(`<!DOCTYPE html>
<html lang="bn">
<head>
    <meta charset="utf-8">
    <title>${d} - Sikhun.com</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Hind+Siliguri:wght@400;600;700&display=swap">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/katex@0.16.11/dist/katex.min.css">
    <style>
        @page { size: A4 portrait; margin: 14mm; }
        body { font-family: 'Hind Siliguri', sans-serif; color: #0f172a; background: #fff; margin: 0; padding: 12px; line-height: 1.55; }
        table { width: 100%; border-collapse: collapse; }
    </style>
</head>
<body>
    <div style="display:flex;justify-content:space-between;align-items:flex-start;border-bottom:2.5px solid #d97706;padding-bottom:12px;margin-bottom:18px;">
        <div>
            <div style="font-size:10px;font-weight:800;letter-spacing:0.12em;color:#d97706;">SIKHUN.COM • SMART STUDENT DESK</div>
            <h1 style="margin:4px 0 0;font-size:20px;color:#0f172a;">${d}</h1>
            ${e?`<div style="margin-top:3px;font-size:12px;color:#475569;">${e}</div>`:""}
        </div>
        <div style="text-align:right;font-size:11px;color:#475569;">
            <div>তারিখ: ${p}</div>
            ${o?`<div style="font-weight:700;color:#0f172a;">শিক্ষার্থী: ${o}</div>`:""}
            <div style="color:#059669;font-weight:700;">sikhun.com/tools</div>
        </div>
    </div>
    <div>${r}</div>
    <div style="margin-top:24px;padding-top:10px;border-top:1px solid #cbd5e1;display:flex;justify-content:space-between;font-size:10.5px;color:#64748b;">
        <span>Sikhun.com (শিখুন) — ৩৮,০০০+ ফ্রি MCQ প্রশ্নব্যাংক ও AI টিউটর</span>
        <span>sikhun.com/questions</span>
    </div>
    <script>window.onload = function(){ setTimeout(function(){ window.print(); }, 350); };<\/script>
</body>
</html>`),i.document.close()}export{S as d,k as o};
