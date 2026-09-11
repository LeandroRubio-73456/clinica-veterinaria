<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Veterinarian extends Model
{
    /** @use HasFactory<\Database\Factories\VeterinarianFactory> */
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'cedula',
        'first_name',
        'last_name',
        'email',
        'phone',
        'state',
        'specialty_id'
    ];

    public function specialty()
    {
        return $this->belongsTo(Specialty::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function surgeries()
    {
        return $this->hasMany(Surgery::class);
    }
}
