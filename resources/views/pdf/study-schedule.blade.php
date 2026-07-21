<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: sans-serif; font-size: 12px; color: #1a1a2e; }
        h1 { font-size: 18px; margin-bottom: 16px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #e2e2ee; padding: 6px 8px; text-align: left; }
        th { background: #f0f0fa; }
    </style>
</head>
<body>
    <h1>Study Schedule — Exam Date: {{ \Carbon\Carbon::parse($schedule->exam_date)->format('d M Y') }}</h1>
    <table>
        <thead><tr><th>Date</th><th>Subject</th><th>Topic</th><th>Hours</th><th>Tip</th></tr></thead>
        <tbody>
            @foreach ($schedule->schedule_data ?? [] as $day)
                <tr>
                    <td>{{ $day['date'] ?? '' }}</td>
                    <td>{{ $day['subject'] ?? '' }}</td>
                    <td>{{ $day['topic'] ?? '' }}</td>
                    <td>{{ $day['hours'] ?? '' }}</td>
                    <td>{{ $day['tip'] ?? '' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
