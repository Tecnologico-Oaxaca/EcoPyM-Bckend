<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductPromotion extends Model
{
    use HasFactory;

    protected $table = 'product_promotion'; // Asegúrate de que el nombre de la tabla sea correcto

    protected $fillable = [
        'product_id', 
        'promotion_id', 
        'stock', 
        'product_price_buy',
        'product_price_sale'
    ];

    protected $hidden = [
        'created_at',
        'updated_at'
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($productPromotion) {
            $product = Product::find($productPromotion->product_id);
            if ($product) {
                $productPromotion->product_price_buy = $product->price_buy; // Precio de compra
                $productPromotion->product_price_sale = $product->price_sale; // Precio de venta
            } else {
                throw new \Exception("Producto no encontrado.");
            }
        });
    }
}
