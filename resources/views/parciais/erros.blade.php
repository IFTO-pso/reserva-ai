{{-- Resumo dos erros de validação com links para os campos. --}}
@if ($errors->any())
    <div role="alert" aria-labelledby="titulo-erros" class="mb-6 rounded-2xl border-2 border-ameixa bg-lima/30 p-5 text-ameixa">
        <h2 id="titulo-erros" class="flex items-center gap-2 font-subtitulo text-base font-bold leading-normal tracking-normal">
            @include('parciais.icone', ['nome' => 'alerta'])
            Corrija os campos indicados:
        </h2>
        <ul class="mt-2 list-disc space-y-1 pl-6">
            @foreach ($errors->keys() as $campo)
                <li><a class="underline" href="#{{ $campo }}">{{ $errors->first($campo) }}</a></li>
            @endforeach
        </ul>
    </div>
@endif
