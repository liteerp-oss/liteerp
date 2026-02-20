<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PermissionGroupUserModel extends Model
{
    //
    public $table = "permission_group_user";
    protected $fillable = ['group_id','account_id'];
}
