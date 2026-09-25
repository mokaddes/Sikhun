<script setup>
import { ref, computed, onMounted, onBeforeUnmount, nextTick } from 'vue';
import axios from 'axios';
import { useI18n } from '@/i18n';

import $ from 'jquery';

const props = defineProps({
    bookId: { type: Number, required: true },
    totalPages: { type: Number, required: true },
    // null = unrestricted (full access); array = the only pages the
    // student's chapter purchases cover. Server re-validates every page
    // request anyway — this list only drives which pages get a book leaf.
    accessiblePages: { type: Array, default: null },
    // Base used to mint page-image URLs, e.g. `/library/${bookId}/read` or
    // `/my-books/${bookId}/read`. The full URL is `${base}/page/${n}/url`.
    urlPrefix: { type: String, default: null },
});

const emit = defineEmits(['page-change']);

const { t } = useI18n();

const PAGE_ASPECT = 1.414; // A4 portrait at 150 DPI (matches the SVG placeholder)

const rootEl = ref(null);
const bookEl = ref(null);
const initializing = ref(true);
const initError = ref(false);
const ready = ref(false);

const size = ref(computeSize());
const loaded = {};
let inflight = new Set();

const currentPos = ref(1);
const currentPage = ref(null);
const currentView = ref([]);
const jumpTarget = ref('');

const realPages = computed(() => {
    if (props.accessiblePages?.length) {
        return [...props.accessiblePages].sort((a, b) => a - b);
    }
    return Array.from({ length: Math.max(props.totalPages || 1, 1) }, (_, i) => i + 1);
});

const isDouble = computed(() => size.value.double);

const indicatorText = computed(() => {
    const total = props.totalPages;
    if (isDouble.value && currentView.value.length > 1) {
        return t('reader.pages_of', {
            start: currentView.value[0],
            end: currentView.value[currentView.value.length - 1],
            total,
        });
    }
    return t('reader.page_of', { current: currentPage.value, total });
});

function computeSize() {
    const viewportW = rootEl.value?.clientWidth ?? 0;
    const containerW = Math.max(viewportW > 0 ? viewportW : window.innerWidth - 32, 240);
    const maxH = Math.max(window.innerHeight * 0.72, 320);
    const double = window.innerWidth >= 768;

    let pageW = double
        ? Math.min(Math.floor((containerW - 16) / 2), 380)
        : Math.min(containerW - 16, 440);
    let pageH = Math.round(pageW * PAGE_ASPECT);

    if (pageH > maxH) {
        pageH = Math.floor(maxH);
        pageW = Math.round(pageH / PAGE_ASPECT);
    }

    return { pageW, pageH, double };
}

function pageUrl(realPage) {
    return `${props.urlPrefix ?? `/library/${props.bookId}/read`}/page/${realPage}/url`;
}

function buildPages() {
    const book = $(bookEl.value);
    book.empty();
    realPages.value.forEach((p) => {
        book.append(
            $('<div class="turn-page"></div>')
                .attr('data-real', p)
                .append(`<div class="turn-page-loader">${t('reader.loading')}</div>`)
                .on('contextmenu', (e) => e.preventDefault())
        );
    });
}

function ensureUrl(realPage) {
    if (loaded[realPage] || inflight.has(realPage)) return;
    inflight.add(realPage);
    const $page = $(bookEl.value).find(`.turn-page[data-real="${realPage}"]`).first();
    axios
        .get(pageUrl(realPage))
        .then(({ data }) => {
            loaded[realPage] = true;
            $page.css('background-image', `url(${data.url})`);
            $page.children('.turn-page-loader').remove();
        })
        .catch(() => {
            $page.children('.turn-page-loader').text(t('reader.load_failed')).addClass('is-error');
        })
        .finally(() => {
            inflight.delete(realPage);
        });
}

