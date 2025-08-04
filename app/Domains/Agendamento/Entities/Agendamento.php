<?php

namespace App\Domains\Agendamento\Entities;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Agendamento extends Model
{
    use HasFactory,softDeletes;

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($user) {
            $user->uuid = (string)Str::uuid();
        });
    }

    protected $fillable = [
        'uuid',
        'user_id',
        'data',
        'horario',
        'grupo',
        'status',
        'observacao',
        'quantidade',
    ];

    protected $casts = [
        'data' => 'date',
        'horario' => 'datetime:H:i',
        'grupo' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
    public function getDataFormatadaAttribute(): string
    {
        return $this->data->format('d/m/Y');
    }
    public function getHorarioFormatadoAttribute(): string
    {
        return $this->horario->format('H:i');
    }

}
