<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $invoice->invoice_number }} — Sikhun.com</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Anek+Bangla:wght@400;500;600;700&family=DM+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root { --ink: #16202c; --muted: #6b7280; --line: #e5e7eb; --brand: #f97316; }
        * { box-sizing: border-box; }
        body {
            margin: 0; padding: 32px 20px;
            font-family: 'DM Sans', 'Anek Bangla', 'Noto Sans Bengali', sans-serif;
            color: var(--ink); background: #f3f4f6;
        }
        .sheet {
            max-width: 780px; margin: 0 auto; background: #fff; border-radius: 16px;
            padding: 40px; box-shadow: 0 10px 40px rgba(15, 23, 42, .08);
        }
        .top { display: flex; justify-content: space-between; gap: 24px; flex-wrap: wrap; }
        .brand { font-size: 22px; font-weight: 700; letter-spacing: -.02em; }
        .brand span { color: var(--brand); }
        .muted { color: var(--muted); font-size: 13px; line-height: 1.7; }
        h1 { font-size: 26px; margin: 0 0 4px; letter-spacing: -.02em; }
        .badge {
            display: inline-block; padding: 5px 12px; border-radius: 999px;
            font-size: 12px; font-weight: 700; text-transform: uppercase; letter-spacing: .06em;
        }
        .badge-paid { background: #dcfce7; color: #15803d; }
        .badge-sent { background: #dbeafe; color: #1d4ed8; }
        .badge-overdue { background: #fee2e2; color: #b91c1c; }
        .badge-void { background: #e5e7eb; color: #4b5563; }
        .badge-draft { background: #fef3c7; color: #b45309; }
        .meta { display: grid; grid-template-columns: 1fr 1fr; gap: 24px; margin: 32px 0; }
        .meta h2 { font-size: 11px; text-transform: uppercase; letter-spacing: .1em; color: var(--muted); margin: 0 0 6px; }
        table { width: 100%; border-collapse: collapse; margin-top: 8px; }
        th { text-align: left; font-size: 11px; text-transform: uppercase; letter-spacing: .08em; color: var(--muted); border-bottom: 1px solid var(--line); padding: 10px 8px; }
        td { padding: 12px 8px; border-bottom: 1px solid var(--line); font-size: 14px; vertical-align: top; }
        .num { text-align: right; white-space: nowrap; }
        .totals { margin-left: auto; margin-top: 18px; width: 280px; }
        .totals div { display: flex; justify-content: space-between; padding: 6px 8px; font-size: 14px; }
        .totals .grand { border-top: 2px solid var(--ink); margin-top: 6px; padding-top: 10px; font-size: 17px; font-weight: 700; }
        .notes { margin-top: 28px; padding: 14px 16px; background: #f9fafb; border-radius: 10px; font-size: 13px; line-height: 1.7; }
        .footer { margin-top: 36px; border-top: 1px solid var(--line); padding-top: 14px; font-size: 12px; color: var(--muted); text-align: center; }
        .actions { max-width: 780px; margin: 0 auto 18px; display: flex; gap: 10px; justify-content: flex-end; }
        .btn { border: 0; border-radius: 10px; padding: 10px 18px; font: inherit; font-weight: 600; cursor: pointer; text-decoration: none; }
        .btn-primary { background: var(--brand); color: #fff; }
        .btn-ghost { background: #fff; color: var(--ink); border: 1px solid var(--line); }
        @media print {
            body { background: #fff; padding: 0; }
            .sheet { box-shadow: none; border-radius: 0; max-width: none; padding: 0; }
            .actions { display: none; }
            @page { margin: 18mm; }
        }
        @media (max-width: 640px) { .sheet { padding: 24px; } .meta { grid-template-columns: 1fr; } }
    </style>
</head>
<body>
    @php
        $bn = app()->getLocale() === 'bn';
        $site = app(\App\Services\SiteSettingService::class);
        $siteName = $site->get('site_name', 'Sikhun.com');
        $statusLabels = [
            'paid' => $bn ? 'পরিশোধিত' : 'Paid',
            'sent' => $bn ? 'পাঠানো হয়েছে' : 'Sent',
            'overdue' => $bn ? 'মেয়াদোত্তীর্ণ' : 'Overdue',
            'void' => $bn ? 'বাতিল' : 'Void',
            'draft' => $bn ? 'খসড়া' : 'Draft',
        ];

        // The rest of the student-facing UI uses Bengali numerals, so the
        // printed receipt must match — otherwise a student sees ৳250.00
        // online and ৳২৫০.০০ on the PDF and thinks they are different bills.
        $bnDigit = fn ($value) => $bn
            ? strtr((string) $value, ['0' => '০', '1' => '১', '2' => '২', '3' => '৩', '4' => '৪', '5' => '৫', '6' => '৬', '7' => '৭', '8' => '৮', '9' => '৯'])
            : (string) $value;
        $fmtMoney = fn ($value) => '৳'.$bnDigit(number_format((float) $value, 2));
        $bnMonths = ['Jan' => 'জানু', 'Feb' => 'ফেব', 'Mar' => 'মার্চ', 'Apr' => 'এপ্রিল', 'May' => 'মে', 'Jun' => 'জুন', 'Jul' => 'জুলাই', 'Aug' => 'আগস্ট', 'Sep' => 'সেপ্টে', 'Oct' => 'অক্টো', 'Nov' => 'নভে', 'Dec' => 'ডিসে'];
        $fmtDate = fn ($value) => $value
            ? $bnDigit($bn ? strtr($value->format('d M Y'), $bnMonths) : $value->format('d M Y'))
            : '—';
        $fmtQty = fn ($value) => $bnDigit(rtrim(rtrim(number_format((float) $value, 2, '.', ','), '0'), '.'));
    @endphp

    <div class="actions">
        <a class="btn btn-ghost" href="{{ $forAdmin ? route('admin.invoices.show', $invoice) : route('invoices.show', $invoice) }}">
            {{ $bn ? 'ফিরে যান' : 'Back' }}
        </a>
        <button class="btn btn-primary" type="button" onclick="window.print()">
            {{ $bn ? 'প্রিন্ট / PDF সেভ করুন' : 'Print / Save as PDF' }}
        </button>
    </div>

    <div class="sheet">
        <div class="top">
            <div>
                <div class="brand">{{ $siteName }}</div>
                <div class="muted">
                    {{ $site->get('contact_email', 'hello@sikhun.com') }}<br>
                    {{ $site->get('contact_phone', '') }}
                </div>
            </div>
            <div style="text-align:right">
                <h1>{{ $bn ? 'ইনভয়েস' : 'Invoice' }}</h1>
                <div class="muted">{{ $invoice->invoice_number }}</div>
                <div style="margin-top:8px">
                    <span class="badge badge-{{ $invoice->status }}">{{ $statusLabels[$invoice->status] ?? $invoice->status }}</span>
                </div>
            </div>
        </div>

        <div class="meta">
            <div>
                <h2>{{ $bn ? 'বিল প্রাপক' : 'Billed to' }}</h2>
                <div style="font-weight:600">{{ $invoice->student->name }}</div>
                <div class="muted">
                    {{ $invoice->student->email }}<br>
                    {{ $bn ? 'শিক্ষার্থী আইডি' : 'Student ID' }}: #{{ $bnDigit($invoice->student->id) }}
                </div>
            </div>
            <div>
                <h2>{{ $bn ? 'ইনভয়েসের তথ্য' : 'Invoice details' }}</h2>
                <div class="muted">
                    {{ $bn ? 'ইস্যু' : 'Issued' }}: {{ $fmtDate($invoice->issued_at) }}<br>
                    {{ $bn ? 'পরিশোধের শেষ তারিখ' : 'Due' }}: {{ $fmtDate($invoice->due_at) }}<br>
                    @if ($invoice->period_start)
                        {{ $bn ? 'সময়কাল' : 'Period' }}: {{ $fmtDate($invoice->period_start) }} – {{ $fmtDate($invoice->period_end) }}<br>
                    @endif
                    @if ($invoice->order)
                        {{ $bn ? 'অর্ডার' : 'Order' }}: {{ $invoice->order->order_number }}
                    @endif
                </div>
            </div>
        </div>

        <table>
            <thead>
                <tr>
                    <th>{{ $bn ? 'বিবরণ' : 'Description' }}</th>
                    <th class="num">{{ $bn ? 'পরিমাণ' : 'Qty' }}</th>
                    <th class="num">{{ $bn ? 'একক মূল্য' : 'Unit price' }}</th>
                    <th class="num">{{ $bn ? 'মোট' : 'Amount' }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($invoice->items as $item)
                    <tr>
                        <td>{{ $item->description }}</td>
                        <td class="num">{{ $fmtQty($item->qty) }}</td>
                        <td class="num">{{ $fmtMoney($item->unit_price) }}</td>
                        <td class="num">{{ $fmtMoney($item->amount) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="totals">
            <div><span>{{ $bn ? 'সাবটোটাল' : 'Subtotal' }}</span><span>{{ $fmtMoney($invoice->subtotal) }}</span></div>
            @if ((float) $invoice->discount > 0)
                <div><span>{{ $bn ? 'ডিসকাউন্ট' : 'Discount' }}</span><span>−{{ $fmtMoney($invoice->discount) }}</span></div>
            @endif
            @if ((float) $invoice->vat > 0)
                <div><span>{{ $bn ? 'ভ্যাট' : 'VAT' }}</span><span>{{ $fmtMoney($invoice->vat) }}</span></div>
            @endif
            <div class="grand"><span>{{ $bn ? 'সর্বমোট' : 'Total' }}</span><span>{{ $fmtMoney($invoice->total) }}</span></div>
        </div>

        @if ($invoice->notes)
            <div class="notes"><strong>{{ $bn ? 'নোট' : 'Notes' }}:</strong> {{ $invoice->notes }}</div>
        @endif

        <div class="footer">
            {{ $bn ? 'এই ইনভয়েসটি Sikhun.com সিস্টেম থেকে স্বয়ংক্রিয়ভাবে তৈরি।' : 'This invoice was generated automatically by Sikhun.com.' }}
        </div>
    </div>
</body>
</html>
