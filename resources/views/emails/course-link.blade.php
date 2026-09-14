<!DOCTYPE html>
<html>
<head><meta charset="utf-8"></head>
<body style="font-family: sans-serif; color: #1a1a2e; max-width: 500px; margin: 0 auto;">
    <h2 style="color: #6c63ff;">Your course link is ready</h2>
    <p>Hi {{ $student->name }}, you now have access to <strong>{{ $course->title }}</strong>.</p>

    <p style="margin: 24px 0;">
        <a href="{{ $course->external_link }}"
           style="display: inline-block; padding: 12px 24px; background: #6c63ff; color: #ffffff; text-decoration: none; border-radius: 8px; font-weight: bold;">
            {{ $course->delivery_type === 'file_download' ? 'Download your files' : 'Open your course' }}
        </a>
    </p>

    @if ($course->link_note)
        <p style="padding: 12px; background: #f4f4fb; border-radius: 8px; white-space: pre-line;">{{ $course->link_note }}</p>
    @endif

    <p style="color: #6b6b8a; font-size: 13px;">
        If the button does not work, copy this link into your browser:<br>
        <a href="{{ $course->external_link }}" style="color: #6c63ff;">{{ $course->external_link }}</a>
    </p>

    @if ($order)
        <table style="width: 100%; border-collapse: collapse; margin: 16px 0;">
            <tr><td style="padding: 6px 0; color: #6b6b8a;">Order #</td><td style="padding: 6px 0;">{{ $order->order_number }}</td></tr>
            <tr><td style="padding: 6px 0; color: #6b6b8a;">Amount</td><td style="padding: 6px 0;">৳{{ $order->amount }}</td></tr>
        </table>
    @endif

    <p>Thanks for learning with Sikhun.com!</p>
</body>
</html>
