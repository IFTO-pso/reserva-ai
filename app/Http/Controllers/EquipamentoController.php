<?php

namespace App\Http\Controllers;

use App\Models\Equipamento;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class EquipamentoController extends Controller
{
    private const INDICE_PATRIMONIO_UNICO = 'equipamentos_numero_patrimonio_unique';

    public function listar(Request $request)
    {
        $filtros = $request->validate([
            'busca' => ['nullable', 'string', 'max:100'],
            'situacao' => ['nullable', 'string', Rule::in(array_keys(Equipamento::SITUACOES))],
        ]);

        $equipamentos = Equipamento::query()
            ->when($filtros['busca'] ?? null, function ($consulta, $busca) {
                $termo = '%' . addcslashes($busca, '\\%_') . '%';
                $consulta->where(function ($consulta) use ($termo) {
                    $consulta->where('nome', 'like', $termo)
                        ->orWhere('numero_patrimonio', 'like', $termo);
                });
            })
            ->when($filtros['situacao'] ?? null, function ($consulta, $situacao) {
                $consulta->where('situacao', $situacao);
            })
            ->orderBy('nome')
            ->orderBy('id')
            ->paginate(15)
            ->withQueryString();

        return view('equipamentos.listar', [
            'equipamentos' => $equipamentos,
            'filtros' => $filtros,
        ]);
    }

    public function criar()
    {
        return view('equipamentos.criar', [
            'equipamento' => new Equipamento(),
        ]);
    }

    public function cadastrar(Request $request)
    {
        $dados = $request->validate($this->regras(), $this->mensagens(), $this->atributos());
        $dados['situacao'] = $dados['situacao'] ?? Equipamento::SITUACAO_DISPONIVEL;

        try {
            $equipamento = Equipamento::create($dados);
        } catch (QueryException $erro) {
            $this->tratarPatrimonioDuplicado($erro);
        }

        return redirect()
            ->route('equipamentos.consultar', $equipamento)
            ->with('sucesso', 'Equipamento cadastrado com sucesso.');
    }

    public function consultar(Equipamento $equipamento)
    {
        $proximasReservas = $equipamento->reservas()
            ->vigentes()
            ->orderBy('inicio')
            ->limit(10)
            ->get();

        return view('equipamentos.consultar', [
            'equipamento' => $equipamento,
            'reservaEmAndamento' => $equipamento->reservaEmAndamento(),
            'proximasReservas' => $proximasReservas,
        ]);
    }

    public function editar(Equipamento $equipamento)
    {
        return view('equipamentos.editar', [
            'equipamento' => $equipamento,
        ]);
    }

    public function atualizar(Request $request, Equipamento $equipamento)
    {
        $dados = $request->validate($this->regras($equipamento), $this->mensagens(), $this->atributos());

        try {
            DB::transaction(function () use ($equipamento, $dados) {
                $bloqueado = Equipamento::whereKey($equipamento->getKey())->lockForUpdate()->firstOrFail();

                if ($dados['situacao'] !== Equipamento::SITUACAO_DISPONIVEL
                    && $bloqueado->reservas()->vigentes()->exists()) {
                    throw ValidationException::withMessages([
                        'situacao' => 'A situação não pode ser alterada para "' . Equipamento::SITUACOES[$dados['situacao']]
                            . '" porque o equipamento possui reservas futuras ou em andamento. Cancele-as antes.',
                    ]);
                }

                $bloqueado->update($dados);
            });
        } catch (QueryException $erro) {
            $this->tratarPatrimonioDuplicado($erro);
        }

        return redirect()
            ->route('equipamentos.consultar', $equipamento)
            ->with('sucesso', 'Equipamento atualizado com sucesso.');
    }

    public function confirmarExclusao(Equipamento $equipamento)
    {
        return view('equipamentos.excluir', [
            'equipamento' => $equipamento,
            'possuiReservas' => $equipamento->reservas()->exists(),
        ]);
    }

    public function excluir(Equipamento $equipamento)
    {
        $excluido = DB::transaction(function () use ($equipamento) {
            $bloqueado = Equipamento::whereKey($equipamento->getKey())->lockForUpdate()->firstOrFail();

            if ($bloqueado->reservas()->exists()) {
                return false;
            }

            $bloqueado->delete();

            return true;
        });

        if (! $excluido) {
            return redirect()
                ->route('equipamentos.consultar', $equipamento)
                ->with('erro', 'Este equipamento possui histórico de reservas e não pode ser excluído. Altere a situação para "Fora de uso" se ele não será mais utilizado.');
        }

        return redirect()
            ->route('equipamentos.listar')
            ->with('sucesso', 'Equipamento "' . $equipamento->nome . '" excluído com sucesso.');
    }

    private function regras(?Equipamento $equipamento = null): array
    {
        $unico = Rule::unique('equipamentos', 'numero_patrimonio');

        if ($equipamento !== null) {
            $unico->ignore($equipamento->getKey());
        }

        return [
            'nome' => ['required', 'string', 'max:255'],
            'numero_patrimonio' => ['required', 'string', 'max:100', 'regex:/^[A-Za-z0-9.\/-]+$/', $unico],
            'categoria' => ['required', 'string', Rule::in(array_keys(Equipamento::CATEGORIAS))],
            'situacao' => [$equipamento === null ? 'nullable' : 'required', 'string', Rule::in(array_keys(Equipamento::SITUACOES))],
        ];
    }

    private function mensagens(): array
    {
        return [
            'numero_patrimonio.unique' => 'Já existe um equipamento cadastrado com este número de patrimônio.',
            'numero_patrimonio.regex' => 'O número de patrimônio deve conter apenas letras, números, ponto (.), hífen (-) ou barra (/), sem espaços.',
            'categoria.in' => 'Selecione uma categoria válida: ' . implode(', ', Equipamento::CATEGORIAS) . '.',
            'situacao.in' => 'Selecione uma situação válida: ' . implode(', ', Equipamento::SITUACOES) . '.',
        ];
    }

    private function atributos(): array
    {
        return [
            'nome' => 'nome',
            'numero_patrimonio' => 'número de patrimônio',
            'categoria' => 'categoria',
            'situacao' => 'situação',
        ];
    }

    /**
     * Converte a violação do índice único de patrimônio (ex.: gravações concorrentes) em erro do campo.
     */
    private function tratarPatrimonioDuplicado(QueryException $erro): void
    {
        $codigoMysql = $erro->errorInfo[1] ?? null;

        if ($codigoMysql === 1062 && str_contains($erro->getMessage(), self::INDICE_PATRIMONIO_UNICO)) {
            throw ValidationException::withMessages([
                'numero_patrimonio' => $this->mensagens()['numero_patrimonio.unique'],
            ]);
        }

        throw $erro;
    }
}
