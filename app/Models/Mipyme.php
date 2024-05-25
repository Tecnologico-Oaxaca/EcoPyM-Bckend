<?php

namespace App\Models;

use App\Http\Controllers\BusineController;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Mipyme extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'image'
    ];
    public function businesses(){
        return $this->belongsToMany(Busine::class, 'mipyme_business', 'mipyme_id', 'business_id');
    }

    public function branches(){
        return $this->hasMany(Branch::class);
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
