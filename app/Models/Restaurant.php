<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Restaurant extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'address',
        'description',
        'image',
        'menu_access_pin',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function mysteryBoxes()
    {
        return $this->hasMany(MysteryBox::class);
    }
}