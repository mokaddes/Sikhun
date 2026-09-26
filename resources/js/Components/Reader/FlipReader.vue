<script setup>
import { ref, computed, onMounted, onBeforeUnmount, nextTick } from 'vue';
import axios from 'axios';
import { useI18n } from '@/i18n';
import { MagnifyingGlassMinusIcon, MagnifyingGlassPlusIcon } from '@heroicons/vue/24/outline';

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
    // Pre-signed image URLs for the opening window, keyed by page number —
    // lets the first pages paint without any extra requests.
    pageUrls: { type: Object, default: () => null },
});

const emit = defineEmits(['page-change']);

const { t } = useI18n();

const PAGE_ASPECT = 1.414; // A4 portrait at 150 DPI

// Max zoom factor applied by the turn.js zoom plugin (magazine sample uses a
// similar ~2.3x). Pages are rendered at ~992px wide, so ~2.2x reaches
// display-native resolution for reading close-up.
const ZOOM_MAX = 2.2;

const rootEl = ref(null);
const bookEl = ref(null);
const initializing = ref(true);
const initError = ref(false);
const ready = ref(false);
const zoomed = ref(false);
const zoomReady = ref(false);

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

const bookStyle = computed(() => {
    const { pageW, pageH, double } = size.value;
    const width = double ? pageW * 2 : pageW;
    return {
        width: `${width}px`,
        height: `${pageH}px`,
        left: `${-width / 2}px`,
        top: `${-pageH / 2}px`,
    };
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

function goToRealPage(page) {
    const index = realPages.value.indexOf(Number(page));
    if (ready.value && index >= 0) $(bookEl.value).turn('page', index + 1);
}

defineExpose({ goToRealPage });

function buildPages() {
    const book = $(bookEl.value);
    book.empty();
    realPages.value.forEach((p) => {
        $('<div class="page"></div>')
            .attr('data-real', p)
            .append(`<div class="turn-page-loader">${t('reader.loading')}</div>`)
            .on('contextmenu', (e) => e.preventDefault())
            .appendTo(book);
    });
}

function applyPageImage(realPage, url) {
    const $page = $(bookEl.value).find(`.page[data-real="${realPage}"]`).first();
    if ($page.length) {
        $page.css('background-image', `url(${url})`);
        $page.children('.turn-page-loader').remove();
    }
}

function ensureUrl(realPage) {
    if (loaded[realPage] || inflight.has(realPage)) return;

    // Pages pre-signed by the server (props.pageUrls) paint instantly with
    // zero round-trips; anything else falls back to an on-demand mint.
    const direct = props.pageUrls?.[realPage];
    if (direct) {
        loaded[realPage] = true;
        applyPageImage(realPage, direct);
        return;
    }

    inflight.add(realPage);
    axios
        .get(pageUrl(realPage))
        .then(({ data }) => {
            loaded[realPage] = true;
            applyPageImage(realPage, data.url);
        })
        .catch(() => {
            const $page = $(bookEl.value).find(`.page[data-real="${realPage}"]`).first();
            $page.children('.turn-page-loader').text(t('reader.load_failed')).addClass('is-error');
        })
        .finally(() => {
            inflight.delete(realPage);
        });
}

// Off-screen pages must be _downloaded_ ahead of time (browsers won't fetch
// a display:none page's background), so warm them via a hidden Image AND
// render the element's background from the same URL. Rendering matters as
// much as caching: turn.js clones the visible .page elements when zooming in,
// so a page that was only cache-warmed would come back blank in the zoomed
// view (its loader div would still be up).
function prewarm(realPage) {
    if (loaded[realPage] || inflight.has(realPage)) return;

    const direct = props.pageUrls?.[realPage];
    const via = (url) => {
        loaded[realPage] = true;
        applyPageImage(realPage, url);
        inflight.add(realPage);
        const img = new Image();
        img.onload = () => inflight.delete(realPage);
        img.onerror = () => {
            loaded[realPage] = false;
            inflight.delete(realPage);
        };
        img.src = url;
    };
    if (direct) {
        via(direct);
        return;
    }
    inflight.add(realPage);
    axios
        .get(pageUrl(realPage))
        .then(({ data }) => via(data.url))
        .catch(() => {})
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
            [pos - 2, pos - 1, pos + 1, pos + 2].forEach((p) => {
                const real = realPages.value[p - 1];
                if (real !== undefined && !seen.has(real)) {
                    seen.add(real);
                    prewarm(real);
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
    const { pageH, double } = size.value;
    const { width } = bookStyle.value;

    const $el = $(el);

    // If a previous zoom session is still live, snap it closed before the
    // flipbook is torn down below (zoom.js cleans up on turn('destroy'), but
    // closing it synchronously avoids its 500ms animate racing the teardown).
    const vp = rootEl.value;
    if (vp && $.fn.zoom && $(vp).data('zoom')) {
        try {
            $(vp).zoom('zoomOut', 0);
        } catch (err) {
            console.warn('zoom:zoomOut skipped', err);
        }
        zoomed.value = false;
    }

    try {
        // Turn.js stores its instance data on the element; destroy only
        // touches an element that actually has a flipbook on it.
        if ($el.turn('is')) $el.turn('destroy');
    } catch (err) {
        console.warn('turn:destroy skipped', err);
    }

    for (const key in loaded) delete loaded[key];
    inflight = new Set();
    buildPages();

    // Same initialisation as turnjs4/samples/basic (raw turn.js), with the
    // two page lifecycle callbacks we need for per-view image loading.
    $el.turn({
        width,
        height: pageH,
        elevation: 50,
        gradients: true,
        autoCenter: true,
        display: double ? 'double' : 'single',
        when: {
            turning: (e, page, view) => {
                if (view) loadView(view);
            },
            turned: handleTurned,
        },
    });

    const startPos = Math.max(1, Math.min(currentPos.value || 1, realPages.value.length));
    $el.turn('page', startPos);

    initZoom();

    syncFromView($el.turn('view') ?? [startPos]);
}

async function initTurn() {
    // The raw turn.js file reads the global `jQuery` at load time, so it must
    // exist before the chunk executes (this is what the stock samples rely on).
    window.jQuery = window.$ = $;
    try {
        await import('@/vendor/turnjs/turn.js');
    } catch (e) {
        if (!window.jQuery && !$.fn.turn) {
            window.jQuery = window.$ = $;
        }
        initializing.value = false;
        initError.value = true;
        console.error('Turn.js failed to initialise', e);
        return;
    }

    // The zoom plugin (turnjs4/lib/zoom.js) is an enhancement — if it ever
    // fails to load the flipbook still works, just without zooming.
    try {
        await import('@/vendor/turnjs/zoom.js');
    } catch (e) {
        console.warn('Turn.js zoom plugin failed to load', e);
        zoomReady.value = false;
    }
    await nextTick();
    size.value = computeSize();
    try {
        createTurn();
    } catch (e) {
        console.error('Turn.js failed to create the flipbook', e);
        initError.value = true;
        initializing.value = false;
        return;
    }
    ready.value = true;
    initializing.value = false;
}

function goPrev() {
    if (ready.value && bookEl.value) $(bookEl.value).turn('previous');
}

function goNext() {
    if (ready.value && bookEl.value) $(bookEl.value).turn('next');
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
    if (ready.value && bookEl.value) $(bookEl.value).turn('page', Math.max(1, Math.min(pos, realPages.value.length)));
}

function zoomValue() {
    const vp = rootEl.value;
    if (!vp || !$.fn.zoom || !$(vp).data('zoom')) return 1;
    return $(vp).zoom('value');
}

function zoomToggle(e) {
    if (!zoomReady.value) return;
    if (zoomValue() === 1) $(rootEl.value).zoom('zoomIn', e ?? null);
    else $(rootEl.value).zoom('zoomOut');
}

function zoomIn() {
    if (!zoomReady.value || !$(rootEl.value).data('zoom')) return;
    $(rootEl.value).zoom('zoomIn');
}

function zoomOut() {
    if (!zoomReady.value || !$(rootEl.value).data('zoom')) return;
    $(rootEl.value).zoom('zoomOut');
}

function initZoom() {
    const vp = rootEl.value;
    if (!vp || !$.fn.turn || !$.fn.zoom || !$(bookEl.value).turn('is')) {
        zoomReady.value = false;
        return;
    }
    const $vp = $(vp);
    $vp.zoom({
        flipbook: $(bookEl.value),
        max: ZOOM_MAX,
        when: {
            zoomIn: () => {
                zoomed.value = true;
            },
            zoomOut: () => {
                zoomed.value = false;
            },
        },
    });
    zoomReady.value = true;

    // zoom.js fires 'zoom.change' just before every zoom in/out. Use it as a
    // last-chance gate: pages in the current view that somehow still lack a
    // rendered background (a fast zoom right after a jump) get one now, so the
    // zoomer clone never captures a bare loader div.
    $vp.on('zoom.change.turnzoom', () => {
        const view = $(bookEl.value).turn('view');
        if (Array.isArray(view)) loadView(view, false);
    });

    // zoom.js listens for jQuery's legacy `mousewheel` event, which needs an
    // extra plugin that modern browsers never emit; mirror the native `wheel`
    // event into the plugin's scroll API instead.
    if (!vp.dataset.zoomWheel) {
        vp.dataset.zoomWheel = '1';
        $vp.on('wheel.turnzoom', (e) => {
            const zd = $vp.data('zoom');
            if (!zd || $vp.zoom('value') <= 1) return;
            const de = e.originalEvent;
            const cur = {
                x: (zd.scrollPos?.x ?? 0) + de.deltaX,
                y: (zd.scrollPos?.y ?? 0) + de.deltaY,
            };
            $vp.zoom('scroll', cur, false, true);
            e.preventDefault();
        });
    }

    // Magazine reference behaviour: tapping the book toggles the zoom scale.
    // Desktop uses a single tap; touch uses a double tap so single taps don't
    // fight with page dragging.
    if ($.isTouch) $vp.off('.turnzoomtap').on('zoom.doubleTap.turnzoomtap', zoomToggle);
    else $vp.off('.turnzoomtap').on('zoom.tap.turnzoomtap', zoomToggle);
}

function onKeydown(e) {
    if (e.target.closest('input')) return;
    if (e.key === 'Escape') {
        if (zoomed.value) {
            e.preventDefault();
            zoomOut();
        }
        return;
    }
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
            try {
                createTurn();
            } catch (e) {
                console.error('Turn.js resize failed', e);
            }
        }
    }, 220);
}

// Console helper for diagnosing the flipbook state in the browser.
onMounted(() => {
    window.__sikhunTurn = function () {
        return {
            turnLoaded: Boolean($.fn.turn),
            state: initError.value ? 'error' : initializing.value ? 'loading' : ready.value ? 'ready' : 'unknown',
            size: size.value,
            pages: bookEl.value ? (($.fn.turn && ready.value) ? $(bookEl.value).turn('pages') : null) : null,
            view: bookEl.value ? (($.fn.turn && ready.value) ? $(bookEl.value).turn('view') : null) : null,
            pageWrappers: bookEl.value ? $(bookEl.value).find('.page-wrapper').length : 0,
            loadedImages: Object.keys(loaded).length,
            realPages: realPages.value.length,
            zoomPlugin: Boolean($.fn.zoom),
            zoomReady: zoomReady.value,
            zoom: ready.value ? zoomValue() : null,
            zoomed: zoomed.value,
        };
    };

    currentPage.value = realPages.value[0] ?? 1;
    initTurn();
    window.addEventListener('keydown', onKeydown);
    window.addEventListener('resize', onResize);
});

onBeforeUnmount(() => {
    window.clearTimeout(resizeTimer);
    window.removeEventListener('keydown', onKeydown);
    window.removeEventListener('resize', onResize);
    if (rootEl.value && $.fn.zoom) {
        $(rootEl.value).off('.turnzoom').off('.turnzoomtap');
    }
    if (bookEl.value && $.fn.turn) {
        try {
            const $el = $(bookEl.value);
            if ($el.turn('is')) $el.turn('destroy');
        } catch (e) {
            /* already torn down */
        }
    }
});
</script>

<template>
    <div
        ref="rootEl"
        class="flipbook-viewport turn-reader relative select-none"
        :style="{ height: `${size.pageH}px` }"
    >
        <div
            v-if="initError"
            class="absolute inset-0 z-20 flex items-center justify-center text-sm text-red-600"
        >
            {{ t('reader.load_failed') }}
        </div>

        <div
            v-else-if="!realPages.length"
            class="absolute inset-0 z-20 flex items-center justify-center text-sm text-[var(--text-muted)]"
        >
            {{ t('reader.load_failed') }}
        </div>

        <template v-else>
            <div
                v-if="initializing"
                class="absolute inset-0 z-20 flex items-center justify-center gap-3 rounded-xl bg-[var(--surface)] text-sm text-[var(--text-muted)]"
            >
                {{ t('reader.loading') }}
            </div>

            <div class="container">
                <div ref="bookEl" class="flipbook" :style="bookStyle"></div>
            </div>

            <div
                v-if="ready && zoomReady"
                class="absolute top-2 right-2 z-30 flex items-center gap-1.5"
            >
                <button
                    v-if="zoomed"
                    type="button"
                    class="zoom-exit"
                    :aria-label="t('reader.zoom_exit')"
                    @click="zoomOut"
                >
                    {{ t('reader.zoom_exit') }} · ESC
                </button>
                <button
                    type="button"
                    class="zoom-ctrl"
                    :aria-label="t('reader.zoom_out')"
                    @click="zoomOut"
                >
                    <MagnifyingGlassMinusIcon class="h-4 w-4" />
                </button>
                <button
                    type="button"
                    class="zoom-ctrl"
                    :aria-label="t('reader.zoom_in')"
                    @click="zoomIn"
                >
                    <MagnifyingGlassPlusIcon class="h-4 w-4" />
                </button>
            </div>
        </template>
    </div>

    <div v-if="!zoomed" class="mt-6 flex items-center justify-center gap-4 text-sm">
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
.zoom-ctrl {
    width: 2.25rem;
    height: 2.25rem;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 9999px;
    background: rgba(0, 0, 0, 0.55);
    color: #fff;
    box-shadow: 0 1px 4px rgba(0, 0, 0, 0.35);
    transition: background 0.15s ease, transform 0.15s ease;
}
.zoom-ctrl:hover {
    background: rgba(0, 0, 0, 0.78);
    transform: scale(1.05);
}
.zoom-exit {
    display: flex;
    align-items: center;
    font-size: 12px;
    font-weight: 600;
    color: #fff;
    background: var(--primary);
    border-radius: 9999px;
    padding: 0.375rem 0.75rem;
    box-shadow: 0 1px 4px rgba(0, 0, 0, 0.3);
    transition: opacity 0.15s ease;
}
.zoom-exit:hover {
    opacity: 0.88;
}
</style>

<style>
/* Stock styles straight from turnjs4/samples/basic/css/basic.css (body
   overrides removed — they must not affect the rest of the app). Pages are
   built by Turn.js outside Vue's scoped attribute reach, so this stays
   unscoped. */

.turn-reader.flipbook-viewport {
    overflow: hidden;
    width: 100%;
}

.turn-reader .container {
    position: absolute;
    top: 50%;
    left: 50%;
    margin: auto;
}

.turn-reader .flipbook {
    position: absolute;
}

.turn-reader .page {
    background-color: #ffffff;
    background-repeat: no-repeat;
    background-size: 100% 100%;
}

.turn-reader .flipbook .page {
    -webkit-box-shadow: 0 0 20px rgba(0, 0, 0, 0.2);
    -moz-box-shadow: 0 0 20px rgba(0, 0, 0, 0.2);
    -ms-box-shadow: 0 0 20px rgba(0, 0, 0, 0.2);
    box-shadow: 0 0 20px rgba(0, 0, 0, 0.2);
}

.turn-reader .page img,
.turn-reader .page {
    -webkit-touch-callout: none;
    -webkit-user-select: none;
    -khtml-user-select: none;
    -moz-user-select: none;
    -ms-user-select: none;
    user-select: none;
}

.turn-reader .shadow {
    transition: box-shadow 0.5s;
    box-shadow: 0 0 20px #cccccc;
}

.turn-reader .page .turn-page-loader {
    position: absolute;
    inset: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    background: #ffffff;
    color: var(--text-muted, #6b7280);
    font-size: 12px;
    text-align: center;
    padding: 0.5rem;
}
.turn-reader .page .turn-page-loader.is-error {
    color: #dc2626;
}
</style>
