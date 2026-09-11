<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Species extends Model
{
    /** @use HasFactory<\Database\Factories\SpeciesFactory> */
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'description',
        'state'
    ];

    public function pets()
    {
        return $this->hasMany(Pet::class);
    }
}
