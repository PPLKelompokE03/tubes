<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Restaurant extends Model
{
    // Other model properties and methods

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function mysteryBoxes() {
        return $this->hasMany(MysteryBox::class);
    }
}