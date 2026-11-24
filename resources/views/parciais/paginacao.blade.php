@if ($paginator->hasPages())
    <nav aria-label="Paginação" class="flex flex-wrap items-center justify-between gap-3">
        <p class="text-sm">Página {{ $paginator->currentPage() }} de {{ $paginator->lastPage() }}</p>
        <div class="flex gap-2">
            @if ($paginator->onFirstPage())
                <span class="botao border-floresta/20 text-floresta/50" aria-disabled="true">Anterior</span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="botao-secundario">Anterior</a>
            @endif

            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="botao-secundario">Próxima</a>
            @else
                <span class="botao border-floresta/20 text-floresta/50" aria-disabled="true">Próxima</span>
            @endif
        </div>
    </nav>
@endif
