<?php

namespace App\Models;

use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Reserva extends Model
{
    public const ESTADO_FUTURA = 'futura';
    public const ESTADO_EM_ANDAMENTO = 'em_andamento';
    public const ESTADO_ENCERRADA = 'encerrada';
    public const ESTADO_CANCELADA = 'cancelada';

    public const ESTADOS = [
        self::ESTADO_FUTURA => 'Futura',
        self::ESTADO_EM_ANDAMENTO => 'Em andamento',
        self::ESTADO_ENCERRADA => 'Encerrada',
        self::ESTADO_CANCELADA => 'Cancelada',
    ];

    protected $table = 'reservas';

    protected $fillable = [
        'equipamento_id',
        'responsavel',
        'inicio',
        'fim',
    ];

    protected $casts = [
        'inicio' => 'datetime',
        'fim' => 'datetime',
        'cancelada_em' => 'datetime',
    ];

    public function equipamento(): BelongsTo
    {
        return $this->belongsTo(Equipamento::class, 'equipamento_id');
    }

    public function scopeNaoCanceladas(Builder $consulta): Builder
    {
        return $consulta->whereNull('cancelada_em');
    }

    /**
     * Reservas não canceladas que se sobrepõem ao intervalo [inicio, fim). O fim é exclusivo,
     * portanto intervalos consecutivos não conflitam.
     */
    public function scopeConflitantes(Builder $consulta, CarbonInterface $inicio, CarbonInterface $fim): Builder
    {
        return $consulta->naoCanceladas()
            ->where('inicio', '<', $fim)
            ->where('fim', '>', $inicio);
    }

    /**
     * Reservas não canceladas que ainda não terminaram (futuras ou em andamento).
     */
    public function scopeVigentes(Builder $consulta): Builder
    {
        return $consulta->naoCanceladas()->where('fim', '>', now());
    }

    public function scopeEmAndamento(Builder $consulta): Builder
    {
        $agora = now();

        return $consulta->naoCanceladas()
            ->where('inicio', '<=', $agora)
            ->where('fim', '>', $agora);
    }

    public function estado(?CarbonInterface $referencia = null): string
    {
        $referencia = $referencia ?? now();

        if ($this->cancelada_em !== null) {
            return self::ESTADO_CANCELADA;
        }

        if ($this->fim->lessThanOrEqualTo($referencia)) {
            return self::ESTADO_ENCERRADA;
        }

        if ($this->inicio->lessThanOrEqualTo($referencia)) {
            return self::ESTADO_EM_ANDAMENTO;
        }

        return self::ESTADO_FUTURA;
    }

    public function rotuloEstado(?CarbonInterface $referencia = null): string
    {
        return self::ESTADOS[$this->estado($referencia)];
    }

    public function podeSerCancelada(?CarbonInterface $referencia = null): bool
    {
        return in_array($this->estado($referencia), [self::ESTADO_FUTURA, self::ESTADO_EM_ANDAMENTO], true);
    }
}
