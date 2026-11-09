<script setup>
import { ref, computed, watch, onMounted, onUnmounted } from 'vue';
import {
    PlayIcon,
    PauseIcon,
    ArrowPathIcon,
    SpeakerWaveIcon,
    SpeakerXMarkIcon,
    CheckCircleIcon,
    SparklesIcon,
    ClockIcon,
} from '@heroicons/vue/24/outline';

const props = defineProps({
    en: { type: Boolean, default: false },
});

const mode = ref('focus'); // 'focus' | 'short' | 'long'
const customMinutes = ref(25);
const timeLeft = ref(25 * 60);
const isRunning = ref(false);
const taskName = ref('');
const completedSessions = ref(0);
const soundEnabled = ref(true);
const ambientSound = ref('none'); // 'none' | 'rain' | 'whitenoise'

let timerInterval = null;
let audioCtx = null;
let ambientSource = null;
let ambientGain = null;

const totalDuration = computed(() => {
    if (mode.value === 'focus') return customMinutes.value * 60;
    if (mode.value === 'short') return 5 * 60;
    if (mode.value === 'long') return 15 * 60;
    return 25 * 60;
});

const formattedTime = computed(() => {
    const mins = Math.floor(timeLeft.value / 60);
    const secs = timeLeft.value % 60;
    return `${String(mins).padStart(2, '0')}:${String(secs).padStart(2, '0')}`;
});

const progressPercent = computed(() => {
    if (totalDuration.value === 0) return 0;
    return ((totalDuration.value - timeLeft.value) / totalDuration.value) * 100;
});

onMounted(() => {
    const saved = localStorage.getItem('sikhun_pomodoro_sessions');
    if (saved) completedSessions.value = parseInt(saved, 10) || 0;
});

onUnmounted(() => {
    clearInterval(timerInterval);
    stopAmbientSound();
    if (audioCtx) {
        audioCtx.close().catch(() => {});
    }
});

function setMode(newMode) {
    pauseTimer();
    mode.value = newMode;
    if (newMode === 'focus') timeLeft.value = customMinutes.value * 60;
    else if (newMode === 'short') timeLeft.value = 5 * 60;
    else if (newMode === 'long') timeLeft.value = 15 * 60;
}

function updateCustomMinutes() {
    if (customMinutes.value < 1) customMinutes.value = 1;
    if (customMinutes.value > 120) customMinutes.value = 120;
    if (mode.value === 'focus') {
        pauseTimer();
        timeLeft.value = customMinutes.value * 60;
    }
}

function toggleTimer() {
    if (isRunning.value) pauseTimer();
    else startTimer();
}

function startTimer() {
    if (isRunning.value) return;
    isRunning.value = true;
    startAmbientSound();

    timerInterval = setInterval(() => {
        if (timeLeft.value > 0) {
            timeLeft.value -= 1;
        } else {
            handleComplete();
        }
    }, 1000);
}

function pauseTimer() {
    isRunning.value = false;
    clearInterval(timerInterval);
    stopAmbientSound();
}

function resetTimer() {
    pauseTimer();
    setMode(mode.value);
}

function handleComplete() {
    pauseTimer();
    playBeepSound();

    if (mode.value === 'focus') {
        completedSessions.value += 1;
        localStorage.setItem('sikhun_pomodoro_sessions', String(completedSessions.value));
        // Auto suggest short break
        setMode('short');
    } else {
        setMode('focus');
    }
}

// ==========================================
// PURE CLIENT-SIDE SYNTHESIZER (0 Bandwidth Cost)
// ==========================================
function getAudioContext() {
    if (!audioCtx) {
        const AudioContextClass = window.AudioContext || window.webkitAudioContext;
        if (AudioContextClass) audioCtx = new AudioContextClass();
    }
    if (audioCtx && audioCtx.state === 'suspended') {
        audioCtx.resume();
    }
    return audioCtx;
}

function playBeepSound() {
    if (!soundEnabled.value) return;
    try {
        const ctx = getAudioContext();
        if (!ctx) return;
        const now = ctx.currentTime;
        
        // Gentle bell chord
        [523.25, 659.25, 783.99].forEach((freq, i) => {
            const osc = ctx.createOscillator();
            const gain = ctx.createGain();
            osc.type = 'sine';
            osc.frequency.setValueAtTime(freq, now + i * 0.08);
            gain.gain.setValueAtTime(0.2, now + i * 0.08);
            gain.gain.exponentialRampToValueAtTime(0.001, now + i * 0.08 + 1.2);
            osc.connect(gain);
            gain.connect(ctx.destination);
            osc.start(now + i * 0.08);
            osc.stop(now + i * 0.08 + 1.2);
        });
    } catch (e) {
        // Audio error silently handled
    }
}

