<script setup>
import { ref, computed, onMounted, watch } from 'vue';
import { Link } from '@inertiajs/vue3';
import { geoMercator, geoPath, geoCentroid } from 'd3-geo';
import {
    MapIcon,
    MagnifyingGlassIcon,
    MagnifyingGlassPlusIcon,
    MagnifyingGlassMinusIcon,
    ArrowPathIcon,
    ArrowTopRightOnSquareIcon,
    ShareIcon,
    CheckIcon,
    LockClosedIcon,
    SparklesIcon,
    AcademicCapIcon,
    ArrowDownTrayIcon,
    BookOpenIcon,
    TrophyIcon,
    MapPinIcon,
    XMarkIcon,
    FireIcon,
} from '@heroicons/vue/24/outline';
import {
    BD_MAP_SOURCES,
    BD_DIVISIONS,
    BD_DISTRICTS,
    BD_RIVERS,
    BD_LANDMARKS,
} from '@/Data/bangladeshMapData';
import { downloadBrandedPdf } from '@/Utils/studentPdfExport';

const props = defineProps({
    en: { type: Boolean, default: false },
    isPaid: { type: Boolean, default: false },
    isAuthenticated: { type: Boolean, default: false },
});

const FREE_EXAM_POINTS_LIMIT = 2;
const FREE_QUIZ_LIMIT = 5;

// ================= MAP GEOMETRY STATE =================
const loadingMap = ref(true);
const mapError = ref('');
const divisionsGeo = ref(null);
const districtsGeo = ref(null);

const SVG_WIDTH = 600;
const SVG_HEIGHT = 760;

// ================= INTERACTION STATE =================
const activeLayer = ref('divisions'); // divisions | districts | rivers | landmarks
const selectedDivisionKey = ref('dhaka');
const selectedDistrictKey = ref('dhaka');
const selectedRiverId = ref(BD_RIVERS[0]?.id || 'padma');
const selectedLandmarkId = ref(BD_LANDMARKS[0]?.id || 'du');

const districtDivisionFilter = ref('all');
const landmarkCategoryFilter = ref('all');
const heatmapMetric = ref('none'); // none | population | area | density
const detailSubTab = ref('overview'); // overview | exam

const searchQuery = ref('');
const showSearchDropdown = ref(false);
const hoveredInfo = ref(null); // { title, subtitle, x, y }
const copiedShare = ref(false);
const exportingPdf = ref(false);
const showPdfProNotice = ref(false);

// Zoom & Pan
const zoomLevel = ref(1);
const panX = ref(0);
const panY = ref(0);
const isDragging = ref(false);
const dragStart = ref({ x: 0, y: 0 });

// ================= QUIZ STATE =================
const quizActive = ref(false);
const quizLayer = ref('divisions'); // divisions | districts
const quizTargetKey = ref('');
const quizAskedKeys = ref([]);
const quizQuestionNumber = ref(0);
const quizScore = ref(0);
const quizStreak = ref(0);
const quizStatus = ref('idle'); // idle | asking | correct | wrong | finished
const quizLastClickedName = ref('');

// ================= COLORS & CATEGORIES =================
const DIVISION_COLORS = {
    dhaka: '#0ea5e9',
    chattogram: '#10b981',
    rajshahi: '#f59e0b',
    khulna: '#8b5cf6',
    barishal: '#ec4899',
    sylhet: '#14b8a6',
    rangpur: '#f97316',
    mymensingh: '#6366f1',
};

const LANDMARK_CATEGORIES = [
    { id: 'all', bn: 'সব', en: 'All', color: '#64748b' },
    { id: 'university', bn: 'বিশ্ববিদ্যালয়', en: 'University', color: '#6366f1' },
    { id: 'seaport', bn: 'সমুদ্রবন্দর', en: 'Seaport', color: '#0284c7' },
    { id: 'airport', bn: 'বিমানবন্দর', en: 'Airport', color: '#0ea5e9' },
    { id: 'mega_project', bn: 'মেগা প্রকল্প', en: 'Mega Project', color: '#ea580c' },
    { id: 'heritage', bn: 'বিশ্ব ঐতিহ্য', en: 'UNESCO Heritage', color: '#059669' },
    { id: 'power_plant', bn: 'বিদ্যুৎকেন্দ্র', en: 'Power Plant', color: '#eab308' },
    { id: 'epz', bn: 'ইপিজেড', en: 'EPZ', color: '#8b5cf6' },
    { id: 'historic', bn: 'ঐতিহাসিক', en: 'Historic', color: '#e11d48' },
];

const categoryColorMap = Object.fromEntries(LANDMARK_CATEGORIES.map((c) => [c.id, c.color]));

// ================= NUMBER & TEXT HELPERS =================
const bnDigits = ['০', '১', '২', '৩', '৪', '৫', '৬', '৭', '৮', '৯'];
function toBnNumber(val) {
    if (val === null || val === undefined) return '';
    return String(val).replace(/\d/g, (d) => bnDigits[Number(d)]);
}

function formatNumber(num) {
    if (num === null || num === undefined || Number.isNaN(Number(num))) return '—';
    const formatted = Number(num).toLocaleString('en-IN');
    return props.en ? formatted : toBnNumber(formatted);
}

function formatYear(val) {
    if (!val) return '—';
    return props.en ? String(val) : toBnNumber(String(val));
}

function getCategoryLabel(catId) {
    const found = LANDMARK_CATEGORIES.find((c) => c.id === catId);
    if (!found) return catId;
    return props.en ? found.en : found.bn;
}

// ================= D3 PROJECTION & PATHS =================
const projection = computed(() => {
    if (!divisionsGeo.value) return null;
    return geoMercator().fitExtent(
        [
            [24, 24],
            [SVG_WIDTH - 24, SVG_HEIGHT - 24],
        ],
        divisionsGeo.value
    );
});

const pathGenerator = computed(() => {
    if (!projection.value) return null;
    return geoPath().projection(projection.value);
});

const divisionShapes = computed(() => {
    if (!divisionsGeo.value || !pathGenerator.value) return [];
    return divisionsGeo.value.features.map((f) => {
        const key = f.properties.geoKey;
        const meta = BD_DIVISIONS[key] || {};
        const centroid = pathGenerator.value.centroid(f);
        return {
            key,
            d: pathGenerator.value(f) || '',
            cx: Number.isFinite(centroid[0]) ? centroid[0] : 300,
            cy: Number.isFinite(centroid[1]) ? centroid[1] : 380,
            meta,
        };
    });
});

const districtShapes = computed(() => {
    if (!districtsGeo.value || !pathGenerator.value) return [];
    return districtsGeo.value.features.map((f) => {
        const key = f.properties.geoKey;
        const meta = BD_DISTRICTS[key] || {};
        const centroid = pathGenerator.value.centroid(f);
        return {
            key,
            d: pathGenerator.value(f) || '',
            cx: Number.isFinite(centroid[0]) ? centroid[0] : 300,
            cy: Number.isFinite(centroid[1]) ? centroid[1] : 380,
            meta,
        };
    });
});

const riverPaths = computed(() => {
    if (!projection.value) return [];
    return BD_RIVERS.map((r) => {
        const pts = (r.coords || [])
            .map((coord) => projection.value(coord))
            .filter((p) => p && Number.isFinite(p[0]) && Number.isFinite(p[1]));
        const d = pts.length ? `M ${pts.map((p) => `${p[0].toFixed(1)},${p[1].toFixed(1)}`).join(' L ')}` : '';
        const mid = pts[Math.floor(pts.length / 2)] || [300, 380];
        return { ...r, d, lx: mid[0], ly: mid[1] };
    });
});

const landmarkPins = computed(() => {
    if (!projection.value) return [];
    return BD_LANDMARKS.filter((l) => landmarkCategoryFilter.value === 'all' || l.category === landmarkCategoryFilter.value)
        .map((l) => {
            const pt = projection.value(l.coords);
            if (!pt || !Number.isFinite(pt[0]) || !Number.isFinite(pt[1])) return null;
            return {
                ...l,
                x: pt[0],
                y: pt[1],
                color: categoryColorMap[l.category] || '#0ea5e9',
            };
        })
        .filter(Boolean);
});

// ================= HEATMAP COMPUTATION =================
const heatmapRange = computed(() => {
    if (heatmapMetric.value === 'none') return { min: 0, max: 1 };
    const source = activeLayer.value === 'divisions' ? Object.values(BD_DIVISIONS) : Object.values(BD_DISTRICTS);
    const field = heatmapMetric.value === 'area' ? 'areaKm2' : heatmapMetric.value;
    const vals = source.map((item) => Number(item[field] || 0)).filter((v) => v > 0);
    return {
        min: Math.min(...vals),
        max: Math.max(...vals),
    };
});

function getHeatmapColor(value) {
    const { min, max } = heatmapRange.value;
    const ratio = max > min ? Math.max(0, Math.min(1, (Number(value || 0) - min) / (max - min))) : 0.5;
    // Emerald -> Teal -> Amber -> Rose scale (high legibility)
    if (ratio < 0.25) return '#a7f3d0';
    if (ratio < 0.5) return '#34d399';
    if (ratio < 0.75) return '#f59e0b';
    return '#e11d48';
}

function getDivisionFill(shape) {
    if (quizActive.value && quizLayer.value === 'divisions') {
        if (quizStatus.value === 'correct' && shape.key === quizTargetKey.value) return '#10b981';
        if (quizStatus.value === 'wrong' && shape.key === quizTargetKey.value) return '#10b981';
        return '#cbd5e1';
    }
    if (heatmapMetric.value !== 'none') {
        const field = heatmapMetric.value === 'area' ? 'areaKm2' : heatmapMetric.value;
        return getHeatmapColor(shape.meta[field]);
    }
    return DIVISION_COLORS[shape.key] || '#0ea5e9';
}

