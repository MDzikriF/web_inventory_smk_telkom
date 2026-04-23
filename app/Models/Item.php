<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    protected $fillable = ['kode_barang', 'name', 'category_id', 'unit_id', 'stock', 'type', 'sub_kategori', 'photo', 'min_stock'];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }

    public function itemRequestDetails()
    {
        return $this->hasMany(ItemRequestDetail::class);
    }

    public function damageReports()
    {
        return $this->hasMany(DamageReport::class);
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }
}
