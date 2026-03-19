<?php

namespace Tests\Feature\Reports;

use App\Models\Order;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminReportExportsTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_download_sales_excel_report(): void
    {
        [$admin] = $this->seedAdminAndOrder();

        $response = $this->actingAs($admin)->get('/admin/reports/export/excel');

        $response->assertOk();
        self::assertStringContainsString('.xlsx', (string) $response->headers->get('content-disposition'));
    }

    public function test_admin_can_download_sales_pdf_report(): void
    {
        [$admin] = $this->seedAdminAndOrder();

        $response = $this->actingAs($admin)->get('/admin/reports/export/pdf');

        $response->assertOk();
        self::assertStringContainsString('.pdf', (string) $response->headers->get('content-disposition'));
    }

    private function seedAdminAndOrder(): array
    {
        $adminRoleId = Role::query()->firstOrCreate(['name' => 'admin'])->id;
        $clientRoleId = Role::query()->firstOrCreate(['name' => 'client'])->id;

        $admin = User::factory()->create(['role_id' => $adminRoleId]);
        $customer = User::factory()->create(['role_id' => $clientRoleId]);

        Order::query()->create([
            'user_id' => $customer->id,
            'payment_method' => 'tarjeta',
            'status' => 'entregado',
            'address' => 'Av. Deskcir 123',
            'city' => 'Ciudad de Mexico',
            'postal_code' => '01000',
            'phone' => '5555555555',
            'total' => 2499.50,
            'subtotal' => 2200,
            'shipping_fee' => 149,
            'service_fee' => 150.50,
            'discount' => 0,
            'wallet_used' => 0,
        ]);

        return [$admin, $customer];
    }
}
