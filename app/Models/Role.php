<?php

namespace App\Models;

use Spatie\Permission\Models\Role as SpatieRole;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends SpatieRole{
    
    use HasFactory;

    public function areas(){
        return $this->belongsToMany(Area::class, 'area_role', 'role_id', 'area_id');
    }
}