function loadView(view, prefetch = true) {
    if (!Array.isArray(view)) return;
    const seen = new Set();
    view.forEach((pos) => {
        const real = realPages.value[pos - 1];
        if (real !== undefined && !seen.has(real)) {
            seen.add(real);
            ensureUrl(real);
        }
    });
    if (prefetch) {
        view.forEach((pos) => {
            [pos - 1, pos + 1].forEach((p) => {
                const real = realPages.value[p - 1];
                if (real !== undefined && !seen.has(real)) {
                    seen.add(real);
                    ensureUrl(real);
                }
            });
        });
    }
}

function syncFromView(view) {
    if (!Array.isArray(view) || !view.length) return;
    const real = view.map((pos) => realPages.value[pos - 1]).filter((p) => p !== undefined);
    if (!real.length) return;
    currentView.value = real;
    currentPos.value = Math.min(view[0], realPages.value.length);
    currentPage.value = real[0];
    emit('page-change', currentPage.value);
    loadView(view);
}

function handleTurned(e, page, view) {
    syncFromView(view ?? [page]);
}

function createTurn() {
    const el = bookEl.value;
    if (!el) return;
    const { pageW, pageH, double } = size.value;

    try {
        $(el).turn('destroy');
    } catch (e) {
        /* element may have no instance yet */
    }
    for (const key in loaded) delete loaded[key];
    inflight = new Set();
    buildPages();

    $(el).turn({
        width: double ? pageW * 2 : pageW,
        height: pageH,
        elevation: 50,
        gradients: true,
        autoCenter: true,
        display: double ? 'double' : 'single',
        duration: 600,
        when: {
            turning: (e, page, view) => {
                if (view) loadView(view);
            },
            turned: handleTurned,
        },
    });

    const startPos = Math.max(1, Math.min(currentPos.value || 1, realPages.value.length));
    $(el).turn('page', startPos);
    syncFromView($(el).turn('view') ?? [startPos]);
}

async function initTurn() {
    try {
        window.jQuery = window.$ = $;
        await import('@/vendor/turnjs/turn.js');
    } catch (e) {
        initializing.value = false;
        initError.value = true;
        console.error('Turn.js failed to initialise', e);
        return;
    }
    await nextTick();
    size.value = computeSize();
    createTurn();
    ready.value = true;
    initializing.value = false;
}

function goPrev() {
    if ($.fn.turn && bookEl.value) $(bookEl.value).turn('previous');
}

function goNext() {
    if ($.fn.turn && bookEl.value) $(bookEl.value).turn('next');
}

function jumpTo() {
    const target = parseInt(jumpTarget.value, 10);
    if (Number.isNaN(target)) return;
    const requested = Math.max(1, Math.min(target, props.totalPages));
    let pos = realPages.value.indexOf(requested);
    if (pos === -1) {
        let nearest = realPages.value[0];
        for (const p of realPages.value) {
            if (Math.abs(p - requested) < Math.abs(nearest - requested)) nearest = p;
        }
        pos = realPages.value.indexOf(nearest);
        jumpTarget.value = String(nearest);
        nextTick(() => turnTo(pos + 1));
        return;
    }
    turnTo(pos + 1);
    jumpTarget.value = '';
}

function turnTo(pos) {
    if ($.fn.turn && bookEl.value) $(bookEl.value).turn('page', Math.max(1, Math.min(pos, realPages.value.length)));
}

function onKeydown(e) {
    if (e.target.closest('input')) return;
    if (e.key === 'ArrowRight') goNext();
    else if (e.key === 'ArrowLeft') goPrev();
    else if (e.key === 'Home') turnTo(1);
    else if (e.key === 'End') turnTo(realPages.value.length);
}

let resizeTimer = 0;
function onResize() {
    window.clearTimeout(resizeTimer);
    resizeTimer = window.setTimeout(() => {
        const next = computeSize();
        if (next.double !== size.value.double || Math.abs(next.pageW - size.value.pageW) > 4) {
            size.value = next;
            if (!ready.value) return;
            if ($.fn.turn && bookEl.value) {
                try {
                    $(bookEl.value).turn('destroy');
                } catch (e) {
                    /* no instance */
                }
            }
            createTurn();
        }
    }, 220);
}

