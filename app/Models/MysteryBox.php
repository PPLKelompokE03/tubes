<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MysteryBox extends Model
{
    use HasFactory;

    protected $fillable = [
        'restaurant_id',
        'name',
        'description',
        'price',
        'discount_percentage',
        'final_price',
        'stock',
        'image',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'discount_percentage' => 'integer',
            'final_price' => 'decimal:2',
        ];
    }

    protected static function booted(): void
    {
        static::saving(function (MysteryBox $box): void {
            $discount = max(0, min(100, (float) ($box->discount_percentage ?? 0)));
            $box->discount_percentage = (int) round($discount);
            $price = (float) $box->price;
            $box->final_price = round($price - ($price * $box->discount_percentage / 100), 2);
        });
    }

    public function restaurant()
    {
        return $this->belongsTo(Restaurant::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }
}
