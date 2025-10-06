<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Coupon extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'discount_type',
        'discount_value',
        'min_purchase',
        'max_discount',
        'is_active',
        'valid_from',
        'valid_until',
        'usage_limit',
        'used_count',
    ];

    protected $casts = [
        'valid_from' => 'datetime',
        'valid_until' => 'datetime',
        'discount_value' => 'decimal:2',
        'min_purchase' => 'decimal:2',
        'max_discount' => 'decimal:2',
        'usage_limit' => 'integer',
        'used_count' => 'integer',
        'is_active' => 'boolean',
    ];
}

