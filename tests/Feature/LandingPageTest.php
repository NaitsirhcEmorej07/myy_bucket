<?php

namespace Tests\Feature;

use Tests\TestCase;

class LandingPageTest extends TestCase
{
    public function test_landing_page_renders_successfully_with_saas_and_unlimited_storage(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('Myy Bucket');
        $response->assertSee('Your Alternative to iCloud');
        $response->assertSee('Early Access Free Trial');
        $response->assertSee('Unlimited Cloud Storage');
        $response->assertSee('Transparent Pricing');
        $response->assertSee('$0');
        $response->assertSee('Claim Your Free Trial');
    }
}
