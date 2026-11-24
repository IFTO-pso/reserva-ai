@extends('layouts.principal')

@section('titulo', 'Editar ' . $equipamento->nome)

@section('conteudo')
    <section class="mx-auto grid max-w-7xl gap-6 px-4 pt-8 sm:px-8 lg:grid-cols-principal">
        <div class="cartao border-2 border-pastel">
            <a href="{{ route('equipamentos.consultar', $equipamento) }}" class="inline-flex items-center gap-1 font-subtitulo text-sm font-semibold text-floresta underline-offset-4 hover:underline">
                @include('parciais.icone', ['nome' => 'voltar', 'classe' => 'h-4 w-4']) Voltar para o equipamento
            </a>
            <h1 class="mt-4 text-4xl text-floresta sm:text-5xl">Editar equipamento</h1>
            <p class="mb-8 mt-3 text-lg">Altere os dados de <strong>{{ $equipamento->nome }}</strong>. O número de patrimônio atual pode ser mantido.</p>

            <form method="POST" action="{{ route('equipamentos.atualizar', $equipamento) }}" novalidate>
                @csrf
                @method('PUT')
                @include('equipamentos.formulario')

                <div class="mt-8 flex flex-col gap-3 sm:flex-row">
                    <button type="submit" class="botao-primario">
                        @include('parciais.icone', ['nome' => 'confirmado'])
                        Salvar alterações
                    </button>
                    <a href="{{ route('equipamentos.consultar', $equipamento) }}" class="botao-secundario">Cancelar</a>
                </div>
            </form>
        </div>

        @include('equipamentos.painel-lateral')
    </section>
@endsection
