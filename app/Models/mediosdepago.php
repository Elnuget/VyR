<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class mediosdepago extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'id',
        'medio_de_pago',
    ];

    protected $dates = ['deleted_at'];
}
