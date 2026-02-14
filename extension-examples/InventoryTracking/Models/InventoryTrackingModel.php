<?php

namespace Extensions\InventoryTracking\Models;

use Illuminate\Database\Eloquent\Model;

class InventoryTrackingModel extends Model
{
    protected $table = 'inventory_tracking';
    protected $fillable = ['min','business_id'];
}
