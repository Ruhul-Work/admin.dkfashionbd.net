<?php
namespace App\Models\backend;

use App\Models\backend\Role;
use App\Models\Traits\HasPermissions;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable, SoftDeletes, HasPermissions, HasApiTokens;

    protected $table = 'users';

    protected $fillable = [
        'name', 'email', 'phone', 'username', 'password',
        'role_id', 'branch_id', 'status', 'meta', 'remember_token',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'last_login'        => 'datetime',
        'meta'              => 'array',
    ];

    public function role()
    {
        return $this->belongsTo(Role::class, 'role_id');
    }
    public function branch()
    {
        return $this->belongsTo(\App\Models\backend\Branch::class, 'branch_id');
    }

    public function isSuperAdmin()
    {
        return $this->role?->is_super == 1;
    }

    public function coupon()
    {
        return $this->belongsToMany(Coupon::class);
    }
    public function customer()
    {
        return $this->hasOne(Customer::class);
    }
    public function addressBooks()
    {
        return $this->hasMany(AddressBook::class);
    }
    
}
