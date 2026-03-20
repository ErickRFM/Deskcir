<?php

namespace Tests\Feature;

use App\Models\Appointment;
use App\Models\Role;
use App\Models\Ticket;
use App\Models\TicketChecklist;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminChecklistAndTechnicianProfileTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_open_full_ticket_checklist_view(): void
    {
        [$adminRole, $clientRole, $technicianRole] = $this->createRoles();

        $admin = User::factory()->create(['role_id' => $adminRole->id]);
        $client = User::factory()->create(['role_id' => $clientRole->id]);
        $technician = User::factory()->create(['role_id' => $technicianRole->id]);

        $ticket = Ticket::create([
            'user_id' => $client->id,
            'subject' => 'Laptop no enciende',
            'description' => 'Requiere diagnostico completo',
            'status' => 'en_proceso',
            'priority' => 'alta',
        ]);

        $ticket->forceFill(['technician_id' => $technician->id])->save();

        TicketChecklist::create([
            'ticket_id' => $ticket->id,
            'technician_id' => $technician->id,
            'diagnostico' => true,
            'diagnostico_notes' => 'Fuente y bateria revisadas.',
            'progress' => 'diagnostico',
            'status' => 'diagnostico',
        ]);

        $this->actingAs($admin)
            ->get(route('admin.tickets.checklist', $ticket->id))
            ->assertOk()
            ->assertSee('Vista completa del seguimiento tecnico para administracion.')
            ->assertSee('Laptop no enciende')
            ->assertSee($technician->name);
    }

    public function test_technician_profile_view_loads_with_ticket_and_appointment_stats(): void
    {
        [$adminRole, $clientRole, $technicianRole] = $this->createRoles();

        $client = User::factory()->create(['role_id' => $clientRole->id]);
        $technician = User::factory()->create([
            'role_id' => $technicianRole->id,
            'name' => 'Tecnico Demo',
        ]);

        $openTicket = Ticket::create([
            'user_id' => $client->id,
            'subject' => 'Pantalla azul',
            'description' => 'Revision tecnica',
            'status' => 'abierto',
            'priority' => 'media',
        ]);

        $openTicket->forceFill(['technician_id' => $technician->id])->save();

        $closedTicket = Ticket::create([
            'user_id' => $client->id,
            'subject' => 'Cambio de disco',
            'description' => 'Servicio finalizado',
            'status' => 'cerrado',
            'priority' => 'baja',
        ]);

        $closedTicket->forceFill(['technician_id' => $technician->id])->save();

        Appointment::create([
            'ticket_id' => $openTicket->id,
            'user_id' => $client->id,
            'technician_id' => $technician->id,
            'date' => now()->addDay()->toDateString(),
            'time' => '10:00',
            'type' => 'visita',
            'status' => 'programada',
            'notes' => 'Prueba de agenda',
        ]);

        $this->actingAs($technician)
            ->get(route('technician.profile'))
            ->assertOk()
            ->assertSee('Perfil Tecnico')
            ->assertSee('Tecnico Demo')
            ->assertSee('Datos del tecnico')
            ->assertSee('2')
            ->assertSee('1');
    }

    private function createRoles(): array
    {
        $adminRole = Role::query()->firstOrCreate(['name' => 'admin']);
        $clientRole = Role::query()->firstOrCreate(['name' => 'client']);
        $technicianRole = Role::query()->firstOrCreate(['name' => 'technician']);

        return [$adminRole, $clientRole, $technicianRole];
    }
}
