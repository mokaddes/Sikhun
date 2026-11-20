<!DOCTYPE html>
<html>
<head><meta charset="utf-8"></head>
<body style="font-family: sans-serif; color: #1a1a2e; max-width: 540px; margin: 0 auto; padding: 24px;">
    <h2 style="color: #6c63ff; margin-bottom: 12px;">{{ $heading }}</h2>
    <p style="line-height: 1.6;">Hi {{ $student->name }},</p>
    <p style="line-height: 1.6;">{{ $summary }}</p>

    @if (! empty($details))
        <table style="width: 100%; border-collapse: collapse; margin: 18px 0; border-top: 1px solid #e5e7eb;">
            @foreach ($details as $label => $value)
                <tr style="border-bottom: 1px solid #e5e7eb;">
                    <td style="padding: 8px 0; color: #6b6b8a; width: 42%;">{{ $label }}</td>
                    <td style="padding: 8px 0; font-weight: 600;">{{ $value }}</td>
                </tr>
            @endforeach
        </table>
    @endif

    <p style="margin-top: 20px;">
        <a href="{{ url('/dashboard') }}" style="display: inline-block; background: #6c63ff; color: #ffffff; text-decoration: none; padding: 10px 18px; border-radius: 8px; font-weight: 600;">
            Go to Sikhun Dashboard
        </a>
    </p>
    <p style="color: #6b6b8a; font-size: 13px; margin-top: 24px;">Thanks for learning with Sikhun.com!</p>
</body>
</html>
