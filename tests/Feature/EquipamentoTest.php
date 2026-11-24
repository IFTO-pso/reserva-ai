<?php

namespace Tests\Feature;

use App\Models\Equipamento;
use App\Models\Reserva;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class EquipamentoTest extends TestCase
{
    use RefreshDatabase;

    private function criarEquipamento(array $dados = []): Equipamento
    {
        return Equipamento::create(array_merge([
            'nome' => 'Projetor Epson',
            'numero_patrimonio' => 'PAT-001',
            'categoria' => 'audiovisual',
            'situacao' => Equipamento::SITUACAO_DISPONIVEL,
        ], $dados));
    }

    public function test_lista_vazia_e_informada(): void
    {
        $this->get('/equipamentos')
            ->assertOk()
            ->assertSee('A lista de equipamentos está vazia.');
    }

    public function test_lista_exibe_nome_patrimonio_categoria_e_situacao(): void
    {
        $this->criarEquipamento(['situacao' => Equipamento::SITUACAO_EM_MANUTENCAO]);

        $this->get('/equipamentos')
            ->assertOk()
            ->assertSee('Projetor Epson')
            ->assertSee('PAT-001')
            ->assertSee('Audiovisual')
            ->assertSee('Em manutenção');
    }

    public function test_busca_filtra_por_nome_ou_patrimonio(): void
    {
        $this->criarEquipamento();
        $this->criarEquipamento(['nome' => 'Notebook Dell', 'numero_patrimonio' => 'NB-77', 'categoria' => 'informatica']);

        $this->get('/equipamentos?busca=NB-77')
            ->assertOk()
            ->assertSee('Notebook Dell')
            ->assertDontSee('Projetor Epson');
    }

    public function test_cadastra_equipamento_com_situacao_padrao_disponivel(): void
    {
        $resposta = $this->post('/equipamentos', [
            'nome' => 'Furadeira',
            'numero_patrimonio' => '000123',
            'categoria' => 'ferramentas',
        ]);

        $equipamento = Equipamento::firstOrFail();
        $resposta->assertRedirect('/equipamentos/' . $equipamento->id);
        $this->assertSame('000123', $equipamento->numero_patrimonio);
        $this->assertSame(Equipamento::SITUACAO_DISPONIVEL, $equipamento->situacao);
    }

    public function test_cadastra_equipamento_com_outra_situacao_valida(): void
    {
        $this->post('/equipamentos', [
            'nome' => 'Câmera',
            'numero_patrimonio' => 'CAM-1',
            'categoria' => 'audiovisual',
            'situacao' => Equipamento::SITUACAO_FORA_DE_USO,
        ])->assertSessionHasNoErrors();

        $this->assertDatabaseHas('equipamentos', ['numero_patrimonio' => 'CAM-1', 'situacao' => 'fora_de_uso']);
    }

    public function test_rejeita_dados_invalidos_indicando_o_campo(): void
    {
        $this->post('/equipamentos', [
            'nome' => str_repeat('a', 256),
            'numero_patrimonio' => 'PAT 01',
            'categoria' => 'moveis',
            'situacao' => 'reservado',
        ])->assertSessionHasErrors(['nome', 'numero_patrimonio', 'categoria', 'situacao']);

        $this->post('/equipamentos', [
            'nome' => ['array'],
            'numero_patrimonio' => str_repeat('1', 101),
            'categoria' => 'outros',
        ])->assertSessionHasErrors(['nome', 'numero_patrimonio']);

        $this->assertDatabaseCount('equipamentos', 0);
    }

    public function test_rejeita_patrimonio_duplicado(): void
    {
        $this->criarEquipamento();

        $this->post('/equipamentos', [
            'nome' => 'Outro',
            'numero_patrimonio' => 'PAT-001',
            'categoria' => 'outros',
        ])->assertSessionHasErrors([
            'numero_patrimonio' => 'Já existe um equipamento cadastrado com este número de patrimônio.',
        ]);

        $this->assertDatabaseCount('equipamentos', 1);
    }

    public function test_banco_impede_patrimonio_duplicado(): void
    {
        $this->criarEquipamento();

        $this->expectException(QueryException::class);
        $this->criarEquipamento(['nome' => 'Duplicado']);
    }

    public function test_consulta_exibe_dados_do_equipamento(): void
    {
        $equipamento = $this->criarEquipamento();

        $this->get('/equipamentos/' . $equipamento->id)
            ->assertOk()
            ->assertSee('Projetor Epson')
            ->assertSee('PAT-001')
            ->assertSee('Disponível')
            ->assertSee('Livre no momento');
    }

    public function test_equipamento_inexistente_retorna_404(): void
    {
        $this->get('/equipamentos/999')->assertNotFound();
        $this->get('/equipamentos/abc')->assertNotFound();
    }

    public function test_edicao_mantem_o_proprio_patrimonio_sem_acusar_duplicidade(): void
    {
        $equipamento = $this->criarEquipamento();

        $this->put('/equipamentos/' . $equipamento->id, [
            'nome' => 'Projetor Epson X',
            'numero_patrimonio' => 'PAT-001',
            'categoria' => 'audiovisual',
            'situacao' => Equipamento::SITUACAO_EM_MANUTENCAO,
        ])->assertSessionHasNoErrors()->assertRedirect('/equipamentos/' . $equipamento->id);

        $this->assertSame('Projetor Epson X', $equipamento->fresh()->nome);
        $this->assertSame(Equipamento::SITUACAO_EM_MANUTENCAO, $equipamento->fresh()->situacao);
    }

    public function test_edicao_rejeita_patrimonio_de_outro_equipamento(): void
    {
        $this->criarEquipamento();
        $outro = $this->criarEquipamento(['nome' => 'Notebook', 'numero_patrimonio' => 'NB-1', 'categoria' => 'informatica']);

        $this->put('/equipamentos/' . $outro->id, [
            'nome' => 'Notebook',
            'numero_patrimonio' => 'PAT-001',
            'categoria' => 'informatica',
            'situacao' => Equipamento::SITUACAO_DISPONIVEL,
        ])->assertSessionHasErrors('numero_patrimonio');

        $this->assertSame('NB-1', $outro->fresh()->numero_patrimonio);
    }

    public function test_edicao_exige_situacao_valida(): void
    {
        $equipamento = $this->criarEquipamento();

        $this->put('/equipamentos/' . $equipamento->id, [
            'nome' => 'Projetor Epson',
            'numero_patrimonio' => 'PAT-001',
            'categoria' => 'audiovisual',
        ])->assertSessionHasErrors('situacao');
    }

    public function test_nao_muda_situacao_com_reservas_vigentes(): void
    {
        $this->travelTo(Carbon::parse('2030-01-10 08:00'));
        $equipamento = $this->criarEquipamento();
        $equipamento->reservas()->create(['responsavel' => 'Ana', 'inicio' => '2030-01-11 09:00', 'fim' => '2030-01-11 10:00']);

        $this->put('/equipamentos/' . $equipamento->id, [
            'nome' => 'Projetor Epson',
            'numero_patrimonio' => 'PAT-001',
            'categoria' => 'audiovisual',
            'situacao' => Equipamento::SITUACAO_EM_MANUTENCAO,
        ])->assertSessionHasErrors('situacao');

        $this->assertSame(Equipamento::SITUACAO_DISPONIVEL, $equipamento->fresh()->situacao);
    }

    public function test_reservas_encerradas_ou_canceladas_nao_impedem_mudanca_de_situacao(): void
    {
        $this->travelTo(Carbon::parse('2030-01-10 08:00'));
        $equipamento = $this->criarEquipamento();
        $equipamento->reservas()->create(['responsavel' => 'Ana', 'inicio' => '2030-01-09 09:00', 'fim' => '2030-01-09 10:00']);
        $cancelada = $equipamento->reservas()->create(['responsavel' => 'Bia', 'inicio' => '2030-01-11 09:00', 'fim' => '2030-01-11 10:00']);
        $cancelada->cancelada_em = now();
        $cancelada->save();

        $this->put('/equipamentos/' . $equipamento->id, [
            'nome' => 'Projetor Epson',
            'numero_patrimonio' => 'PAT-001',
            'categoria' => 'audiovisual',
            'situacao' => Equipamento::SITUACAO_FORA_DE_USO,
        ])->assertSessionHasNoErrors();

        $this->assertSame(Equipamento::SITUACAO_FORA_DE_USO, $equipamento->fresh()->situacao);
    }

    public function test_confirmacao_de_exclusao_nao_altera_dados(): void
    {
        $equipamento = $this->criarEquipamento();

        $this->get('/equipamentos/' . $equipamento->id . '/excluir')
            ->assertOk()
            ->assertSee('Excluir equipamento?')
            ->assertSee('Sim, excluir equipamento');

        $this->assertModelExists($equipamento);
    }

    public function test_exclui_equipamento_sem_reservas(): void
    {
        $equipamento = $this->criarEquipamento();

        $this->delete('/equipamentos/' . $equipamento->id)->assertRedirect('/equipamentos');

        $this->assertModelMissing($equipamento);
    }

    public function test_nao_exclui_equipamento_com_historico_de_reservas(): void
    {
        $this->travelTo(Carbon::parse('2030-01-10 08:00'));
        $equipamento = $this->criarEquipamento();
        $reserva = $equipamento->reservas()->create(['responsavel' => 'Ana', 'inicio' => '2030-01-09 09:00', 'fim' => '2030-01-09 10:00']);
        $reserva->cancelada_em = now();
        $reserva->save();

        $this->delete('/equipamentos/' . $equipamento->id)
            ->assertRedirect('/equipamentos/' . $equipamento->id)
            ->assertSessionHas('erro');

        $this->assertModelExists($equipamento);
    }

    public function test_chave_estrangeira_impede_excluir_equipamento_com_reservas(): void
    {
        $equipamento = $this->criarEquipamento();
        Reserva::create(['equipamento_id' => $equipamento->id, 'responsavel' => 'Ana', 'inicio' => '2030-01-09 09:00', 'fim' => '2030-01-09 10:00']);

        $this->expectException(QueryException::class);
        $equipamento->delete();
    }

    public function test_dados_do_usuario_sao_exibidos_com_escape(): void
    {
        $equipamento = $this->criarEquipamento(['nome' => '<script>alert(1)</script>']);

        $this->get('/equipamentos/' . $equipamento->id)
            ->assertOk()
            ->assertDontSee('<script>alert(1)</script>', false)
            ->assertSee('&lt;script&gt;alert(1)&lt;/script&gt;', false);
    }
}
