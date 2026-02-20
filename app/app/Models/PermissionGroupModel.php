<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PermissionGroupModel extends Model
{
    //
    public $table = "permission_groups";
    protected $fillable = ['name', 'business_id','type','user_id'];
    public function business(){
        return $this->hasOne(BusinessModel::class,'id','business_id');
    }
}
