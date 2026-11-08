import katex from 'katex';
import 'katex/dist/katex.min.css';

/**
 * Safely renders LaTeX equations in mixed Bengali/English text.
 * Handles $$...$$ (display mode) and $...$ (inline mode).
 */
export function renderMath(text) {
    if (!text || typeof text !== 'string') return '';

    // First, process display math $$...$$
    let processed = text.replace(/\$\$([\s\S]+?)\$\$/g, (match, formula) => {
        try {
            return `<div class="my-2 overflow-x-auto">${katex.renderToString(formula.trim(), {
                displayMode: true,
                throwOnError: false,
            })}</div>`;
        } catch (e) {
            return match;
        }
    });

    // Next, process inline math $...$
    processed = processed.replace(/\$([^\$\n]+?)\$/g, (match, formula) => {
        try {
            return katex.renderToString(formula.trim(), {
                displayMode: false,
                throwOnError: false,
            });
        } catch (e) {
            return match;
        }
    });

    // Convert newlines to <br> for plain paragraphs if no HTML paragraph tags exist
    if (!processed.includes('<p>') && !processed.includes('<div>')) {
        processed = processed.replace(/\n/g, '<br>');
    }

    return processed;
}
