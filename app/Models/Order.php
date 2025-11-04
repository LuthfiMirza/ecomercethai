<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Order extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'total_amount',
        'status',
        'shipping_address',
        'payment_method',
        'coupon_code',
        'discount_amount',
        'payment_status',
        'payment_proof_path',
        'payment_verified_at',
        'track_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'payment_verified_at' => 'datetime',
    ];

    /**
     * Get the user that owns the order.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the order items for the order.
     */
    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Resolve the public URL for the payment proof file.
     */
    public function getPaymentProofUrlAttribute(): ?string
    {
        if (! $this->payment_proof_path) {
            return null;
        }

        $path = str_replace('\\', '/', (string) $this->payment_proof_path);
        $path = ltrim($path, '/');

        return asset('storage/' . $path);
    }

    /**
     * Public tracking URL accessor.
     */
    public function getTrackUrlAttribute(): ?string
    {
        if (! $this->track_token) {
            return null;
        }

        try {
            return localized_route('orders.track', ['token' => $this->track_token]);
        } catch (\Throwable $e) {
            return null;
        }
    }
}
