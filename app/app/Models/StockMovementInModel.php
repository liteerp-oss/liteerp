<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockMovementInModel extends Model
{
    protected $table = 'stock_movements_in';

    protected $fillable = [
        'product_id',
        'warehouse_id',
        'qty_change',
        'stock_in_id',
        'created_by',
    ];
}
