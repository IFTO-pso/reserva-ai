<?php

namespace Tests\Feature;

use App\Http\Middleware\VerifyCsrfToken;
use Tests\TestCase;

/**
 * Testes que não acessam o banco: proteção CSRF e validação de campos obrigatórios no servidor.
 */
class SegurancaFormulariosTest extends TestCase
{
    private function ativarVerificacaoCsrf(): void
    {
        // O Laravel desativa a verificação CSRF em testes; aqui ela é reativada explicitamente.
        $this->app->instance(VerifyCsrfToken::class, new class($this->app, $this->app['encrypter']) extends VerifyCsrfToken {
            protected function runningUnitTests()
            {
                return false;
            }
        });
    }

    public function test_gravacoes_sem_token_csrf_sao_rejeitadas(): void
    {
        $this->ativarVerificacaoCsrf();

        $this->post('/equipamentos', ['nome' => 'X'])->assertStatus(419);
        $this->put('/equipamentos/1', ['nome' => 'X'])->assertStatus(419);
        $this->delete('/equipamentos/1')->assertStatus(419);
        $this->post('/reservas', ['responsavel' => 'X'])->assertStatus(419);
        $this->patch('/reservas/1/cancelar')->assertStatus(419);
    }

    public function test_gravacao_com_token_csrf_invalido_e_rejeitada(): void
    {
        $this->ativarVerificacaoCsrf();

        $this->withSession(['_token' => 'token-da-sessao'])
            ->post('/equipamentos', ['_token' => 'token-forjado', 'nome' => 'X'])
            ->assertStatus(419);
    }

    public function test_cadastro_de_equipamento_exige_campos_obrigatorios(): void
    {
        $this->from('/equipamentos/novo')
            ->post('/equipamentos', [])
            ->assertRedirect('/equipamentos/novo')
            ->assertSessionHasErrors([
                'nome' => 'O campo nome é obrigatório.',
                'numero_patrimonio' => 'O campo número de patrimônio é obrigatório.',
                'categoria' => 'O campo categoria é obrigatório.',
            ])
            ->assertSessionDoesntHaveErrors('situacao');
    }

    public function test_reserva_exige_campos_obrigatorios(): void
    {
        $this->from('/reservas/nova')
            ->post('/reservas', [])
            ->assertRedirect('/reservas/nova')
            ->assertSessionHasErrors([
                'equipamento_id' => 'O campo equipamento é obrigatório.',
                'responsavel' => 'O campo responsável é obrigatório.',
                'inicio' => 'O campo início é obrigatório.',
                'fim' => 'O campo fim é obrigatório.',
            ]);
    }

    public function test_formulario_de_cadastro_inclui_token_csrf_e_situacao_padrao(): void
    {
        $this->get('/equipamentos/novo')
            ->assertOk()
            ->assertSee('name="_token"', false)
            ->assertSee('<option value="disponivel" selected>Disponível</option>', false);
    }
}
