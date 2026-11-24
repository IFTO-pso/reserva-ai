<?php

namespace App\Http\Controllers;

use App\Models\Equipamento;
use App\Models\Reserva;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class ReservaController extends Controller
{
    public const FORMATO_DATA_HORA = 'Y-m-d\TH:i';

    public function listar()
    {
        $reservas = Reserva::with('equipamento')
            ->orderByDesc('inicio')
            ->orderByDesc('id')
            ->paginate(15);

        return view('reservas.listar', [
            'reservas' => $reservas,
        ]);
    }

    public function criar(Request $request)
    {
        $filtro = $request->validate([
            'equipamento' => ['nullable', 'integer', 'min:1'],
        ]);

        return view('reservas.criar', [
            'equipamentos' => Equipamento::where('situacao', Equipamento::SITUACAO_DISPONIVEL)
                ->orderBy('nome')
                ->get(),
            'equipamentoSelecionado' => $filtro['equipamento'] ?? null,
            'minimoInicio' => now()->format(self::FORMATO_DATA_HORA),
        ]);
    }

    public function cadastrar(Request $request)
    {
        $agora = now()->startOfMinute();

        $dados = $request->validate([
            'equipamento_id' => ['required', 'integer', Rule::exists('equipamentos', 'id')],
            'responsavel' => ['required', 'string', 'max:255'],
            'inicio' => ['required', 'date_format:' . self::FORMATO_DATA_HORA, 'after_or_equal:' . $agora->format(self::FORMATO_DATA_HORA)],
            'fim' => ['required', 'date_format:' . self::FORMATO_DATA_HORA, 'after:inicio'],
        ], [
            'equipamento_id.exists' => 'O equipamento selecionado não foi encontrado.',
            'inicio.after_or_equal' => 'O início da reserva não pode estar no passado.',
            'fim.after' => 'O fim da reserva deve ser posterior ao início.',
        ], [
            'equipamento_id' => 'equipamento',
            'responsavel' => 'responsável',
            'inicio' => 'início',
            'fim' => 'fim',
        ]);

        $inicio = Carbon::createFromFormat(self::FORMATO_DATA_HORA, $dados['inicio'])->startOfMinute();
        $fim = Carbon::createFromFormat(self::FORMATO_DATA_HORA, $dados['fim'])->startOfMinute();

        $reserva = DB::transaction(function () use ($dados, $inicio, $fim) {
            // Bloqueia a linha do equipamento para serializar reservas, mudanças de situação e exclusões.
            $equipamento = Equipamento::whereKey($dados['equipamento_id'])->lockForUpdate()->first();

            if ($equipamento === null) {
                throw ValidationException::withMessages([
                    'equipamento_id' => 'O equipamento selecionado não foi encontrado.',
                ]);
            }

            if (! $equipamento->estaDisponivel()) {
                throw ValidationException::withMessages([
                    'equipamento_id' => 'O equipamento "' . $equipamento->nome . '" está com a situação "'
                        . $equipamento->rotuloSituacao() . '" e não pode ser reservado.',
                ]);
            }

            if ($inicio->lessThan(now()->startOfMinute())) {
                throw ValidationException::withMessages([
                    'inicio' => 'O início da reserva não pode estar no passado.',
                ]);
            }

            $conflito = $equipamento->reservas()
                ->conflitantes($inicio, $fim)
                ->orderBy('inicio')
                ->first();

            if ($conflito !== null) {
                throw ValidationException::withMessages([
                    'inicio' => 'O equipamento já está reservado de ' . $conflito->inicio->format('d/m/Y H:i')
                        . ' até ' . $conflito->fim->format('d/m/Y H:i') . '. Escolha outro período.',
                ]);
            }

            return $equipamento->reservas()->create([
                'responsavel' => $dados['responsavel'],
                'inicio' => $inicio,
                'fim' => $fim,
            ]);
        });

        return redirect()
            ->route('reservas.consultar', $reserva)
            ->with('sucesso', 'Reserva registrada com sucesso.');
    }

    public function consultar(Reserva $reserva)
    {
        $reserva->load('equipamento');

        return view('reservas.consultar', [
            'reserva' => $reserva,
        ]);
    }

    public function confirmarCancelamento(Reserva $reserva)
    {
        $reserva->load('equipamento');

        return view('reservas.cancelar', [
            'reserva' => $reserva,
        ]);
    }

    public function cancelar(Reserva $reserva)
    {
        $cancelada = DB::transaction(function () use ($reserva) {
            // Mesma ordem de bloqueio do cadastro: primeiro o equipamento, depois a reserva.
            Equipamento::whereKey($reserva->equipamento_id)->lockForUpdate()->first();
            $bloqueada = Reserva::whereKey($reserva->getKey())->lockForUpdate()->firstOrFail();

            if (! $bloqueada->podeSerCancelada()) {
                return false;
            }

            $bloqueada->cancelada_em = now();
            $bloqueada->save();

            return true;
        });

        if (! $cancelada) {
            return redirect()
                ->route('reservas.consultar', $reserva)
                ->with('erro', 'Somente reservas futuras ou em andamento podem ser canceladas.');
        }

        return redirect()
            ->route('reservas.consultar', $reserva)
            ->with('sucesso', 'Reserva cancelada. O período foi liberado e o registro foi mantido no histórico.');
    }
}
