<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('equipamentos', function (Blueprint $tabela) {
            $tabela->engine = 'InnoDB';
            $tabela->id();
            $tabela->string('nome', 255);
            $tabela->string('numero_patrimonio', 100)->unique();
            $tabela->enum('categoria', ['informatica', 'audiovisual', 'ferramentas', 'outros']);
            $tabela->enum('situacao', ['disponivel', 'em_manutencao', 'fora_de_uso'])->default('disponivel');
            $tabela->timestamps();

            $tabela->index('nome');
        });
    }

    public function down()
    {
        Schema::dropIfExists('equipamentos');
    }
};
