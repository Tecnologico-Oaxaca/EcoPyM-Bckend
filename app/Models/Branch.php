<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Branch extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'image',
        'open_time',
        'close_time',
        'phone',
        'state',
        'city',
        'district',
        'street',
        'number',
        'mipyme_id',
    ];

    public function mipyme(){
        return $this->belongsTo(Mipyme::class, 'mipyme_id');
    }
    
    public function users() {
        return $this->hasMany(User::class);
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
}
