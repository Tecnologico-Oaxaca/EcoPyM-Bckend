<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'date',
        'time',
        'total_sale',
        'discount',
        'user_id',
    ];
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function paymentMethods()
    {
        return $this->belongsToMany(Payment_Method::class, 'payment_method_sale', 'sale_id', 'payment_method_id');
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
    protected static function booted()
    {
        static::creating(function ($cashOpening) {
            $cashOpening->date = Carbon::now()->format('Y-m-d');
            $cashOpening->time = Carbon::now()->format('H:i');
        });
    }
    
}
