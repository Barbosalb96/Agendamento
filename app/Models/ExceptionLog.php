<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExceptionLog extends Model
{
    protected $table = 'exceptions';

    protected $fillable = [
        'url',
        'method',
        'ip',
        'message',
        'file',
        'line',
        'trace',
    ];
}
