<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\User;

class AdminVerificationController extends Controller
{
    // عرض جميع الحسابات مع الحالة
    public function index()
    {
        $users = User::whereIn('verification_status', ['unverified', 'pending', 'verified', 'rejected'])
            ->orderBy('created_at', 'desc')
            ->get([
                'id',
                'name',
                'email',
                'phone',
                'national_id',
                'verification_status',
                'id_card_image',
                'selfie_with_id_image'
            ]);

        return Inertia::render('Admin/Verifications', [
            'users' => $users
        ]);
    }

    // تحديث حالة التوثيق
    public function update(Request $request, User $user)
    {
        $request->validate([
            'verification_status' => 'required|in:verified,rejected',
        ]);

        // تحديث الحالة فقط إذا كانت pending أو unverified
        if (!in_array($user->verification_status, ['pending', 'unverified'])) {
            return redirect()->back()->with('message', 'Cannot update this user.');
        }

        $user->verification_status = $request->verification_status;
        $user->save();

        return redirect()->back()->with('message', 'User verification updated successfully.');
    }
}