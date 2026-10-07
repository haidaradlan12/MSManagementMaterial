<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Material extends Model
{
    /** @use HasFactory<\Database\Factories\MaterialFactory> */
    use HasFactory;

    protected $fillable = [
        'material_code',
        'material_name',
        'stock_quantity',
        'unit',
        'remarks',
    ];

    public function stockOpnames()
    {
        return $this->hasMany(StockOpname::class);
    }
}
