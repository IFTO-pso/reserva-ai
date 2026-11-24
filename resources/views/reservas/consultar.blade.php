@extends('layouts.principal')

@section('titulo', 'Reserva #' . $reserva->id)

@section('conteudo')
    <section class="mx-auto grid max-w-7xl gap-6 px-4 pt-8 sm:px-8 lg:grid-cols-principal">
        <div class="relative overflow-hidden rounded-[2rem] bg-floresta p-6 text-white sm:p-10">
            <a href="{{ route('reservas.listar') }}" class="inline-flex items-center gap-1 font-subtitulo text-sm font-semibold text-limao underline-offset-4 hover:underline">
                @include('parciais.icone', ['nome' => 'voltar', 'classe' => 'h-4 w-4']) Voltar para as reservas
            </a>
            <div class="mt-6 flex flex-wrap items-center gap-3">
                <p class="font-subtitulo text-sm font-semibold uppercase tracking-widest text-limao">Reserva #{{ $reserva->id }}</p>
                @include('parciais.estado-reserva')
            </div>
            <h1 class="mt-2 break-words text-4xl sm:text-6xl">{{ $reserva->equipamento->nome }}</h1>

            <dl class="mt-8 grid gap-4 sm:grid-cols-2">
                <div class="rounded-2xl bg-white/10 p-4">
                    <dt class="font-subtitulo text-sm font-semibold text-pastel">Início</dt>
                    <dd class="mt-1 text-lg">{{ $reserva->inicio->format('d/m/Y H:i') }}</dd>
                </div>
                <div class="rounded-2xl bg-white/10 p-4">
                    <dt class="font-subtitulo text-sm font-semibold text-pastel">Fim</dt>
                    <dd class="mt-1 text-lg">{{ $reserva->fim->format('d/m/Y H:i') }}</dd>
                </div>
                <div class="rounded-2xl bg-white/10 p-4">
                    <dt class="font-subtitulo text-sm font-semibold text-pastel">Responsável</dt>
                    <dd class="mt-1 break-words text-lg">{{ $reserva->responsavel }}</dd>
                </div>
                <div class="rounded-2xl bg-white/10 p-4">
                    <dt class="font-subtitulo text-sm font-semibold text-pastel">Registrada em</dt>
                    <dd class="mt-1 text-lg">{{ $reserva->created_at?->format('d/m/Y H:i') }}</dd>
                </div>
                @if ($reserva->cancelada_em)
                    <div class="rounded-2xl bg-ameixa p-4 sm:col-span-2">
                        <dt class="font-subtitulo text-sm font-semibold text-pastel">Cancelada em</dt>
                        <dd class="mt-1 text-lg">{{ $reserva->cancelada_em->format('d/m/Y H:i') }}</dd>
                    </div>
                @endif
            </dl>

            @if ($reserva->podeSerCancelada())
                <a href="{{ route('reservas.confirmar-cancelamento', $reserva) }}" class="botao mt-8 border-white text-white hover:bg-white/10">
                    @include('parciais.icone', ['nome' => 'bloqueado'])
                    Cancelar reserva
                </a>
            @endif
        </div>

        <div class="rounded-[2rem] bg-pastel p-6 sm:p-10">
            <div class="flex items-center gap-3">
                <span class="caixa-icone bg-floresta text-limao">@include('parciais.icone', ['nome' => 'caixa'])</span>
                <h2 class="text-3xl text-floresta">Equipamento</h2>
            </div>
            @php($equipamento = $reserva->equipamento)
            <dl class="mt-6 grid gap-3 rounded-2xl bg-white p-5">
                <div><dt class="inline font-subtitulo font-semibold text-floresta">Patrimônio:</dt> <dd class="inline font-mono">{{ $equipamento->numero_patrimonio }}</dd></div>
                <div><dt class="inline font-subtitulo font-semibold text-floresta">Categoria:</dt> <dd class="inline">{{ $equipamento->rotuloCategoria() }}</dd></div>
                <div><dt class="inline font-subtitulo font-semibold text-floresta">Situação atual:</dt> <dd class="inline">@include('parciais.situacao')</dd></div>
            </dl>
            <a href="{{ route('equipamentos.consultar', $equipamento) }}" class="botao-secundario mt-6">
                @include('parciais.icone', ['nome' => 'ver'])
                Ver equipamento
            </a>
        </div>
    </section>
@endsection
