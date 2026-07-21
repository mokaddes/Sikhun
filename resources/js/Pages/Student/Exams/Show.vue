<script setup>
import { ref, onMounted, onBeforeUnmount, computed } from 'vue';
import { Head, router } from '@inertiajs/vue3';
import axios from 'axios';
import StudentLayout from '@/Components/Layout/StudentLayout.vue';
import ExamTimer from '@/Components/Exam/ExamTimer.vue';
import { useI18n } from '@/i18n';

const props = defineProps({ exam: Object });
const { t } = useI18n();

const exam = ref(props.exam);
const answers = ref({});
const currentIndex = ref(0);
const revealed = ref(false);
let pollInterval;

function poll() {
    pollInterval = setInterval(async () => {
        const { data } = await axios.get(`/exams/${exam.value.id}/status`);
        if (data.status !== 'generating') {
            exam.value = data.exam;
            clearInterval(pollInterval);
        }
    }, 2500);
}

onMounted(() => {
    if (exam.value.status === 'generating') poll();
});
onBeforeUnmount(() => clearInterval(pollInterval));

const isPractice = computed(() => exam.value.mode === 'practice');
const currentQuestion = computed(() => exam.value.questions?.[currentIndex.value]);

function selectAnswer(questionId, value) {
    answers.value[questionId] = value;
}

function nextQuestion() {
    revealed.value = false;
    if (currentIndex.value < exam.value.questions.length - 1) currentIndex.value++;
}

function checkAnswer() {
    revealed.value = true;
}

function submitExam() {
    router.post(`/exams/${exam.value.id}/complete`, { answers: answers.value });
}
</script>

<template>
    <Head :title="t('exams.title')" />
    <StudentLayout>
        <div v-if="exam.status === 'generating'" class="rounded-xl border border-[var(--border)] bg-[var(--surface)] p-12 text-center text-[var(--text-muted)]">
            {{ t('exams.generating') }}
        </div>

        <div v-else>
            <div class="flex items-center justify-between mb-6">
                <h1 class="font-heading text-xl font-bold">{{ exam.config?.type?.toUpperCase() }} · {{ exam.total }} {{ t('exams.count') }}</h1>
                <ExamTimer v-if="!isPractice && exam.config?.duration > 0" :duration-minutes="exam.config.duration" @expired="submitExam" />
            </div>

            <!-- Practice mode: one question at a time with reveal -->
            <div v-if="isPractice && currentQuestion" class="rounded-xl border border-[var(--border)] bg-[var(--surface)] p-6 max-w-2xl">
                <div class="text-xs text-[var(--text-muted)] mb-2">{{ currentIndex + 1 }} / {{ exam.questions.length }}</div>
                <div class="font-medium mb-4">{{ currentQuestion.question }}</div>

                <div v-if="currentQuestion.options" class="space-y-2 mb-5">
                    <button v-for="opt in currentQuestion.options" :key="opt" @click="selectAnswer(currentQuestion.id, opt)"
                        class="w-full text-left px-4 py-2.5 rounded-lg border text-sm"
                        :class="answers[currentQuestion.id] === opt ? 'border-[var(--primary)] bg-[var(--primary)]/10' : 'border-[var(--border)]'">
                        {{ opt }}
                    </button>
                </div>
                <input v-else v-model="answers[currentQuestion.id]" type="text" class="w-full px-4 py-2.5 rounded-lg bg-[var(--surface2)] border border-[var(--border)] mb-5" />

                <div v-if="revealed" class="mb-5 p-4 rounded-lg bg-[var(--secondary)]/10 text-sm">
                    <div class="font-semibold text-[var(--secondary)]">{{ t('exams.correct') }}: {{ currentQuestion.correct_answer }}</div>
                    <div v-if="currentQuestion.explanation" class="text-[var(--text-muted)] mt-1">{{ currentQuestion.explanation }}</div>
                </div>

                <div class="flex gap-3">
                    <button v-if="!revealed" @click="checkAnswer" class="px-5 py-2.5 rounded-lg bg-[var(--primary)] text-white text-sm font-semibold">Check</button>
                    <button v-else-if="currentIndex < exam.questions.length - 1" @click="nextQuestion" class="px-5 py-2.5 rounded-lg bg-[var(--primary)] text-white text-sm font-semibold">Next →</button>
                    <button v-else @click="submitExam" class="px-5 py-2.5 rounded-lg bg-[var(--secondary)] text-white text-sm font-semibold">{{ t('exams.submit_exam') }}</button>
                </div>
            </div>

            <!-- Exam mode: all questions, submit at the end -->
            <div v-else class="space-y-5 max-w-2xl">
                <div v-for="(q, i) in exam.questions" :key="q.id" class="rounded-xl border border-[var(--border)] bg-[var(--surface)] p-5">
                    <div class="text-xs text-[var(--text-muted)] mb-2">Q{{ i + 1 }}</div>
                    <div class="font-medium mb-3">{{ q.question }}</div>
                    <div v-if="q.options" class="space-y-2">
                        <label v-for="opt in q.options" :key="opt" class="flex items-center gap-2 px-3 py-2 rounded-lg border border-[var(--border)] text-sm cursor-pointer">
                            <input type="radio" :name="`q-${q.id}`" :value="opt" v-model="answers[q.id]" /> {{ opt }}
                        </label>
                    </div>
                    <input v-else v-model="answers[q.id]" type="text" class="w-full px-4 py-2.5 rounded-lg bg-[var(--surface2)] border border-[var(--border)]" />
                </div>
                <button @click="submitExam" class="w-full py-3 rounded-lg bg-[var(--primary)] hover:bg-[var(--primary-hover)] text-white font-semibold">
                    {{ t('exams.submit_exam') }}
                </button>
            </div>
        </div>
    </StudentLayout>
</template>
