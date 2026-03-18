<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class VerificationController extends Controller
{
    // عرض صفحة التوثيق
    public function index()
    {
        $user = Auth::user();

        // إذا كان موثق بالفعل يمكن إعادة التوجيه
        if($user->verification_status === 'verified') {
            return redirect()->route('dashboard');
        }

        return Inertia::render('Verification', [
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'national_id' => $user->national_id,
                'verification_status' => $user->verification_status,
                'id_card_image' => $user->id_card_image,
                'selfie_with_id_image' => $user->selfie_with_id_image,
            ]
        ]);
    }

    // استقبال الصور والتوثيق
    
public function upload(Request $request)
{
    $user = Auth::user();

    // منع الرفع إذا الحالة pending أو verified
    if ($user->verification_status === 'pending' || $user->verification_status === 'verified') {
        return redirect()->back()->with('error', 'Cannot upload new documents until current verification is reviewed.');
    }

    $request->validate([
        'id_card_image' => 'required|image|max:2048',
        'selfie_with_id_image' => 'required|image|max:2048',
    ]);

    if($request->hasFile('id_card_image')) {
        $user->id_card_image = $request->file('id_card_image')->store('verifications', 'public');
    }
    if($request->hasFile('selfie_with_id_image')) {
        $user->selfie_with_id_image = $request->file('selfie_with_id_image')->store('verifications', 'public');
    }

    $user->verification_status = 'pending';
    $user->save();

    return redirect()->back()->with('message', 'Verification submitted successfully. Admin will review your account.');
}
}