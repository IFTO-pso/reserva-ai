<aside class="relative flex flex-col gap-6 overflow-hidden rounded-[2rem] bg-floresta p-6 text-white sm:p-10">
    <svg class="linha-decorativa -bottom-8 -right-8 h-40 w-40 text-limao/30" viewBox="0 0 100 100" fill="none" aria-hidden="true">
        <rect x="10" y="10" width="80" height="80" rx="20" stroke="currentColor" stroke-width="1.5" />
        <rect x="25" y="25" width="50" height="50" rx="14" stroke="currentColor" stroke-width="1.5" stroke-dasharray="4 6" />
    </svg>
    <h2 class="text-3xl text-limao">Situações possíveis</h2>
    <ul class="relative grid gap-4">
        <li class="flex items-start gap-3">
            <span class="caixa-icone bg-limao text-floresta">@include('parciais.icone', ['nome' => 'confirmado'])</span>
            <p><strong class="font-subtitulo">Disponível</strong><br><span class="text-pastel">Pode ser reservado.</span></p>
        </li>
        <li class="flex items-start gap-3">
            <span class="caixa-icone bg-lima text-ameixa">@include('parciais.icone', ['nome' => 'chave'])</span>
            <p><strong class="font-subtitulo">Em manutenção</strong><br><span class="text-pastel">Temporariamente indisponível para reservas.</span></p>
        </li>
        <li class="flex items-start gap-3">
            <span class="caixa-icone bg-white text-ameixa">@include('parciais.icone', ['nome' => 'bloqueado'])</span>
            <p><strong class="font-subtitulo">Fora de uso</strong><br><span class="text-pastel">Não pode ser reservado.</span></p>
        </li>
    </ul>
    <p class="relative rounded-2xl bg-white/10 p-4 text-sm text-pastel">
        Um equipamento com reservas futuras ou em andamento só pode mudar para manutenção ou fora de uso depois que essas reservas forem canceladas.
    </p>
</aside>
