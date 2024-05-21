<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Contract extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'salary',
        'start_date',
        'end_date',
        'work_shift_id',
        'user_id',
    ];

    public function workShift()
    {
        return $this->belongsTo(WorkShift::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function days()
    {
        return $this->belongsToMany(Day::class, 'contract_days','contract_id','day_id')
                    ->withPivot('is_work_day');
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
        static::creating(function ($contract) {
            $user = User::findOrFail($contract->user_id);
            $contract->start_date = $user->created_at->format('Y-m-d');
        });
    }
}
