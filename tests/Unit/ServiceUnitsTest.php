<?php

namespace Tests\Unit;

use App\Http\Middleware\SecurityHeaders;
use App\Services\Media\GoogleDriveStreamService;
use App\Services\OtpService;
use App\Services\SiteSettingService;
use App\Services\SmsService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use PHPUnit\Framework\TestCase;

class ServiceUnitsTest extends TestCase
{
    public function test_google_drive_stream_service_extracts_file_ids_from_all_url_formats(): void
    {
        $service = new GoogleDriveStreamService();

        $this->assertSame('1AbCdEfGhIj', $service->extractFileId('https://drive.google.com/file/d/1AbCdEfGhIj/view?usp=sharing'));
        $this->assertSame('1AbCdEfGhIj', $service->extractFileId('https://drive.google.com/open?id=1AbCdEfGhIj'));
        $this->assertSame('1AbCdEfGhIj', $service->extractFileId('https://drive.google.com/uc?id=1AbCdEfGhIj&export=download'));
        $this->assertNull($service->extractFileId('https://example.com/video.mp4'));
    }

    public function test_otp_service_normalizes_bangladesh_phone_numbers(): void
    {
        $sms = new SmsService(new SiteSettingService());
        $otp = new OtpService($sms);

        $this->assertSame('01711223344', $otp->normalizePhone('+8801711223344'));
        $this->assertSame('01711223344', $otp->normalizePhone('8801711223344'));
        $this->assertSame('01711223344', $otp->normalizePhone('1711223344'));
        $this->assertSame('01711223344', $otp->normalizePhone('01711-223344'));
    }

    public function test_security_headers_middleware_sets_csp_and_hardening_headers(): void
    {
        $middleware = new SecurityHeaders();
        $request = Request::create('/test', 'GET');
        $appMock = \Mockery::mock();
        // Skip app()->isProduction() when running outside Laravel container by using non-secure request
        $response = $middleware->handle($request, fn () => new Response('ok'));

        $this->assertSame('nosniff', $response->headers->get('X-Content-Type-Options'));
        $this->assertSame('SAMEORIGIN', $response->headers->get('X-Frame-Options'));
        $this->assertStringContainsString("object-src 'none'", (string) $response->headers->get('Content-Security-Policy'));
    }
}
