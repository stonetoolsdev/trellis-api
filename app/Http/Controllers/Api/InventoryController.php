<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\InventoryItem;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class InventoryController extends Controller
{
    public function index(): JsonResponse
    {
        $items = InventoryItem::orderBy('category')->orderBy('name')->get();
        return response()->json($items);
    }

    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'category' => ['nullable', 'string', 'max:255'],
            'quantity' => ['sometimes', 'integer', 'min:0'],
            'stock_status' => ['sometimes', 'in:in_stock,low_stock,out_of_stock'],
            'notes' => ['nullable', 'string'],
        ]);

        $item = InventoryItem::create($request->only([
            'name', 'category', 'quantity', 'stock_status', 'notes'
        ]));

        return response()->json($item, 201);
    }

    public function show(InventoryItem $inventory): JsonResponse
    {
        return response()->json($inventory);
    }

    public function update(Request $request, InventoryItem $inventory): JsonResponse
    {
        $request->validate([
            'name' => ['sometimes', 'string', 'max:255'],
            'category' => ['nullable', 'string', 'max:255'],
            'quantity' => ['sometimes', 'integer', 'min:0'],
            'stock_status' => ['sometimes', 'in:in_stock,low_stock,out_of_stock'],
            'notes' => ['nullable', 'string'],
        ]);

        $inventory->update($request->only([
            'name', 'category', 'quantity', 'stock_status', 'notes'
        ]));

        return response()->json($inventory);
    }

    public function destroy(InventoryItem $inventory): JsonResponse
    {
        $inventory->delete();
        return response()->json(['message' => 'Item deleted successfully']);
    }
}
