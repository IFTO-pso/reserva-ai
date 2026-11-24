<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('reservas', function (Blueprint $tabela) {
            $tabela->engine = 'InnoDB';
            $tabela->id();
            $tabela->foreignId('equipamento_id')
                ->constrained('equipamentos')
                ->restrictOnDelete()
                ->cascadeOnUpdate();
            $tabela->string('responsavel', 255);
            $tabela->dateTime('inicio');
            $tabela->dateTime('fim');
            $tabela->dateTime('cancelada_em')->nullable();
            $tabela->timestamps();

            $tabela->index(['equipamento_id', 'inicio', 'fim']);
            $tabela->index('inicio');
        });
    }

    public function down()
    {
        Schema::dropIfExists('reservas');
    }
};
