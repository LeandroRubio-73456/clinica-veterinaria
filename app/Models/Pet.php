<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Pet extends Model
{
    /** @use HasFactory<\Database\Factories\PetFactory> */
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'breed',
        'age',
        'weight',
        'gender',
        'state',
        'species_id',
        'owner_id'
    ];

    public function owner() 
    {
        return $this->belongsTo(Owner::class);
    }

    public function species() 
    {
        return $this->belongsTo(Species::class);
    }

    public function surgeries()
    {
        return $this->hasMany(Surgery::class);
    }
}
