<script setup>
import { ref, computed, onMounted, onBeforeUnmount, nextTick } from 'vue';
import axios from 'axios';
import { useI18n } from '@/i18n';
import {
    MagnifyingGlassMinusIcon,
    MagnifyingGlassPlusIcon,
    ArrowPathIcon,
    ArrowsPointingOutIcon,
    ArrowsPointingInIcon,
    ChevronLeftIcon,
    ChevronRightIcon,
} from '@heroicons/vue/24/outline';
import $ from 'jquery';

const props = defineProps({
    bookId: { type: Number, required: true },
    totalPages: { type: Number, required: true },
    // null = unrestricted (full access); array = the only pages the student's chapter purchases cover.
    accessiblePages: { type: Array, default: null },
    // Base used to mint page-image URLs, e.g. `/library/${bookId}/read`
    urlPrefix: { type: String, default: null },
    // Pre-signed image URLs for pages, keyed by page number — lets pages paint instantly with 0 round-trips.
    pageUrls: { type: Object, default: () => null },
    // Real page number to open on (resume last read page).
    initialPage: { type: Number, default: null },
});

const emit = defineEmits(['page-change']);
const { t } = useI18n();

const PAGE_ASPECT = 1.414; // Standard A4 portrait aspect ratio

const rootEl = ref(null);
const bookEl = ref(null);
const initializing = ref(true);
const initError = ref(false);
const ready = ref(false);
const isFullscreen = ref(false);

// Smooth CSS Zoom & Pan state
const zoomLevel = ref(1.0);
const ZOOM_LEVELS = [1.0, 1.4, 1.8, 2.2];
const pan = ref({ x: 0, y: 0 });
const isDragging = ref(false);
let dragStart = { x: 0, y: 0 };
let panStart = { x: 0, y: 0 };

const size = ref(computeSize());
const loaded = {};
let inflight = new Set();

const currentPos = ref(1);
const currentPage = ref(1);
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

const containerStyle = computed(() => {
    if (zoomLevel.value <= 1.0) {
        return {
            transform: 'none',
            transition: isDragging.value ? 'none' : 'transform 0.22s cubic-bezier(0.2, 0, 0, 1)',
        };
    }
    return {
        transform: `translate(${pan.value.x}px, ${pan.value.y}px) scale(${zoomLevel.value})`,
        transformOrigin: 'center center',
        transition: isDragging.value ? 'none' : 'transform 0.22s cubic-bezier(0.2, 0, 0, 1)',
        cursor: isDragging.value ? 'grabbing' : 'grab',
    };
});

function computeSize() {
    const viewportW = rootEl.value?.clientWidth ?? 0;
    const containerW = Math.max(viewportW > 0 ? viewportW : window.innerWidth - 32, 280);
    const maxH = Math.max(Math.min(window.innerHeight * 0.76, 880), 380);
    const double = window.innerWidth >= 768;

    let pageW = double
        ? Math.min(Math.floor((containerW - 32) / 2), 520)
        : Math.min(containerW - 20, 520);
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
    if (ready.value && index >= 0) {
        $(bookEl.value).turn('page', index + 1);
    }
}

defineExpose({ goToRealPage });

function resolveInitialPos() {
    const target = Number(props.initialPage);
    if (!target || target < 1) return 1;

    let idx = realPages.value.indexOf(target);
    if (idx === -1) {
        let nearest = realPages.value[0];
        for (const p of realPages.value) {
            if (Math.abs(p - target) < Math.abs(nearest - target)) nearest = p;
        }
        idx = realPages.value.indexOf(nearest);
    }
    return idx >= 0 ? idx + 1 : 1;
}

function buildPages() {
    const book = $(bookEl.value);
    book.empty();
    const loading = t('reader.loading');
    const html = realPages.value
        .map(
            (p) =>
                `<div class="page" data-real="${p}"><div class="turn-page-loader"><div class="loader-spinner"></div><span>${loading}</span></div></div>`
        )
        .join('');
    book.html(html);
    book.off('contextmenu.reader').on('contextmenu.reader', '.page', (e) => e.preventDefault());
}

