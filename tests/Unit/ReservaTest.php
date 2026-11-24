<?php

namespace Tests\Unit;

use App\Models\Equipamento;
use App\Models\Reserva;
use Carbon\Carbon;
use Tests\TestCase;

class ReservaTest extends TestCase
{
    private function novaReserva(string $inicio, string $fim, ?string $canceladaEm = null): Reserva
    {
        $reserva = new Reserva();
        $reserva->inicio = Carbon::parse($inicio);
        $reserva->fim = Carbon::parse($fim);
        $reserva->cancelada_em = $canceladaEm ? Carbon::parse($canceladaEm) : null;

        return $reserva;
    }

    public function test_estado_e_derivado_dos_horarios_e_do_cancelamento(): void
    {
        $referencia = Carbon::parse('2030-01-10 10:00');

        $this->assertSame(Reserva::ESTADO_FUTURA, $this->novaReserva('2030-01-10 11:00', '2030-01-10 12:00')->estado($referencia));
        $this->assertSame(Reserva::ESTADO_EM_ANDAMENTO, $this->novaReserva('2030-01-10 10:00', '2030-01-10 12:00')->estado($referencia));
        $this->assertSame(Reserva::ESTADO_ENCERRADA, $this->novaReserva('2030-01-10 08:00', '2030-01-10 10:00')->estado($referencia));
        $this->assertSame(Reserva::ESTADO_CANCELADA, $this->novaReserva('2030-01-10 11:00', '2030-01-10 12:00', '2030-01-09 09:00')->estado($referencia));
    }

    public function test_somente_reservas_futuras_ou_em_andamento_podem_ser_canceladas(): void
    {
        $referencia = Carbon::parse('2030-01-10 10:00');

        $this->assertTrue($this->novaReserva('2030-01-10 11:00', '2030-01-10 12:00')->podeSerCancelada($referencia));
        $this->assertTrue($this->novaReserva('2030-01-10 09:00', '2030-01-10 11:00')->podeSerCancelada($referencia));
        $this->assertFalse($this->novaReserva('2030-01-10 08:00', '2030-01-10 09:00')->podeSerCancelada($referencia));
        $this->assertFalse($this->novaReserva('2030-01-10 11:00', '2030-01-10 12:00', '2030-01-09 09:00')->podeSerCancelada($referencia));
    }

    public function test_equipamento_novo_inicia_como_disponivel(): void
    {
        $equipamento = new Equipamento(['nome' => 'Projetor', 'numero_patrimonio' => '001', 'categoria' => 'audiovisual']);

        $this->assertSame(Equipamento::SITUACAO_DISPONIVEL, $equipamento->situacao);
        $this->assertTrue($equipamento->estaDisponivel());
        $this->assertSame('Disponível', $equipamento->rotuloSituacao());
        $this->assertSame('Audiovisual', $equipamento->rotuloCategoria());
    }
}
