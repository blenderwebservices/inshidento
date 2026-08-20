<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RolePermissionsTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_has_full_access(): void
    {
        $this->seed();
        $admin = User::where('rol', 'admin')->first();

        $this->actingAs($admin)
            ->get(route('reports.dashboard'))
            ->assertStatus(200);

        $this->actingAs($admin)
            ->get(route('purchase-orders.index'))
            ->assertStatus(200);

        $this->actingAs($admin)
            ->get(route('suppliers.index'))
            ->assertStatus(200);

        $this->actingAs($admin)
            ->get(route('incidents.create'))
            ->assertStatus(200);
    }

    public function test_fm_can_manage_incidents_and_purchase_orders(): void
    {
        $this->seed();
        $fm = User::where('rol', 'fm')->first();

        $this->actingAs($fm)
            ->get(route('reports.dashboard'))
            ->assertStatus(200);

        $this->actingAs($fm)
            ->get(route('purchase-orders.index'))
            ->assertStatus(200);

        $this->actingAs($fm)
            ->get(route('incidents.create'))
            ->assertStatus(200);
    }

    public function test_stakeholder_can_view_all_reports_but_cannot_create_incidents(): void
    {
        $this->seed();
        $stakeholder = User::where('rol', 'stakeholder')->first();

        $this->actingAs($stakeholder)
            ->get(route('reports.dashboard'))
            ->assertStatus(200);

        $this->actingAs($stakeholder)
            ->get(route('purchase-orders.index'))
            ->assertStatus(200);

        $this->actingAs($stakeholder)
            ->get(route('incidents.create'))
            ->assertRedirect(route('incidents.index'));
    }

    public function test_user_can_only_register_incidents_and_cannot_view_reports(): void
    {
        $this->seed();
        $user = User::where('rol', 'user')->first();

        $this->actingAs($user)
            ->get(route('incidents.create'))
            ->assertStatus(200);

        $this->actingAs($user)
            ->get(route('reports.dashboard'))
            ->assertRedirect(route('incidents.index'));

        $this->actingAs($user)
            ->get(route('purchase-orders.index'))
            ->assertRedirect(route('incidents.index'));

        $this->actingAs($user)
            ->get(route('suppliers.index'))
            ->assertRedirect(route('incidents.index'));
    }

    public function test_admin_can_impersonate_roles_and_leave(): void
    {
        $this->seed();
        $admin = User::where('rol', 'admin')->first();

        // 1. Iniciar como Admin y cambiar a rol User vía impersonación
        $this->actingAs($admin)
            ->get(route('switch-role', 'user'))
            ->assertRedirect(route('incidents.index'));

        $this->assertTrue(session()->has('impersonator_id'));
        $this->assertEquals('user', auth()->user()->rol);

        // 2. Comprobar que como User no puede entrar al dashboard
        $this->get(route('reports.dashboard'))
            ->assertRedirect(route('incidents.index'));

        // 3. Regresar a Admin General
        $this->get(route('impersonate.leave'))
            ->assertRedirect(route('reports.dashboard'));

        $this->assertFalse(session()->has('impersonator_id'));
        $this->assertEquals('admin', auth()->user()->rol);
    }
}
