<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Intento de programación rechazado por solapamiento de recursos.
 *
 * Este modelo no representa una cirugía válida; conserva evidencia del
 * control de disponibilidad para los indicadores operativos.
 */
class SchedulingConflict extends Model
{
    use HasFactory;

    protected $fillable = [
        'scheduled_date',
        'start_time',
        'end_time',
        'conflict_type',
        'source',
        'veterinarian_id',
        'operating_room_id',
        'surgery_id',
        'user_id',
        'details',
    ];

    protected function casts(): array
    {
        return [
            'details' => 'array',
        ];
    }
}
