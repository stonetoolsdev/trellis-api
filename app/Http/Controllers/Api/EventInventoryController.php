<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\EventInventory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class EventInventoryController extends Controller
{
    public function index(Event $event): JsonResponse
    {
        $items = $event->inventory()
            ->with('inventoryItem')
            ->get();
        return response()->json($items);
    }

    public function store(Request $request, Event $event): JsonResponse
    {
        $request->validate([
            'inventory_item_id' => ['nullable', 'exists:inventory_items,id'],
            'quantity_needed' => ['sometimes', 'integer', 'min:1'],
            'notes' => ['nullable', 'string'],
            'other_description' => ['nullable', 'string'],
        ]);

        $item = $event->inventory()->create([
            'inventory_item_id' => $request->inventory_item_id,
            'quantity_needed' => $request->input('quantity_needed', 1),
            'notes' => $request->notes,
            'other_description' => $request->other_description,
        ]);

        return response()->json($item->load('inventoryItem'), 201);
    }

    public function update(Request $request, Event $event, EventInventory $eventInventory): JsonResponse
    {
        $request->validate([
            'inventory_item_id' => ['nullable', 'exists:inventory_items,id'],
            'quantity_needed' => ['sometimes', 'integer', 'min:1'],
            'notes' => ['nullable', 'string'],
            'other_description' => ['nullable', 'string'],
        ]);

        $eventInventory->update($request->only([
            'inventory_item_id', 'quantity_needed', 'notes', 'other_description'
        ]));

        return response()->json($eventInventory->load('inventoryItem'));
    }

    public function destroy(Event $event, EventInventory $eventInventory): JsonResponse
    {
        $eventInventory->delete();
        return response()->json(['message' => 'Item removed from event']);
    }
}
