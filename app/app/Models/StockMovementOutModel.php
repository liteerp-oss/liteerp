<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockMovementOutModel extends Model
{
    protected $table = 'stock_movements_out';

    protected $fillable = [
        'order_item_id',
        'created_by'
    ];
}
