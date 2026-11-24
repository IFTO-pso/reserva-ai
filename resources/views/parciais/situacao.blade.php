{{-- Situação do equipamento: sempre com ícone e texto, nunca apenas cor. --}}
@php
    $estilos = [
        \App\Models\Equipamento::SITUACAO_DISPONIVEL => ['bg-limao text-floresta', 'confirmado'],
        \App\Models\Equipamento::SITUACAO_EM_MANUTENCAO => ['bg-lima text-ameixa ring-1 ring-ameixa/40', 'chave'],
        \App\Models\Equipamento::SITUACAO_FORA_DE_USO => ['bg-ameixa text-white', 'bloqueado'],
    ];
    [$classes, $icone] = $estilos[$equipamento->situacao] ?? ['bg-white text-ameixa ring-1 ring-ameixa', 'alerta'];
@endphp
<span class="selo {{ $classes }}">
    @include('parciais.icone', ['nome' => $icone, 'classe' => 'h-4 w-4'])
    <span>{{ $equipamento->rotuloSituacao() }}</span>
</span>
