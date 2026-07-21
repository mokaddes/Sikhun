<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: sans-serif; font-size: 12px; color: #1a1a2e; }
        h1 { font-size: 18px; margin-bottom: 4px; }
        .meta { color: #6b6b8a; margin-bottom: 20px; }
        .question { margin-bottom: 16px; padding-bottom: 12px; border-bottom: 1px solid #e2e2ee; }
        .q-title { font-weight: bold; margin-bottom: 6px; }
        .answer { color: #00806a; }
        .wrong { color: #cc3333; }
        .score-box { background: #f0f0fa; padding: 12px 16px; border-radius: 8px; margin-bottom: 20px; }
    </style>
</head>
<body>
    <h1>Sikhun.com — Exam Answer Sheet</h1>
    <div class="meta">
        Student: {{ $student->name }} &nbsp;|&nbsp;
        Date: {{ $exam->completed_at->format('d M Y, h:i A') }}
    </div>

    <div class="score-box">
        <strong>Score: {{ $exam->score }} / {{ $exam->total }} ({{ $exam->percentage }}%)</strong>
    </div>

    @foreach ($exam->questions as $q)
        @php $given = $exam->answers[$q['id']] ?? '—'; @endphp
        <div class="question">
            <div class="q-title">{{ $loop->iteration }}. {{ $q['question'] }}</div>
            @if (!empty($q['options']))
                <div>Options: {{ implode(' / ', $q['options']) }}</div>
            @endif
            <div>Your answer: <span class="{{ mb_strtolower(trim($given)) === mb_strtolower(trim($q['correct_answer'] ?? '')) ? 'answer' : 'wrong' }}">{{ $given }}</span></div>
            <div>Correct answer: <span class="answer">{{ $q['correct_answer'] ?? '—' }}</span></div>
            @if (!empty($q['explanation']))
                <div style="color:#6b6b8a; margin-top: 4px;">{{ $q['explanation'] }}</div>
            @endif
        </div>
    @endforeach
</body>
</html>
