<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'mystery_box_id',
        'quantity',
        'total_price',
        'status',
        'payment_proof',
    ];

    protected function casts(): array
    {
        return [
            'total_price' => 'decimal:2',
        ];
    }

    public function user()
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