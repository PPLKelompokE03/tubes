<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    // Existing code...

    public function customer()
    {
        return $this->belongsTo(User::class);
    }

    public function mysteryBox()
    {
        return $this->belongsTo(MysteryBox::class);
    }

    public function review()
    {
        return $this->hasOne(Review::class);
    }
}