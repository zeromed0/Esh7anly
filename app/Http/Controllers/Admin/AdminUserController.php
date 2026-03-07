<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Order;
use App\Models\AdminLog;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;

class AdminUserController extends Controller
{
    

public function index()
{
    $users = User::withCount([
            'orders as orders_count' => function ($query) {
                $query->where('status', 'completed');
            }
        ])
        ->withSum([
            'orders as total_spent' => function ($query) {
                $query->where('status', 'completed');
            }
        ], 'total_price')
        ->orderBy('created_at','desc')
        ->paginate(10)
        ->withQueryString();

    $stats = [
        'totalUsers' => User::count(),
        'totalBalance' => User::sum('balance'),

        'totalCompletedOrders' => Order::where('status', 'completed')->count(),
            'totalRevenue' => Order::where('status', 'completed')->sum('total_price'),
        
    ];

    return Inertia::render('Admin/Users', [
        'users' => $users,
        'stats' => $stats
    ]);
}
    public function toggleBan(User $user)
    {
        $user->is_banned = !$user->is_banned;
        $user->save();

        AdminLog::create([
            'admin_id' => auth()->id(),
            'action' => $user->is_banned ? 'Banned User' : 'Unbanned User',
            'target_id' => $user->id,
        ]);

        return back();
    }

    
public function togglePremium(Request $request, User $user)
{
    $days = (int) $request->days;

    if ($days <= 0) {
        $user->update([
            'is_premium' => false,
            'premium_until' => null,
        ]);
    } else {
        $until = now();

        if ($user->premium_until && $user->premium_until > now()) {
            $until = $user->premium_until;
        }

        $user->update([
            'is_premium' => true,
            'premium_until' => $until->addDays($days),
        ]);
    }

    return back();
}

    public function updateBalance(Request $request, User $user)
    {
        $request->validate(['amount' => 'required|numeric']);

        DB::transaction(function () use ($request, $user) {
            $user->balance += $request->amount;
            $user->save();

            Transaction::create([
                'user_id' => $user->id,
                'type' => 'admin_adjustment',
                'amount' => $request->amount,
                'balance_after' => $user->balance,
            ]);

            AdminLog::create([
                'admin_id' => auth()->id(),
                'action' => 'Adjusted Balance',
                'target_id' => $user->id,
            ]);
        });

        return back();
    }

    public function updateNote(Request $request, User $user)
    {
        $user->admin_note = $request->admin_note;
        $user->save();

        AdminLog::create([
            'admin_id' => auth()->id(),
            'action' => 'Updated Note',
            'target_id' => $user->id,
        ]);

        return back();
    }

    public function activity(User $user)
    {
        $activity = Transaction::where('user_id', $user->id)
            ->latest()
            ->take(10)
            ->get();

        return response()->json($activity);
    }
}