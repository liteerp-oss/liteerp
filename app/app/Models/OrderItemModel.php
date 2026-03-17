<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderItemModel extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'order_items';

    /**
     * Mass assignable fields
     */
    protected $fillable = [
        'order_id',
        'stock_movements_in_id',
        'discount',
        'tax',
        'buy_quantity',
        'gift_quantity',
        'compensation_quantity',
        'conversion_quantity',
        'price',
        'cancelled'
    ];

    /**
     * Casts for correct data types
     */
    protected $casts = [
        'price'                  => 'decimal:2',
        'discount'               => 'decimal:2',
        'tax'                    => 'decimal:2',
        'buy_quantity'           => 'decimal:2',
        'gift_quantity'          => 'decimal:2',
        'compensation_quantity'  => 'decimal:2',
        'conversion_quantity'    => 'decimal:2',
    ];
}
