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

        // Build a set of "material_name|location" pairs that have ever been opnamed
        $opnamedPairs = StockOpname::query()
            ->get(['material_name_manual', 'material_id', 'location'])
            ->map(function ($o) {
                $name = $o->material?->material_name ?? $o->material_name_manual ?? '';

                return strtolower(trim($name)).'|'.strtolower(trim($o->location ?? ''));
            })
            ->filter()
            ->unique()
            ->flip(); // flip so we can use isset() for O(1) lookup

        // Filter computed stocks to those whose pair has never been opnamed
        $notOpnamed = InventoryTransaction::computedStock()
            ->values()
            ->filter(function ($row) use ($opnamedPairs) {
                $key = strtolower(trim($row->material_name)).'|'.strtolower(trim($row->location ?? ''));

                return ! isset($opnamedPairs[$key]);
            })
            ->values();

        return view('stock_opnames.index', compact('stockOpnames', 'notOpnamed'));
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