function getDistrictFill(shape) {
    if (quizActive.value && quizLayer.value === 'districts') {
        if ((quizStatus.value === 'correct' || quizStatus.value === 'wrong') && shape.key === quizTargetKey.value) {
            return '#10b981';
        }
        return '#cbd5e1';
    }
    if (districtDivisionFilter.value !== 'all' && shape.meta.divisionKey !== districtDivisionFilter.value) {
        return '#e2e8f0';
    }
    if (heatmapMetric.value !== 'none') {
        const field = heatmapMetric.value === 'area' ? 'areaKm2' : heatmapMetric.value;
        return getHeatmapColor(shape.meta[field]);
    }
    return DIVISION_COLORS[shape.meta.divisionKey] || '#0ea5e9';
}

// ================= ACTIVE SELECTION =================
const selectedDivision = computed(() => BD_DIVISIONS[selectedDivisionKey.value] || BD_DIVISIONS.dhaka);
const selectedDistrict = computed(() => BD_DISTRICTS[selectedDistrictKey.value] || BD_DISTRICTS.dhaka);
const selectedRiver = computed(() => BD_RIVERS.find((r) => r.id === selectedRiverId.value) || BD_RIVERS[0]);
const selectedLandmark = computed(() => BD_LANDMARKS.find((l) => l.id === selectedLandmarkId.value) || BD_LANDMARKS[0]);

const activeDetail = computed(() => {
    if (activeLayer.value === 'divisions') {
        const d = selectedDivision.value;
        return {
            type: 'division',
            key: d.geoKey,
            title: props.en ? d.nameEn : d.nameBn,
            altTitle: props.en ? d.nameBn : d.nameEn,
            badge: props.en ? `Division • ${d.iso}` : `বিভাগ • ${d.iso}`,
            govUrl: d.govUrl,
            history: props.en ? d.historyEn : d.historyBn,
            famous: props.en ? d.famousEn : d.famousBn,
            examPoints: props.en ? d.examPointsEn || [] : d.examPointsBn || [],
            searchKeyword: d.nameBn.replace(' বিভাগ', ''),
            stats: [
                { label: props.en ? 'Established' : 'প্রতিষ্ঠাকাল', value: formatYear(d.established) },
                { label: props.en ? 'Districts' : 'মোট জেলা', value: formatNumber(d.districtCount) },
                { label: props.en ? 'Population (2022)' : 'জনসংখ্যা (২০২২)', value: formatNumber(d.population) },
                { label: props.en ? 'Area (km²)' : 'আয়তন (বর্গ কি.মি.)', value: formatNumber(d.areaKm2) },
                { label: props.en ? 'Density (/km²)' : 'ঘনত্ব (প্রতি ব.কি.মি.)', value: formatNumber(d.density) },
                { label: props.en ? 'Parliament Seats' : 'সংসদীয় আসন', value: formatNumber(d.parliamentSeats) },
            ],
            districtsList: (d.districts || []).map((dk) => BD_DISTRICTS[dk]).filter(Boolean),
        };
    }

    if (activeLayer.value === 'districts') {
        const d = selectedDistrict.value;
        const parentDiv = BD_DIVISIONS[d.divisionKey];
        return {
            type: 'district',
            key: d.geoKey,
            divisionKey: d.divisionKey,
            title: props.en ? `${d.nameEn} District` : `${d.nameBn} জেলা`,
            altTitle: props.en ? `${d.nameBn} জেলা` : `${d.nameEn} District`,
            badge: parentDiv ? (props.en ? parentDiv.nameEn : parentDiv.nameBn) : '',
            govUrl: d.govUrl,
            history: props.en ? d.historyEn : d.historyBn,
            famous: props.en ? d.famousEn : d.famousBn,
            examPoints: props.en ? d.examPointsEn || [] : d.examPointsBn || [],
            searchKeyword: d.nameBn,
            stats: [
                { label: props.en ? 'Established' : 'প্রতিষ্ঠাকাল', value: formatYear(d.established) },
                { label: props.en ? 'Population (2022)' : 'জনসংখ্যা (২০২২)', value: formatNumber(d.population) },
                { label: props.en ? 'Area (km²)' : 'আয়তন (বর্গ কি.মি.)', value: formatNumber(d.areaKm2) },
                { label: props.en ? 'Density (/km²)' : 'ঘনত্ব (প্রতি ব.কি.মি.)', value: formatNumber(d.density) },
                { label: props.en ? 'Upazilas' : 'উপজেলা', value: formatNumber(d.upazilaCount) },
                { label: props.en ? 'Parliament Seats' : 'সংসদীয় আসন', value: formatNumber(d.parliamentSeats) },
            ],
            districtsList: [],
        };
    }

    if (activeLayer.value === 'rivers') {
        const r = selectedRiver.value;
        return {
            type: 'river',
            key: r.id,
            title: props.en ? r.nameEn : r.nameBn,
            altTitle: props.en ? r.nameBn : r.nameEn,
            badge: props.en ? 'Major River of Bangladesh' : 'বাংলাদেশের প্রধান নদ-নদী',
            govUrl: '',
            history: props.en ? r.summaryEn : r.summaryBn,
            famous: props.en ? r.tributariesEn : r.tributariesBn,
            examPoints: props.en ? r.examPointsEn || [] : r.examPointsBn || [],
            searchKeyword: r.nameBn,
            stats: [
                { label: props.en ? 'Length' : 'দৈর্ঘ্য', value: props.en ? r.lengthKmEn : r.lengthKm },
                { label: props.en ? 'Origin' : 'উৎপত্তিস্থল', value: props.en ? r.originEn : r.originBn },
                { label: props.en ? 'Mouth / Outfall' : 'পতনস্থল / মোহনা', value: props.en ? r.mouthEn : r.mouthBn },
                { label: props.en ? 'Key Districts' : 'প্রবাহিত জেলা', value: props.en ? r.districtsEn : r.districtsBn },
            ],
            districtsList: [],
        };
    }

    const l = selectedLandmark.value;
    const dist = BD_DISTRICTS[l.districtKey];
    return {
        type: 'landmark',
        key: l.id,
        districtKey: l.districtKey,
        title: props.en ? l.nameEn : l.nameBn,
        altTitle: props.en ? l.nameBn : l.nameEn,
        badge: getCategoryLabel(l.category),
        govUrl: dist?.govUrl || '',
        history: props.en ? l.summaryEn : l.summaryBn,
        famous: '',
        examPoints: props.en ? l.examPointsEn || [] : l.examPointsBn || [],
        searchKeyword: l.nameBn,
        stats: [
            { label: props.en ? 'Category' : 'ধরন', value: getCategoryLabel(l.category) },
            { label: props.en ? 'Established' : 'প্রতিষ্ঠা / উদ্বোধন', value: formatYear(l.established) },
            {
                label: props.en ? 'District' : 'অবস্থান (জেলা)',
                value: dist ? (props.en ? dist.nameEn : dist.nameBn) : l.districtKey,
                clickableDistrict: dist ? dist.geoKey : '',
            },
        ],
        districtsList: [],
    };
});

const freeExamPoints = computed(() => activeDetail.value.examPoints.slice(0, FREE_EXAM_POINTS_LIMIT));
const lockedExamPoints = computed(() =>
    props.isPaid ? [] : activeDetail.value.examPoints.slice(FREE_EXAM_POINTS_LIMIT)
);
const visibleExamPoints = computed(() =>
    props.isPaid ? activeDetail.value.examPoints : freeExamPoints.value
);

// ================= GLOBAL SEARCH =================
const searchResults = computed(() => {
    const q = searchQuery.value.trim().toLowerCase();
    if (!q) return [];
    const results = [];

    for (const d of Object.values(BD_DIVISIONS)) {
        if (d.nameBn.toLowerCase().includes(q) || d.nameEn.toLowerCase().includes(q)) {
            results.push({
                layer: 'divisions',
                key: d.geoKey,
                title: props.en ? d.nameEn : d.nameBn,
                sub: props.en ? 'Division' : 'বিভাগ',
            });
        }
    }
    for (const d of Object.values(BD_DISTRICTS)) {
        if (
            d.nameBn.toLowerCase().includes(q) ||
            d.nameEn.toLowerCase().includes(q) ||
            (d.famousBn || '').toLowerCase().includes(q) ||
            (d.famousEn || '').toLowerCase().includes(q)
        ) {
            results.push({
                layer: 'districts',
                key: d.geoKey,
                title: props.en ? `${d.nameEn} District` : `${d.nameBn} জেলা`,
                sub: props.en ? BD_DIVISIONS[d.divisionKey]?.nameEn : BD_DIVISIONS[d.divisionKey]?.nameBn,
            });
        }
    }
    for (const r of BD_RIVERS) {
        if (r.nameBn.toLowerCase().includes(q) || r.nameEn.toLowerCase().includes(q)) {
            results.push({
                layer: 'rivers',
                key: r.id,
                title: props.en ? r.nameEn : r.nameBn,
                sub: props.en ? 'River' : 'নদ-নদী',
            });
        }
    }
    for (const l of BD_LANDMARKS) {
        if (l.nameBn.toLowerCase().includes(q) || l.nameEn.toLowerCase().includes(q)) {
            results.push({
                layer: 'landmarks',
                key: l.id,
                title: props.en ? l.nameEn : l.nameBn,
                sub: getCategoryLabel(l.category),
            });
        }
    }
    return results.slice(0, 10);
});

function selectSearchResult(item) {
    quizActive.value = false;
    activeLayer.value = item.layer;
    if (item.layer === 'divisions') selectedDivisionKey.value = item.key;
    if (item.layer === 'districts') {
        selectedDistrictKey.value = item.key;
        districtDivisionFilter.value = 'all';
    }
    if (item.layer === 'rivers') selectedRiverId.value = item.key;
    if (item.layer === 'landmarks') {
        selectedLandmarkId.value = item.key;
        landmarkCategoryFilter.value = 'all';
    }
    searchQuery.value = '';
    showSearchDropdown.value = false;
    syncUrlParams();
}

// ================= REGION SELECTION HANDLERS =================
function selectDivision(key) {
    if (quizActive.value && quizLayer.value === 'divisions') {
        handleQuizGuess(key);
        return;
    }
    selectedDivisionKey.value = key;
    showPdfProNotice.value = false;
    syncUrlParams();
}

