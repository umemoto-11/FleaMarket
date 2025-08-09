<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Profile extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'postcode',
        'address',
        'building',
        'image',
    ];

    public function user()
    {
        return $this->hasOne(User::class);
    }
}
