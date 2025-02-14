<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MensajesEnviados extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'mensajes_enviados';

    protected $fillable = [
        'historial_id',
        'tipo',
        'mensaje',
        'fecha_envio'
    ];

    protected $dates = [
        'fecha_envio',
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    // Relación con HistorialClinico
    public function historialClinico()
    {
        return $this->belongsTo(HistorialClinico::class, 'historial_id');
    }
} 