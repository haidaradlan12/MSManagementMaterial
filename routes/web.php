<?php

use App\Http\Controllers\InventoryTransactionController;
use App\Http\Controllers\MaterialController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\StockOpnameController;
use App\Http\Controllers\UserController;
use App\Http\Middleware\IsAdmin;
use App\Models\InventoryTransaction;
use App\Models\StockOpname;
use Illuminate\Support\Facades\Route;

// Root redirect to public portal
Route::get('/', function () {
    return redirect()->route('transactions.public');
});

// ── Public Dashboard (no login required) ────────────────────────────────────
Route::get('/portal', [InventoryTransactionController::class, 'publicIndex'])->name('transactions.public');
Route::post('/portal', [InventoryTransactionController::class, 'publicStore'])->name('transactions.public.store');

// ── Protected Dashboard ──────────────────────────────────────────────────────
Route::get('/dashboard', function () {
    $stock = InventoryTransaction::computedStock();
    $totalMaterials = $stock->unique('material_name')->count();
    $totalOpnames = StockOpname::count();
    $totalPending = InventoryTransaction::where('status', 'pending')->count();
    $latestOpnames = StockOpname::with('material')->latest()->take(5)->get();

    // Build latest opname keyed by material_name|location
    $latestOpnameMap = StockOpname::with('material')
        ->get()
        ->groupBy(function ($o) {
            $name = strtolower(trim($o->material?->material_name ?? $o->material_name_manual ?? ''));

            return $name.'|'.strtolower(trim($o->location ?? ''));
        })
        ->map(fn ($g) => $g->sortByDesc(fn ($o) => $o->opname_date.'_'.str_pad($o->id, 10, '0', STR_PAD_LEFT))->first());

    // Build overview: each stock row + latest opname data
    $overviewMaterial = $stock->values()->map(function ($row) use ($latestOpnameMap) {
        $key = strtolower(trim($row->material_name)).'|'.strtolower(trim($row->location ?? ''));
        $opname = $latestOpnameMap->get($key);

        return (object) [
            'material_name' => $row->material_name,
            'location' => $row->location,
            'system_qty' => $row->stock_qty,
            'actual_qty' => $opname?->actual_quantity,
            'status' => $opname?->status,
            'difference' => $opname?->difference,
            'last_opname_at' => $opname?->opname_date,
        ];
    })->sortBy('material_name')->values();

    return view('dashboard', compact('totalMaterials', 'totalOpnames', 'totalPending', 'latestOpnames', 'stock', 'overviewMaterial'));
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::resource('materials', MaterialController::class);
    Route::resource('stock-opnames', StockOpnameController::class);

    // Inventory transactions – admin area
    Route::resource('transactions', InventoryTransactionController::class)
        ->except(['create', 'store'])
        ->parameters(['transactions' => 'inventoryTransaction']);
    Route::post('transactions/{inventoryTransaction}/approve', [InventoryTransactionController::class, 'approve'])->name('transactions.approve');
    Route::patch('transactions/{inventoryTransaction}/reject', [InventoryTransactionController::class, 'reject'])->name('transactions.reject');

    // User management - only for admin
    Route::middleware([IsAdmin::class])->group(function () {
        Route::get('users', [UserController::class, 'index'])->name('users.index');
        Route::patch('users/{user}/approve', [UserController::class, 'approve'])->name('users.approve');
        Route::patch('users/{user}/reject', [UserController::class, 'reject'])->name('users.reject');
        Route::patch('users/{user}/make-admin', [UserController::class, 'makeAdmin'])->name('users.make-admin');
        Route::patch('users/{user}/remove-admin', [UserController::class, 'removeAdmin'])->name('users.remove-admin');
        Route::delete('users/{user}', [UserController::class, 'destroy'])->name('users.destroy');
    });
});

require __DIR__.'/auth.php';
