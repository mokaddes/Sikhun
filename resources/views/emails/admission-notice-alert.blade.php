<!DOCTYPE html>
<html>
<head><meta charset="utf-8"></head>
<body style="font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; color: #1e293b; max-width: 620px; margin: 0 auto; padding: 24px; background: #f8fafc;">
    <div style="background: #ffffff; border-radius: 16px; padding: 28px; border: 1px solid #e2e8f0; box-shadow: 0 4px 12px rgba(0,0,0,0.03);">
        <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 16px;">
            <span style="background: #f97316; color: white; padding: 4px 10px; border-radius: 6px; font-size: 11px; font-weight: bold; text-transform: uppercase;">
                {{ $notice->university_type === 'public' ? 'পাবলিক বিশ্ববিদ্যালয়' : ($notice->university_type === 'guccho' ? 'গুচ্ছ বিশ্ববিদ্যালয়' : 'বেসরকারি বিশ্ববিদ্যালয়') }}
            </span>
            <span style="color: #64748b; font-size: 12px; margin-left: 8px;">শিক্ষাবর্ষ: {{ $notice->academic_year }}</span>
        </div>

        <h2 style="color: #0f172a; margin: 0 0 12px; font-size: 20px; line-height: 1.4;">
            {{ $notice->title }}
        </h2>

        <p style="color: #475569; font-size: 14px; line-height: 1.6; margin: 0 0 20px;">
            একটি নতুন বিশ্ববিদ্যালয় ভর্তি বিজ্ঞপ্তি স্বয়ংক্রিয়ভাবে সংগ্রহ ও ড্রাফট হিসেবে প্রস্তুত করা হয়েছে। অ্যাডমিন প্যানেল থেকে প্রিভিউ দেখে এটি ১-ক্লিকে প্রকাশ করতে পারবেন।
        </p>

        <table style="width: 100%; border-collapse: collapse; margin-bottom: 24px; font-size: 13px;">
            <tr style="border-bottom: 1px solid #f1f5f9;">
                <td style="padding: 10px 0; color: #64748b; width: 35%;">প্রতিষ্ঠান</td>
                <td style="padding: 10px 0; font-weight: 600; color: #0f172a;">{{ $notice->institution_name }}</td>
            </tr>
            <tr style="border-bottom: 1px solid #f1f5f9;">
                <td style="padding: 10px 0; color: #64748b;">আবেদনের সময়সীমা</td>
                <td style="padding: 10px 0; font-weight: 600; color: #dc2626;">
                    {{ $notice->application_deadline ? $notice->application_deadline->format('d M, Y') : 'ঘোষণা সাপেক্ষে' }}
                </td>
            </tr>
            <tr style="border-bottom: 1px solid #f1f5f9;">
                <td style="padding: 10px 0; color: #64748b;">ন্যূনতম জিপিএ যোগ্যতা</td>
                <td style="padding: 10px 0; font-weight: 600; color: #0f172a;">
                    {{ $notice->total_gpa_required ?: ($notice->minimum_gpa_hsc ? "HSC {$notice->minimum_gpa_hsc}" : 'সার্কুলার দেখুন') }}
                </td>
            </tr>
            @if($notice->application_fee)
            <tr style="border-bottom: 1px solid #f1f5f9;">
                <td style="padding: 10px 0; color: #64748b;">আবেদন ফি</td>
                <td style="padding: 10px 0; font-weight: 600; color: #0f172a;">{{ $notice->application_fee }}</td>
            </tr>
            @endif
            @if($notice->apply_url)
            <tr style="border-bottom: 1px solid #f1f5f9;">
                <td style="padding: 10px 0; color: #64748b;">অফিসিয়াল আবেদন লিংক</td>
                <td style="padding: 10px 0; font-weight: 600; color: #2563eb;">
                    <a href="{{ $notice->apply_url }}" target="_blank" style="color: #2563eb; text-decoration: none;">আবেদন পোর্টাল ↗</a>
                </td>
            </tr>
            @endif
        </table>

        @if($notice->notice_image_url)
        <div style="margin-bottom: 24px; text-align: center; background: #f8fafc; padding: 12px; border-radius: 8px;">
            <img src="{{ $notice->notice_image_url }}" alt="Circular Notice" style="max-width: 100%; border-radius: 6px; max-height: 280px; object-fit: contain;">
        </div>
        @endif

        <div style="display: flex; gap: 12px; margin-top: 24px;">
            <a href="{{ url('/admin/admissions/' . $notice->id . '/edit') }}"
               style="background: #f97316; color: #ffffff; text-decoration: none; padding: 12px 24px; border-radius: 8px; font-weight: bold; font-size: 14px; display: inline-block;">
                অ্যাডমিন প্যানেলে প্রিভিউ ও প্রকাশ করুন →
            </a>
        </div>
        <p style="margin-top: 16px; font-size: 12px; color: #94a3b8;">
            মোট অপেক্ষমাণ ড্রাফট সংখ্যা: {{ $totalPendingDrafts }} টি | Sikhun.com Admission Intelligence
        </p>
    </div>
</body>
</html>
