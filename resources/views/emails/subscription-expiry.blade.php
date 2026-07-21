<!DOCTYPE html>
<html>
<head><meta charset="utf-8"></head>
<body style="font-family: sans-serif; color: #1a1a2e; max-width: 500px; margin: 0 auto;">
    <h2 style="color: #ff6b6b;">Your subscription is expiring soon</h2>
    <p>Hi {{ $subscription->student->name }}, your <strong>{{ $subscription->plan->name }}</strong> subscription
        expires in <strong>{{ $daysRemaining }} day(s)</strong> (on {{ $subscription->expires_at->format('d M Y') }}).</p>
    <p>Renew now to keep uninterrupted access to AI Chat, exams, flashcards, and your gift books.</p>
    <p><a href="{{ url('/plans') }}" style="color: #6c63ff;">Renew your subscription →</a></p>
</body>
</html>
