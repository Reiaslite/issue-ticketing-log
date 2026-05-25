<?php

namespace Tests\Feature;

use Tests\TestCase;

class HealthCheckTest extends TestCase
{
    /**
     * Ensure the API health endpoint responds with the contract payload.
     */
    public function test_health_check_returns_expected_payload(): void
    {
        $response = $this->getJson('/api/health');

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Healthy',
            ])
            ->assertJsonStructure([
                'success',
                'message',
                'data' => ['timestamp', 'app'],
            ])
            ->assertJsonPath('data.app', config('app.name'));
    }
}