function selectDistrict(key, switchLayer = false) {
    if (quizActive.value && quizLayer.value === 'districts' && !switchLayer) {
        handleQuizGuess(key);
        return;
    }
    if (switchLayer) {
        activeLayer.value = 'districts';
        districtDivisionFilter.value = 'all';
    }
    selectedDistrictKey.value = key;
    showPdfProNotice.value = false;
    syncUrlParams();
}

function selectRiver(id) {
    selectedRiverId.value = id;
    showPdfProNotice.value = false;
    syncUrlParams();
}

function selectLandmark(id) {
    selectedLandmarkId.value = id;
    showPdfProNotice.value = false;
    syncUrlParams();
}

function switchLayer(layer) {
    quizActive.value = false;
    activeLayer.value = layer;
    showPdfProNotice.value = false;
    syncUrlParams();
}

// ================= QUIZ ENGINE =================
const quizTargetName = computed(() => {
    if (!quizTargetKey.value) return '';
    if (quizLayer.value === 'divisions') {
        const d = BD_DIVISIONS[quizTargetKey.value];
        return d ? (props.en ? d.nameEn : d.nameBn) : '';
    }
    const d = BD_DISTRICTS[quizTargetKey.value];
    return d ? (props.en ? `${d.nameEn} District` : `${d.nameBn} জেলা`) : '';
});

function startQuiz(layer = null) {
    const targetLayer = layer || (activeLayer.value === 'districts' ? 'districts' : 'divisions');
    quizLayer.value = targetLayer;
    activeLayer.value = targetLayer;
    districtDivisionFilter.value = 'all';
    heatmapMetric.value = 'none';
    quizActive.value = true;
    quizAskedKeys.value = [];
    quizQuestionNumber.value = 0;
    quizScore.value = 0;
    quizStreak.value = 0;
    nextQuizQuestion();
}

function nextQuizQuestion() {
    if (!props.isPaid && quizQuestionNumber.value >= FREE_QUIZ_LIMIT) {
        quizStatus.value = 'finished';
        return;
    }
    const pool = quizLayer.value === 'divisions' ? Object.keys(BD_DIVISIONS) : Object.keys(BD_DISTRICTS);
    let remaining = pool.filter((k) => !quizAskedKeys.value.includes(k));
    if (remaining.length === 0) {
        quizAskedKeys.value = [];
        remaining = pool;
    }
    const pick = remaining[Math.floor(Math.random() * remaining.length)];
    quizTargetKey.value = pick;
    quizAskedKeys.value.push(pick);
    quizQuestionNumber.value += 1;
    quizStatus.value = 'asking';
    quizLastClickedName.value = '';
}

function handleQuizGuess(clickedKey) {
    if (quizStatus.value !== 'asking') return;
    if (clickedKey === quizTargetKey.value) {
        quizScore.value += 1;
        quizStreak.value += 1;
        quizStatus.value = 'correct';
    } else {
        quizStreak.value = 0;
        quizStatus.value = 'wrong';
        const clickedObj = quizLayer.value === 'divisions' ? BD_DIVISIONS[clickedKey] : BD_DISTRICTS[clickedKey];
        quizLastClickedName.value = clickedObj ? (props.en ? clickedObj.nameEn : clickedObj.nameBn) : clickedKey;
    }
    if (quizLayer.value === 'divisions') selectedDivisionKey.value = quizTargetKey.value;
    else selectedDistrictKey.value = quizTargetKey.value;
}

function exitQuiz() {
    quizActive.value = false;
    quizStatus.value = 'idle';
}

// ================= ZOOM & PAN =================
function zoomIn() {
    zoomLevel.value = Math.min(3.2, +(zoomLevel.value + 0.35).toFixed(2));
}
function zoomOut() {
    zoomLevel.value = Math.max(1, +(zoomLevel.value - 0.35).toFixed(2));
    if (zoomLevel.value === 1) {
        panX.value = 0;
        panY.value = 0;
    }
}
function resetZoom() {
    zoomLevel.value = 1;
    panX.value = 0;
    panY.value = 0;
}

function onPointerDown(e) {
    if (zoomLevel.value <= 1) return;
    isDragging.value = true;
    dragStart.value = { x: e.clientX - panX.value, y: e.clientY - panY.value };
}
function onPointerMove(e) {
    if (!isDragging.value) return;
    const maxPan = (zoomLevel.value - 1) * 220;
    panX.value = Math.max(-maxPan, Math.min(maxPan, e.clientX - dragStart.value.x));
    panY.value = Math.max(-maxPan, Math.min(maxPan, e.clientY - dragStart.value.y));
}
function onPointerUp() {
    isDragging.value = false;
}

// ================= SHARE & PDF EXPORT =================
function syncUrlParams() {
    if (typeof window === 'undefined') return;
    const url = new URL(window.location.href);
    url.searchParams.set('layer', activeLayer.value);
    url.searchParams.set('region', activeDetail.value.key);
    window.history.replaceState({}, '', url.toString());
}

async function copyShareLink() {
    syncUrlParams();
    try {
        await navigator.clipboard.writeText(window.location.href);
        copiedShare.value = true;
        setTimeout(() => {
            copiedShare.value = false;
        }, 2000);
    } catch {
        copiedShare.value = false;
    }
}

async function exportStudySheetPdf() {
    if (!props.isPaid) {
        showPdfProNotice.value = true;
        return;
    }
    if (exportingPdf.value) return;
    exportingPdf.value = true;
    try {
        const d = activeDetail.value;
        const statsRows = d.stats
            .map(
                (s) =>
                    `<div style="padding:10px 12px;border:1px solid #e2e8f0;border-radius:10px;background:#f8fafc;">
                        <div style="font-size:11px;color:#64748b;font-weight:600;">${s.label}</div>
                        <div style="font-size:14px;color:#0f172a;font-weight:700;margin-top:3px;">${s.value}</div>
                    </div>`
            )
            .join('');

        const examHtml = d.examPoints
            .map((pt, i) => `<li style="margin-bottom:6px;line-height:1.55;">${i + 1}. ${pt}</li>`)
            .join('');

        const districtsHtml =
            d.districtsList && d.districtsList.length
                ? `<div style="margin-top:16px;">
                    <h3 style="font-size:14px;font-weight:700;color:#0f172a;margin:0 0 8px;">${
                        props.en ? 'Districts in this Division' : 'এই বিভাগের জেলাসমূহ'
                    }</h3>
                    <p style="font-size:13px;color:#334155;line-height:1.6;">${d.districtsList
                        .map((item) => (props.en ? item.nameEn : item.nameBn))
                        .join(' • ')}</p>
                  </div>`
                : '';

        const bodyHtml = `
            <div style="font-family: 'Hind Siliguri', 'Noto Sans Bengali', sans-serif; color:#0f172a;">
                <div style="display:grid;grid-template-columns:repeat(2,1fr);gap:10px;margin-bottom:16px;">
                    ${statsRows}
                </div>
                <div style="margin-bottom:14px;padding:12px 14px;border-radius:10px;background:#f0f9ff;border:1px solid #bae6fd;">
                    <h3 style="font-size:14px;font-weight:700;color:#0369a1;margin:0 0 6px;">${
                        props.en ? 'Overview & Background' : 'সংক্ষিপ্ত পরিচিতি ও প্রেক্ষাপট'
                    }</h3>
                    <p style="font-size:13px;color:#1e293b;margin:0;line-height:1.6;">${d.history}</p>
                </div>
                ${
                    d.famous
                        ? `<div style="margin-bottom:14px;padding:12px 14px;border-radius:10px;background:#ecfdf5;border:1px solid #a7f3d0;">
                            <h3 style="font-size:14px;font-weight:700;color:#047857;margin:0 0 6px;">${
                                props.en ? 'Famous Specialties & Highlights' : 'বিখ্যাত পরিচিতি ও বৈশিষ্ট্য'
                            }</h3>
                            <p style="font-size:13px;color:#1e293b;margin:0;line-height:1.6;">${d.famous}</p>
                          </div>`
                        : ''
                }
                <div style="margin-bottom:14px;padding:12px 14px;border-radius:10px;background:#fffbeb;border:1px solid #fde68a;">
                    <h3 style="font-size:14px;font-weight:700;color:#b45309;margin:0 0 8px;">${
                        props.en ? 'BCS & Admission High-Yield Exam Notes' : 'বিসিএস ও ভর্তি পরীক্ষার গুরুত্বপূর্ণ টপিক'
                    }</h3>
                    <ul style="list-style:none;padding:0;margin:0;font-size:13px;color:#1e293b;">${examHtml}</ul>
                </div>
                ${districtsHtml}
                <p style="margin-top:18px;font-size:11px;color:#64748b;border-top:1px solid #e2e8f0;padding-top:8px;">
                    ${props.en ? BD_MAP_SOURCES.en : BD_MAP_SOURCES.bn}
                </p>
            </div>
        `;

        await downloadBrandedPdf({
            title: `${d.title} — ${props.en ? 'Bangladesh Map Study Sheet' : 'বাংলাদেশ মানচিত্র স্টাডি শিট'}`,
            subtitle: `${d.badge} • ${d.altTitle}`,
            bodyHtml,
            fileName: `sikhun-bd-map-${d.type}-${d.key}.pdf`,
        });
    } finally {
        exportingPdf.value = false;
    }
}

// ================= LOAD GEOJSON =================
async function loadGeoData() {
    loadingMap.value = true;
    mapError.value = '';
    try {
        const [divRes, distRes] = await Promise.all([
            fetch('/maps/bd-divisions.geojson'),
            fetch('/maps/bd-districts.geojson'),
        ]);
        if (!divRes.ok || !distRes.ok) {
            throw new Error('Failed to load map boundary files.');
        }
        divisionsGeo.value = await divRes.json();
        districtsGeo.value = await distRes.json();
    } catch (err) {
        mapError.value = props.en
            ? 'Could not load Bangladesh map data. Please retry.'
            : 'বাংলাদেশের মানচিত্র ডেটা লোড করা যায়নি। অনুগ্রহ করে আবার চেষ্টা করুন।';
    } finally {
        loadingMap.value = false;
    }
}