function applyPageImage(realPage, url) {
    const $page = $(bookEl.value).find(`.page[data-real="${realPage}"]`).first();
    if (!$page.length) return;

    if ($page.attr('data-loaded-url') === url) return;

    // Use Image preloader so background never flashes blank or white
    const img = new Image();
    img.onload = () => {
        $page.attr('data-loaded-url', url);
        $page.css({
            'background-image': `url("${url}")`,
            'background-size': '100% 100%',
            'background-repeat': 'no-repeat',
            'background-position': 'center',
        });
        const loader = $page.children('.turn-page-loader');
        if (loader.length) {
            loader.fadeOut(120, function () {
                $(this).remove();
            });
        }
    };
    img.onerror = () => {
        loaded[realPage] = false;
        const loader = $page.children('.turn-page-loader');
        if (loader.length) {
            loader.html(`
                <span class="text-red-500 mb-1">${t('reader.load_failed')}</span>
                <button type="button" class="retry-btn text-xs text-indigo-600 underline hover:text-indigo-800">Retry</button>
            `);
            loader.find('.retry-btn').off('click').on('click', () => {
                delete loaded[realPage];
                ensureUrl(realPage);
            });
        }
    };
    img.src = url;
}

function ensureUrl(realPage) {
    if (loaded[realPage] || inflight.has(realPage)) return;

    // Pre-signed direct URL from server
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
            const loader = $page.children('.turn-page-loader');
            if (loader.length) {
                loader.html(`
                    <span class="text-red-500 mb-1">${t('reader.load_failed')}</span>
                    <button type="button" class="retry-btn text-xs text-indigo-600 underline hover:text-indigo-800">Retry</button>
                `);
                loader.find('.retry-btn').off('click').on('click', () => {
                    delete loaded[realPage];
                    ensureUrl(realPage);
                });
            }
        })
        .finally(() => {
            inflight.delete(realPage);
        });
}

// Pre-load neighboring pages into browser cache for instant page flips
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
        if (pos > 0) {
            const real = realPages.value[pos - 1];
            if (real !== undefined && !seen.has(real)) {
                seen.add(real);
                ensureUrl(real);
            }
        }
    });

    if (prefetch) {
        // Aggressive prefetch: ahead by 1..5 pages and behind by 1..2 pages
        const activePos = view.filter((p) => p > 0);
        const maxPos = Math.max(...activePos, 1);
        const minPos = Math.min(...activePos, 1);

        const targets = [
            maxPos + 1,
            maxPos + 2,
            maxPos + 3,
            maxPos + 4,
            maxPos + 5,
            minPos - 1,
            minPos - 2,
        ];

        targets.forEach((pos) => {
            if (pos > 0 && pos <= realPages.value.length) {
                const real = realPages.value[pos - 1];
                if (real !== undefined && !seen.has(real)) {
                    seen.add(real);
                    prewarm(real);
                }
            }
        });
    }
}

function syncFromView(view) {
    if (!Array.isArray(view) || !view.length) return;
    const valid = view.filter((p) => p > 0);
    if (!valid.length) return;

    const real = valid.map((pos) => realPages.value[pos - 1]).filter((p) => p !== undefined);
    if (!real.length) return;

    currentView.value = real;
    currentPos.value = valid[0];
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

    try {
        if ($el.turn('is')) $el.turn('destroy');
    } catch (err) {
        console.warn('turn:destroy skipped', err);
    }

    for (const key in loaded) delete loaded[key];
    inflight = new Set();
    buildPages();

    $el.turn({
        width,
        height: pageH,
        elevation: 50,
        duration: 380, // Snappy & super responsive page flip
        gradients: true,
        autoCenter: true,
        display: double ? 'double' : 'single',
        acceleration: true,
        when: {
            turning: (e, page, view) => {
                if (view) loadView(view);
            },
            turned: handleTurned,
        },
    });

    const startPos = Math.max(1, Math.min(currentPos.value || 1, realPages.value.length));
    $el.turn('page', startPos);

    syncFromView($el.turn('view') ?? [startPos]);
}

async function initTurn() {
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
    if (ready.value && bookEl.value) {
        $(bookEl.value).turn('previous');
    }
}

