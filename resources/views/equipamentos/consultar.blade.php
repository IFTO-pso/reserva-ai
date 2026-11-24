@extends('layouts.principal')

@section('titulo', $equipamento->nome)

@section('conteudo')
    <section class="mx-auto grid max-w-7xl gap-6 px-4 pt-8 sm:px-8 lg:grid-cols-principal">
        <div class="relative overflow-hidden rounded-[2rem] bg-floresta p-6 text-white sm:p-10">
            <svg class="linha-decorativa right-6 top-6 h-20 w-20 text-limao/40" viewBox="0 0 100 100" fill="none" aria-hidden="true">
                <path d="M10 50h80M50 10v80" stroke="currentColor" stroke-width="2" stroke-dasharray="3 7" />
            </svg>
            <a href="{{ route('equipamentos.listar') }}" class="relative inline-flex items-center gap-1 font-subtitulo text-sm font-semibold text-limao underline-offset-4 hover:underline">
                @include('parciais.icone', ['nome' => 'voltar', 'classe' => 'h-4 w-4']) Voltar para a lista
            </a>
            <p class="mt-6 font-subtitulo text-sm font-semibold uppercase tracking-widest text-limao">{{ $equipamento->rotuloCategoria() }}</p>
            <h1 class="mt-2 break-words text-4xl sm:text-6xl">{{ $equipamento->nome }}</h1>

            <dl class="relative mt-8 grid gap-4 sm:grid-cols-2">
                <div class="rounded-2xl bg-white/10 p-4">
                    <dt class="font-subtitulo text-sm font-semibold text-pastel">Número de patrimônio</dt>
                    <dd class="mt-1 break-all font-mono text-lg">{{ $equipamento->numero_patrimonio }}</dd>
                </div>
                <div class="rounded-2xl bg-white/10 p-4">
                    <dt class="font-subtitulo text-sm font-semibold text-pastel">Categoria</dt>
                    <dd class="mt-1 text-lg">{{ $equipamento->rotuloCategoria() }}</dd>
                </div>
                <div class="rounded-2xl bg-white/10 p-4">
                    <dt class="font-subtitulo text-sm font-semibold text-pastel">Situação</dt>
                    <dd class="mt-2">@include('parciais.situacao')</dd>
                </div>
                <div class="rounded-2xl bg-white/10 p-4">
                    <dt class="font-subtitulo text-sm font-semibold text-pastel">Ocupação agora</dt>
                    <dd class="mt-1">
                        @if ($reservaEmAndamento)
                            Em uso por <strong>{{ $reservaEmAndamento->responsavel }}</strong> até {{ $reservaEmAndamento->fim->format('d/m/Y H:i') }}
                        @else
                            Livre no momento
                        @endif
                    </dd>
                </div>
            </dl>

            <div class="relative mt-8 flex flex-col gap-3 sm:flex-row sm:flex-wrap">
                @if ($equipamento->estaDisponivel())
                    <a href="{{ route('reservas.criar', ['equipamento' => $equipamento->id]) }}" class="botao-primario">
                        @include('parciais.icone', ['nome' => 'calendario'])
                        Reservar este equipamento
                    </a>
                @endif
                <a href="{{ route('equipamentos.editar', $equipamento) }}" class="botao border-white text-white hover:bg-white/10">
                    @include('parciais.icone', ['nome' => 'editar'])
                    Editar
                </a>
                <a href="{{ route('equipamentos.confirmar-exclusao', $equipamento) }}" class="botao border-white text-white hover:bg-white/10">
                    @include('parciais.icone', ['nome' => 'lixeira'])
                    Excluir
                </a>
            </div>
        </div>

        <div class="rounded-[2rem] bg-pastel p-6 sm:p-10">
            <div class="flex items-center gap-3">
                <span class="caixa-icone bg-floresta text-limao">@include('parciais.icone', ['nome' => 'calendario'])</span>
                <h2 class="text-3xl text-floresta">Próximas reservas</h2>
            </div>

            @if ($proximasReservas->isEmpty())
                <p class="mt-6 rounded-2xl bg-white p-5">Não há reservas futuras ou em andamento para este equipamento.</p>
            @else
                <ul class="mt-6 grid gap-3">
                    @foreach ($proximasReservas as $reserva)
                        <li class="rounded-2xl bg-white p-4">
                            <div class="flex flex-wrap items-center justify-between gap-2">
                                <p class="font-subtitulo font-semibold text-floresta">{{ $reserva->inicio->format('d/m/Y H:i') }} – {{ $reserva->fim->format('d/m/Y H:i') }}</p>
                                @include('parciais.estado-reserva')
                            </div>
                            <p class="mt-1 text-sm">Responsável: {{ $reserva->responsavel }}</p>
                            <a href="{{ route('reservas.consultar', $reserva) }}" class="mt-2 inline-block text-sm font-semibold text-floresta underline underline-offset-4">Ver reserva</a>
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>
    </section>
@endsection
