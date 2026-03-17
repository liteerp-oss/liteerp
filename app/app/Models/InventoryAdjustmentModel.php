<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InventoryAdjustmentModel extends Model
{
    protected $table = 'inventory_adjustments';

    protected $fillable = [
        'stock_movements_in_id',
        'qty_adjusted',
        'reason',
        'adjusted_by',
        'purchase_id'
    ];
}
