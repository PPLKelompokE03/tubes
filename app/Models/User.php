<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    // Other properties and methods...

    public function restaurants()
    {
        return $this->hasMany(Restaurant::class, 'seller_id');
    }

    public function orders()
    {
        return $this->hasMany(Order::class, 'customer_id');
    }
}