function goNext() {
    if (ready.value && bookEl.value) {
        $(bookEl.value).turn('next');
    }
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
    if (ready.value && bookEl.value) {
        $(bookEl.value).turn('page', Math.max(1, Math.min(pos, realPages.value.length)));
    }
}

// ----------------- Modern Smooth Zoom & Pan -----------------

function setZoom(next) {
    const clamped = Math.min(Math.max(next, 1.0), 2.5);
    zoomLevel.value = Math.round(clamped * 100) / 100;
    if (zoomLevel.value <= 1.0) {
        pan.value = { x: 0, y: 0 };
    }
}

function zoomIn() {
    const currentIndex = ZOOM_LEVELS.findIndex((z) => z >= zoomLevel.value - 0.05);
    if (currentIndex < ZOOM_LEVELS.length - 1) {
        setZoom(ZOOM_LEVELS[currentIndex + 1]);
    } else {
        setZoom(Math.min(zoomLevel.value + 0.3, 2.5));
    }
}

function zoomOut() {
    const currentIndex = ZOOM_LEVELS.findIndex((z) => z >= zoomLevel.value - 0.05);
    if (currentIndex > 0) {
        setZoom(ZOOM_LEVELS[currentIndex - 1]);
    } else {
        resetZoom();
    }
}

function resetZoom() {
    zoomLevel.value = 1.0;
    pan.value = { x: 0, y: 0 };
}

function toggleZoom(e) {
    if (zoomLevel.value > 1.0) {
        resetZoom();
    } else {
        // Zoom into 1.8x centered towards where the user double-clicked
        if (e && rootEl.value) {
            const rect = rootEl.value.getBoundingClientRect();
            const clickX = e.clientX - rect.left - rect.width / 2;
            const clickY = e.clientY - rect.top - rect.height / 2;
            pan.value = {
                x: Math.round(-clickX * 0.7),
                y: Math.round(-clickY * 0.7),
            };
        }
        zoomLevel.value = 1.8;
    }
}

function onMouseDown(e) {
    if (zoomLevel.value <= 1.0) return;
    if (e.button !== 0) return; // Left click only
    isDragging.value = true;
    dragStart = { x: e.clientX, y: e.clientY };
    panStart = { ...pan.value };
    e.preventDefault();
}

function onMouseMove(e) {
    if (!isDragging.value) return;
    const dx = e.clientX - dragStart.x;
    const dy = e.clientY - dragStart.y;
    pan.value = {
        x: panStart.x + dx,
        y: panStart.y + dy,
    };
}

function onMouseUp() {
    isDragging.value = false;
}

function onTouchStart(e) {
    if (zoomLevel.value <= 1.0 || e.touches.length !== 1) return;
    isDragging.value = true;
    dragStart = { x: e.touches[0].clientX, y: e.touches[0].clientY };
    panStart = { ...pan.value };
}

function onTouchMove(e) {
    if (!isDragging.value || e.touches.length !== 1) return;
    const dx = e.touches[0].clientX - dragStart.x;
    const dy = e.touches[0].clientY - dragStart.y;
    pan.value = {
        x: panStart.x + dx,
        y: panStart.y + dy,
    };
}

function onTouchEnd() {
    isDragging.value = false;
}

function onWheel(e) {
    // Zoom with Ctrl + Wheel or when already zoomed
    if (e.ctrlKey || zoomLevel.value > 1.0) {
        e.preventDefault();
        const delta = e.deltaY < 0 ? 0.15 : -0.15;
        setZoom(zoomLevel.value + delta);
    }
}

function toggleFullscreen() {
    if (!document.fullscreenElement) {
        rootEl.value?.requestFullscreen().catch(() => {});
        isFullscreen.value = true;
    } else {
        document.exitFullscreen().catch(() => {});
        isFullscreen.value = false;
    }
}

function onFullscreenChange() {
    isFullscreen.value = Boolean(document.fullscreenElement);
    onResize();
}

function onKeydown(e) {
    if (e.target.closest('input')) return;
    if (e.key === 'Escape') {
        if (zoomLevel.value > 1.0) {
            e.preventDefault();
            resetZoom();
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
    }, 200);
}

