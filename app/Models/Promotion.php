<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Promotion extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'quantity',
        'date_start',
        'date_end',
        'description',
        'price_promotion',
        'price_real'
    ];

    public function products()
    {
        return $this->belongsToMany(Product::class, 'product_promotion')
                    ->withPivot('stock', 'product_price_buy', 'product_price_sale')
                    ->withTimestamps();
    }

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'created_at',
        'updated_at'
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            
        ];
    }
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($promotion) {
            $promotion->date_start = $promotion->date_start ?? Carbon::today()->toDateString();
        });
    }
}
