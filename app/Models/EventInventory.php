<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EventInventory extends Model
{
  use HasFactory, HasUuids;

  protected $table = 'event_inventory';

  protected $fillable = [
    'event_id',
    'inventory_item_id',
    'quantity_needed',
    'notes',
    'other_description',
  ];

  public function event()
  {
    return $this->belongsTo(Event::class);
  }

  public function inventoryItem()
  {
    return $this->belongsTo(InventoryItem::class);
  }
}
