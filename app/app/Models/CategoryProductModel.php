<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CategoryProductModel extends Model
{
    use HasFactory,SoftDeletes;
    protected $table = 'category_product';
    protected $fillable = [
        'name',
        'slug',
        'deleted_at',
        'business_id',
        'description',
        'created_by',
        'tax'
    ];
}
