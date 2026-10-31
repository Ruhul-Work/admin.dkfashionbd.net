<?php

namespace App\Models\backend;

use Illuminate\Database\Eloquent\Model;

class AddressBook extends Model
{
    protected $table = "address_book";
    protected $guarded = [
        'id',
        'created_at',
        'updated_at'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

}
