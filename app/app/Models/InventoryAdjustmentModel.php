<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InventoryAdjustmentModel extends Model
{
    protected $table = 'inventory_adjustments';

    protected $fillable = [
        'product_id',
        'warehouse_id',
        'qty_adjusted',
        'reason',
        'adjusted_by',
        'purchase_id'
    ];
}
