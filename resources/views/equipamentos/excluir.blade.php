@extends('layouts.principal')

@section('titulo', 'Excluir ' . $equipamento->nome)

@section('conteudo')
    <section class="mx-auto grid max-w-7xl gap-6 px-4 pt-8 sm:px-8 lg:grid-cols-principal">
        <div class="cartao border-2 border-ameixa">
            <span class="caixa-icone bg-ameixa text-white">@include('parciais.icone', ['nome' => 'lixeira'])</span>
            <h1 class="mt-4 text-4xl text-ameixa sm:text-5xl">Excluir equipamento?</h1>

            <dl class="mt-6 grid gap-3 rounded-2xl bg-pastel p-5">
                <div><dt class="inline font-subtitulo font-semibold text-floresta">Nome:</dt> <dd class="inline">{{ $equipamento->nome }}</dd></div>
                <div><dt class="inline font-subtitulo font-semibold text-floresta">Patrimônio:</dt> <dd class="inline font-mono">{{ $equipamento->numero_patrimonio }}</dd></div>
                <div><dt class="inline font-subtitulo font-semibold text-floresta">Categoria:</dt> <dd class="inline">{{ $equipamento->rotuloCategoria() }}</dd></div>
                <div><dt class="inline font-subtitulo font-semibold text-floresta">Situação:</dt> <dd class="inline">@include('parciais.situacao')</dd></div>
            </dl>

            @if ($possuiReservas)
                <p role="alert" class="mt-6 flex items-start gap-2 rounded-2xl bg-lima/40 p-4 font-medium">
                    @include('parciais.icone', ['nome' => 'alerta', 'classe' => 'mt-0.5 h-5 w-5 shrink-0'])
                    Este equipamento possui histórico de reservas e não pode ser excluído. Se ele não será mais utilizado, altere a situação para "Fora de uso".
                </p>
                <div class="mt-8 flex flex-col gap-3 sm:flex-row">
                    <a href="{{ route('equipamentos.editar', $equipamento) }}" class="botao-primario">Alterar situação</a>
                    <a href="{{ route('equipamentos.consultar', $equipamento) }}" class="botao-secundario">Voltar</a>
                </div>
            @else
                <p class="mt-6 text-lg">Esta ação é <strong>permanente</strong> e não poderá ser desfeita. Confirme para concluir a exclusão.</p>

                <form method="POST" action="{{ route('equipamentos.excluir', $equipamento) }}" class="mt-8 flex flex-col gap-3 sm:flex-row">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="botao-perigo">
                        @include('parciais.icone', ['nome' => 'lixeira'])
                        Sim, excluir equipamento
                    </button>
                    <a href="{{ route('equipamentos.consultar', $equipamento) }}" class="botao-secundario">Não, voltar</a>
                </form>
            @endif
        </div>

        <div class="hidden items-center justify-center rounded-[2rem] bg-pastel p-10 lg:flex">
            @include('parciais.ilustracao', ['classe' => 'h-auto w-full max-w-sm'])
        </div>
    </section>
@endsection
