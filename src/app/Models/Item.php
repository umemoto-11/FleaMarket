<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Enums\Condition;

class Item extends Model
{
    use HasFactory;

    public function getTargetLabelAttribute()
    {
        return Condition::from($this->condition)->label();
    }

    protected $casts = [
        'condition' => Condition::class,
    ];

    protected $fillable = [
        'user_id',
        'name',
        'price',
        'brand',
        'image',
        'condition',
        'description',
        'buyer_id',
        'is_sold',
        'shipping_postcode',
        'shipping_address',
        'shipping_building',
    ];

    public function categories()
    {
        return $this->belongsToMany(Category::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function likedUsers()
    {
        return $this->belongsToMany(User::class, 'likes', 'item_id', 'user_id');
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function buyer()
    {
        return $this->belongsTo(User::class, 'buyer_id');
    }
}
