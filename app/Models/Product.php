<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    public $incrementing = false;
    protected $keyType = 'string';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'id',
        'name',
        'stock',
        'description',
        'price_sale',
        'price_buy',
        'image',
        'unit',
        'unit_quantity_id',
        'business_id',
        'brand_id',
        'clasification_id',
        'provider_id'
    ];

    public function provider()
    {
        return $this->belongsTo(Provider::class, 'provider_id');
    }
    public function unitQuantity()
    {
        return $this->belongsTo(UnitQuantity::class, 'unit_quantity_id');
    }
    public function clasification()
    {
        return $this->belongsTo(Clasification::class, 'clasification_id');
    }
    public function business()  
    {
        return $this->belongsTo(Busine::class, 'business_id');
    }
    public function brand()
    {
        return $this->belongsTo(Brand::class, 'brand_id');
    }
    public function saleDetails()
    {
        return $this->hasMany(SaleDetail::class, 'product_id', 'id');
    }
    

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'created_at',
        'updated_at',
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
}
