<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ItemRequest extends Model
{
    protected $guarded = ['id'];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'nip');
    }

    public function itemRequestDetails()
    {
        return $this->hasMany(ItemRequestDetail::class);
    }

    public function details()
    {
        return $this->hasMany(ItemRequestDetail::class);
    }
}
