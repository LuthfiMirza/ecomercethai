<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class MediaController extends Controller
{
    public function paymentProof(Request $request, $orderId)
    {
        $order = Order::findOrFail($orderId);

        if (! $order->payment_proof_path) {
            abort(404);
        }

        $user = $request->user();

        if (! $user) {
            abort(403);
        }

        $isOwner = (int) $order->user_id === (int) $user->id;
        $isAdmin = property_exists($user, 'is_admin') ? (bool) $user->is_admin : false;

        if (! $isOwner && ! $isAdmin) {
            abort(403);
        }

        $path = Str::of($order->payment_proof_path ?? '')->replace('\\', '/')->ltrim('/');

        if ($path->isEmpty()) {
            abort(404);
        }

        $disk = Storage::disk('public');

        if (! $disk->exists($path->toString())) {
            abort(404);
        }

        return $disk->response($path->toString());
    }
}