onMounted(() => {
    const startPos = resolveInitialPos();
    currentPos.value = startPos;
    currentPage.value = realPages.value[startPos - 1] ?? 1;

    initTurn();
    window.addEventListener('keydown', onKeydown);
    window.addEventListener('resize', onResize);
    document.addEventListener('fullscreenchange', onFullscreenChange);
});

onBeforeUnmount(() => {
    window.clearTimeout(resizeTimer);
    window.removeEventListener('keydown', onKeydown);
    window.removeEventListener('resize', onResize);
    document.removeEventListener('fullscreenchange', onFullscreenChange);

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
        class="flipbook-viewport turn-reader relative select-none rounded-2xl bg-neutral-900 shadow-2xl overflow-hidden flex flex-col justify-center items-center"
        :class="{ 'is-zoomed': zoomLevel > 1.0, 'is-fullscreen': isFullscreen }"
        :style="{ height: isFullscreen ? '100vh' : `${size.pageH + 40}px` }"
        @mousedown="onMouseDown"
        @mousemove="onMouseMove"
        @mouseup="onMouseUp"
        @mouseleave="onMouseUp"
        @touchstart.passive="onTouchStart"
        @touchmove.passive="onTouchMove"
        @touchend.passive="onTouchEnd"
        @wheel.passive="onWheel"
        @dblclick="toggleZoom"
    >
        <!-- Floating Header Controls (Zoom + Fullscreen) -->
        <div class="absolute top-3 right-3 z-30 flex items-center gap-1.5 bg-black/60 backdrop-blur-md px-3 py-1.5 rounded-full border border-white/10 shadow-lg">
            <span v-if="zoomLevel > 1.0" class="text-xs font-semibold text-white/90 mr-1">
                {{ Math.round(zoomLevel * 100) }}%
            </span>

            <button
                v-if="zoomLevel > 1.0"
                type="button"
                class="zoom-btn"
                title="Reset Zoom (ESC)"
                aria-label="Reset Zoom"
                @click.stop="resetZoom"
            >
                <ArrowPathIcon class="w-4 h-4" />
            </button>

            <button
                type="button"
                class="zoom-btn"
                :disabled="zoomLevel <= 1.0"
                title="Zoom Out"
                aria-label="Zoom Out"
                @click.stop="zoomOut"
            >
                <MagnifyingGlassMinusIcon class="w-4 h-4" />
            </button>

            <button
                type="button"
                class="zoom-btn"
                :disabled="zoomLevel >= 2.5"
                title="Zoom In"
                aria-label="Zoom In"
                @click.stop="zoomIn"
            >
                <MagnifyingGlassPlusIcon class="w-4 h-4" />
            </button>

            <div class="w-px h-4 bg-white/20 mx-1"></div>

            <button
                type="button"
                class="zoom-btn"
                :title="isFullscreen ? 'Exit Fullscreen' : 'Fullscreen'"
                :aria-label="isFullscreen ? 'Exit Fullscreen' : 'Fullscreen'"
                @click.stop="toggleFullscreen"
            >
                <ArrowsPointingInIcon v-if="isFullscreen" class="w-4 h-4" />
                <ArrowsPointingOutIcon v-else class="w-4 h-4" />
            </button>
        </div>

        <div v-if="initError" class="absolute inset-0 z-20 flex flex-col items-center justify-center text-sm text-red-400 gap-2">
            <span>{{ t('reader.load_failed') }}</span>
            <button type="button" @click="initTurn" class="px-3 py-1 bg-white/10 hover:bg-white/20 rounded text-xs text-white">
                Retry
            </button>
        </div>

        <div v-else-if="!realPages.length" class="absolute inset-0 z-20 flex items-center justify-center text-sm text-white/60">
            {{ t('reader.load_failed') }}
        </div>

        <template v-else>
            <div
                v-if="initializing"
                class="absolute inset-0 z-20 flex flex-col items-center justify-center gap-3 bg-neutral-900/90 text-sm text-white/80"
            >
                <div class="w-8 h-8 border-2 border-indigo-500 border-t-transparent rounded-full animate-spin"></div>
                <span>{{ t('reader.loading') }}</span>
            </div>

            <!-- Book Container with dynamic CSS Pan & Zoom -->
            <div class="container" :style="containerStyle">
                <div ref="bookEl" class="flipbook" :style="bookStyle"></div>
            </div>
        </template>
    </div>

    <!-- Bottom Navigation Bar -->
    <div class="mt-5 flex items-center justify-center gap-4 text-sm">
        <button
            type="button"
            class="reader-ctrl"
            :aria-label="t('reader.prev')"
            :disabled="!ready || currentPos <= 1"
            @click="goPrev"
        >
            <ChevronLeftIcon class="w-5 h-5" />
        </button>

        <div class="flex items-center gap-2 text-[var(--text-muted)] bg-[var(--surface)] border border-[var(--border)] px-3 py-1.5 rounded-xl shadow-sm">
            <input
                type="number"
                min="1"
                :max="totalPages"
                v-model.number="jumpTarget"
                :placeholder="String(currentPage)"
                :disabled="!ready"
                @keydown.enter="jumpTo"
                class="w-14 rounded-md border border-[var(--border)] bg-[var(--surface2)] px-2 py-0.5 text-center text-sm font-semibold outline-none focus:border-[var(--primary)] disabled:opacity-50"
            />
            <span class="font-medium text-xs sm:text-sm">{{ indicatorText }}</span>
        </div>

        <button
            type="button"
            class="reader-ctrl"
            :aria-label="t('reader.next')"
            :disabled="!ready || currentPos >= realPages.length"
            @click="goNext"
        >
            <ChevronRightIcon class="w-5 h-5" />
        </button>
    </div>
</template>

<style scoped>
.reader-ctrl {
    width: 2.75rem;
    height: 2.75rem;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 9999px;
    background: color-mix(in srgb, var(--primary) 12%, transparent);
    color: var(--primary);
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.08);
    transition: all 0.15s ease;
}
.reader-ctrl:hover:not(:disabled) {
    background: color-mix(in srgb, var(--primary) 24%, transparent);
    transform: scale(1.05);
}
.reader-ctrl:disabled {
    opacity: 0.3;
    cursor: not-allowed;
}

.zoom-btn {
    width: 2rem;
    height: 2rem;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 9999px;
    color: #fff;
    background: transparent;
    transition: all 0.15s ease;
}
.zoom-btn:hover:not(:disabled) {
    background: rgba(255, 255, 255, 0.2);
    transform: scale(1.1);
}
.zoom-btn:disabled {
    opacity: 0.35;
    cursor: not-allowed;
}
</style>

<style>
/* Unscoped Turn.js container & page layout */
.turn-reader.flipbook-viewport {
    width: 100%;
}

.turn-reader.is-fullscreen {
    position: fixed !important;
    inset: 0 !important;
    z-index: 9999 !important;
    border-radius: 0 !important;
    background: #000000 !important;
}

.turn-reader .container {
    position: absolute;
    top: 50%;
    left: 50%;
    margin: auto;
    will-change: transform;
}

.turn-reader .flipbook {
    position: absolute;
    box-shadow: 0 20px 40px -15px rgba(0, 0, 0, 0.5);
}

.turn-reader .page {
    background-color: #ffffff;
    background-repeat: no-repeat;
    background-size: 100% 100%;
    background-position: center;
    box-shadow: 0 0 20px rgba(0, 0, 0, 0.25);
    overflow: hidden;
}

.turn-reader .page img,
.turn-reader .page {
    -webkit-touch-callout: none;
    -webkit-user-select: none;
    -moz-user-select: none;
    -ms-user-select: none;
    user-select: none;
}

.turn-reader .page .turn-page-loader {
    position: absolute;
    inset: 0;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    background: #f8fafc;
    color: #64748b;
    font-size: 13px;
    text-align: center;
    padding: 1rem;
    gap: 8px;
    z-index: 5;
}

.turn-reader .loader-spinner {
    width: 24px;
    height: 24px;
    border: 3px solid #e2e8f0;
    border-top-color: #6366f1;
    border-radius: 50%;
    animation: turn-spin 0.8s linear infinite;
}

@keyframes turn-spin {
    to {
        transform: rotate(360deg);
    }
}
</style>
