<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\Referral;
use App\Models\SiteSetting;
use App\Models\Student;
use App\Models\WalletTransaction;
use App\Services\ReferralService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Tests\TestCase;

class ReferralEnhancementsTest extends TestCase
{
    use RefreshDatabase;

    private function student(string $label, ?Student $referrer = null): Student
    {
        return Student::create([
            'name' => 'Referral '.$label,
            'email' => strtolower($label.'-'.uniqid()).'@sikhun.test',
            'password' => 'password',
            'type' => 'hsc',
            'status' => 'active',
            'referral_code' => strtoupper(substr($label, 0, 5)).strtoupper(uniqid()),
            'referred_by_student_id' => $referrer?->id,
        ]);
    }

    private function requestWithSignals(string $ip, string $device, ?string $phone = null): Request
    {
        return Request::create('/register', 'POST', array_filter(['phone' => $phone]), [], [], [
            'REMOTE_ADDR' => $ip,
            'HTTP_X_DEVICE_ID' => $device,
        ]);
    }

    public function test_click_registration_and_paid_stages_are_tracked_and_rewards_are_idempotent(): void
    {
        $service = app(ReferralService::class);
        $referrer = $this->student('referrer');
        $session = app('session')->driver();
        $session->start();
        $clickRequest = Request::create('/r/'.$referrer->referral_code, 'GET', [], [], [], [
            'REMOTE_ADDR' => '192.0.2.10',
        ]);
        $clickRequest->setLaravelSession($session);
        $click = $service->recordClick($clickRequest, $referrer->referral_code);

        $referee = $this->student('referee', $referrer);
        $registrationRequest = $this->requestWithSignals('192.0.2.11', 'device-a', '01700000001');
        $registrationRequest->setLaravelSession($session);
        $registration = $service->recordRegistration($referee, $registrationRequest);
        $service->rewardIfEligible($referee);
        $service->rewardIfEligible($referee);

        $this->assertNotNull($registration);
        $this->assertSame($registration->id, $click->fresh()->referral_id);
        $this->assertDatabaseHas('referral_events', ['event_type' => 'click', 'referrer_student_id' => $referrer->id]);
        $this->assertDatabaseHas('referral_events', ['event_type' => 'registered', 'referral_id' => $registration->id]);
        $this->assertDatabaseHas('referral_events', ['event_type' => 'paid', 'referral_id' => $registration->id]);
        $this->assertSame('rewarded', $registration->fresh()->status);
        $this->assertSame(2, WalletTransaction::where('category', 'referral_bonus')->count());
    }

    public function test_milestone_tiers_change_the_reward_on_the_qualifying_paid_referral(): void
    {
        SiteSetting::updateOrCreate(['key' => 'referral_reward_tiers'], ['value' => [
            ['milestone' => 2, 'referrer_reward' => 45, 'referee_reward' => 25],
        ]]);
        $service = app(ReferralService::class);
        $referrer = $this->student('tier-owner');

        foreach ([1, 2] as $number) {
            $referee = $this->student('tier-referee-'.$number, $referrer);
            $service->recordRegistration($referee, $this->requestWithSignals('198.51.100.'.$number, 'tier-device-'.$number));
            $service->rewardIfEligible($referee);
        }

        $rewards = Referral::where('referrer_student_id', $referrer->id)->orderBy('id')->pluck('referrer_reward')->all();
        $this->assertSame('20.00', $rewards[0]);
        $this->assertSame('45.00', $rewards[1]);
        $this->assertSame(65.0, (float) $referrer->fresh()->wallet_balance);
    }

    public function test_matching_phone_or_device_holds_rewards_for_admin_review_but_ip_alone_does_not(): void
    {
        $service = app(ReferralService::class);
        $firstReferrer = $this->student('first-owner');
        $firstReferee = $this->student('first-account', $firstReferrer);
        $service->recordRegistration($firstReferee, $this->requestWithSignals('203.0.113.7', 'shared-device', '01711111111'));

        $secondReferrer = $this->student('second-owner');
        $sameDeviceReferee = $this->student('device-match', $secondReferrer);
        $held = $service->recordRegistration($sameDeviceReferee, $this->requestWithSignals('203.0.113.8', 'shared-device'));
        $service->rewardIfEligible($sameDeviceReferee);

        $this->assertSame('held', $held->fresh()->fraud_status);
        $this->assertSame('pending', $held->fresh()->status);
        $this->assertNotNull($held->fresh()->paid_at);
        $this->assertSame(0.0, (float) $sameDeviceReferee->fresh()->wallet_balance);

        $admin = Admin::create([
            'name' => 'Referral Admin',
            'email' => 'referral-admin-'.uniqid().'@sikhun.test',
            'password' => 'password',
            'role' => 'super_admin',
            'is_active' => true,
        ]);
        $service->review($held->fresh(), $admin, 'approve');
        $this->assertSame('rewarded', $held->fresh()->status);
        $this->assertSame('approved', $held->fresh()->fraud_status);
        $this->assertSame(20.0, (float) $sameDeviceReferee->fresh()->wallet_balance);

        $phoneReferrer = $this->student('phone-owner');
        $phoneMatchReferee = $this->student('phone-match', $phoneReferrer);
        $phoneMatch = $service->recordRegistration($phoneMatchReferee, $this->requestWithSignals('203.0.113.9', 'phone-device-a', '01711111111'));
        $this->assertSame('held', $phoneMatch->fresh()->fraud_status);
        $this->assertContains('same_phone', $phoneMatch->fresh()->risk_signals);

        $thirdReferrer = $this->student('third-owner');
        $ipOnlyReferee = $this->student('ip-only', $thirdReferrer);
        $ipOnly = $service->recordRegistration($ipOnlyReferee, $this->requestWithSignals('203.0.113.7', 'different-device'));
        $this->assertSame('clear', $ipOnly->fresh()->fraud_status);
        $this->assertContains('same_ip', $ipOnly->fresh()->risk_signals);
    }
}
