<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreStockOpnameRequest;
use App\Http\Requests\UpdateStockOpnameRequest;
use App\Models\InventoryTransaction;
use App\Models\StockOpname;
use Illuminate\Http\Request;

class StockOpnameController extends Controller
{
    public function index(Request $request)
    {
        $stockOpnames = StockOpname::with('material', 'user')
            ->when($request->filled('date_from'), fn ($q) => $q->whereDate('opname_date', '>=', $request->date_from))
            ->when($request->filled('date_to'), fn ($q) => $q->whereDate('opname_date', '<=', $request->date_to))
            ->latest()
            ->paginate(10)
            ->withQueryString();

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

        // Build recommended re-opname: stocks whose LATEST opname result is surplus or missing
        // OR where current system stock != latest actual stock
        $latestOpnamePerPair = StockOpname::query()
            ->with('material')
            ->get()
            ->groupBy(function ($o) {
                $name = strtolower(trim($o->material?->material_name ?? $o->material_name_manual ?? ''));

                return $name.'|'.strtolower(trim($o->location ?? ''));
            })
            ->map(fn ($group) => $group->sortByDesc(fn ($o) => $o->opname_date.'_'.str_pad($o->id, 10, '0', STR_PAD_LEFT))->first());

        // Match those pairs back against computed stocks for current qty info
        $computedStocksByKey = InventoryTransaction::computedStock()
            ->values()
            ->keyBy(function ($row) {
                return strtolower(trim($row->material_name)).'|'.strtolower(trim($row->location ?? ''));
            });

        $recommendedOpname = $latestOpnamePerPair->map(function ($opname, $key) use ($computedStocksByKey) {
            $stock = $computedStocksByKey->get($key);
            $stockQty = $stock?->stock_qty ?? 0;

            return (object) [
                'material_name' => $opname->material?->material_name ?? $opname->material_name_manual ?? '—',
                'location' => $opname->location ?? '-',
                'stock_qty' => $stockQty,
                'actual_qty' => $opname->actual_quantity,
                'last_status' => $opname->status,
                'last_diff' => $opname->difference,
                'last_opname_at' => $opname->opname_date,
            ];
        })->filter(function ($item) {
            return in_array($item->last_status, ['surplus', 'missing']) || ($item->stock_qty != $item->actual_qty);
        })->values();

        return view('stock_opnames.index', compact('stockOpnames', 'notOpnamed', 'recommendedOpname'));

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
