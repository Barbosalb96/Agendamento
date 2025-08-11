<?php

namespace App\Domains\Agendamento\Entities;

use App\Models\User;
use Database\Factories\AgendamentoFactory;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class Agendamento extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'agendamentos';

    protected static function newFactory()
    {
        return AgendamentoFactory::new();
    }

    protected static function booted(): void
    {
        static::creating(function (Agendamento $agendamento) {
                $agendamento->uuid = (string) Str::uuid();
        });
    }

    protected $fillable = [
        'uuid',
        'nome',
        'user_id',
        'email',
        'cpf',
        'rg',
        'telefone',
        'nacionalidade',
        'nacionalidade_grupo',
        'deficiencia',
        'data',
        'horario',
        'grupo',
        'observacao',
        'quantidade',
    ];

    protected $casts = [
        'data' => 'date:Y-m-d',
        'grupo' => 'boolean',
        'deficiencia' => 'boolean',
        'quantidade' => 'integer',
    ];

    protected function email(): Attribute
    {
        return Attribute::make(
            set: fn ($v) => is_string($v) ? mb_strtolower(trim($v)) : $v,
        );
    }

    protected function cpf(): Attribute
    {
        return Attribute::make(
            set: fn ($v) => is_string($v) ? preg_replace('/\D+/', '', $v) : $v,
        );
    }

    protected function telefone(): Attribute
    {
        return Attribute::make(
            set: fn ($v) => is_string($v) ? preg_replace('/\D+/', '', $v) : $v,
        );
    }

    protected function horario(): Attribute
    {
        return Attribute::make(
            get: function ($value) {
                // value geralmente vem como 'HH:MM:SS'
                if (empty($value)) {
                    return $value;
                }
                try {
                    return Carbon::createFromFormat('H:i:s', $value)->format('H:i');
                } catch (\Throwable) {
                    // Se por algum motivo vier 'H:i', apenas padroniza
                    if (preg_match('/^\d{2}:\d{2}$/', (string) $value)) {
                        return (string) $value;
                    }

                    return $value;
                }
            },
            set: function ($value) {
                if (empty($value)) {
                    return $value;
                }
                try {
                    // aceita 'H:i' ou 'H:i:s' de entrada
                    $c = Carbon::parse($value);

                    return $c->format('H:i:s');
                } catch (\Throwable) {
                    return $value;
                }
            }
        );
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // ---- Atributos computados úteis ----

    public function getDataFormatadaAttribute(): string
    {
        return $this->data instanceof Carbon
            ? $this->data->format('d/m/Y')
            : Carbon::parse($this->data)->format('d/m/Y');
    }

    public function getHorarioFormatadoAttribute(): string
    {
        // usa o accessor de 'horario' que já devolve 'H:i'
        return (string) $this->horario;
    }

    public function scopeDoDia($query, Carbon|string $data)
    {
        $data = $data instanceof Carbon ? $data->toDateString() : Carbon::parse($data)->toDateString();

        return $query->whereDate('data', $data);
    }

    public function scopeDoHorario($query, string $horario)
    {
        $h = Carbon::parse($horario)->format('H:i:s');

        return $query->where('horario', $h);
    }
}
