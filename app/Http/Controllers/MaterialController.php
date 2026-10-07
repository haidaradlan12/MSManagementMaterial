<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreMaterialRequest;
use App\Http\Requests\UpdateMaterialRequest;
use App\Models\InventoryTransaction;
use App\Models\Material;

class MaterialController extends Controller
{
    /**
     * Show available stock derived from approved inventory transactions,
     * grouped by (material_name, location).
     */
    public function index()
    {
        $stocks = InventoryTransaction::computedStock();

        return view('materials.index', compact('stocks'));
    }

    public function create()
    {
        return view('materials.create');
    }

    public function store(StoreMaterialRequest $request)
    {
        Material::create($request->validated());

        return redirect()->route('materials.index')->with('success', 'Material berhasil ditambahkan.');
    }

    public function show(Material $material)
    {
        return view('materials.show', compact('material'));
    }

    public function edit(Material $material)
    {
        return view('materials.edit', compact('material'));
    }

    public function update(UpdateMaterialRequest $request, Material $material)
    {
        $material->update($request->validated());

        return redirect()->route('materials.index')->with('success', 'Material berhasil diperbarui.');
    }

    public function destroy(Material $material)
    {
        $material->delete();

        return redirect()->route('materials.index')->with('success', 'Material berhasil dihapus.');
    }
}
