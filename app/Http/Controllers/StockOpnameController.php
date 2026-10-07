<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreStockOpnameRequest;
use App\Http\Requests\UpdateStockOpnameRequest;
use App\Models\InventoryTransaction;
use App\Models\StockOpname;

class StockOpnameController extends Controller
{
    public function index()
    {
        $stockOpnames = StockOpname::with('material', 'user')->latest()->paginate(10);

        return view('stock_opnames.index', compact('stockOpnames'));
    }

    public function create()
    {
        // Show ALL computed stocks, same as the Materials page
        $stocks = InventoryTransaction::computedStock()->values();

        return view('stock_opnames.create', compact('stocks'));
    }

    public function store(StoreStockOpnameRequest $request)
    {
        $data = $request->validated();
        $data['difference'] = $data['actual_quantity'] - $data['system_quantity'];
        $data['status'] = $data['difference'] > 0 ? 'surplus' : ($data['difference'] < 0 ? 'missing' : 'matched');
        $data['user_id'] = auth()->id();

        // If material_id is absent (manual stock), clear it so FK is not violated
        if (empty($data['material_id'])) {
            $data['material_id'] = null;
        }

        StockOpname::create($data);

        return redirect()->route('stock-opnames.index')->with('success', 'Stock Opname berhasil dicatat.');
    }

    public function show(StockOpname $stockOpname)
    {
        return view('stock_opnames.show', compact('stockOpname'));
    }

    public function edit(StockOpname $stockOpname)
    {
        // Show ALL computed stocks, same as the Materials page
        $stocks = InventoryTransaction::computedStock()->values();

        return view('stock_opnames.edit', compact('stockOpname', 'stocks'));
    }

    public function update(UpdateStockOpnameRequest $request, StockOpname $stockOpname)
    {
        $data = $request->validated();
        $data['difference'] = $data['actual_quantity'] - $data['system_quantity'];
        $data['status'] = $data['difference'] > 0 ? 'surplus' : ($data['difference'] < 0 ? 'missing' : 'matched');

        $stockOpname->update($data);

        return redirect()->route('stock-opnames.index')->with('success', 'Stock Opname berhasil diperbarui.');
    }

    public function destroy(StockOpname $stockOpname)
    {
        $stockOpname->delete();

        return redirect()->route('stock-opnames.index')->with('success', 'Stock Opname berhasil dihapus.');
    }
}
