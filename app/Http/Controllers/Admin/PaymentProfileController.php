<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PaymentProfile;
use App\Models\User;
use Illuminate\Http\Request;

class PaymentProfileController extends Controller
{
    public function index(Request $request)
    {
        $q = trim($request->query('q', ''));

        $profiles = PaymentProfile::with('user')
            ->when($q !== '', function ($query) use ($q) {
                $query->where(function ($sub) use ($q) {
                    $sub->where('provider', 'like', "%$q%")
                        ->orWhere('account_name', 'like', "%$q%")
                        ->orWhere('account_number', 'like', "%$q%")
                        ->orWhereHas('user', function ($uq) use ($q) {
                            $uq->where('name', 'like', "%$q%")
                               ->orWhere('email', 'like', "%$q%");
                        });
                });
            })
            ->orderByDesc('created_at')
            ->paginate(12)
            ->appends(['q' => $q]);

        return view('admin.payment_profiles.index', compact('profiles', 'q'));
    }

    public function create()
    {
        $users = User::orderBy('name')->get();

        return view('admin.payment_profiles.create', compact('users'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'user_id' => 'required|exists:users,id',
            'provider' => 'required|string|max:255',
            'account_number' => 'nullable|string|max:255',
            'account_name' => 'nullable|string|max:255',
            'is_default' => 'sometimes|boolean',
        ]);

        $data['is_default'] = (bool) ($data['is_default'] ?? false);

        PaymentProfile::create($data);

        return redirect()->route('admin.payment-profiles.index')->with('status', 'Payment profile created');
    }

    public function show(string $id)
    {
        return redirect()->route('admin.payment-profiles.edit', $id);
    }

    public function edit(string $id)
    {
        $profile = PaymentProfile::findOrFail($id);
        $users = User::orderBy('name')->get();

        return view('admin.payment_profiles.edit', compact('profile', 'users'));
    }

    public function update(Request $request, string $id)
    {
        $profile = PaymentProfile::findOrFail($id);

        $data = $request->validate([
            'user_id' => 'required|exists:users,id',
            'provider' => 'required|string|max:255',
            'account_number' => 'nullable|string|max:255',
            'account_name' => 'nullable|string|max:255',
            'is_default' => 'sometimes|boolean',
        ]);

        $data['is_default'] = (bool) ($data['is_default'] ?? false);

        $profile->update($data);

        return redirect()->route('admin.payment-profiles.index')->with('status', 'Payment profile updated');
    }

    public function destroy(string $id)
    {
        $profile = PaymentProfile::findOrFail($id);
        $profile->delete();

        return back()->with('status', 'Payment profile deleted');
    }
}
