{{-- Ícones SVG minimalistas próprios. Uso: @include('parciais.icone', ['nome' => 'mais']) --}}
<svg class="{{ $classe ?? 'h-5 w-5' }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false">
    @switch($nome)
        @case('caixa')
            <path d="M3.5 8 12 3.5 20.5 8v8L12 20.5 3.5 16z" /><path d="M3.5 8 12 12.5 20.5 8M12 12.5v8" />
            @break
        @case('mais')
            <path d="M12 5v14M5 12h14" />
            @break
        @case('ver')
            <path d="M2.5 12S6 5.5 12 5.5 21.5 12 21.5 12 18 18.5 12 18.5 2.5 12 2.5 12z" /><circle cx="12" cy="12" r="3" />
            @break
        @case('editar')
            <path d="M4 20h4L19 9l-4-4L4 16z" /><path d="m13.5 6.5 4 4" />
            @break
        @case('lixeira')
            <path d="M4 7h16M9 7V4.5h6V7M6.5 7l1 13h9l1-13" /><path d="M10 11v5M14 11v5" />
            @break
        @case('calendario')
            <rect x="3.5" y="5" width="17" height="15" rx="3" /><path d="M3.5 10h17M8 3v4M16 3v4" />
            @break
        @case('relogio')
            <circle cx="12" cy="12" r="8.5" /><path d="M12 7.5V12l3 2" />
            @break
        @case('confirmado')
            <circle cx="12" cy="12" r="8.5" /><path d="m8.5 12 2.5 2.5 4.5-5" />
            @break
        @case('chave')
            <path d="M14.5 4.5a4.5 4.5 0 0 0-4.2 6.1L4 17v3h3l6.4-6.3a4.5 4.5 0 0 0 5.6-5.6l-2.7 2.7-2.4-.6-.6-2.4z" />
            @break
        @case('bloqueado')
            <circle cx="12" cy="12" r="8.5" /><path d="m6 6 12 12" />
            @break
        @case('alerta')
            <path d="M12 3.5 21 19.5H3z" /><path d="M12 10v4M12 17h.01" />
            @break
        @case('busca')
            <circle cx="11" cy="11" r="6.5" /><path d="m16 16 4.5 4.5" />
            @break
        @case('voltar')
            <path d="M15 5l-7 7 7 7" />
            @break
        @case('play')
            <circle cx="12" cy="12" r="8.5" /><path d="m10 8.5 5 3.5-5 3.5z" />
            @break
        @default
            <circle cx="12" cy="12" r="8.5" />
    @endswitch
</svg>
