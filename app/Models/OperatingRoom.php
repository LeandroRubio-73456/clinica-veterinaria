<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OperatingRoom extends Model
{
    /** @use HasFactory<\Database\Factories\OperatingRoomFactory> */
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'type',
        'state'
    ];

    public function surgeries()
    {
        return $this->hasMany(Surgery::class);
    }
}
