<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Owner extends Model
{
    /** @use HasFactory<\Database\Factories\OwnerFactory> */
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'cedula',
        'first_name',
        'last_name',
        'email',
        'phone',
        'address'
    ];

    public function pets()
    {
        return $this->hasMany(Pet::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function surgeries()
    {
        return $this->hasManyThrough(Surgery::class, Pet::class);
    }

}
