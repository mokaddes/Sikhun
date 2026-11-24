/**
 * Branded A4 Student PDF & Print Generator for Sikhun.com Tools.
 *
 * Uses off-screen DOM rendering + html2canvas + jsPDF so Bengali Unicode
 * conjuncts (যুক্তবর্ণ) and KaTeX math equations render accurately on both
 * mobile and desktop browsers without broken glyphs.
 */
export async function downloadBrandedPdf({
    title = 'Sikhun Study Sheet',
    subtitle = '',
    studentName = '',
    bodyHtml = '',
    fileName = 'Sikhun_Study_Sheet.pdf',
}) {
    const [{ default: html2canvas }, { jsPDF }] = await Promise.all([
        import('html2canvas'),
        import('jspdf'),
    ]);

    const dateStr = new Date().toLocaleDateString('bn-BD', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
    });

    const container = document.createElement('div');
    container.style.position = 'fixed';
    container.style.left = '-9999px';
    container.style.top = '0';
    container.style.width = '794px';
    container.style.background = '#ffffff';
    container.style.color = '#0f172a';
    container.style.padding = '36px 40px';
    container.style.fontFamily = "'Hind Siliguri', 'Noto Sans Bengali', 'Plus Jakarta Sans', sans-serif";
    container.style.lineHeight = '1.55';
    container.style.boxSizing = 'border-box';

    container.innerHTML = `
        <div style="display:flex;justify-content:space-between;align-items:flex-start;border-bottom:2.5px solid #d97706;padding-bottom:14px;margin-bottom:20px;">
            <div>
                <div style="font-size:11px;font-weight:800;letter-spacing:0.12em;color:#d97706;text-transform:uppercase;">SIKHUN.COM • SMART STUDENT DESK</div>
                <h1 style="margin:4px 0 0;font-size:22px;font-weight:800;color:#0f172a;">${title}</h1>
                ${subtitle ? `<div style="margin-top:4px;font-size:13px;color:#475569;font-weight:600;">${subtitle}</div>` : ''}
            </div>
            <div style="text-align:right;font-size:12px;color:#475569;">
                <div><strong>তারিখ:</strong> ${dateStr}</div>
                ${studentName ? `<div style="margin-top:3px;color:#0f172a;font-weight:700;">শিক্ষার্থী: ${studentName}</div>` : ''}
                <div style="margin-top:3px;color:#059669;font-weight:700;">https://sikhun.com/tools</div>
            </div>
        </div>

        <div style="font-size:13px;color:#1e293b;">
            ${bodyHtml}
        </div>

        <div style="margin-top:26px;padding-top:12px;border-top:1px solid #cbd5e1;display:flex;justify-content:space-between;align-items:center;font-size:11px;color:#64748b;">
            <div><strong>Sikhun.com (শিখুন)</strong> — ৩৮,০০০+ ফ্রি MCQ প্রশ্নব্যাংক, ডিজিটাল বই ও AI টিউটর</div>
            <div>ফ্রি প্র্যাকটিস: <strong>sikhun.com/questions</strong></div>
        </div>
    `;

    document.body.appendChild(container);

    try {
        await new Promise((r) => setTimeout(r, 120));

        const canvas = await html2canvas(container, {
            scale: 2,
            useCORS: true,
            backgroundColor: '#ffffff',
            logging: false,
        });

        const imgData = canvas.toDataURL('image/jpeg', 0.95);
        const pdf = new jsPDF({
            orientation: 'portrait',
            unit: 'mm',
            format: 'a4',
        });

        const pageWidth = 210;
        const pageHeight = 297;
        const imgWidth = pageWidth;
        const imgHeight = (canvas.height * imgWidth) / canvas.width;

        let heightLeft = imgHeight;
        let position = 0;

        pdf.addImage(imgData, 'JPEG', 0, position, imgWidth, imgHeight, undefined, 'FAST');
        heightLeft -= pageHeight;

        while (heightLeft > 4) {
            position = heightLeft - imgHeight;
            pdf.addPage();
            pdf.addImage(imgData, 'JPEG', 0, position, imgWidth, imgHeight, undefined, 'FAST');
            heightLeft -= pageHeight;
        }

        const safeName = fileName.endsWith('.pdf') ? fileName : `${fileName}.pdf`;
        pdf.save(safeName);
    } finally {
        document.body.removeChild(container);
    }
}

export function openBrandedPrintWindow({
    title = 'Sikhun Study Sheet',
    subtitle = '',
    studentName = '',
    bodyHtml = '',
}) {
    const win = window.open('', '_blank', 'width=900,height=950');
    if (!win) {
        window.print();
        return;
    }

    const dateStr = new Date().toLocaleDateString('bn-BD', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
    });

    win.document.write(`<!DOCTYPE html>
<html lang="bn">
<head>
    <meta charset="utf-8">
    <title>${title} - Sikhun.com</title>
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
            <h1 style="margin:4px 0 0;font-size:20px;color:#0f172a;">${title}</h1>
            ${subtitle ? `<div style="margin-top:3px;font-size:12px;color:#475569;">${subtitle}</div>` : ''}
        </div>
        <div style="text-align:right;font-size:11px;color:#475569;">
            <div>তারিখ: ${dateStr}</div>
            ${studentName ? `<div style="font-weight:700;color:#0f172a;">শিক্ষার্থী: ${studentName}</div>` : ''}
            <div style="color:#059669;font-weight:700;">sikhun.com/tools</div>
        </div>
    </div>
    <div>${bodyHtml}</div>
    <div style="margin-top:24px;padding-top:10px;border-top:1px solid #cbd5e1;display:flex;justify-content:space-between;font-size:10.5px;color:#64748b;">
        <span>Sikhun.com (শিখুন) — ৩৮,০০০+ ফ্রি MCQ প্রশ্নব্যাংক ও AI টিউটর</span>
        <span>sikhun.com/questions</span>
    </div>
    <script>window.onload = function(){ setTimeout(function(){ window.print(); }, 350); };<\/script>
</body>
</html>`);
    win.document.close();
}
