<!DOCTYPE html>
<html>
<head><meta charset="utf-8"></head>
<body style="font-family: sans-serif; color: #1a1a2e; max-width: 560px; margin: 0 auto; padding: 16px;">
    <h2 style="color: #f97316; margin-bottom: 8px;">নতুন ব্লগ ড্রাফট রিপোর্ট — Sikhun.com</h2>
    <p style="color: #4a4a68; line-height: 1.6;">
        স্বয়ংক্রিয় ব্লগ স্ক্র্যাপার রান সম্পন্ন হয়েছে। অনুবাদ ও AI রিরাইট শেষে নিচের ব্লগগুলো আপনার রিভিউ এবং ম্যানুয়াল পাবলিশের জন্য <strong>Draft</strong> হিসেবে সংরক্ষিত আছে:
    </p>

    <table style="width: 100%; border-collapse: collapse; margin: 16px 0; background: #f8fafc; border-radius: 8px; padding: 12px;">
        <tr>
            <td style="padding: 8px 12px; color: #6b6b8a;">আজকের রানে নতুন ড্রাফট</td>
            <td style="padding: 8px 12px; font-weight: bold; color: #10b981;">{{ $summary['new_drafts'] ?? 0 }} টি</td>
        </tr>
        <tr>
            <td style="padding: 8px 12px; color: #6b6b8a;">অপেক্ষমাণ মোট ড্রাফট</td>
            <td style="padding: 8px 12px; font-weight: bold; color: #f97316;">{{ $summary['total_drafts'] ?? 0 }} টি</td>
        </tr>
        <tr>
            <td style="padding: 8px 12px; color: #6b6b8a;">ডুপ্লিকেট স্কিপ করা হয়েছে</td>
            <td style="padding: 8px 12px;">{{ $summary['duplicates'] ?? 0 }} টি</td>
        </tr>
        <tr>
            <td style="padding: 8px 12px; color: #6b6b8a;">ব্লকলিস্ট স্কিপ করা হয়েছে</td>
            <td style="padding: 8px 12px;">{{ $summary['blocked'] ?? 0 }} টি</td>
        </tr>
        @if(!empty($summary['failed']))
        <tr>
            <td style="padding: 8px 12px; color: #6b6b8a;">ব্যর্থ হয়েছে</td>
            <td style="padding: 8px 12px; color: #ef4444;">{{ $summary['failed'] }} টি</td>
        </tr>
        @endif
    </table>

    @if(!empty($summary['recent_drafts']) && count($summary['recent_drafts']) > 0)
        <h3 style="font-size: 15px; margin-top: 20px;">সর্বশেষ তৈরি ড্রাফটসমূহ:</h3>
        <ul style="padding-left: 20px; line-height: 1.7;">
            @foreach($summary['recent_drafts'] as $draft)
                <li>
                    <strong>{{ $draft->title }}</strong>
                    @if($draft->source_url)
                        <br><small style="color: #6b6b8a;">মূল সূত্র: {{ $draft->source_url }}</small>
                    @endif
                </li>
            @endforeach
        </ul>
    @endif

    <p style="margin-top: 24px;">
        <a href="{{ url('/admin/blog?status=draft') }}"
           style="display: inline-block; background: #f97316; color: #ffffff; text-decoration: none; padding: 10px 18px; border-radius: 6px; font-weight: bold;">
            অ্যাডমিন প্যানেলে ড্রাফট রিভিউ ও পাবলিশ করুন →
        </a>
    </p>
</body>
</html>
