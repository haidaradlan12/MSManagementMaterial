<?php

namespace App\Models;

use Database\Factories\StockOpnameFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockOpname extends Model
{
    /** @use HasFactory<StockOpnameFactory> */
    use HasFactory;

    protected $fillable = [
        'material_id',
        'material_name_manual',
        'system_quantity',
        'actual_quantity',
        'difference',
        'status',
        'opname_date',
        'location',
        'user_id',
        'notes',
    ];

    /** Display label: prefer material name from relation, fall back to manual name. */
    public function getMaterialLabelAttribute(): string
    {
        return $this->material?->material_name ?? $this->material_name_manual ?? '—';
    }

    public function material()
    {
        return $this->belongsTo(Material::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
