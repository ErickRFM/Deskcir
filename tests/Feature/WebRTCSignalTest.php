<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WebRTCSignalTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        config(['broadcasting.default' => 'null']);
    }

    public function test_ticket_owner_can_send_screen_request_offer_and_hangup(): void
    {
        [$clientRole, $technicianRole] = $this->createRoles();
        $client = User::factory()->create(['role_id' => $clientRole->id]);
        $technician = User::factory()->create(['role_id' => $technicianRole->id]);

        $ticket = Ticket::create([
            'user_id' => $client->id,
            'subject' => 'No conecta impresora',
            'description' => 'Necesita asistencia remota',
            'status' => 'abierto',
            'priority' => 'media',
        ]);

        $ticket->forceFill(['technician_id' => $technician->id])->save();

        $this->actingAs($client)
            ->postJson('/webrtc/offer', [
                'ticket_id' => $ticket->id,
                'offer' => ['type' => 'offer', 'sdp' => 'fake-sdp'],
                'request_mode' => 'screen-request',
            ])
            ->assertOk()
            ->assertJsonPath('ok', true);

        $this->assertDatabaseHas('rtc_signals', [
            'ticket_id' => $ticket->id,
            'sender_id' => $client->id,
            'type' => 'offer',
            'request_mode' => 'screen-request',
        ]);

        $this->actingAs($client)
            ->postJson('/webrtc/hangup', [
                'ticket_id' => $ticket->id,
                'reason' => 'ended',
            ])
            ->assertOk()
            ->assertJsonPath('ok', true);

        $this->assertDatabaseHas('rtc_signals', [
            'ticket_id' => $ticket->id,
            'sender_id' => $client->id,
            'type' => 'hangup',
        ]);
    }

    public function test_unrelated_user_cannot_send_webrtc_hangup_signal(): void
    {
        [$clientRole, $technicianRole] = $this->createRoles();
        $client = User::factory()->create(['role_id' => $clientRole->id]);
        $technician = User::factory()->create(['role_id' => $technicianRole->id]);
        $outsider = User::factory()->create(['role_id' => $clientRole->id]);

        $ticket = Ticket::create([
            'user_id' => $client->id,
            'subject' => 'Pantalla en negro',
            'description' => 'Ticket de prueba',
            'status' => 'abierto',
            'priority' => 'media',
        ]);

        $ticket->forceFill(['technician_id' => $technician->id])->save();

        $this->actingAs($outsider)
            ->postJson('/webrtc/hangup', [
                'ticket_id' => $ticket->id,
                'reason' => 'ended',
            ])
            ->assertForbidden();
    }

    private function createRoles(): array
    {
        $clientRole = Role::query()->firstOrCreate(['name' => 'client']);
        $technicianRole = Role::query()->firstOrCreate(['name' => 'technician']);

        return [$clientRole, $technicianRole];
    }
}
