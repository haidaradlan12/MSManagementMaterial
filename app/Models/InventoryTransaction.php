<?php

namespace App\Models;

use Database\Factories\InventoryTransactionFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class InventoryTransaction extends Model
{
    /** @use HasFactory<InventoryTransactionFactory> */
    use HasFactory;

    protected $fillable = [
        'type',
        'material_id',
        'material_name_manual',
        'quantity',
        'location',
        'person_name',
        'purpose',
        'status',
        'validated_at',
    ];

    protected $casts = [
        'validated_at' => 'datetime',
    ];

    /** @return BelongsTo<Material, InventoryTransaction> */
    public function material()
    {
        return $this->belongsTo(Material::class);
    }

    public function getMaterialLabelAttribute(): string
    {
        return $this->material?->material_name ?? $this->material_name_manual ?? '-';
    }

    /**
     * Compute available stock per (material, location) from approved transactions.
     *
     * @return Collection<int, object{
     *   material_id: int|null,
     *   material_name_manual: string|null,
     *   material_name: string,
     *   material_code: string|null,
     *   location: string,
     *   stock_qty: int
     * }>
     */
    public static function computedStock(): Collection
    {
        return DB::table('inventory_transactions')
            ->leftJoin('materials', 'inventory_transactions.material_id', '=', 'materials.id')
            ->where('inventory_transactions.status', 'approved')
            ->groupBy(
                'inventory_transactions.material_id',
                'inventory_transactions.material_name_manual',
                'inventory_transactions.location',
                'materials.material_name',
                'materials.material_code'
            )
            ->select([
                'inventory_transactions.material_id',
                'inventory_transactions.material_name_manual',
                DB::raw('COALESCE(materials.material_name, inventory_transactions.material_name_manual) AS material_name'),
                DB::raw('materials.material_code AS material_code'),
                'inventory_transactions.location',
                DB::raw(
                    'SUM(CASE WHEN inventory_transactions.type = "receive" THEN inventory_transactions.quantity ELSE 0 END) '
                    .'- SUM(CASE WHEN inventory_transactions.type = "take" THEN inventory_transactions.quantity ELSE 0 END) '
                    .'AS stock_qty'
                ),
            ])
            ->havingRaw('stock_qty >= 0')
            ->orderBy('material_name')
            ->orderBy('inventory_transactions.location')
            ->get();
    }
}
