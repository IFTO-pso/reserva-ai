{{-- Campos compartilhados entre cadastro e edição de equipamentos. --}}
@include('parciais.erros')

<div class="grid gap-6">
    <div>
        <label for="nome" class="rotulo">Nome <span aria-hidden="true">*</span></label>
        <input id="nome" name="nome" type="text" required maxlength="255" autocomplete="off"
               value="{{ old('nome', $equipamento->nome) }}"
               class="campo @error('nome') campo-invalido @enderror"
               @error('nome') aria-invalid="true" aria-describedby="nome-erro" @enderror>
        @error('nome')
            <p id="nome-erro" class="erro-campo">@include('parciais.icone', ['nome' => 'alerta', 'classe' => 'mt-0.5 h-4 w-4 shrink-0']) {{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="numero_patrimonio" class="rotulo">Número de patrimônio <span aria-hidden="true">*</span></label>
        <input id="numero_patrimonio" name="numero_patrimonio" type="text" required maxlength="100" autocomplete="off"
               pattern="[A-Za-z0-9.\/\-]+"
               value="{{ old('numero_patrimonio', $equipamento->numero_patrimonio) }}"
               class="campo font-mono @error('numero_patrimonio') campo-invalido @enderror"
               aria-describedby="numero_patrimonio-ajuda @error('numero_patrimonio') numero_patrimonio-erro @enderror"
               @error('numero_patrimonio') aria-invalid="true" @enderror>
        <p id="numero_patrimonio-ajuda" class="mt-2 text-sm">Único por equipamento. Use letras, números, ponto, hífen ou barra (ex.: PAT-0042).</p>
        @error('numero_patrimonio')
            <p id="numero_patrimonio-erro" class="erro-campo">@include('parciais.icone', ['nome' => 'alerta', 'classe' => 'mt-0.5 h-4 w-4 shrink-0']) {{ $message }}</p>
        @enderror
    </div>

    <div class="grid gap-6 sm:grid-cols-2">
        <div>
            <label for="categoria" class="rotulo">Categoria <span aria-hidden="true">*</span></label>
            <select id="categoria" name="categoria" required
                    class="campo @error('categoria') campo-invalido @enderror"
                    @error('categoria') aria-invalid="true" aria-describedby="categoria-erro" @enderror>
                <option value="">Selecione…</option>
                @foreach (\App\Models\Equipamento::CATEGORIAS as $valor => $rotulo)
                    <option value="{{ $valor }}" @selected(old('categoria', $equipamento->categoria) === $valor)>{{ $rotulo }}</option>
                @endforeach
            </select>
            @error('categoria')
                <p id="categoria-erro" class="erro-campo">@include('parciais.icone', ['nome' => 'alerta', 'classe' => 'mt-0.5 h-4 w-4 shrink-0']) {{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="situacao" class="rotulo">Situação <span aria-hidden="true">*</span></label>
            <select id="situacao" name="situacao" required
                    class="campo @error('situacao') campo-invalido @enderror"
                    @error('situacao') aria-invalid="true" aria-describedby="situacao-erro" @enderror>
                @foreach (\App\Models\Equipamento::SITUACOES as $valor => $rotulo)
                    <option value="{{ $valor }}" @selected(old('situacao', $equipamento->situacao) === $valor)>{{ $rotulo }}</option>
                @endforeach
            </select>
            @error('situacao')
                <p id="situacao-erro" class="erro-campo">@include('parciais.icone', ['nome' => 'alerta', 'classe' => 'mt-0.5 h-4 w-4 shrink-0']) {{ $message }}</p>
            @enderror
        </div>
    </div>

    <p class="text-sm"><span aria-hidden="true">*</span> Campos obrigatórios.</p>
</div>
