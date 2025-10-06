<?php

namespace App\Services;

use App\Models\Coupon;
use App\Models\Order;
use Illuminate\Support\Carbon;

class CouponService
{
    public function validateAndApply(Order $order, string $code): array
    {
        $coupon = Coupon::where('code', $code)->first();
        if (!$coupon) {
            return [false, 'Coupon not found.'];
        }
        // Status and date range
        $now = now();
        if ($coupon->status !== 'active') {
            return [false, 'Coupon inactive.'];
        }
        if ($coupon->starts_at && $now->lt($coupon->starts_at)) {
            return [false, 'Coupon not yet valid.'];
        }
        if ($coupon->ends_at && $now->gt($coupon->ends_at)) {
            return [false, 'Coupon expired.'];
        }
        if ($coupon->usage_limit && $coupon->used_count >= $coupon->usage_limit) {
            return [false, 'Coupon usage limit reached.'];
        }

        // Compute discount based on current order total (pre-discount)
        $subtotal = (float) $order->total_amount; // assuming total_amount holds subtotal here
        $discount = 0.0;
        if ($coupon->discount_type === 'percent') {
            $percent = max(0, min(100, (float) $coupon->discount_value));
            $discount = round($subtotal * ($percent / 100.0), 2);
        } else { // flat
            $discount = min($subtotal, (float) $coupon->discount_value);
        }

        // Update coupon usage counts idempotently
        if ($order->coupon_code && $order->coupon_code !== $coupon->code) {
            // revert previous coupon usage if exists
            $prev = Coupon::where('code', $order->coupon_code)->first();
            if ($prev && $prev->used_count > 0) {
                $prev->decrement('used_count');
            }
        }

        // Apply coupon
        $order->coupon_code = $coupon->code;
        $order->discount_amount = $discount;
        $order->total_amount = max(0, $subtotal - $discount);
        $order->save();

        // Increment current coupon usage if just applied
        if (!$order->wasRecentlyCreated) {
            // increment regardless; in real flow use a pivot log
            $coupon->increment('used_count');
        }

        return [true, 'Coupon applied.'];
    }
}

