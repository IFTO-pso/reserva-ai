@extends('layouts.principal')

@section('titulo', 'Equipamentos')

@section('conteudo')
    <section class="mx-auto grid max-w-7xl gap-6 px-4 pt-8 sm:px-8 lg:grid-cols-principal">
        <div class="relative overflow-hidden rounded-[2rem] bg-floresta p-6 text-white sm:p-10">
            <svg class="linha-decorativa bottom-6 right-6 h-24 w-24 text-limao/40" viewBox="0 0 100 100" fill="none" aria-hidden="true">
                <path d="M10 90 90 10M30 90 90 30M50 90 90 50" stroke="currentColor" stroke-width="2" />
            </svg>
            <p class="font-subtitulo text-sm font-semibold uppercase tracking-widest text-limao">Inventário</p>
            <h1 class="mt-3 text-5xl sm:text-6xl">Equipamentos</h1>
            <p class="mt-4 max-w-md text-lg text-pastel">Cadastre, consulte e acompanhe a situação de cada equipamento da equipe.</p>

            <form method="GET" action="{{ route('equipamentos.listar') }}" role="search" class="relative mt-8 grid gap-3 sm:grid-cols-[1fr_auto_auto]">
                <div>
                    <label for="busca" class="mb-1 block font-subtitulo text-sm font-semibold text-pastel">Buscar por nome ou patrimônio</label>
                    <input id="busca" name="busca" type="search" maxlength="100" value="{{ $filtros['busca'] ?? '' }}" class="campo" placeholder="Ex.: Projetor ou PAT-001">
                </div>
                <div>
                    <label for="filtro-situacao" class="mb-1 block font-subtitulo text-sm font-semibold text-pastel">Situação</label>
                    <select id="filtro-situacao" name="situacao" class="campo">
                        <option value="">Todas</option>
                        @foreach (\App\Models\Equipamento::SITUACOES as $valor => $rotulo)
                            <option value="{{ $valor }}" @selected(($filtros['situacao'] ?? '') === $valor)>{{ $rotulo }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex items-end">
                    <button type="submit" class="botao-primario w-full">
                        @include('parciais.icone', ['nome' => 'busca'])
                        Filtrar
                    </button>
                </div>
            </form>
        </div>

        <div class="relative flex flex-col justify-between gap-6 overflow-hidden rounded-[2rem] bg-pastel p-6 sm:p-10">
            @include('parciais.ilustracao', ['classe' => 'mx-auto h-auto w-full max-w-xs'])
            <div class="flex flex-col gap-3 sm:flex-row">
                <a href="{{ route('equipamentos.criar') }}" class="botao-primario">
                    @include('parciais.icone', ['nome' => 'mais'])
                    Novo equipamento
                </a>
                <a href="{{ route('reservas.criar') }}" class="botao-secundario">
                    @include('parciais.icone', ['nome' => 'calendario'])
                    Reservar
                </a>
            </div>
        </div>
    </section>

    <section aria-labelledby="titulo-lista" class="mx-auto max-w-7xl px-4 pt-10 sm:px-8">
        <div class="flex flex-wrap items-end justify-between gap-2">
            <h2 id="titulo-lista" class="text-3xl text-floresta sm:text-4xl">Lista de equipamentos</h2>
            @if ($equipamentos->total() > 0)
                <p class="font-subtitulo text-sm font-semibold text-floresta">{{ $equipamentos->total() }} {{ $equipamentos->total() === 1 ? 'equipamento' : 'equipamentos' }}</p>
            @endif
        </div>

        @if ($equipamentos->isEmpty())
            <div class="mt-6 flex flex-col items-start gap-4 rounded-[2rem] border-2 border-dashed border-floresta/40 bg-white p-8">
                <span class="caixa-icone bg-pastel text-floresta">@include('parciais.icone', ['nome' => 'caixa'])</span>
                @if (! empty($filtros['busca']) || ! empty($filtros['situacao']))
                    <p class="text-lg font-medium">Nenhum equipamento encontrado com os filtros informados.</p>
                    <a href="{{ route('equipamentos.listar') }}" class="botao-secundario">Limpar filtros</a>
                @else
                    <p class="text-lg font-medium">A lista de equipamentos está vazia. Nenhum equipamento foi cadastrado ainda.</p>
                    <a href="{{ route('equipamentos.criar') }}" class="botao-primario">
                        @include('parciais.icone', ['nome' => 'mais'])
                        Cadastrar o primeiro equipamento
                    </a>
                @endif
            </div>
        @else
            {{-- Tabela em telas médias e grandes --}}
            <div class="mt-6 hidden overflow-hidden rounded-[2rem] border-2 border-pastel md:block">
                <table class="w-full text-left">
                    <caption class="sr-only">Equipamentos cadastrados com nome, patrimônio, categoria, situação e ações</caption>
                    <thead class="bg-pastel font-subtitulo text-sm text-floresta">
                        <tr>
                            <th scope="col" class="px-6 py-4">Nome</th>
                            <th scope="col" class="px-6 py-4">Patrimônio</th>
                            <th scope="col" class="px-6 py-4">Categoria</th>
                            <th scope="col" class="px-6 py-4">Situação</th>
                            <th scope="col" class="px-6 py-4 text-right">Ações</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-pastel">
                        @foreach ($equipamentos as $equipamento)
                            <tr>
                                <td class="px-6 py-4 font-medium">{{ $equipamento->nome }}</td>
                                <td class="whitespace-nowrap px-6 py-4 font-mono text-sm">{{ $equipamento->numero_patrimonio }}</td>
                                <td class="px-6 py-4">{{ $equipamento->rotuloCategoria() }}</td>
                                <td class="px-6 py-4">@include('parciais.situacao')</td>
                                <td class="px-6 py-4">
                                    @include('equipamentos.acoes')
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Cartões em telas pequenas --}}
            <ul class="mt-6 grid gap-4 md:hidden">
                @foreach ($equipamentos as $equipamento)
                    <li class="cartao border-2 border-pastel">
                        <div class="flex items-start justify-between gap-3">
                            <h3 class="text-lg leading-tight text-floresta">{{ $equipamento->nome }}</h3>
                            @include('parciais.situacao')
                        </div>
                        <dl class="mt-4 grid grid-cols-2 gap-3 text-sm">
                            <div>
                                <dt class="font-subtitulo font-semibold text-floresta">Patrimônio</dt>
                                <dd class="break-all font-mono">{{ $equipamento->numero_patrimonio }}</dd>
                            </div>
                            <div>
                                <dt class="font-subtitulo font-semibold text-floresta">Categoria</dt>
                                <dd>{{ $equipamento->rotuloCategoria() }}</dd>
                            </div>
                        </dl>
                        <div class="mt-4">@include('equipamentos.acoes')</div>
                    </li>
                @endforeach
            </ul>

            <div class="mt-6">
                {{ $equipamentos->links('parciais.paginacao') }}
            </div>
        @endif
    </section>
@endsection
