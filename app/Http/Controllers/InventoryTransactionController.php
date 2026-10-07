<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreInventoryTransactionRequest;
use App\Http\Requests\UpdateInventoryTransactionRequest;
use App\Models\InventoryTransaction;
use App\Models\Material;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class InventoryTransactionController extends Controller
{
    /** Public landing page – no auth required. */
    public function publicIndex()
    {
        $materials = Material::orderBy('material_name')->get();
        $stocks = InventoryTransaction::computedStock();

        return view('transactions.public', compact('materials', 'stocks'));
    }

    /** Store submission from public form. */
    public function publicStore(StoreInventoryTransactionRequest $request)
    {
        $data = $request->validated();

        // For receive: material_name_manual is now the only identifier
        if ($data['type'] === 'receive' && empty($data['material_name_manual'])) {
            return back()
                ->withInput()
                ->withErrors(['material_name_manual' => 'Nama barang wajib diisi.']);
        }

        // For take: require either material_id or material_name_manual (for manual stock entries)
        if ($data['type'] === 'take' && empty($data['material_id']) && empty($data['material_name_manual'])) {
            return back()
                ->withInput()
                ->withErrors(['material_id' => 'Pilih barang yang tersedia dari daftar.']);
        }

        InventoryTransaction::create(array_merge($data, ['status' => 'pending']));

        return redirect()
            ->route('transactions.public')
            ->with('success', 'Pengajuan berhasil dikirim! Silakan tunggu konfirmasi admin.');
    }

    /** Admin: list pending & history. */
    public function index(Request $request)
    {
        $pending = InventoryTransaction::with('material')
            ->where('status', 'pending')
            ->latest()
            ->get();

        $query = InventoryTransaction::with('material')
            ->whereIn('status', ['approved', 'rejected']);

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('material_name')) {
            $search = $request->material_name;
            $query->where(function ($q) use ($search) {
                $q->whereHas('material', function ($mq) use ($search) {
                    $mq->where('material_name', 'like', '%'.$search.'%');
                })->orWhere('material_name_manual', 'like', '%'.$search.'%');
            });
        }

        $history = $query->latest()->paginate(15)->withQueryString();

        return view('transactions.index', compact('pending', 'history'));
    }

    /** Admin: approve a transaction. */
    public function approve(InventoryTransaction $inventoryTransaction)
    {
        $inventoryTransaction->update([
            'status' => 'approved',
            'validated_at' => Carbon::now(),
        ]);

        return redirect()->route('transactions.index')->with('success', 'Transaksi disetujui.');
    }

    /** Admin: reject a transaction. */
    public function reject(InventoryTransaction $inventoryTransaction)
    {
        $inventoryTransaction->update([
            'status' => 'rejected',
            'validated_at' => Carbon::now(),
        ]);

        return redirect()->route('transactions.index')->with('success', 'Transaksi ditolak.');
    }

    /** Admin: delete a transaction. */
    public function destroy(InventoryTransaction $inventoryTransaction)
    {
        $inventoryTransaction->delete();

        return redirect()->route('transactions.index')->with('success', 'Transaksi dihapus.');
    }

    public function create()
    {
        $materials = Material::orderBy('material_name')->get();
        $stocks = InventoryTransaction::computedStock();

        return view('transactions.public', compact('materials', 'stocks'));
    }

    public function store(StoreInventoryTransactionRequest $request)
    {
        return $this->publicStore($request);
    }

    public function show(InventoryTransaction $inventoryTransaction)
    {
        return view('transactions.show', compact('inventoryTransaction'));
    }

    public function edit(InventoryTransaction $inventoryTransaction)
    {
        $materials = Material::orderBy('material_name')->get();

        return view('transactions.edit', compact('inventoryTransaction', 'materials'));
    }

    public function update(UpdateInventoryTransactionRequest $request, InventoryTransaction $inventoryTransaction)
    {
        $inventoryTransaction->update($request->validated());

        return redirect()->route('transactions.index')->with('success', 'Transaksi diperbarui.');
    }
}
