<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ItemRequestDetail extends Model
{
    protected $guarded = ['id'];

    public function itemRequest()
    {
        return $this->belongsTo(ItemRequest::class);
    }

    public function item()
    {
        return $this->belongsTo(Item::class);
    }
}