onMounted(() => {
    currentPage.value = realPages.value[0] ?? 1;
    initTurn();
    window.addEventListener('keydown', onKeydown);
    window.addEventListener('resize', onResize);
});

onBeforeUnmount(() => {
    window.clearTimeout(resizeTimer);
    window.removeEventListener('keydown', onKeydown);
    window.removeEventListener('resize', onResize);
    if ($.fn.turn && bookEl.value) {
        try {
            $(bookEl.value).turn('destroy');
        } catch (e) {
            /* already torn down */
        }
    }
});
</script>

<template>
    <div ref="rootEl" class="turn-reader select-none">
        <div v-if="initError" class="py-24 text-center text-sm text-red-600">
            {{ t('reader.load_failed') }}
        </div>

        <div v-else-if="!realPages.length" class="py-24 text-center text-sm text-[var(--text-muted)]">
            {{ t('reader.load_failed') }}
        </div>

        <template v-else>
            <div class="turn-viewport relative" :style="{ height: `${size.pageH}px` }">
                <div
                    v-if="initializing"
                    class="absolute inset-0 z-10 flex items-center justify-center gap-3 rounded-xl bg-[var(--surface)] text-sm text-[var(--text-muted)]"
                >
                    {{ t('reader.loading') }}
                </div>

                <div ref="bookEl" class="flipbook"></div>
            </div>

            <div class="mt-6 flex items-center justify-center gap-4 text-sm">
                <button
                    type="button"
                    class="reader-ctrl"
                    :aria-label="t('reader.prev')"
                    :disabled="!ready || currentPos <= 1"
                    @click="goPrev"
                >
                    ‹
                </button>

                <div class="flex items-center gap-2 text-[var(--text-muted)]">
                    <input
                        type="number"
                        min="1"
                        :max="totalPages"
                        v-model.number="jumpTarget"
                        :placeholder="String(currentPage)"
                        :disabled="!ready"
                        @keydown.enter="jumpTo"
                        class="w-16 rounded-lg border border-[var(--border)] bg-[var(--surface2)] px-2 py-1 text-center text-sm outline-none focus:border-[var(--primary)] disabled:opacity-50"
                    />
                    <span>{{ indicatorText }}</span>
                </div>

                <button
                    type="button"
                    class="reader-ctrl"
                    :aria-label="t('reader.next')"
                    :disabled="!ready || currentPos >= realPages.length"
                    @click="goNext"
                >
                    ›
                </button>
            </div>
        </template>
    </div>
</template>

<style scoped>
.reader-ctrl {
    width: 2.5rem;
    height: 2.5rem;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 9999px;
    background: color-mix(in srgb, var(--primary) 12%, transparent);
    color: var(--primary);
    font-size: 1.25rem;
    line-height: 1;
    transition: background 0.15s ease;
}
.reader-ctrl:hover:not(:disabled) {
    background: color-mix(in srgb, var(--primary) 22%, transparent);
}
.reader-ctrl:disabled {
    opacity: 0.35;
    cursor: not-allowed;
}
</style>

<style>
/* Global styles — pages are built imperatively by Turn.js, outside Vue's
   scoped attribute reach, so these must be unscoped. */
.turn-reader .flipbook {
    position: relative;
}
.turn-reader .turn-page {
    background-color: #fff;
    background-repeat: no-repeat;
    background-size: 100% 100%;
    -webkit-backface-visibility: hidden;
    backface-visibility: hidden;
}
.turn-reader .turn-page .turn-page-loader {
    position: absolute;
    inset: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    background: #fff;
    color: var(--text-muted);
    font-size: 12px;
    text-align: center;
    padding: 0.5rem;
}
.turn-reader .turn-page .turn-page-loader.is-error {
    color: #dc2626;
}
.turn-reader .turn-shadow {
    box-shadow: 0 0 20px rgba(0, 0, 0, 0.25);
}
</style>