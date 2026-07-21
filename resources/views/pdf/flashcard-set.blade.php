<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: sans-serif; font-size: 12px; color: #1a1a2e; }
        h1 { font-size: 18px; margin-bottom: 16px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #e2e2ee; padding: 8px; text-align: left; vertical-align: top; width: 50%; }
        th { background: #f0f0fa; }
    </style>
</head>
<body>
    <h1>{{ $set->title }}</h1>
    <table>
        <thead><tr><th>Question</th><th>Answer</th></tr></thead>
        <tbody>
            @foreach ($set->flashcards as $card)
                <tr><td>{{ $card->front }}</td><td>{{ $card->back }}</td></tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
