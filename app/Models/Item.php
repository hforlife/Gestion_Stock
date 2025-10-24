<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    /** @use HasFactory<\Database\Factories\ItemFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'sku',
        'price',
        'status',
    ];

    public function inventory()
    {
        return $this->hasOne(Inventory::class, 'item_id');
    }
    public function saleItems()
    {
        return $this->hasMany(SalesItem::class, 'item_id');
    }
}
