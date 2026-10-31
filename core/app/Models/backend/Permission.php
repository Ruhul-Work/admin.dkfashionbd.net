<?php

namespace App\Models\backend;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Permission extends Model
{
    use SoftDeletes;
    protected $fillable = ['key','name','module','type','description','sort','is_active'];

    public function routes()
    {
        return $this->hasMany(PermissionRoute::class);
    }
}
