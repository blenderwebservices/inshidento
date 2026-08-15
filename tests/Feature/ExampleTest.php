<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;

    public function test_dashboard_returns_a_successful_response(): void
    {
        $this->seed();
        $response = $this->get('/reports/dashboard');

        $response->assertStatus(200);
        $response->assertSee('Dashboard de Reportes');
        $response->assertSee('Noreste');
        $response->assertSee('Bajío');
    }
}
