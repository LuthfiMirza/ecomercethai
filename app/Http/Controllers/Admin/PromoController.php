<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Coupon;

class PromoController extends Controller
{
    public function index()
    {
        $q = request('q');
        $status = request('status');
        $query = Coupon::query()->latest();
        if ($q) {
            $query->where('code', 'like', "%$q%");
        }
        if ($status) {
            $query->where('status', $status);
        }
        $coupons = $query->paginate(10)->appends(request()->query());
        return view('admin.promos.index', compact('coupons','q','status'));
    }

    public function create()
    {
        return view('admin.promos.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'code' => 'required|string|max:50|unique:coupons,code',
            'discount_type' => 'required|in:percent,flat',
            'discount_value' => 'required|numeric|min:0',
            'starts_at' => 'nullable|date',
            'ends_at' => 'nullable|date|after_or_equal:starts_at',
            'usage_limit' => 'nullable|integer|min:1',
            'status' => 'required|in:active,inactive',
        ]);
        Coupon::create($data);
        return redirect()->route('admin.promos.index')->with('success', 'Coupon created successfully.');
    }

    public function edit($id)
    {
        $coupon = Coupon::findOrFail($id);
        return view('admin.promos.edit', compact('coupon'));
    }

    public function update(Request $request, $id)
    {
        $coupon = Coupon::findOrFail($id);
        $data = $request->validate([
            'code' => 'required|string|max:50|unique:coupons,code,'.$coupon->id,
            'discount_type' => 'required|in:percent,flat',
            'discount_value' => 'required|numeric|min:0',
            'starts_at' => 'nullable|date',
            'ends_at' => 'nullable|date|after_or_equal:starts_at',
            'usage_limit' => 'nullable|integer|min:1',
            'status' => 'required|in:active,inactive',
        ]);
        $coupon->update($data);
        return redirect()->route('admin.promos.index')->with('success', 'Coupon updated successfully.');
    }

    public function destroy($id)
    {
        $coupon = Coupon::findOrFail($id);
        $coupon->delete();
        return redirect()->route('admin.promos.index')->with('success', 'Coupon deleted.');
    }
}

