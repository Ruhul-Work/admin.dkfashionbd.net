<?php

namespace App\Models\backend;


use Illuminate\Database\Eloquent\Model;


class AccountType extends Model
{
    protected $fillable = ['name', 'code'];

    public function accounts()
    {
        return $this->hasMany(Account::class, 'account_type_id');
    }
}