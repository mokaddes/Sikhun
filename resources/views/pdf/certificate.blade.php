<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: sans-serif; text-align: center; padding: 60px; color: #1a1a2e; }
        .border-box { border: 6px double #6c63ff; padding: 60px 40px; height: 100%; }
        .brand { font-size: 16px; color: #6b6b8a; letter-spacing: 2px; text-transform: uppercase; margin-bottom: 40px; }
        h1 { font-size: 32px; margin-bottom: 10px; }
        .subtitle { color: #6b6b8a; margin-bottom: 40px; }
        .student-name { font-size: 36px; font-weight: bold; color: #6c63ff; margin-bottom: 20px; border-bottom: 2px solid #e2e2ee; display: inline-block; padding-bottom: 10px; }
        .course-title { font-size: 22px; margin-bottom: 30px; }
        .date { color: #6b6b8a; margin-top: 40px; }
    </style>
</head>
<body>
    <div class="border-box">
        <div class="brand">Sikhun.com</div>
        <h1>Certificate of Completion</h1>
        <p class="subtitle">This certifies that</p>
        <div class="student-name">{{ $student->name }}</div>
        <p class="subtitle">has successfully completed the course</p>
        <div class="course-title">{{ $course->title }}</div>
        <div class="date">{{ $completedAt?->format('d F Y') }}</div>
    </div>
</body>
</html>
