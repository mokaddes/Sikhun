<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $exam->title ?: 'Exam Result' }} — Sikhun.com</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Anek+Bangla:wght@400;500;600;700&family=DM+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root { --ink: #16202c; --muted: #6b7280; --line: #e5e7eb; --brand: #f97316; --success: #16a34a; --danger: #dc2626; }
        * { box-sizing: border-box; }
        body {
            margin: 0; padding: 32px 20px;
            font-family: 'DM Sans', 'Anek Bangla', 'Noto Sans Bengali', sans-serif;
            color: var(--ink); background: #f3f4f6;
        }
        .sheet {
            max-width: 820px; margin: 0 auto; background: #fff; border-radius: 16px;
            padding: 40px; box-shadow: 0 10px 40px rgba(15, 23, 42, .08);
        }
        .top { display: flex; justify-content: space-between; align-items: flex-start; gap: 20px; border-bottom: 2px solid var(--line); padding-bottom: 20px; margin-bottom: 24px; }
        .brand { font-size: 22px; font-weight: 700; }
        .brand span { color: var(--brand); }
        .muted { color: var(--muted); font-size: 13px; line-height: 1.6; }
        h1 { font-size: 24px; margin: 0 0 8px; }
        .score-card {
            background: #f8fafc; border: 1px solid var(--line); border-radius: 12px;
            padding: 16px 20px; margin-bottom: 28px; display: flex; justify-content: space-between; align-items: center;
        }
        .score-val { font-size: 24px; font-weight: 800; color: var(--brand); }
        .question { padding: 18px 0; border-bottom: 1px solid var(--line); }
        .q-header { display: flex; gap: 8px; font-weight: 600; font-size: 15px; margin-bottom: 10px; line-height: 1.5; }
        .options-list { margin: 8px 0 12px 24px; font-size: 14px; color: var(--muted); }
        .ans-row { display: flex; flex-wrap: wrap; gap: 16px; font-size: 14px; margin-top: 6px; }
        .correct-ans { color: var(--success); font-weight: 600; }
        .given-ans { font-weight: 600; }
        .given-ans.is-correct { color: var(--success); }
        .given-ans.is-wrong { color: var(--danger); }
        .exp-box { background: #f9fafb; border-radius: 8px; padding: 10px 14px; margin-top: 10px; font-size: 13px; color: var(--muted); }
        .actions { max-width: 820px; margin: 0 auto 18px; display: flex; gap: 10px; justify-content: flex-end; }
        .btn { border: 0; border-radius: 10px; padding: 10px 18px; font: inherit; font-weight: 600; cursor: pointer; text-decoration: none; }
        .btn-primary { background: var(--brand); color: #fff; }
        @media print {
            body { background: #fff; padding: 0; }
            .sheet { box-shadow: none; border-radius: 0; max-width: none; padding: 0; }
            .actions { display: none; }
            @page { margin: 15mm; }
        }
    </style>
</head>
<body>
    <div class="actions">
        <button onclick="window.print()" class="btn btn-primary">🖨️ প্রিন্ট / PDF সংরক্ষণ করুন</button>
    </div>

    <div class="sheet">
        <div class="top">
            <div>
                <div class="brand">Sikhun<span>.com</span></div>
                <div class="muted">অনলাইন মূল্যায়ন ও সমাধান পত্র</div>
            </div>
            <div style="text-align: right;">
                <h1>{{ $exam->title ?: 'মডেল টেস্ট উত্তরপত্র' }}</h1>
                <div class="muted">
                    শিক্ষার্থী: {{ $student->name }}<br>
                    তারিখ: {{ $exam->completed_at ? $exam->completed_at->format('d M Y, h:i A') : now()->format('d M Y') }}
                </div>
            </div>
        </div>

        <div class="score-card">
            <div>
                <div style="font-size: 13px; color: var(--muted); text-transform: uppercase; font-weight: 700;">মোট ফলাফল (Total Score)</div>
                <div style="font-size: 14px; margin-top: 4px;">{{ $exam->config['type'] ?? 'MCQ' }} · মোট প্রশ্ন: {{ $exam->total }}</div>
            </div>
            <div style="text-align: right;">
                <div class="score-val">{{ $exam->score }} / {{ $exam->total }}</div>
                <div style="font-size: 13px; font-weight: 600; color: var(--muted);">প্রাপ্ত নম্বর: {{ $exam->percentage }}%</div>
            </div>
        </div>

        @foreach ($exam->questions as $q)
            @php
                $qId = $q['id'] ?? null; $given = ($qId !== null && isset($exam->answers[$qId])) ? $exam->answers[$qId] : ($q['student_answer'] ?? null);
                $correct = $q['correct_answer'] ?? '';
                $isCorrect = $given !== null && mb_strtolower(trim($given)) === mb_strtolower(trim($correct));
            @endphp
            <div class="question">
                <div class="q-header">
                    <span>{{ $loop->iteration }}.</span>
                    <span>{{ $q['question'] }}</span>
                </div>
                @if (!empty($q['options']))
                    <div class="options-list">
                        @foreach ($q['options'] as $idx => $opt)
                            <div>{{ chr(65 + $idx) }}. {{ $opt }}</div>
                        @endforeach
                    </div>
                @endif
                <div class="ans-row">
                    <div>আপনার উত্তর:
                        <span class="given-ans {{ $isCorrect ? 'is-correct' : 'is-wrong' }}">
                            {{ $given ?? 'উত্তর দেওয়া হয়নি' }}
                        </span>
                    </div>
                    <div>সঠিক উত্তর: <span class="correct-ans">{{ $correct }}</span></div>
                </div>
                @if (!empty($q['explanation']))
                    <div class="exp-box">
                        <strong>ব্যাখ্যা:</strong> {{ $q['explanation'] }}
                    </div>
                @endif
            </div>
        @endforeach
    </div>
</body>
</html>
