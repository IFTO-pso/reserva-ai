<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('titulo') · {{ config('app.name') }}</title>
    <link rel="preload" href="/fonts/baloo-2.woff2" as="font" type="font/woff2" crossorigin>
    <link rel="stylesheet" href="{{ mix('css/app.css') }}">
</head>
<body class="min-h-screen bg-white">
    <a href="#conteudo" class="sr-only focus:not-sr-only focus:absolute focus:left-4 focus:top-4 focus:z-50 focus:rounded-xl focus:bg-lima focus:px-4 focus:py-2 focus:font-bold focus:text-floresta">
        Pular para o conteúdo
    </a>

    <header class="relative overflow-hidden bg-floresta text-white">
        <svg class="linha-decorativa -right-10 -top-10 h-40 w-40 text-limao/30" viewBox="0 0 100 100" fill="none" aria-hidden="true">
            <circle cx="50" cy="50" r="40" stroke="currentColor" stroke-width="1.5" />
            <circle cx="50" cy="50" r="28" stroke="currentColor" stroke-width="1.5" stroke-dasharray="4 6" />
        </svg>
        <div class="relative mx-auto flex max-w-7xl flex-wrap items-center justify-between gap-4 px-4 py-4 sm:px-8">
            <a href="{{ route('equipamentos.listar') }}" class="flex items-center gap-3 rounded-xl">
                <span class="caixa-icone bg-limao text-floresta">
                    @include('parciais.icone', ['nome' => 'caixa'])
                </span>
                <span class="font-titulo text-2xl font-extrabold leading-none">Reserva<span class="text-limao">AI</span></span>
            </a>

            <nav aria-label="Navegação principal">
                <ul class="flex flex-wrap items-center gap-2 font-subtitulo text-sm font-semibold">
                    <li>
                        <a href="{{ route('equipamentos.listar') }}"
                           class="inline-flex min-h-[44px] items-center rounded-xl px-4 py-2 {{ request()->routeIs('equipamentos.*') ? 'bg-pastel text-floresta' : 'text-white hover:bg-white/10' }}"
                           @if (request()->routeIs('equipamentos.*')) aria-current="page" @endif>
                            Equipamentos
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('reservas.listar') }}"
                           class="inline-flex min-h-[44px] items-center rounded-xl px-4 py-2 {{ request()->routeIs('reservas.*') ? 'bg-pastel text-floresta' : 'text-white hover:bg-white/10' }}"
                           @if (request()->routeIs('reservas.*')) aria-current="page" @endif>
                            Reservas
                        </a>
                    </li>
                </ul>
            </nav>
        </div>
    </header>

    <main id="conteudo" tabindex="-1">
        @if (session('sucesso') || session('erro'))
            <div class="mx-auto max-w-7xl px-4 pt-6 sm:px-8">
                @if (session('sucesso'))
                    <div role="status" class="flex items-start gap-3 rounded-2xl bg-limao px-5 py-4 text-floresta">
                        <span class="mt-0.5">@include('parciais.icone', ['nome' => 'confirmado'])</span>
                        <p class="font-medium"><strong class="font-subtitulo">Sucesso:</strong> {{ session('sucesso') }}</p>
                    </div>
                @endif
                @if (session('erro'))
                    <div role="alert" class="flex items-start gap-3 rounded-2xl bg-ameixa px-5 py-4 text-white">
                        <span class="mt-0.5">@include('parciais.icone', ['nome' => 'alerta'])</span>
                        <p class="font-medium"><strong class="font-subtitulo">Atenção:</strong> {{ session('erro') }}</p>
                    </div>
                @endif
            </div>
        @endif

        @yield('conteudo')
    </main>

    <footer class="mt-16 bg-ameixa text-white">
        <div class="mx-auto flex max-w-7xl flex-col gap-2 px-4 py-6 text-sm sm:flex-row sm:items-center sm:justify-between sm:px-8">
            <p>{{ config('app.name') }} · gestão e reserva de equipamentos</p>
            <p>Horários no fuso de Brasília (America/Sao_Paulo).</p>
        </div>
    </footer>
</body>
</html>
