<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InventoryItem extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'name',
        'category',
        'quantity',
        'stock_status',
        'notes',
    ];

    public function eventInventory()
    {
        return $this->hasMany(EventInventory::class);
    }
}