function hydrateFromUrl() {
    if (typeof window === 'undefined') return;
    const params = new URLSearchParams(window.location.search);
    const layer = params.get('layer');
    const region = params.get('region');
    if (['divisions', 'districts', 'rivers', 'landmarks'].includes(layer)) {
        activeLayer.value = layer;
    }
    if (region) {
        if (activeLayer.value === 'divisions' && BD_DIVISIONS[region]) selectedDivisionKey.value = region;
        if (activeLayer.value === 'districts' && BD_DISTRICTS[region]) selectedDistrictKey.value = region;
        if (activeLayer.value === 'rivers' && BD_RIVERS.some((r) => r.id === region)) selectedRiverId.value = region;
        if (activeLayer.value === 'landmarks' && BD_LANDMARKS.some((l) => l.id === region)) {
            selectedLandmarkId.value = region;
        }
    }
}

watch(activeLayer, () => {
    detailSubTab.value = 'overview';
});

onMounted(() => {
    hydrateFromUrl();
    loadGeoData();
});
</script>

<template>
    <div class="bd-map-shell space-y-5">
        <!-- HEADER & LAYER NAVIGATION -->
        <div
            class="rounded-3xl border border-[var(--border)] bg-[var(--surface)] p-4 sm:p-6 shadow-sm space-y-4"
        >
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                <div class="space-y-1">
                    <div class="flex flex-wrap items-center gap-2">
                        <span
                            class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-emerald-500/10 text-emerald-700 dark:text-emerald-300 border border-emerald-500/20"
                        >
                            <MapIcon class="w-3.5 h-3.5" />
                            <span>{{ en ? 'BCS & Admission Atlas' : 'বিসিএস ও ভর্তি অ্যাটলাস' }}</span>
                        </span>
                        <span
                            v-if="isPaid"
                            class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-extrabold bg-amber-500/15 text-amber-700 dark:text-amber-300 border border-amber-500/30"
                        >
                            <SparklesIcon class="w-3.5 h-3.5" />
                            <span>{{ en ? 'Pro Unlocked' : 'প্রো আনলকড' }}</span>
                        </span>
                        <Link
                            v-else
                            href="/plans"
                            class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-[var(--surface2)] text-[var(--text-muted)] hover:text-[var(--primary)] border border-[var(--border)] transition"
                        >
                            <LockClosedIcon class="w-3 h-3" />
                            <span>{{ en ? 'Free Explorer • Upgrade for Pro Notes & PDF' : 'ফ্রি এক্সপ্লোরার • প্রো নোটস ও PDF আনলক করুন' }}</span>
                        </Link>
                    </div>
                    <h2 class="font-heading text-xl sm:text-2xl font-extrabold text-[var(--text)]">
                        {{ en ? 'Bangladesh Interactive Map Explorer' : 'বাংলাদেশের ইন্টারেক্টিভ মানচিত্র এক্সপ্লোরার' }}
                    </h2>
                    <p class="text-xs sm:text-sm text-[var(--text-muted)]">
                        {{
                            en
                                ? 'Explore 8 divisions, 64 districts, 14 major rivers & 22 key organizations with BBS 2022 data and BCS/Admission exam topics.'
                                : '৮টি বিভাগ, ৬৪টি জেলা, ১৪টি প্রধান নদ-নদী ও ২২টি গুরুত্বপূর্ণ প্রতিষ্ঠান—বিবিএস ২০২২ তথ্য এবং বিসিএস ও ভর্তি পরীক্ষার টপিকসহ।'
                        }}
                    </p>
                </div>

                <!-- GLOBAL SEARCH -->
                <div class="relative w-full lg:w-80">
                    <label for="bd-map-search" class="sr-only">
                        {{ en ? 'Search division, district, river or landmark' : 'বিভাগ, জেলা, নদী বা বিখ্যাত স্থান খুঁজুন' }}
                    </label>
                    <MagnifyingGlassIcon
                        class="w-4 h-4 text-[var(--text-muted)] absolute left-3.5 top-1/2 -translate-y-1/2 pointer-events-none"
                    />
                    <input
                        id="bd-map-search"
                        v-model="searchQuery"
                        type="text"
                        autocomplete="off"
                        :placeholder="en ? 'Search Dhaka, Padma, Sundarbans...' : 'খুঁজুন: ঢাকা, পদ্মা, সুন্দরবন, বগুড়া...'"
                        class="w-full pl-10 pr-8 py-2.5 rounded-2xl bg-[var(--surface2)] border border-[var(--border)] text-sm text-[var(--text)] placeholder:text-[var(--text-muted)] focus:outline-none focus:ring-2 focus:ring-[var(--primary)]"
                        @focus="showSearchDropdown = true"
                        @blur="setTimeout(() => (showSearchDropdown = false), 180)"
                    />
                    <button
                        v-if="searchQuery"
                        type="button"
                        class="absolute right-2.5 top-1/2 -translate-y-1/2 p-1 text-[var(--text-muted)] hover:text-[var(--text)]"
                        :aria-label="en ? 'Clear search' : 'সার্চ মুছুন'"
                        @click="searchQuery = ''"
                    >
                        <XMarkIcon class="w-4 h-4" />
                    </button>

                    <!-- Search Suggestions -->
                    <div
                        v-if="showSearchDropdown && searchResults.length"
                        class="absolute z-30 mt-1.5 w-full rounded-2xl border border-[var(--border)] bg-[var(--surface)] shadow-xl overflow-hidden max-h-72 overflow-y-auto"
                    >
                        <button
                            v-for="item in searchResults"
                            :key="`${item.layer}-${item.key}`"
                            type="button"
                            class="w-full px-3.5 py-2.5 text-left hover:bg-[var(--surface2)] flex items-center justify-between gap-2 border-b last:border-b-0 border-[var(--border)] transition"
                            @mousedown.prevent="selectSearchResult(item)"
                        >
                            <span class="text-sm font-semibold text-[var(--text)]">{{ item.title }}</span>
                            <span class="text-[11px] font-bold px-2 py-0.5 rounded-full bg-[var(--surface2)] text-[var(--text-muted)]">
                                {{ item.sub }}
                            </span>
                        </button>
                    </div>
                </div>
            </div>

            <!-- 4 LAYER TABS + QUIZ BUTTON -->
            <div class="flex flex-wrap items-center justify-between gap-2 pt-2 border-t border-[var(--border)]">
                <div class="flex flex-wrap items-center gap-1.5" role="tablist">
                    <button
                        type="button"
                        role="tab"
                        :aria-selected="activeLayer === 'divisions'"
                        class="px-3.5 py-2 rounded-xl text-xs sm:text-sm font-bold transition border"
                        :class="
                            activeLayer === 'divisions' && !quizActive
                                ? 'bg-[var(--primary)] text-white border-[var(--primary)] shadow-sm'
                                : 'bg-[var(--surface2)] text-[var(--text)] border-[var(--border)] hover:border-[var(--primary)]/50'
                        "
                        @click="switchLayer('divisions')"
                    >
                        {{ en ? 'Divisions (8)' : 'বিভাগ (৮)' }}
                    </button>
                    <button
                        type="button"
                        role="tab"
                        :aria-selected="activeLayer === 'districts'"
                        class="px-3.5 py-2 rounded-xl text-xs sm:text-sm font-bold transition border"
                        :class="
                            activeLayer === 'districts' && !quizActive
                                ? 'bg-[var(--primary)] text-white border-[var(--primary)] shadow-sm'
                                : 'bg-[var(--surface2)] text-[var(--text)] border-[var(--border)] hover:border-[var(--primary)]/50'
                        "
                        @click="switchLayer('districts')"
                    >
                        {{ en ? 'Districts (64)' : 'জেলা (৬৪)' }}
                    </button>
                    <button
                        type="button"
                        role="tab"
                        :aria-selected="activeLayer === 'rivers'"
                        class="px-3.5 py-2 rounded-xl text-xs sm:text-sm font-bold transition border"
                        :class="
                            activeLayer === 'rivers' && !quizActive
                                ? 'bg-sky-600 text-white border-sky-600 shadow-sm'
                                : 'bg-[var(--surface2)] text-[var(--text)] border-[var(--border)] hover:border-sky-500/50'
                        "
                        @click="switchLayer('rivers')"
                    >
                        {{ en ? 'Rivers (14)' : 'নদ-নদী (১৪)' }}
                    </button>
                    <button
                        type="button"
                        role="tab"
                        :aria-selected="activeLayer === 'landmarks'"
                        class="px-3.5 py-2 rounded-xl text-xs sm:text-sm font-bold transition border"
                        :class="
                            activeLayer === 'landmarks' && !quizActive
                                ? 'bg-emerald-600 text-white border-emerald-600 shadow-sm'
                                : 'bg-[var(--surface2)] text-[var(--text)] border-[var(--border)] hover:border-emerald-500/50'
                        "
                        @click="switchLayer('landmarks')"
                    >
                        {{ en ? 'Places & Orgs (22)' : 'প্রতিষ্ঠান ও স্থান (২২)' }}
                    </button>
                </div>

                <!-- QUIZ MODE TRIGGER -->
                <button
                    type="button"
                    class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl text-xs sm:text-sm font-extrabold border transition"
                    :class="
                        quizActive
                            ? 'bg-amber-500 text-white border-amber-500 shadow-sm'
                            : 'bg-amber-500/10 text-amber-700 dark:text-amber-300 border-amber-500/30 hover:bg-amber-500/20'
                    "
                    @click="quizActive ? exitQuiz() : startQuiz()"
                >
                    <TrophyIcon class="w-4 h-4" />
                    <span>{{ quizActive ? (en ? 'Exit Quiz' : 'কুইজ বন্ধ করুন') : en ? 'Map Quiz' : 'ম্যাপ কুইজ খেলুন' }}</span>
                </button>
            </div>

            <!-- CONTEXTUAL LAYER FILTERS -->
            <div
                v-if="!quizActive && (activeLayer === 'divisions' || activeLayer === 'districts' || activeLayer === 'landmarks')"
                class="flex flex-wrap items-center gap-2.5 pt-1 text-xs"
            >
                <!-- District Division Filter -->
                <div v-if="activeLayer === 'districts'" class="flex items-center gap-2">
                    <label for="bd-division-filter" class="font-bold text-[var(--text-muted)]">
                        {{ en ? 'Division:' : 'বিভাগ ফিল্টার:' }}
                    </label>
                    <select
                        id="bd-division-filter"
                        v-model="districtDivisionFilter"
                        class="px-3 py-1.5 rounded-xl bg-[var(--surface2)] border border-[var(--border)] text-[var(--text)] font-semibold focus:outline-none focus:ring-2 focus:ring-[var(--primary)]"
                    >
                        <option value="all">{{ en ? 'All Divisions (64)' : 'সব বিভাগ (৬৪ জেলা)' }}</option>
                        <option v-for="d in Object.values(BD_DIVISIONS)" :key="d.geoKey" :value="d.geoKey">
                            {{ en ? `${d.nameEn} (${d.districtCount})` : `${d.nameBn} (${toBnNumber(d.districtCount)})` }}
                        </option>
                    </select>
                </div>

                <!-- Heatmap Metric -->
                <div
                    v-if="activeLayer === 'divisions' || activeLayer === 'districts'"
                    class="flex flex-wrap items-center gap-1.5"
                >
                    <span class="font-bold text-[var(--text-muted)]">{{ en ? 'Color By:' : 'হিটম্যাপ:' }}</span>
                    <button
                        v-for="opt in [
                            { id: 'none', bn: 'বিভাগীয় রং', en: 'Region' },
                            { id: 'population', bn: 'জনসংখ্যা', en: 'Population' },
                            { id: 'area', bn: 'আয়তন', en: 'Area' },
                            { id: 'density', bn: 'ঘনত্ব', en: 'Density' },
                        ]"
                        :key="opt.id"
                        type="button"
                        class="px-2.5 py-1 rounded-lg font-bold border transition"
                        :class="
                            heatmapMetric === opt.id
                                ? 'bg-[var(--primary)]/15 text-[var(--primary)] border-[var(--primary)]/40'
                                : 'bg-[var(--surface2)] text-[var(--text-muted)] border-[var(--border)] hover:text-[var(--text)]'
                        "
                        @click="heatmapMetric = opt.id"
                    >
                        {{ en ? opt.en : opt.bn }}
                    </button>
                </div>

                <!-- Landmark Category Chips -->
                <div v-if="activeLayer === 'landmarks'" class="flex flex-wrap items-center gap-1.5">
                    <button
                        v-for="cat in LANDMARK_CATEGORIES"
                        :key="cat.id"
                        type="button"
                        class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full font-bold border transition"
                        :class="
                            landmarkCategoryFilter === cat.id
                                ? 'bg-[var(--primary)] text-white border-[var(--primary)]'
                                : 'bg-[var(--surface2)] text-[var(--text-muted)] border-[var(--border)] hover:text-[var(--text)]'
                        "
                        @click="landmarkCategoryFilter = cat.id"
                    >
                        <span
                            class="w-2 h-2 rounded-full"
                            :style="{ backgroundColor: landmarkCategoryFilter === cat.id ? '#fff' : cat.color }"
                        />
                        <span>{{ en ? cat.en : cat.bn }}</span>
                    </button>
                </div>
            </div>
        </div>

        <!-- QUIZ BANNER (WHEN ACTIVE) -->
        <div
            v-if="quizActive"
            class="rounded-3xl border border-amber-500/30 bg-amber-500/10 p-4 sm:p-5 flex flex-col md:flex-row md:items-center md:justify-between gap-4"
        >
            <div class="space-y-1">
                <div class="flex flex-wrap items-center gap-2 text-xs font-extrabold">
                    <span class="px-2.5 py-0.5 rounded-full bg-amber-500 text-white">
                        {{
                            isPaid
                                ? en
                                    ? `Pro Unlimited • Q${quizQuestionNumber}`
                                    : `প্রো আনলিমিটেড • প্রশ্ন ${toBnNumber(quizQuestionNumber)}`
                                : en
                                    ? `Free Sample • Q${Math.min(quizQuestionNumber, FREE_QUIZ_LIMIT)}/${FREE_QUIZ_LIMIT}`
                                    : `ফ্রি কুইজ • প্রশ্ন ${toBnNumber(Math.min(quizQuestionNumber, FREE_QUIZ_LIMIT))}/${toBnNumber(FREE_QUIZ_LIMIT)}`
                        }}
                    </span>
                    <span class="text-emerald-700 dark:text-emerald-300">
                        {{ en ? `Score: ${quizScore}` : `স্কোর: ${toBnNumber(quizScore)}` }}
                    </span>
                    <span v-if="quizStreak > 1" class="inline-flex items-center gap-0.5 text-orange-600 dark:text-orange-400">
                        <FireIcon class="w-3.5 h-3.5" />
                        <span>{{ en ? `${quizStreak} Streak` : `${toBnNumber(quizStreak)} টানা সঠিক` }}</span>
                    </span>
                    <div class="inline-flex items-center gap-1 ml-1">
                        <button
                            type="button"
                            class="px-2 py-0.5 rounded-md border text-[11px]"
                            :class="
                                quizLayer === 'divisions'
                                    ? 'bg-amber-600 text-white border-amber-600'
                                    : 'bg-[var(--surface)] text-[var(--text-muted)] border-[var(--border)]'
                            "
                            @click="startQuiz('divisions')"
                        >
                            {{ en ? 'Divisions' : 'বিভাগ' }}
                        </button>
                        <button
                            type="button"
                            class="px-2 py-0.5 rounded-md border text-[11px]"
                            :class="
                                quizLayer === 'districts'
                                    ? 'bg-amber-600 text-white border-amber-600'
                                    : 'bg-[var(--surface)] text-[var(--text-muted)] border-[var(--border)]'
                            "
                            @click="startQuiz('districts')"
                        >
                            {{ en ? '64 Districts' : '৬৪ জেলা' }}
                        </button>
                    </div>
                </div>

                <div v-if="quizStatus === 'asking'" class="text-base sm:text-lg font-extrabold text-[var(--text)]">
                    {{ en ? 'Click on the map to locate:' : 'মানচিত্রে ক্লিক করে খুঁজে বের করুন:' }}
                    <span class="text-[var(--primary)] underline decoration-2 underline-offset-4 ml-1">{{ quizTargetName }}</span>
                </div>
                <div v-else-if="quizStatus === 'correct'" class="text-sm sm:text-base font-bold text-emerald-700 dark:text-emerald-300">
                    ✅ {{ en ? `Correct! That is ${quizTargetName}.` : `সঠিক উত্তর! এটিই ${quizTargetName}।` }}
                </div>
                <div v-else-if="quizStatus === 'wrong'" class="text-sm sm:text-base font-bold text-rose-600 dark:text-rose-400">
                    ❌
                    {{
                        en
                            ? `You clicked ${quizLastClickedName}. The highlighted green area is ${quizTargetName}.`
                            : `আপনি ${quizLastClickedName}-এ ক্লিক করেছেন। সবুজ চিহ্নিত অঞ্চলটিই ${quizTargetName}।`
                    }}
                </div>
                <div v-else-if="quizStatus === 'finished'" class="space-y-1">
                    <p class="text-base font-extrabold text-[var(--text)]">
                        {{
                            en
                                ? `Free 5-Question Quiz Complete! Final Score: ${quizScore}/${FREE_QUIZ_LIMIT}`
                                : `৫ প্রশ্নের ফ্রি কুইজ সম্পন্ন! আপনার স্কোর: ${toBnNumber(quizScore)}/${toBnNumber(FREE_QUIZ_LIMIT)}`
                        }}
                    </p>
                    <p class="text-xs text-[var(--text-muted)]">
                        {{
                            en
                                ? 'Upgrade to Sikhun Pro for unlimited 64-district Map Quiz rounds, full BCS notes, and A4 PDF study sheets.'
                                : '৬৪ জেলার আনলিমিটেড ম্যাপ কুইজ, সম্পূর্ণ বিসিএস নোটস ও A4 PDF স্টাডি শিটের জন্য শিখুন প্রো প্ল্যান আনলক করুন।'
                        }}
                    </p>
                </div>
            </div>

            <div class="flex flex-wrap items-center gap-2 shrink-0">
                <button
                    v-if="quizStatus === 'correct' || quizStatus === 'wrong'"
                    type="button"
                    class="px-4 py-2 rounded-xl bg-[var(--primary)] text-white text-xs sm:text-sm font-bold shadow-sm hover:opacity-95 transition"
                    @click="nextQuizQuestion"
                >
                    {{ en ? 'Next Question →' : 'পরবর্তী প্রশ্ন →' }}
                </button>
                <template v-if="quizStatus === 'finished'">
                    <Link
                        href="/plans"
                        class="px-4 py-2 rounded-xl bg-[var(--primary)] text-white text-xs sm:text-sm font-bold shadow-sm hover:opacity-95 transition"
                    >
                        {{ en ? 'Unlock Unlimited Quiz (Pro)' : 'আনলিমিটেড কুইজ আনলক করুন (প্রো)' }}
                    </Link>
                    <button
                        type="button"
                        class="px-3.5 py-2 rounded-xl bg-[var(--surface)] border border-[var(--border)] text-[var(--text)] text-xs font-bold hover:border-[var(--primary)] transition"
                        @click="startQuiz(quizLayer)"
                    >
                        {{ en ? 'Play 5 Free Again' : 'আবার ৫টি খেলুন' }}
                    </button>
                </template>
            </div>
        </div>

        <!-- MAIN 2-COLUMN WORKSPACE -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-5 items-start">
            <!-- LEFT: INTERACTIVE SVG MAP CANVAS -->
            <div
                class="lg:col-span-7 rounded-3xl border border-[var(--border)] bg-[var(--surface)] p-3 sm:p-4 shadow-sm relative overflow-hidden"
            >
                <!-- Map Controls Overlay -->
                <div class="absolute top-4 right-4 z-20 flex flex-col gap-1.5">
                    <button
                        type="button"
                        class="p-2 rounded-xl bg-[var(--surface)]/95 border border-[var(--border)] text-[var(--text)] shadow-sm hover:border-[var(--primary)] transition"
                        :aria-label="en ? 'Zoom in' : 'জুম ইন'"
                        @click="zoomIn"
                    >
                        <MagnifyingGlassPlusIcon class="w-4 h-4" />
                    </button>
                    <button
                        type="button"
                        class="p-2 rounded-xl bg-[var(--surface)]/95 border border-[var(--border)] text-[var(--text)] shadow-sm hover:border-[var(--primary)] transition"
                        :aria-label="en ? 'Zoom out' : 'জুম আউট'"
                        @click="zoomOut"
                    >
                        <MagnifyingGlassMinusIcon class="w-4 h-4" />
                    </button>
                    <button
                        type="button"
                        class="p-2 rounded-xl bg-[var(--surface)]/95 border border-[var(--border)] text-[var(--text)] shadow-sm hover:border-[var(--primary)] transition"
                        :aria-label="en ? 'Reset map view' : 'রিসেট ভিউ'"
                        @click="resetZoom"
                    >
                        <ArrowPathIcon class="w-4 h-4" />
                    </button>
                </div>

                <!-- Map Loading / Error States -->
                <div v-if="loadingMap" class="h-[520px] sm:h-[620px] flex flex-col items-center justify-center gap-3 text-[var(--text-muted)]">
                    <div class="w-10 h-10 rounded-full border-4 border-[var(--primary)]/20 border-t-[var(--primary)] animate-spin" />
                    <span class="text-sm font-semibold">
                        {{ en ? 'Loading Bangladesh GeoJSON map...' : 'বাংলাদেশের মানচিত্র লোড হচ্ছে...' }}
                    </span>
                </div>

                <div v-else-if="mapError" class="h-[520px] flex flex-col items-center justify-center gap-3 text-center p-6">
                    <p class="text-sm font-semibold text-rose-500">{{ mapError }}</p>
                    <button
                        type="button"
                        class="px-4 py-2 rounded-xl bg-[var(--primary)] text-white text-xs font-bold"
                        @click="loadGeoData"
                    >
                        {{ en ? 'Retry' : 'আবার চেষ্টা করুন' }}
                    </button>
                </div>

                <!-- SVG MAP CONTAINER -->
                <div
                    v-else
                    class="relative rounded-2xl bg-gradient-to-b from-sky-50/70 to-emerald-50/40 dark:from-[#0b1220] dark:to-[#0f172a] border border-[var(--border)] overflow-hidden select-none"
                    :class="zoomLevel > 1 ? 'cursor-grab active:cursor-grabbing' : ''"
                    @pointerdown="onPointerDown"
                    @pointermove="onPointerMove"
                    @pointerup="onPointerUp"
                    @pointerleave="
                        onPointerUp();
                        hoveredInfo = null;
                    "
                >
                    <svg
                        :viewBox="`0 0 ${SVG_WIDTH} ${SVG_HEIGHT}`"
                        class="w-full h-auto max-h-[660px] block"
                        role="img"
                        :aria-label="en ? 'Interactive map of Bangladesh' : 'বাংলাদেশের ইন্টারেক্টিভ মানচিত্র'"
                    >
                        <g
                            :style="{
                                transform: `translate(${panX}px, ${panY}px) scale(${zoomLevel})`,
                                transformOrigin: 'center center',
                                transition: isDragging ? 'none' : 'transform 220ms ease-out',
                            }"
                        >
                            <!-- LAYER 1: DIVISIONS -->
                            <g v-if="activeLayer === 'divisions'">
                                <path
                                    v-for="shape in divisionShapes"
                                    :key="shape.key"
                                    :d="shape.d"
                                    :fill="getDivisionFill(shape)"
                                    :fill-opacity="selectedDivisionKey === shape.key && !quizActive ? 0.95 : 0.72"
                                    :stroke="selectedDivisionKey === shape.key && !quizActive ? '#0f172a' : '#ffffff'"
                                    :stroke-width="selectedDivisionKey === shape.key && !quizActive ? 2.4 : 1.3"
                                    class="bd-region-path cursor-pointer transition-all duration-150 focus:outline-none"
                                    role="button"
                                    tabindex="0"
                                    :aria-label="en ? shape.meta.nameEn : shape.meta.nameBn"
                                    @click="selectDivision(shape.key)"
                                    @keydown.enter.prevent="selectDivision(shape.key)"
                                    @keydown.space.prevent="selectDivision(shape.key)"
                                    @mouseenter="
                                        !quizActive &&
                                            (hoveredInfo = {
                                                title: en ? shape.meta.nameEn : shape.meta.nameBn,
                                                subtitle: en
                                                    ? `${shape.meta.districtCount} Districts • Pop ${formatNumber(shape.meta.population)}`
                                                    : `${toBnNumber(shape.meta.districtCount)} জেলা • জনসংখ্যা ${formatNumber(shape.meta.population)}`,
                                            })
                                    "
                                    @mouseleave="hoveredInfo = null"
                                />
                                <!-- Division Labels (hidden during quiz) -->
                                <g v-if="!quizActive" class="pointer-events-none">
                                    <text
                                        v-for="shape in divisionShapes"
                                        :key="`lbl-${shape.key}`"
                                        :x="shape.cx"
                                        :y="shape.cy"
                                        text-anchor="middle"
                                        class="fill-slate-900 dark:fill-white font-bold text-[12px] drop-shadow-[0_1px_2px_rgba(255,255,255,0.95)] dark:drop-shadow-[0_1px_2px_rgba(0,0,0,0.95)]"
                                    >
                                        {{ en ? shape.meta.hqEn : shape.meta.hqBn }}
                                    </text>
                                </g>
                            </g>

                            <!-- LAYER 2: 64 DISTRICTS -->
                            <g v-else-if="activeLayer === 'districts'">
                                <path
                                    v-for="shape in districtShapes"
                                    :key="shape.key"
                                    :d="shape.d"
                                    :fill="getDistrictFill(shape)"
                                    :fill-opacity="
                                        selectedDistrictKey === shape.key && !quizActive
                                            ? 1
                                            : districtDivisionFilter !== 'all' && shape.meta.divisionKey !== districtDivisionFilter
                                                ? 0.25
                                                : 0.78
                                    "
                                    :stroke="selectedDistrictKey === shape.key && !quizActive ? '#0f172a' : '#ffffff'"
                                    :stroke-width="selectedDistrictKey === shape.key && !quizActive ? 2.1 : 0.85"
                                    class="bd-region-path cursor-pointer transition-all duration-150 focus:outline-none"
                                    role="button"
                                    tabindex="0"
                                    :aria-label="en ? shape.meta.nameEn : shape.meta.nameBn"
                                    @click="selectDistrict(shape.key)"
                                    @keydown.enter.prevent="selectDistrict(shape.key)"
                                    @keydown.space.prevent="selectDistrict(shape.key)"
                                    @mouseenter="
                                        !quizActive &&
                                            (hoveredInfo = {
                                                title: en ? `${shape.meta.nameEn} District` : `${shape.meta.nameBn} জেলা`,
                                                subtitle: en
                                                    ? `Pop ${formatNumber(shape.meta.population)} • ${formatNumber(shape.meta.areaKm2)} km²`
                                                    : `জনসংখ্যা ${formatNumber(shape.meta.population)} • ${formatNumber(shape.meta.areaKm2)} বর্গ কি.মি.`,
                                            })
                                    "
                                    @mouseleave="hoveredInfo = null"
                                />
                            </g>

                            <!-- LAYER 3: MAJOR RIVERS -->
                            <g v-else-if="activeLayer === 'rivers'">
                                <!-- Base outline -->
                                <path
                                    v-for="shape in divisionShapes"
                                    :key="`base-${shape.key}`"
                                    :d="shape.d"
                                    fill="#e2e8f0"
                                    fill-opacity="0.55"
                                    stroke="#94a3b8"
                                    stroke-width="0.9"
                                    class="dark:fill-slate-800 dark:stroke-slate-600"
                                />
                                <!-- River Polylines -->
                                <g v-for="river in riverPaths" :key="river.id">
                                    <!-- Invisible wide hit target -->
                                    <path
                                        :d="river.d"
                                        fill="none"
                                        stroke="transparent"
                                        stroke-width="14"
                                        class="cursor-pointer"
                                        role="button"
                                        tabindex="0"
                                        :aria-label="en ? river.nameEn : river.nameBn"
                                        @click="selectRiver(river.id)"
                                        @keydown.enter.prevent="selectRiver(river.id)"
                                        @keydown.space.prevent="selectRiver(river.id)"
                                        @mouseenter="
                                            hoveredInfo = {
                                                title: en ? river.nameEn : river.nameBn,
                                                subtitle: en ? river.lengthKmEn : river.lengthKm,
                                            }
                                        "
                                        @mouseleave="hoveredInfo = null"
                                    />
                                    <path
                                        :d="river.d"
                                        fill="none"
                                        :stroke="selectedRiverId === river.id ? '#0369a1' : '#0ea5e9'"
                                        :stroke-width="selectedRiverId === river.id ? 5 : 2.6"
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        class="pointer-events-none transition-all duration-150"
                                    />
                                </g>
                                <!-- Selected River Label -->
                                <g v-for="river in riverPaths" :key="`rlbl-${river.id}`" class="pointer-events-none">
                                    <text
                                        v-if="selectedRiverId === river.id"
                                        :x="river.lx"
                                        :y="river.ly - 8"
                                        text-anchor="middle"
                                        class="fill-sky-900 dark:fill-sky-200 font-extrabold text-[12px] drop-shadow-[0_1px_2px_rgba(255,255,255,0.95)] dark:drop-shadow-[0_1px_2px_rgba(0,0,0,0.95)]"
                                    >
                                        {{ en ? river.nameEn : river.nameBn }}
                                    </text>
                                </g>
                            </g>

                            <!-- LAYER 4: LANDMARKS & ORGANIZATIONS -->
                            <g v-else-if="activeLayer === 'landmarks'">
                                <path
                                    v-for="shape in divisionShapes"
                                    :key="`lbase-${shape.key}`"
                                    :d="shape.d"
                                    fill="#e2e8f0"
                                    fill-opacity="0.55"
                                    stroke="#94a3b8"
                                    stroke-width="0.9"
                                    class="dark:fill-slate-800 dark:stroke-slate-600"
                                />
                                <g
                                    v-for="pin in landmarkPins"
                                    :key="pin.id"
                                    :transform="`translate(${pin.x}, ${pin.y})`"
                                    class="cursor-pointer focus:outline-none"
                                    role="button"
                                    tabindex="0"
                                    :aria-label="en ? pin.nameEn : pin.nameBn"
                                    @click="selectLandmark(pin.id)"
                                    @keydown.enter.prevent="selectLandmark(pin.id)"
                                    @keydown.space.prevent="selectLandmark(pin.id)"
                                    @mouseenter="
                                        hoveredInfo = {
                                            title: en ? pin.nameEn : pin.nameBn,
                                            subtitle: getCategoryLabel(pin.category),
                                        }
                                    "
                                    @mouseleave="hoveredInfo = null"
                                >
                                    <circle
                                        v-if="selectedLandmarkId === pin.id"
                                        r="14"
                                        :fill="pin.color"
                                        fill-opacity="0.28"
                                    />
                                    <circle
                                        :r="selectedLandmarkId === pin.id ? 8.5 : 6"
                                        :fill="pin.color"
                                        stroke="#ffffff"
                                        stroke-width="2"
                                    />
                                </g>
                            </g>
                        </g>
                    </svg>

                    <!-- Hover Info Pill -->
                    <div
                        v-if="hoveredInfo"
                        class="pointer-events-none absolute bottom-3 left-3 z-20 px-3.5 py-2 rounded-2xl bg-slate-900/90 text-white shadow-lg backdrop-blur-sm max-w-[85%]"
                    >
                        <div class="text-xs sm:text-sm font-extrabold leading-tight">{{ hoveredInfo.title }}</div>
                        <div class="text-[11px] text-slate-300 mt-0.5">{{ hoveredInfo.subtitle }}</div>
                    </div>

                    <!-- Heatmap Legend -->
                    <div
                        v-if="heatmapMetric !== 'none' && !quizActive && (activeLayer === 'divisions' || activeLayer === 'districts')"
                        class="absolute bottom-3 right-3 z-20 px-3 py-2 rounded-2xl bg-[var(--surface)]/95 border border-[var(--border)] shadow-sm text-[10px] font-bold text-[var(--text)]"
                    >
                        <div class="mb-1 text-[var(--text-muted)]">
                            {{
                                heatmapMetric === 'population'
                                    ? en
                                        ? 'Population (BBS 2022)'
                                        : 'জনসংখ্যা (২০২২)'
                                    : heatmapMetric === 'area'
                                        ? en
                                            ? 'Area (km²)'
                                            : 'আয়তন (বর্গ কি.মি.)'
                                        : en
                                            ? 'Density (/km²)'
                                            : 'ঘনত্ব (প্রতি ব.কি.মি.)'
                            }}
                        </div>
                        <div class="flex items-center gap-1.5">
                            <span>{{ formatNumber(heatmapRange.min) }}</span>
                            <div class="w-20 h-2 rounded-full bg-gradient-to-r from-[#a7f3d0] via-[#34d399] via-[#f59e0b] to-[#e11d48]" />
                            <span>{{ formatNumber(heatmapRange.max) }}</span>
                        </div>
                    </div>
                </div>

                <!-- Quick Horizontal Selector for Rivers / Landmarks -->
                <div v-if="activeLayer === 'rivers'" class="mt-3 flex flex-wrap gap-1.5">
                    <button
                        v-for="r in BD_RIVERS"
                        :key="`chip-${r.id}`"
                        type="button"
                        class="px-2.5 py-1 rounded-lg text-xs font-bold border transition"
                        :class="
                            selectedRiverId === r.id
                                ? 'bg-sky-600 text-white border-sky-600'
                                : 'bg-[var(--surface2)] text-[var(--text)] border-[var(--border)] hover:border-sky-500/50'
                        "
                        @click="selectRiver(r.id)"
                    >
                        {{ en ? r.nameEn.replace(' River', '') : r.nameBn.replace(' নদী', '') }}
                    </button>
                </div>

                <div v-else-if="activeLayer === 'landmarks'" class="mt-3 flex flex-wrap gap-1.5 max-h-28 overflow-y-auto">
                    <button
                        v-for="l in landmarkPins"
                        :key="`lchip-${l.id}`"
                        type="button"
                        class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-bold border transition"
                        :class="
                            selectedLandmarkId === l.id
                                ? 'bg-emerald-600 text-white border-emerald-600'
                                : 'bg-[var(--surface2)] text-[var(--text)] border-[var(--border)] hover:border-emerald-500/50'
                        "
                        @click="selectLandmark(l.id)"
                    >
                        <span
                            class="w-2 h-2 rounded-full"
                            :style="{ backgroundColor: selectedLandmarkId === l.id ? '#fff' : l.color }"
                        />
                        <span>{{ en ? l.nameEn : l.nameBn }}</span>
                    </button>
                </div>
            </div>

            <!-- RIGHT: STICKY DETAIL & EXAM PANEL -->
            <aside class="lg:col-span-5 lg:sticky lg:top-20 space-y-4">
                <div class="rounded-3xl border border-[var(--border)] bg-[var(--surface)] p-5 shadow-sm space-y-4">
                    <!-- Detail Header -->
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <span
                                class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-[var(--primary)]/10 text-[var(--primary)] mb-1.5"
                            >
                                <MapPinIcon class="w-3.5 h-3.5" />
                                <span>{{ activeDetail.badge }}</span>
                            </span>
                            <h3 class="font-heading text-xl font-extrabold text-[var(--text)] leading-snug">
                                {{ activeDetail.title }}
                            </h3>
                            <p class="text-xs font-semibold text-[var(--text-muted)]">{{ activeDetail.altTitle }}</p>
                        </div>

                        <div class="flex items-center gap-1.5 shrink-0">
                            <a
                                v-if="activeDetail.govUrl"
                                :href="activeDetail.govUrl"
                                target="_blank"
                                rel="noopener noreferrer"
                                class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-xl text-[11px] font-bold bg-[var(--surface2)] border border-[var(--border)] text-[var(--text)] hover:border-[var(--primary)] transition"
                                :title="en ? 'Official gov.bd portal' : 'সরকারি ওয়েব পোর্টাল'"
                            >
                                <span>gov.bd</span>
                                <ArrowTopRightOnSquareIcon class="w-3.5 h-3.5" />
                            </a>
                            <button
                                type="button"
                                class="p-2 rounded-xl bg-[var(--surface2)] border border-[var(--border)] text-[var(--text)] hover:border-[var(--primary)] transition"
                                :title="en ? 'Copy share link' : 'শেয়ার লিংক কপি করুন'"
                                @click="copyShareLink"
                            >
                                <CheckIcon v-if="copiedShare" class="w-4 h-4 text-emerald-500" />
                                <ShareIcon v-else class="w-4 h-4" />
                            </button>
                        </div>
                    </div>

                    <!-- Key Stats Grid -->
                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-2.5">
                        <div
                            v-for="st in activeDetail.stats"
                            :key="st.label"
                            class="p-2.5 rounded-2xl bg-[var(--surface2)] border border-[var(--border)]"
                        >
                            <div class="text-[10px] font-bold uppercase tracking-wide text-[var(--text-muted)]">
                                {{ st.label }}
                            </div>
                            <button
                                v-if="st.clickableDistrict"
                                type="button"
                                class="text-xs sm:text-sm font-extrabold text-[var(--primary)] hover:underline mt-0.5 text-left"
                                @click="selectDistrict(st.clickableDistrict, true)"
                            >
                                {{ st.value }} →
                            </button>
                            <div v-else class="text-xs sm:text-sm font-extrabold text-[var(--text)] mt-0.5 leading-snug">
                                {{ st.value }}
                            </div>
                        </div>
                    </div>

                    <!-- Detail Sub-Tabs -->
                    <div class="flex items-center gap-1.5 p-1 rounded-2xl bg-[var(--surface2)] border border-[var(--border)]">
                        <button
                            type="button"
                            class="flex-1 py-2 px-3 rounded-xl text-xs font-extrabold transition"
                            :class="
                                detailSubTab === 'overview'
                                    ? 'bg-[var(--surface)] text-[var(--text)] shadow-sm'
                                    : 'text-[var(--text-muted)] hover:text-[var(--text)]'
                            "
                            @click="detailSubTab = 'overview'"
                        >
                            {{ en ? 'Overview & History' : 'প্রেক্ষাপট ও পরিচিতি' }}
                        </button>
                        <button
                            type="button"
                            class="flex-1 py-2 px-3 rounded-xl text-xs font-extrabold transition inline-flex items-center justify-center gap-1.5"
                            :class="
                                detailSubTab === 'exam'
                                    ? 'bg-[var(--surface)] text-[var(--text)] shadow-sm'
                                    : 'text-[var(--text-muted)] hover:text-[var(--text)]'
                            "
                            @click="detailSubTab = 'exam'"
                        >
                            <AcademicCapIcon class="w-4 h-4 text-amber-500" />
                            <span>{{ en ? 'BCS & Exam Notes' : 'পরীক্ষায় যা আসে' }}</span>
                        </button>
                    </div>

                    <!-- SUB-TAB 1: OVERVIEW -->
                    <div v-if="detailSubTab === 'overview'" class="space-y-3.5 text-sm">
                        <div class="p-3.5 rounded-2xl bg-[var(--surface2)]/70 border border-[var(--border)]">
                            <h4 class="text-xs font-extrabold uppercase tracking-wider text-[var(--primary)] mb-1">
                                {{ en ? 'Background & Significance' : 'ইতিহাস ও প্রেক্ষাপট' }}
                            </h4>
                            <p class="text-xs sm:text-sm text-[var(--text)] leading-relaxed">
                                {{ activeDetail.history }}
                            </p>
                        </div>

                        <div
                            v-if="activeDetail.famous"
                            class="p-3.5 rounded-2xl bg-emerald-500/5 border border-emerald-500/20"
                        >
                            <h4 class="text-xs font-extrabold uppercase tracking-wider text-emerald-700 dark:text-emerald-300 mb-1">
                                {{
                                    activeDetail.type === 'river'
                                        ? en
                                            ? 'Tributaries & Distributaries'
                                            : 'উপনদী ও শাখানদী'
                                        : en
                                            ? 'Famous Specialties & GI Products'
                                            : 'বিখ্যাত পরিচিতি ও GI পণ্য'
                                }}
                            </h4>
                            <p class="text-xs sm:text-sm text-[var(--text)] leading-relaxed">
                                {{ activeDetail.famous }}
                            </p>
                        </div>

                        <!-- Clickable District Chips inside Division view -->
                        <div v-if="activeDetail.districtsList.length" class="space-y-2">
                            <div class="flex items-center justify-between">
                                <h4 class="text-xs font-extrabold text-[var(--text-muted)]">
                                    {{
                                        en
                                            ? `Districts in ${selectedDivision.nameEn} (Click to inspect)`
                                            : `${selectedDivision.nameBn}-এর জেলাসমূহ (ক্লিক করে বিস্তারিত দেখুন)`
                                    }}
                                </h4>
                            </div>
                            <div class="flex flex-wrap gap-1.5">
                                <button
                                    v-for="dist in activeDetail.districtsList"
                                    :key="dist.geoKey"
                                    type="button"
                                    class="px-2.5 py-1 rounded-xl text-xs font-bold bg-[var(--surface2)] border border-[var(--border)] text-[var(--text)] hover:border-[var(--primary)] hover:text-[var(--primary)] transition"
                                    @click="selectDistrict(dist.geoKey, true)"
                                >
                                    {{ en ? dist.nameEn : dist.nameBn }}
                                </button>
                            </div>
                        </div>

                        <!-- Quick peek of top 2 exam points -->
                        <div
                            v-if="freeExamPoints.length"
                            class="p-3.5 rounded-2xl bg-amber-500/5 border border-amber-500/20 space-y-2"
                        >
                            <div class="flex items-center justify-between">
                                <h4 class="text-xs font-extrabold text-amber-700 dark:text-amber-300">
                                    {{ en ? 'Top BCS / Admission Highlights' : 'পরীক্ষার গুরুত্বপূর্ণ পয়েন্ট' }}
                                </h4>
                                <button
                                    type="button"
                                    class="text-[11px] font-bold text-[var(--primary)] hover:underline"
                                    @click="detailSubTab = 'exam'"
                                >
                                    {{ en ? 'View All →' : 'সব দেখুন →' }}
                                </button>
                            </div>
                            <ul class="space-y-1.5 text-xs text-[var(--text)]">
                                <li v-for="(pt, idx) in freeExamPoints" :key="idx" class="flex items-start gap-2 leading-relaxed">
                                    <span class="text-amber-500 font-bold">•</span>
                                    <span>{{ pt }}</span>
                                </li>
                            </ul>
                        </div>
                    </div>

                    <!-- SUB-TAB 2: EXAM NOTES (FREE + INTELLIGENT PRO GATING) -->
                    <div v-else class="space-y-3">
                        <ul class="space-y-2">
                            <li
                                v-for="(pt, idx) in visibleExamPoints"
                                :key="idx"
                                class="p-3 rounded-2xl bg-[var(--surface2)] border border-[var(--border)] text-xs sm:text-sm text-[var(--text)] flex items-start gap-2.5 leading-relaxed"
                            >
                                <span
                                    class="w-5 h-5 rounded-full bg-amber-500/15 text-amber-700 dark:text-amber-300 font-extrabold text-[11px] flex items-center justify-center shrink-0 mt-0.5"
                                >
                                    {{ en ? idx + 1 : toBnNumber(idx + 1) }}
                                </span>
                                <span>{{ pt }}</span>
                            </li>
                        </ul>

                        <!-- Locked Pro Exam Points -->
                        <div
                            v-if="lockedExamPoints.length"
                            class="relative rounded-2xl border border-amber-500/30 bg-amber-500/5 p-4 overflow-hidden"
                        >
                            <ul class="space-y-2 blur-[4px] select-none pointer-events-none opacity-60" aria-hidden="true">
                                <li
                                    v-for="(pt, idx) in lockedExamPoints"
                                    :key="`lock-${idx}`"
                                    class="p-2.5 rounded-xl bg-[var(--surface)] text-xs"
                                >
                                    {{ pt }}
                                </li>
                            </ul>
                            <div
                                class="absolute inset-0 flex flex-col items-center justify-center text-center p-4 bg-[var(--surface)]/70 backdrop-blur-[2px]"
                            >
                                <span
                                    class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-extrabold bg-amber-500 text-white mb-2"
                                >
                                    <LockClosedIcon class="w-3.5 h-3.5" />
                                    <span>{{ en ? 'Sikhun Pro Exam Vault' : 'শিখুন প্রো এক্সাম নোটস' }}</span>
                                </span>
                                <p class="text-xs sm:text-sm font-bold text-[var(--text)] max-w-xs">
                                    {{
                                        en
                                            ? `Unlock ${lockedExamPoints.length} more high-yield BCS & Admission notes + A4 PDF Study Sheets.`
                                            : `আরও ${toBnNumber(lockedExamPoints.length)}টি হাই-ইল্ড বিসিএস ও ভর্তি পয়েন্ট এবং A4 PDF স্টাডি শিট আনলক করুন।`
                                    }}
                                </p>
                                <Link
                                    href="/plans"
                                    class="mt-3 inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-[var(--primary)] text-white text-xs font-extrabold shadow-sm hover:opacity-95 transition"
                                >
                                    <SparklesIcon class="w-4 h-4" />
                                    <span>{{ en ? 'View Pro Plans' : 'প্রো প্ল্যান দেখুন' }}</span>
                                </Link>
                            </div>
                        </div>
                    </div>

                    <!-- PRO PDF NOTICE (IF FREE USER CLICKS PDF) -->
                    <div
                        v-if="showPdfProNotice && !isPaid"
                        class="p-3.5 rounded-2xl border border-amber-500/30 bg-amber-500/10 flex flex-col sm:flex-row sm:items-center justify-between gap-3"
                    >
                        <div class="text-xs text-[var(--text)]">
                            <strong class="block font-extrabold text-amber-800 dark:text-amber-300">
                                {{ en ? 'A4 Branded Map Study Sheet PDF is a Pro Feature' : 'A4 ম্যাপ স্টাডি শিট PDF প্রো ফিচার' }}
                            </strong>
                            <span>
                                {{
                                    en
                                        ? 'Upgrade once to download unlimited division, district & river PDFs + unlimited Map Quiz.'
                                        : 'প্রো প্ল্যানে আনলিমিটেড বিভাগ, জেলা ও নদীর PDF ডাউনলোড এবং আনলিমিটেড ম্যাপ কুইজ আনলক করুন।'
                                }}
                            </span>
                        </div>
                        <Link
                            href="/plans"
                            class="px-3.5 py-2 rounded-xl bg-[var(--primary)] text-white text-xs font-extrabold text-center shrink-0"
                        >
                            {{ en ? 'Upgrade Now' : 'আপগ্রেড করুন' }}
                        </Link>
                    </div>

                    <!-- SMART STUDY ACTIONS FOOTER -->
                    <div class="pt-3 border-t border-[var(--border)] grid grid-cols-1 sm:grid-cols-3 gap-2">
                        <Link
                            :href="`/question-bank?search=${encodeURIComponent(activeDetail.searchKeyword)}`"
                            class="inline-flex items-center justify-center gap-1.5 px-3 py-2.5 rounded-xl bg-[var(--surface2)] border border-[var(--border)] text-xs font-bold text-[var(--text)] hover:border-[var(--primary)] hover:text-[var(--primary)] transition"
                        >
                            <BookOpenIcon class="w-4 h-4" />
                            <span>{{ en ? 'Practice MCQs' : 'প্রশ্নব্যাংক' }}</span>
                        </Link>

                        <Link
                            :href="`/flashcards/create?source_type=topic&title=${encodeURIComponent(activeDetail.title)}`"
                            class="inline-flex items-center justify-center gap-1.5 px-3 py-2.5 rounded-xl bg-[var(--surface2)] border border-[var(--border)] text-xs font-bold text-[var(--text)] hover:border-[var(--primary)] hover:text-[var(--primary)] transition"
                        >
                            <SparklesIcon class="w-4 h-4 text-amber-500" />
                            <span>{{ en ? 'AI Flashcards' : 'AI ফ্ল্যাশকার্ড' }}</span>
                        </Link>

                        <button
                            type="button"
                            :disabled="exportingPdf"
                            class="inline-flex items-center justify-center gap-1.5 px-3 py-2.5 rounded-xl text-xs font-extrabold transition"
                            :class="
                                isPaid
                                    ? 'bg-emerald-600 text-white hover:bg-emerald-700 shadow-sm'
                                    : 'bg-amber-500/15 text-amber-800 dark:text-amber-300 border border-amber-500/30 hover:bg-amber-500/25'
                            "
                            @click="exportStudySheetPdf"
                        >
                            <ArrowDownTrayIcon v-if="isPaid" class="w-4 h-4" />
                            <LockClosedIcon v-else class="w-3.5 h-3.5" />
                            <span>
                                {{
                                    exportingPdf
                                        ? en
                                            ? 'Generating...'
                                            : 'তৈরি হচ্ছে...'
                                        : isPaid
                                            ? en
                                                ? 'Study PDF'
                                                : 'স্টাডি PDF'
                                            : en
                                                ? 'PDF (Pro)'
                                                : 'PDF (প্রো)'
                                }}
                            </span>
                        </button>
                    </div>
                </div>

                <!-- OFFICIAL ATTRIBUTION FOOTER -->
                <p class="text-[11px] text-[var(--text-muted)] px-2 leading-relaxed">
                    {{ en ? BD_MAP_SOURCES.en : BD_MAP_SOURCES.bn }}
                </p>
            </aside>
        </div>
    </div>
</template>

<style scoped>
.bd-region-path:hover {
    filter: brightness(1.06);
}
.bd-region-path:focus-visible {
    stroke: #f59e0b;
    stroke-width: 2.6;
}
@media (prefers-reduced-motion: reduce) {
    .bd-region-path,
    svg g {
        transition: none !important;
    }
}
</style>
