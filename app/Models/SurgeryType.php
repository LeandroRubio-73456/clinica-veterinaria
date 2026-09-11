<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SurgeryType extends Model
{
    /** @use HasFactory<\Database\Factories\SurgeryTypeFactory> */
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'description',
        'estimated_duration',
        'state',
    ];

    public function surgeries()
    {
        return $this->hasMany(Surgery::class);
    }
}
