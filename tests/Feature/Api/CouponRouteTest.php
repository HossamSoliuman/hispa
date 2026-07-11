<?php

namespace Tests\Feature\Api;

use App\Services\SupportServiceManager;
use Tests\TestCase;

class CouponRouteTest extends TestCase
{
    public function test_coupon_validation_route_boots_and_uses_the_renamed_controller_action(): void
    {
        $route = app('router')->getRoutes()->getByName('coupon.validate');

        $this->assertNotNull($route);
        $this->assertStringEndsWith('@validateCoupon', $route->getActionName());
        $this->postJson(route('coupon.validate'), [])->assertUnprocessable()->assertJsonValidationErrors('code');
    }

    public function test_support_service_manager_falls_back_to_configuration_when_the_legacy_model_is_absent(): void
    {
        config()->set('support_service.endpoint', 'https://support.example.test/api');

        $manager = new SupportServiceManager;

        $this->assertSame('https://support.example.test/api', $manager->getEndpoint());
    }
}
