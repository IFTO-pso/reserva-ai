@extends('layouts.principal')

@section('titulo', 'Nova reserva')

@section('conteudo')
    <section class="mx-auto grid max-w-7xl gap-6 px-4 pt-8 sm:px-8 lg:grid-cols-principal">
        <div class="cartao border-2 border-pastel">
            <a href="{{ route('reservas.listar') }}" class="inline-flex items-center gap-1 font-subtitulo text-sm font-semibold text-floresta underline-offset-4 hover:underline">
                @include('parciais.icone', ['nome' => 'voltar', 'classe' => 'h-4 w-4']) Voltar para as reservas
            </a>
            <h1 class="mt-4 text-4xl text-floresta sm:text-5xl">Nova reserva</h1>
            <p class="mb-8 mt-3 text-lg">Escolha um equipamento disponível e o período desejado.</p>

            @if ($equipamentos->isEmpty())
                <div class="rounded-2xl bg-pastel p-6">
                    <p class="font-medium">Não há equipamentos com situação "Disponível" para reservar no momento.</p>
                    <a href="{{ route('equipamentos.listar') }}" class="botao-secundario mt-4">Ver equipamentos</a>
                </div>
            @else
                <form method="POST" action="{{ route('reservas.cadastrar') }}" novalidate>
                    @csrf
                    @include('parciais.erros')

                    <div class="grid gap-6">
                        <div>
                            <label for="equipamento_id" class="rotulo">Equipamento <span aria-hidden="true">*</span></label>
                            <select id="equipamento_id" name="equipamento_id" required
                                    class="campo @error('equipamento_id') campo-invalido @enderror"
                                    @error('equipamento_id') aria-invalid="true" aria-describedby="equipamento_id-erro" @enderror>
                                <option value="">Selecione…</option>
                                @foreach ($equipamentos as $equipamento)
                                    <option value="{{ $equipamento->id }}" @selected((string) old('equipamento_id', $equipamentoSelecionado) === (string) $equipamento->id)>
                                        {{ $equipamento->nome }} — {{ $equipamento->numero_patrimonio }}
                                    </option>
                                @endforeach
                            </select>
                            @error('equipamento_id')
                                <p id="equipamento_id-erro" class="erro-campo">@include('parciais.icone', ['nome' => 'alerta', 'classe' => 'mt-0.5 h-4 w-4 shrink-0']) {{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="responsavel" class="rotulo">Nome do responsável <span aria-hidden="true">*</span></label>
                            <input id="responsavel" name="responsavel" type="text" required maxlength="255" autocomplete="name"
                                   value="{{ old('responsavel') }}"
                                   class="campo @error('responsavel') campo-invalido @enderror"
                                   @error('responsavel') aria-invalid="true" aria-describedby="responsavel-erro" @enderror>
                            @error('responsavel')
                                <p id="responsavel-erro" class="erro-campo">@include('parciais.icone', ['nome' => 'alerta', 'classe' => 'mt-0.5 h-4 w-4 shrink-0']) {{ $message }}</p>
                            @enderror
                        </div>

                        <div class="grid gap-6 sm:grid-cols-2">
                            <div>
                                <label for="inicio" class="rotulo">Início <span aria-hidden="true">*</span></label>
                                <input id="inicio" name="inicio" type="datetime-local" required step="60" min="{{ $minimoInicio }}"
                                       value="{{ old('inicio') }}"
                                       class="campo @error('inicio') campo-invalido @enderror"
                                       @error('inicio') aria-invalid="true" aria-describedby="inicio-erro" @enderror>
                                @error('inicio')
                                    <p id="inicio-erro" class="erro-campo">@include('parciais.icone', ['nome' => 'alerta', 'classe' => 'mt-0.5 h-4 w-4 shrink-0']) {{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label for="fim" class="rotulo">Fim <span aria-hidden="true">*</span></label>
                                <input id="fim" name="fim" type="datetime-local" required step="60" min="{{ $minimoInicio }}"
                                       value="{{ old('fim') }}"
                                       class="campo @error('fim') campo-invalido @enderror"
                                       @error('fim') aria-invalid="true" aria-describedby="fim-erro" @enderror>
                                @error('fim')
                                    <p id="fim-erro" class="erro-campo">@include('parciais.icone', ['nome' => 'alerta', 'classe' => 'mt-0.5 h-4 w-4 shrink-0']) {{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <p class="text-sm">Horários no fuso de Brasília. O fim deve ser posterior ao início; uma reserva pode começar exatamente quando outra termina. <span aria-hidden="true">*</span> Campos obrigatórios.</p>
                    </div>

                    <div class="mt-8 flex flex-col gap-3 sm:flex-row">
                        <button type="submit" class="botao-primario">
                            @include('parciais.icone', ['nome' => 'calendario'])
                            Confirmar reserva
                        </button>
                        <a href="{{ route('reservas.listar') }}" class="botao-secundario">Cancelar</a>
                    </div>
                </form>
            @endif
        </div>

        <div class="flex flex-col items-center justify-center gap-6 rounded-[2rem] bg-floresta p-6 text-white sm:p-10">
            <div class="w-full rounded-[1.5rem] bg-pastel p-4">
                @include('parciais.ilustracao', ['classe' => 'mx-auto h-auto w-full max-w-xs'])
            </div>
            <p class="text-center text-pastel">Equipamentos em manutenção ou fora de uso não aparecem na lista.</p>
        </div>
    </section>
@endsection
