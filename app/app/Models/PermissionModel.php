<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PermissionModel extends Model
{
    //
    public $table = "permissions";
    protected $fillable = ['group_id', 'permission'];
}
