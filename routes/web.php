<?php

use App\Http\Controllers\InventoryTransactionController;
use App\Http\Controllers\MaterialController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\StockOpnameController;
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

    return view('dashboard', compact('totalMaterials', 'totalOpnames', 'totalPending', 'latestOpnames', 'stock'));
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
    Route::patch('transactions/{inventoryTransaction}/approve', [InventoryTransactionController::class, 'approve'])->name('transactions.approve');
    Route::patch('transactions/{inventoryTransaction}/reject', [InventoryTransactionController::class, 'reject'])->name('transactions.reject');

    // User management - only for admin
    Route::middleware([\App\Http\Middleware\IsAdmin::class])->group(function () {
        Route::get('users', [App\Http\Controllers\UserController::class, 'index'])->name('users.index');
        Route::patch('users/{user}/approve', [App\Http\Controllers\UserController::class, 'approve'])->name('users.approve');
        Route::patch('users/{user}/reject', [App\Http\Controllers\UserController::class, 'reject'])->name('users.reject');
        Route::patch('users/{user}/make-admin', [App\Http\Controllers\UserController::class, 'makeAdmin'])->name('users.make-admin');
        Route::patch('users/{user}/remove-admin', [App\Http\Controllers\UserController::class, 'removeAdmin'])->name('users.remove-admin');
        Route::delete('users/{user}', [App\Http\Controllers\UserController::class, 'destroy'])->name('users.destroy');
    });
});

require __DIR__.'/auth.php';
