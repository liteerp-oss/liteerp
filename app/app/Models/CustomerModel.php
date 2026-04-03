<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class CustomerModel extends Model
{
    use HasFactory,SoftDeletes;

    protected $table = 'customers';

    /**
     * Fields allow mass assignment
     */
    protected $fillable = [
        'business_id',
        'name',
        'contact_name',
        'email',
        'phone',
        'address',
        'tax_code',
        'national_id',
        'bank_name',
        'bank_account',
        'type',
        'group',
        'website',
        'note',
        'active',
        'deleted_at'
    ];

    /**
     * Casts for specific fields
     */
    protected $casts = [
        'active' => 'boolean',
        'type' => 'string', // Could be enum if using PHP enum
    ];

    /**
     * Relationship: belongs to Business
     */
    public function business()
    {
        return $this->belongsTo(BusinessModel::class, 'business_id');
    }
}
