<div class="flex flex-wrap gap-2 md:justify-end">
    <a href="{{ route('equipamentos.consultar', $equipamento) }}" class="botao-secundario px-3" aria-label="Consultar {{ $equipamento->nome }}">
        @include('parciais.icone', ['nome' => 'ver'])
        <span>Ver</span>
    </a>
    <a href="{{ route('equipamentos.editar', $equipamento) }}" class="botao-secundario px-3" aria-label="Editar {{ $equipamento->nome }}">
        @include('parciais.icone', ['nome' => 'editar'])
        <span>Editar</span>
    </a>
    <a href="{{ route('equipamentos.confirmar-exclusao', $equipamento) }}" class="botao-secundario px-3" aria-label="Excluir {{ $equipamento->nome }}">
        @include('parciais.icone', ['nome' => 'lixeira'])
        <span>Excluir</span>
    </a>
</div>
