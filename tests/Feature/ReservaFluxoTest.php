<?php

namespace Tests\Feature;

use App\Models\Equipamento;
use App\Models\Reserva;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class ReservaFluxoTest extends TestCase
{
    use RefreshDatabase;

    private Equipamento $equipamento;

    protected function setUp(): void
    {
        parent::setUp();

        $this->travelTo(Carbon::parse('2030-01-10 08:00'));
        $this->equipamento = Equipamento::create([
            'nome' => 'Notebook',
            'numero_patrimonio' => 'NB-1',
            'categoria' => 'informatica',
        ]);
    }

    private function reservar(string $inicio, string $fim, array $extras = [])
    {
        return $this->post('/reservas', array_merge([
            'equipamento_id' => $this->equipamento->id,
            'responsavel' => 'Ana Souza',
            'inicio' => $inicio,
            'fim' => $fim,
        ], $extras));
    }

    public function test_lista_vazia_e_informada(): void
    {
        $this->get('/reservas')->assertOk()->assertSee('A lista de reservas está vazia.');
    }

    public function test_reserva_valida_e_registrada(): void
    {
        $this->reservar('2030-01-10T09:00', '2030-01-10T11:00')->assertSessionHasNoErrors();

        $reserva = Reserva::firstOrFail();
        $this->assertSame('Ana Souza', $reserva->responsavel);
        $this->assertSame('2030-01-10 09:00', $reserva->inicio->format('Y-m-d H:i'));
        $this->assertSame('2030-01-10 11:00', $reserva->fim->format('Y-m-d H:i'));
        $this->assertNull($reserva->cancelada_em);

        $this->get('/reservas/' . $reserva->id)->assertOk()->assertSee('Ana Souza')->assertSee('Futura');
    }

    public function test_rejeita_datas_invalidas_passadas_ou_fim_nao_posterior(): void
    {
        $this->reservar('2030-01-10T07:00', '2030-01-10T09:00')->assertSessionHasErrors('inicio');
        $this->reservar('2030-01-10T10:00', '2030-01-10T10:00')->assertSessionHasErrors('fim');
        $this->reservar('2030-01-10T10:00', '2030-01-10T09:00')->assertSessionHasErrors('fim');
        $this->reservar('2030-02-30T10:00', '2030-02-30T11:00')->assertSessionHasErrors(['inicio', 'fim']);
        $this->reservar('amanha', '2030-01-10T11:00')->assertSessionHasErrors('inicio');
        $this->reservar('2030-01-10T09:00', '2030-01-10T10:00', ['responsavel' => ''])->assertSessionHasErrors('responsavel');

        $this->assertDatabaseCount('reservas', 0);
    }

    public function test_rejeita_equipamento_inexistente_ou_indisponivel(): void
    {
        $this->reservar('2030-01-10T09:00', '2030-01-10T10:00', ['equipamento_id' => 999])
            ->assertSessionHasErrors('equipamento_id');

        $this->equipamento->update(['situacao' => Equipamento::SITUACAO_EM_MANUTENCAO]);
        $this->reservar('2030-01-10T09:00', '2030-01-10T10:00')->assertSessionHasErrors('equipamento_id');

        $this->assertDatabaseCount('reservas', 0);
    }

    public function test_rejeita_intervalos_sobrepostos_e_aceita_consecutivos(): void
    {
        $this->reservar('2030-01-10T10:00', '2030-01-10T12:00')->assertSessionHasNoErrors();

        $this->reservar('2030-01-10T10:00', '2030-01-10T12:00')->assertSessionHasErrors('inicio');
        $this->reservar('2030-01-10T09:00', '2030-01-10T10:30')->assertSessionHasErrors('inicio');
        $this->reservar('2030-01-10T11:30', '2030-01-10T13:00')->assertSessionHasErrors('inicio');
        $this->reservar('2030-01-10T10:30', '2030-01-10T11:00')->assertSessionHasErrors('inicio');
        $this->reservar('2030-01-10T09:00', '2030-01-10T13:00')->assertSessionHasErrors('inicio');

        $this->reservar('2030-01-10T08:00', '2030-01-10T10:00')->assertSessionHasNoErrors();
        $this->reservar('2030-01-10T12:00', '2030-01-10T13:00')->assertSessionHasNoErrors();

        $this->assertDatabaseCount('reservas', 3);
    }

    public function test_outro_equipamento_pode_ser_reservado_no_mesmo_periodo(): void
    {
        $outro = Equipamento::create(['nome' => 'Projetor', 'numero_patrimonio' => 'PJ-1', 'categoria' => 'audiovisual']);

        $this->reservar('2030-01-10T10:00', '2030-01-10T12:00')->assertSessionHasNoErrors();
        $this->reservar('2030-01-10T10:00', '2030-01-10T12:00', ['equipamento_id' => $outro->id])->assertSessionHasNoErrors();

        $this->assertDatabaseCount('reservas', 2);
    }

    public function test_cancelamento_preserva_historico_e_libera_o_periodo(): void
    {
        $this->reservar('2030-01-10T10:00', '2030-01-10T12:00');
        $reserva = Reserva::firstOrFail();

        $this->get('/reservas/' . $reserva->id . '/cancelar')->assertOk()->assertSee('Cancelar reserva?');
        $this->assertNull($reserva->fresh()->cancelada_em);

        $this->patch('/reservas/' . $reserva->id . '/cancelar')
            ->assertRedirect('/reservas/' . $reserva->id)
            ->assertSessionHas('sucesso');

        $this->assertNotNull($reserva->fresh()->cancelada_em);
        $this->assertSame(Reserva::ESTADO_CANCELADA, $reserva->fresh()->estado());

        $this->reservar('2030-01-10T10:00', '2030-01-10T12:00')->assertSessionHasNoErrors();
        $this->assertDatabaseCount('reservas', 2);
    }

    public function test_reserva_em_andamento_pode_ser_cancelada(): void
    {
        $this->reservar('2030-01-10T09:00', '2030-01-10T12:00');
        $reserva = Reserva::firstOrFail();
        $this->travelTo(Carbon::parse('2030-01-10 10:00'));

        $this->get('/equipamentos/' . $this->equipamento->id)->assertSee('Em uso por');

        $this->patch('/reservas/' . $reserva->id . '/cancelar')->assertSessionHas('sucesso');
        $this->assertNotNull($reserva->fresh()->cancelada_em);
    }

    public function test_reserva_encerrada_nao_pode_ser_cancelada(): void
    {
        $this->reservar('2030-01-10T09:00', '2030-01-10T10:00');
        $reserva = Reserva::firstOrFail();
        $this->travelTo(Carbon::parse('2030-01-10 11:00'));

        $this->patch('/reservas/' . $reserva->id . '/cancelar')->assertSessionHas('erro');
        $this->assertNull($reserva->fresh()->cancelada_em);
        $this->assertSame(Reserva::ESTADO_ENCERRADA, $reserva->fresh()->estado());
    }

    public function test_formulario_lista_apenas_equipamentos_disponiveis(): void
    {
        Equipamento::create(['nome' => 'Câmera quebrada', 'numero_patrimonio' => 'CAM-9', 'categoria' => 'audiovisual', 'situacao' => Equipamento::SITUACAO_FORA_DE_USO]);

        $this->get('/reservas/nova?equipamento=' . $this->equipamento->id)
            ->assertOk()
            ->assertSee('Notebook')
            ->assertDontSee('Câmera quebrada')
            ->assertSee('name="_token"', false);
    }

    public function test_reserva_inexistente_retorna_404(): void
    {
        $this->get('/reservas/999')->assertNotFound();
        $this->patch('/reservas/999/cancelar')->assertNotFound();
    }
}
