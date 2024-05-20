<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Comment extends Model{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'title',
        'body',
        'score',
        'user_id'
    ];
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'date',
        'time',
        'user_id',
        'updated_at',
        'created_at'
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'date' => 'date',
            'time' => 'datetime:H:i:s',
        ];
    }

    public static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (is_null($model->date)) {
                $model->date = Carbon::now()->toDateString();
            }
            if (is_null($model->time)) {
                $model->time = Carbon::now()->toTimeString();
            }
        });
    }
}