function startAmbientSound() {
    if (!soundEnabled.value || ambientSound.value === 'none') {
        stopAmbientSound();
        return;
    }
    try {
        const ctx = getAudioContext();
        if (!ctx) return;
        stopAmbientSound();

        // Synthesize 5 seconds of pink/white noise buffer for rain
        const bufferSize = ctx.sampleRate * 4;
        const buffer = ctx.createBuffer(1, bufferSize, ctx.sampleRate);
        const data = buffer.getChannelData(0);
        let b0 = 0, b1 = 0, b2 = 0, b3 = 0, b4 = 0, b5 = 0, b6 = 0;

        for (let i = 0; i < bufferSize; i++) {
            const white = Math.random() * 2 - 1;
            if (ambientSound.value === 'rain') {
                // Pink noise filter algorithm
                b0 = 0.99886 * b0 + white * 0.0555179;
                b1 = 0.99332 * b1 + white * 0.0750759;
                b2 = 0.96900 * b2 + white * 0.1538520;
                b3 = 0.86650 * b3 + white * 0.3104856;
                b4 = 0.55000 * b4 + white * 0.5329522;
                b5 = -0.7616 * b5 - white * 0.0168980;
                data[i] = (b0 + b1 + b2 + b3 + b4 + b5 + b6 + white * 0.5362) * 0.04;
                b6 = white * 0.115926;
            } else {
                // Gentle low-pass white noise
                data[i] = white * 0.03;
            }
        }

        ambientSource = ctx.createBufferSource();
        ambientSource.buffer = buffer;
        ambientSource.loop = true;

        const filter = ctx.createBiquadFilter();
        filter.type = 'lowpass';
        filter.frequency.setValueAtTime(ambientSound.value === 'rain' ? 800 : 1200, ctx.currentTime);

        ambientGain = ctx.createGain();
        ambientGain.gain.setValueAtTime(0.15, ctx.currentTime);

        ambientSource.connect(filter);
        filter.connect(ambientGain);
        ambientGain.connect(ctx.destination);
        ambientSource.start();
    } catch (e) {
        // Audio error silently handled
    }
}

function stopAmbientSound() {
    if (ambientSource) {
        try { ambientSource.stop(); } catch (e) {}
        ambientSource = null;
    }
}

watch(ambientSound, () => {
    if (isRunning.value) startAmbientSound();
});
</script>

