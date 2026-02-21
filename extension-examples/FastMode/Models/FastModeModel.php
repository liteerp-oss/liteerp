<?php

namespace Extensions\FastMode\Models;

use Illuminate\Database\Eloquent\Model;

class FastModeModel extends Model
{
    protected $table = "FastMode";
    protected $fillable = ["status","business_id"];
}