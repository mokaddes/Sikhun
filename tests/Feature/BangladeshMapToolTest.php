<?php

namespace Tests\Feature;

use App\Models\Plan;
use App\Models\Student;
use App\Models\StudentSubscription;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class BangladeshMapToolTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_can_access_bangladesh_map_tool_with_seo_and_free_tier(): void
    {
        $response = $this->get('/tools/bangladesh-map');

        $response->assertStatus(200);
        $response->assertInertia(fn (Assert $page) => $page
            ->component('Public/Tools/Index')
            ->where('initialTool', 'bd_map')
            ->where('isPaid', false)
            ->where('isAuthenticated', false)
            ->has('seo.title')
            ->has('seo.keywords')
            ->where('seo.canonical', fn (string $url) => str_ends_with($url, '/tools/bangladesh-map'))
        );
    }

    public function test_free_authenticated_student_receives_free_flag_on_bangladesh_map_tool(): void
    {
        $student = Student::create([
            'name' => 'Free Map Learner',
            'email' => 'free-map@example.com',
            'password' => Hash::make('password123'),
            'type' => 'job',
            'status' => 'active',
            'referral_code' => 'MAPFREE1',
        ]);

        $response = $this->actingAs($student, 'web')->get('/tools/bangladesh-map');

        $response->assertStatus(200);
        $response->assertInertia(fn (Assert $page) => $page
            ->component('Public/Tools/Index')
            ->where('initialTool', 'bd_map')
            ->where('isPaid', false)
            ->where('isAuthenticated', true)
        );
    }

    public function test_subscribed_student_receives_paid_flag_on_bangladesh_map_tool(): void
    {
        $student = Student::create([
            'name' => 'Pro Map Learner',
            'email' => 'pro-map@example.com',
            'password' => Hash::make('password123'),
            'type' => 'job',
            'status' => 'active',
            'referral_code' => 'MAPPRO01',
        ]);

        $plan = Plan::create([
            'name' => 'BCS Pro',
            'slug' => 'bcs-pro-test',
            'price_monthly' => 199,
            'price_total' => 199,
            'duration_months' => 1,
            'is_active' => true,
            'is_free' => false,
        ]);

        StudentSubscription::create([
            'student_id' => $student->id,
            'plan_id' => $plan->id,
            'status' => 'active',
            'started_at' => now()->subDay(),
            'expires_at' => now()->addMonth(),
        ]);

        $response = $this->actingAs($student, 'web')->get('/tools/bangladesh-map');

        $response->assertStatus(200);
        $response->assertInertia(fn (Assert $page) => $page
            ->component('Public/Tools/Index')
            ->where('initialTool', 'bd_map')
            ->where('isPaid', true)
            ->where('isAuthenticated', true)
        );
    }

    public function test_bangladesh_map_geojson_assets_are_valid_and_complete(): void
    {
        $divisionsPath = public_path('maps/bd-divisions.geojson');
        $districtsPath = public_path('maps/bd-districts.geojson');

        $this->assertFileExists($divisionsPath);
        $this->assertFileExists($districtsPath);

        $divisions = json_decode(file_get_contents($divisionsPath), true);
        $districts = json_decode(file_get_contents($districtsPath), true);

        $this->assertIsArray($divisions['features'] ?? null);
        $this->assertCount(8, $divisions['features']);

        $this->assertIsArray($districts['features'] ?? null);
        $this->assertCount(64, $districts['features']);

        foreach ($divisions['features'] as $feature) {
            $this->assertNotEmpty($feature['properties']['geoKey'] ?? null);
        }

        foreach ($districts['features'] as $feature) {
            $this->assertNotEmpty($feature['properties']['geoKey'] ?? null);
        }
    }
}
