<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head><meta charset="utf-8"></head>
<body style="font-family: 'Noto Sans Bengali', Arial, sans-serif; color: #1a1a2e; max-width: 540px; margin: 0 auto;">
    @php($bn = app()->getLocale() === 'bn')

    <h2 style="color: #6c63ff;">
        {{ $bn ? 'ইনভয়েস পরিশোধের সময় আসছে' : 'Your invoice is due soon' }}
    </h2>

    <p>
        {{ $bn ? "প্রিয় {$invoice->student->name}," : "Hi {$invoice->student->name}," }}
    </p>

    <p>
        {{ $bn
            ? "ইনভয়েস {$invoice->invoice_number} এর ৳{$invoice->total} পরিশোধের শেষ তারিখ {$invoice->due_at?->format('d M Y')} — আর {$daysLeft} দিন বাকি।"
            : "Invoice {$invoice->invoice_number} for ৳{$invoice->total} is due on {$invoice->due_at?->format('d M Y')} — {$daysLeft} day(s) left." }}
    </p>

    <p>
        <a href="{{ route('invoices.show', $invoice) }}"
           style="display: inline-block; background: #f97316; color: #fff; padding: 10px 18px; border-radius: 8px; text-decoration: none; font-weight: 600;">
            {{ $bn ? 'ইনভয়েস দেখুন' : 'View invoice' }}
        </a>
    </p>

    <p style="color: #666; font-size: 13px;">
        {{ $bn
            ? 'প্ল্যান নবায়ন করতে অ্যাপে গিয়ে পেমেন্ট সম্পন্ন করুন। ইতিমধ্যে পরিশোধ করে থাকলে এই মেইলটি উপেক্ষা করুন।'
            : 'Open the app to renew your plan. If you have already paid, please ignore this email.' }}
    </p>

    <p style="color: #999; font-size: 12px;">— Sikhun.com</p>
</body>
</html>
