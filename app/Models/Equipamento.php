<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Equipamento extends Model
{
    public const SITUACAO_DISPONIVEL = 'disponivel';
    public const SITUACAO_EM_MANUTENCAO = 'em_manutencao';
    public const SITUACAO_FORA_DE_USO = 'fora_de_uso';

    public const SITUACOES = [
        self::SITUACAO_DISPONIVEL => 'Disponível',
        self::SITUACAO_EM_MANUTENCAO => 'Em manutenção',
        self::SITUACAO_FORA_DE_USO => 'Fora de uso',
    ];

    public const CATEGORIAS = [
        'informatica' => 'Informática',
        'audiovisual' => 'Audiovisual',
        'ferramentas' => 'Ferramentas',
        'outros' => 'Outros',
    ];

    protected $table = 'equipamentos';

    protected $fillable = [
        'nome',
        'numero_patrimonio',
        'categoria',
        'situacao',
    ];

    protected $attributes = [
        'situacao' => self::SITUACAO_DISPONIVEL,
    ];

    public function reservas(): HasMany
    {
        return $this->hasMany(Reserva::class, 'equipamento_id');
    }

    public function rotuloSituacao(): string
    {
        return self::SITUACOES[$this->situacao] ?? $this->situacao;
    }

    public function rotuloCategoria(): string
    {
        return self::CATEGORIAS[$this->categoria] ?? $this->categoria;
    }

    public function estaDisponivel(): bool
    {
        return $this->situacao === self::SITUACAO_DISPONIVEL;
    }

    public function reservaEmAndamento(): ?Reserva
    {
        return $this->reservas()->emAndamento()->first();
    }
}
