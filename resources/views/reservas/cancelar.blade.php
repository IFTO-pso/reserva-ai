@extends('layouts.principal')

@section('titulo', 'Cancelar reserva #' . $reserva->id)

@section('conteudo')
    <section class="mx-auto grid max-w-7xl gap-6 px-4 pt-8 sm:px-8 lg:grid-cols-principal">
        <div class="cartao border-2 border-ameixa">
            <span class="caixa-icone bg-ameixa text-white">@include('parciais.icone', ['nome' => 'bloqueado'])</span>
            <h1 class="mt-4 text-4xl text-ameixa sm:text-5xl">Cancelar reserva?</h1>

            <dl class="mt-6 grid gap-3 rounded-2xl bg-pastel p-5">
                <div><dt class="inline font-subtitulo font-semibold text-floresta">Equipamento:</dt> <dd class="inline">{{ $reserva->equipamento->nome }} ({{ $reserva->equipamento->numero_patrimonio }})</dd></div>
                <div><dt class="inline font-subtitulo font-semibold text-floresta">Responsável:</dt> <dd class="inline">{{ $reserva->responsavel }}</dd></div>
                <div><dt class="inline font-subtitulo font-semibold text-floresta">Período:</dt> <dd class="inline">{{ $reserva->inicio->format('d/m/Y H:i') }} até {{ $reserva->fim->format('d/m/Y H:i') }}</dd></div>
                <div><dt class="inline font-subtitulo font-semibold text-floresta">Estado:</dt> <dd class="inline">@include('parciais.estado-reserva')</dd></div>
            </dl>

            @if ($reserva->podeSerCancelada())
                <p class="mt-6 text-lg">O período será liberado para novas reservas. O registro continuará no histórico como <strong>cancelado</strong>.</p>

                <form method="POST" action="{{ route('reservas.cancelar', $reserva) }}" class="mt-8 flex flex-col gap-3 sm:flex-row">
                    @csrf
                    @method('PATCH')
                    <button type="submit" class="botao-perigo">
                        @include('parciais.icone', ['nome' => 'bloqueado'])
                        Sim, cancelar reserva
                    </button>
                    <a href="{{ route('reservas.consultar', $reserva) }}" class="botao-secundario">Não, voltar</a>
                </form>
            @else
                <p role="alert" class="mt-6 flex items-start gap-2 rounded-2xl bg-lima/40 p-4 font-medium">
                    @include('parciais.icone', ['nome' => 'alerta', 'classe' => 'mt-0.5 h-5 w-5 shrink-0'])
                    Somente reservas futuras ou em andamento podem ser canceladas.
                </p>
                <a href="{{ route('reservas.consultar', $reserva) }}" class="botao-secundario mt-8">Voltar para a reserva</a>
            @endif
        </div>

        <div class="hidden items-center justify-center rounded-[2rem] bg-pastel p-10 lg:flex">
            @include('parciais.ilustracao', ['classe' => 'h-auto w-full max-w-sm'])
        </div>
    </section>
@endsection
