<!DOCTYPE html>
<html>
<head><meta charset="utf-8"></head>
<body style="font-family: sans-serif; color: #1a1a2e;">
    <h2>New contact form submission</h2>
    <p><strong>From:</strong> {{ $senderName }} ({{ $senderEmail }})</p>
    <p><strong>Subject:</strong> {{ $subjectLine }}</p>
    <p><strong>Message:</strong></p>
    <p style="white-space: pre-wrap;">{{ $body }}</p>
</body>
</html>
