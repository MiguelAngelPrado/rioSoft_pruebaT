<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Models\Role;

class RolUser extends Model
{
    use HasFactory;
    protected $table = 'model_has_roles';
    protected $fillable = [
        'role_id',
        'model_type',
        'model_id'
    ];
    public function getrol()
    {
        return $this->hasOne(Role::class,'id','role_id');
    }
}
