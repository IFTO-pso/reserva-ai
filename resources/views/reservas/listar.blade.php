@extends('layouts.principal')

@section('titulo', 'Reservas')

@section('conteudo')
    <section class="mx-auto grid max-w-7xl gap-6 px-4 pt-8 sm:px-8 lg:grid-cols-principal">
        <div class="relative overflow-hidden rounded-[2rem] bg-pastel p-6 sm:p-10">
            <svg class="linha-decorativa bottom-6 right-6 h-24 w-24 text-floresta/20" viewBox="0 0 100 100" fill="none" aria-hidden="true">
                <path d="M10 90 90 10M30 90 90 30M50 90 90 50" stroke="currentColor" stroke-width="2" />
            </svg>
            <p class="font-subtitulo text-sm font-semibold uppercase tracking-widest text-floresta">Agenda</p>
            <h1 class="mt-3 text-5xl text-floresta sm:text-6xl">Reservas</h1>
            <p class="mt-4 max-w-md text-lg">Acompanhe quem está com cada equipamento e em qual período.</p>
            <a href="{{ route('reservas.criar') }}" class="botao-primario relative mt-8">
                @include('parciais.icone', ['nome' => 'calendario'])
                Nova reserva
            </a>
        </div>

        <div class="flex flex-col justify-center gap-4 rounded-[2rem] bg-floresta p-6 text-white sm:p-10">
            <h2 class="text-3xl text-limao">Como funciona</h2>
            <ul class="grid gap-3 text-pastel">
                <li class="flex gap-3"><span class="caixa-icone h-9 w-9 bg-limao text-floresta">@include('parciais.icone', ['nome' => 'confirmado', 'classe' => 'h-4 w-4'])</span><span>Somente equipamentos disponíveis podem ser reservados.</span></li>
                <li class="flex gap-3"><span class="caixa-icone h-9 w-9 bg-limao text-floresta">@include('parciais.icone', ['nome' => 'relogio', 'classe' => 'h-4 w-4'])</span><span>Períodos não podem se sobrepor; reservas consecutivas são permitidas.</span></li>
                <li class="flex gap-3"><span class="caixa-icone h-9 w-9 bg-limao text-floresta">@include('parciais.icone', ['nome' => 'bloqueado', 'classe' => 'h-4 w-4'])</span><span>Cancelamentos liberam o período e ficam registrados no histórico.</span></li>
            </ul>
        </div>
    </section>

    <section aria-labelledby="titulo-reservas" class="mx-auto max-w-7xl px-4 pt-10 sm:px-8">
        <h2 id="titulo-reservas" class="text-3xl text-floresta sm:text-4xl">Todas as reservas</h2>

        @if ($reservas->isEmpty())
            <div class="mt-6 flex flex-col items-start gap-4 rounded-[2rem] border-2 border-dashed border-floresta/40 bg-white p-8">
                <span class="caixa-icone bg-pastel text-floresta">@include('parciais.icone', ['nome' => 'calendario'])</span>
                <p class="text-lg font-medium">A lista de reservas está vazia. Nenhuma reserva foi registrada ainda.</p>
                <a href="{{ route('reservas.criar') }}" class="botao-primario">Fazer a primeira reserva</a>
            </div>
        @else
            <ul class="mt-6 grid gap-4 md:grid-cols-2">
                @foreach ($reservas as $reserva)
                    <li class="cartao flex flex-col gap-3 border-2 border-pastel">
                        <div class="flex flex-wrap items-start justify-between gap-2">
                            <h3 class="text-lg leading-tight text-floresta">{{ $reserva->equipamento->nome }}</h3>
                            @include('parciais.estado-reserva')
                        </div>
                        <dl class="grid gap-2 text-sm sm:grid-cols-2">
                            <div>
                                <dt class="font-subtitulo font-semibold text-floresta">Período</dt>
                                <dd>{{ $reserva->inicio->format('d/m/Y H:i') }} até {{ $reserva->fim->format('d/m/Y H:i') }}</dd>
                            </div>
                            <div>
                                <dt class="font-subtitulo font-semibold text-floresta">Responsável</dt>
                                <dd class="break-words">{{ $reserva->responsavel }}</dd>
                            </div>
                            <div>
                                <dt class="font-subtitulo font-semibold text-floresta">Patrimônio</dt>
                                <dd class="break-all font-mono">{{ $reserva->equipamento->numero_patrimonio }}</dd>
                            </div>
                        </dl>
                        <div class="flex flex-wrap gap-2">
                            <a href="{{ route('reservas.consultar', $reserva) }}" class="botao-secundario px-3">
                                @include('parciais.icone', ['nome' => 'ver']) Ver
                            </a>
                            @if ($reserva->podeSerCancelada())
                                <a href="{{ route('reservas.confirmar-cancelamento', $reserva) }}" class="botao-secundario px-3">
                                    @include('parciais.icone', ['nome' => 'bloqueado']) Cancelar
                                </a>
                            @endif
                        </div>
                    </li>
                @endforeach
            </ul>

            <div class="mt-6">
                {{ $reservas->links('parciais.paginacao') }}
            </div>
        @endif
    </section>
@endsection