<template>
    <div class="card p-6 md:p-8 rounded-3xl border border-gray-200 dark:border-white/10 bg-white dark:bg-[#161a26] text-gray-900 dark:text-white shadow-xl">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-gray-200 dark:border-white/10 pb-5">
            <div>
                <h2 class="font-heading text-xl md:text-2xl font-bold flex items-center gap-2.5">
                    <ClockIcon class="w-6 h-6 text-rose-500" />
                    {{ en ? 'Pomodoro Study Timer & Focus Hub' : 'পোমোডোরো স্টাডি টাইমার ও ফোকাস হাব' }}
                </h2>
                <p class="text-xs text-gray-600 dark:text-gray-300 mt-1">
                    {{ en ? 'Scientifically proven 25-minute focus intervals with ambient study sounds to boost retention.' : 'বিজ্ঞানসম্মত ২৫ মিনিট গভীর পড়াশোনা ও ৫ মিনিট বিশ্রামের সাইকেল—মনোযোগ ও পড়া মনে রাখার ক্ষমতা বাড়াতে।' }}
                </p>
            </div>

            <!-- Total Completed Counter Badge -->
            <div class="flex items-center gap-3">
                <div class="px-3.5 py-1.5 rounded-2xl bg-rose-500/10 border border-rose-500/20 text-rose-600 dark:text-rose-400 text-xs font-bold flex items-center gap-2">
                    <CheckCircleIcon class="w-4 h-4" />
                    <span>{{ en ? 'Completed Today:' : 'আজকের সেশন:' }} <strong class="font-mono text-sm">{{ completedSessions }}</strong></span>
                </div>
            </div>
        </div>

        <!-- Task Input -->
        <div class="mt-6 max-w-md mx-auto">
            <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1.5 text-center">
                {{ en ? 'What are you studying right now?' : 'বর্তমানে আপনি কী পড়ছেন? (বিষয় বা অধ্যায়)' }}
            </label>
            <input
                v-model="taskName"
                type="text"
                :placeholder="en ? 'e.g. Physics Chapter 3: Dynamics...' : 'যেমন: পদার্থবিজ্ঞান ৩য় অধ্যায়: বল...'"
                class="w-full text-center px-4 py-2.5 rounded-2xl bg-gray-50 dark:bg-[#0e121d] border border-gray-300 dark:border-white/10 text-gray-900 dark:text-white text-xs sm:text-sm focus:ring-2 focus:ring-rose-500 focus:outline-none"
            />
        </div>

        <!-- Mode Buttons -->
        <div class="mt-6 flex items-center justify-center gap-2 flex-wrap">
            <button
                type="button"
                @click="setMode('focus')"
                class="px-4 py-2 rounded-xl text-xs font-bold transition border"
                :class="mode === 'focus' ? 'bg-rose-500 text-white border-rose-500 shadow-md shadow-rose-500/25' : 'bg-gray-100 dark:bg-white/5 text-gray-700 dark:text-gray-300 border-gray-200 dark:border-white/10 hover:bg-gray-200'"
            >
                🔥 {{ en ? 'Focus Time' : 'ফোকাস স্টাডি' }} ({{ customMinutes }}m)
            </button>
            <button
                type="button"
                @click="setMode('short')"
                class="px-4 py-2 rounded-xl text-xs font-bold transition border"
                :class="mode === 'short' ? 'bg-emerald-500 text-white border-emerald-500 shadow-md shadow-emerald-500/25' : 'bg-gray-100 dark:bg-white/5 text-gray-700 dark:text-gray-300 border-gray-200 dark:border-white/10 hover:bg-gray-200'"
            >
                ☕ {{ en ? 'Short Break' : 'ছোট বিরতি' }} (5m)
            </button>
            <button
                type="button"
                @click="setMode('long')"
                class="px-4 py-2 rounded-xl text-xs font-bold transition border"
                :class="mode === 'long' ? 'bg-blue-500 text-white border-blue-500 shadow-md shadow-blue-500/25' : 'bg-gray-100 dark:bg-white/5 text-gray-700 dark:text-gray-300 border-gray-200 dark:border-white/10 hover:bg-gray-200'"
            >
                🧘 {{ en ? 'Long Break' : 'বড় বিরতি' }} (15m)
            </button>
        </div>

        <!-- Timer Circle & Display -->
        <div class="my-8 flex flex-col items-center justify-center">
            <div class="relative w-56 h-56 sm:w-64 sm:h-64 flex items-center justify-center">
                <!-- SVG Progress Ring -->
                <svg class="w-full h-full transform -rotate-90" viewBox="0 0 100 100">
                    <circle
                        cx="50"
                        cy="50"
                        r="42"
                        stroke="currentColor"
                        stroke-width="5"
                        fill="transparent"
                        class="text-gray-200 dark:text-white/5"
                    />
                    <circle
                        cx="50"
                        cy="50"
                        r="42"
                        stroke="currentColor"
                        stroke-width="5"
                        fill="transparent"
                        stroke-linecap="round"
                        :stroke-dasharray="263.89"
                        :stroke-dashoffset="263.89 - (progressPercent / 100) * 263.89"
                        :class="mode === 'focus' ? 'text-rose-500' : (mode === 'short' ? 'text-emerald-500' : 'text-blue-500')"
                        class="transition-all duration-500"
                    />
                </svg>

                <!-- Timer Text -->
                <div class="absolute inset-0 flex flex-col items-center justify-center text-center px-4">
                    <span class="text-xs uppercase tracking-widest font-bold text-gray-500 dark:text-gray-400 mb-1">
                        {{ mode === 'focus' ? (en ? 'Focusing' : 'পড়ার সময়') : (en ? 'Break' : 'বিরতি') }}
                    </span>
                    <span class="font-mono text-4xl sm:text-5xl font-extrabold text-gray-900 dark:text-white tracking-tight">
                        {{ formattedTime }}
                    </span>
                    <span v-if="taskName" class="text-xs text-[var(--primary)] font-semibold truncate max-w-[180px] mt-2">
                        {{ taskName }}
                    </span>
                </div>
            </div>

            <!-- Controls -->
            <div class="mt-6 flex items-center gap-3">
                <button
                    type="button"
                    @click="toggleTimer"
                    class="px-6 py-3 rounded-2xl font-bold text-sm text-white transition flex items-center gap-2 shadow-lg"
                    :class="isRunning ? 'bg-amber-600 hover:bg-amber-700 shadow-amber-600/30' : (mode === 'focus' ? 'bg-rose-600 hover:bg-rose-700 shadow-rose-600/30' : 'bg-emerald-600 hover:bg-emerald-700 shadow-emerald-600/30')"
                >
                    <PauseIcon v-if="isRunning" class="w-5 h-5" />
                    <PlayIcon v-else class="w-5 h-5" />
                    <span>{{ isRunning ? (en ? 'Pause' : 'থামান') : (en ? 'Start Focus' : 'শুরু করুন') }}</span>
                </button>

                <button
                    type="button"
                    @click="resetTimer"
                    title="Reset"
                    class="p-3 rounded-2xl border border-gray-300 dark:border-white/10 bg-gray-100 dark:bg-white/5 hover:bg-gray-200 dark:hover:bg-white/10 text-gray-700 dark:text-gray-300 transition"
                >
                    <ArrowPathIcon class="w-5 h-5" />
                </button>
            </div>
        </div>

        <!-- Settings Bar (Ambient Sound & Duration) -->
        <div class="pt-6 border-t border-gray-200 dark:border-white/10 grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
            <!-- Ambient Sound -->
            <div class="flex items-center justify-between sm:justify-start gap-3 p-3 rounded-2xl bg-gray-50 dark:bg-[#0e121d] border border-gray-200 dark:border-white/10">
                <span class="font-semibold text-gray-700 dark:text-gray-300 flex items-center gap-1.5">
                    <SpeakerWaveIcon class="w-4 h-4 text-rose-500" />
                    {{ en ? 'Background Ambience:' : 'স্টাডি ব্যাকগ্রাউন্ড সাউন্ড:' }}
                </span>
                <select
                    v-model="ambientSound"
                    class="bg-white dark:bg-[#161a26] border border-gray-300 dark:border-white/10 rounded-xl px-2.5 py-1 text-xs text-gray-900 dark:text-white focus:outline-none focus:ring-1 focus:ring-rose-500"
                >
                    <option value="none">{{ en ? 'Off (Silent)' : 'বন্ধ (নীরব)' }}</option>
                    <option value="rain">{{ en ? 'Gentle Rain (Synth)' : 'হালকা বৃষ্টি (Rain)' }}</option>
                    <option value="whitenoise">{{ en ? 'White Noise (Focus)' : 'হোয়াইট নয়েজ' }}</option>
                </select>
            </div>

            <!-- Custom Focus Duration -->
            <div class="flex items-center justify-between sm:justify-end gap-3 p-3 rounded-2xl bg-gray-50 dark:bg-[#0e121d] border border-gray-200 dark:border-white/10">
                <span class="font-semibold text-gray-700 dark:text-gray-300">
                    {{ en ? 'Focus Minutes:' : 'পড়ার সময় (মিনিট):' }}
                </span>
                <div class="flex items-center gap-2">
                    <input
                        v-model.number="customMinutes"
                        type="number"
                        min="5"
                        max="90"
                        @change="updateCustomMinutes"
                        class="w-16 px-2.5 py-1 rounded-xl bg-white dark:bg-[#161a26] border border-gray-300 dark:border-white/10 text-center font-mono font-bold text-gray-900 dark:text-white text-xs focus:outline-none focus:ring-1 focus:ring-rose-500"
                    />
                    <span class="text-gray-500">{{ en ? 'mins' : 'মিনিট' }}</span>
                </div>
            </div>
        </div>

        <!-- Tips Banner -->
        <div class="mt-6 p-4 rounded-2xl bg-rose-500/5 border border-rose-500/15 text-xs text-gray-700 dark:text-gray-300 leading-relaxed">
            <strong class="text-rose-600 dark:text-rose-400 font-bold block mb-1">
                💡 {{ en ? 'How the Pomodoro Method Works:' : 'পোমোডোরো টেকনিক যেভাবে কাজে লাগাবেন:' }}
            </strong>
            <p>
                {{ en ? '1. Pick a single task. 2. Work without any social media/notifications for 25 minutes. 3. When the chime sounds, take a strictly 5-minute break. 4. After 4 completed sessions, take a longer 15-minute refreshing break!' : '১. পড়ার জন্য নির্দিষ্ট একটি বিষয় বা অধ্যায় নির্ধারণ করুন। ২. কোনো ফেসবুক/সোশ্যাল মিডিয়া নোটিফিকেশন ছাড়া একটানা ২৫ মিনিট পড়ুন। ৩. বেল বাজলে ঠিক ৫ মিনিটের বিরতি নিন। ৪. এভাবে টানা ৪টি সেশন শেষ হলে ১৫ মিনিটের বড় বিরতি নিন!' }}
            </p>
        </div>
    </div>
</template>
