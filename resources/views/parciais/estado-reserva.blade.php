{{-- Estado da reserva: sempre com ícone e texto, nunca apenas cor. --}}
@php
    $estilos = [
        \App\Models\Reserva::ESTADO_FUTURA => ['bg-pastel text-floresta ring-1 ring-floresta/30', 'relogio'],
        \App\Models\Reserva::ESTADO_EM_ANDAMENTO => ['bg-limao text-floresta', 'play'],
        \App\Models\Reserva::ESTADO_ENCERRADA => ['bg-white text-ameixa ring-1 ring-ameixa/40', 'confirmado'],
        \App\Models\Reserva::ESTADO_CANCELADA => ['bg-ameixa text-white', 'bloqueado'],
    ];
    [$classes, $icone] = $estilos[$reserva->estado()];
@endphp
<span class="selo {{ $classes }}">
    @include('parciais.icone', ['nome' => $icone, 'classe' => 'h-4 w-4'])
    <span>{{ $reserva->rotuloEstado() }}</span>
</span>
