<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Order;
use App\Models\UserStatusLog;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    /**
     * Display a listing of the users.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $users = User::paginate(10);
        return view('admin.users.index', compact('users'));
    }

    /**
     * Show the form for creating a new user.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.users.create');
    }

    /**
     * Store a newly created user in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, string $locale)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'is_admin' => 'required|boolean',
        ]);
        
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'is_admin' => (bool) $request->is_admin,
        ]);
        
        return redirect()
            ->route('admin.users.index', ['locale' => $locale])
            ->with('success', __('admin.users.quick_admin.success'));
    }

    /**
     * Show the form for editing the specified user.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(string $locale, $id)
    {
        $user = User::findOrFail($id);
        return view('admin.users.edit', compact('user'));
    }

    /**
     * Update the specified user in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, string $locale, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,'.$id,
            'is_admin' => 'required|boolean',
        ]);
        
        $user = User::findOrFail($id);
        
        $user->update($request->except('password'));
        
        if ($request->password) {
            $user->update(['password' => bcrypt($request->password)]);
        }
        
        return redirect()->route('admin.users.index', ['locale' => $locale])->with('success', 'User updated successfully.');
    }

    /**
     * Remove the specified user from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(string $locale, $id)
    {
        $user = User::findOrFail($id);
        $user->delete();
        
        return redirect()->route('admin.users.index', ['locale' => $locale])->with('success', 'User deleted successfully.');
    }

    /**
     * Show a user's profile and order history.
     */
    public function show(string $locale, $id)
    {
        $user = User::findOrFail($id);
        $orders = Order::with('orderItems')
            ->where('user_id', $user->id)
            ->latest()
            ->paginate(10)
            ->appends(request()->query());
        $recentOrders = Order::where('user_id', $user->id)->latest()->take(3)->get();
        $statusLogs = UserStatusLog::where('user_id', $user->id)->latest()->take(10)->get();

        return view('admin.users.show', compact('user', 'orders', 'recentOrders', 'statusLogs'));
    }

    /** Ban user */
    public function ban(string $locale, $id)
    {
        $user = User::findOrFail($id);
        $before = $user->is_banned;
        $user->update(['is_banned' => true]);
        UserStatusLog::create([
            'user_id' => $user->id,
            'admin_id' => Auth::id(),
            'action' => 'ban',
            'from_state' => $before ? 'banned' : 'active',
            'to_state' => 'banned',
        ]);
        return back()->with('success', 'User banned successfully.');
    }

    /** Activate user */
    public function activate(string $locale, $id)
    {
        $user = User::findOrFail($id);
        $before = $user->is_banned;
        $user->update(['is_banned' => false]);
        UserStatusLog::create([
            'user_id' => $user->id,
            'admin_id' => Auth::id(),
            'action' => 'activate',
            'from_state' => $before ? 'banned' : 'active',
            'to_state' => 'active',
        ]);
        return back()->with('success', 'User activated successfully.');
    }
}
