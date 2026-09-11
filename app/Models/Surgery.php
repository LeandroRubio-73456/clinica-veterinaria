<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Surgery extends Model
{
    /** @use HasFactory<\Database\Factories\SurgeryFactory> */
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'scheduled_date',
        'start_time',
        'end_time',
        'actual_start_time',
        'actual_end_time',
        'state',
        'notes',
        'pet_id',
        'veterinarian_id',
        'operating_room_id',
        'surgery_type_id'
    ];

 

    public function pet()
    {
        return $this->belongsTo(Pet::class);
    }

    public function veterinarian()
    {
        return $this->belongsTo(Veterinarian::class);
    }

    public function operatingRoom()
    {
        return $this->belongsTo(OperatingRoom::class);
    }

    public function surgeryType()
    {
        return $this->belongsTo(SurgeryType::class);
    }
}
