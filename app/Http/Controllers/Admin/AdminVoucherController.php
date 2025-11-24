<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Str;
use App\Models\Game;
use App\Models\Offer;
use App\Models\VoucherCode;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AdminVoucherController extends Controller
{
    // تعديل دالة index لتجلب voucherCodes لكل عرض
public function index()
{
    // جلب الالعاب مع العروض ومع أكواد كل عرض
    $games = Game::with('offers.voucherCodes')->where('type', 'voucher')->get();
    return Inertia::render('Admin/Vouchers', ['games' => $games]);
}

    // إضافة لعبة جديدة (Voucher Game)
    public function storeGame(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'image' => 'nullable|string',
        ]);

        Game::create([
            'name' => $request->name,
            'description' => $request->description,
            'image' => $request->image,
            'slug' => Str::slug($request->name),
            'type' => 'voucher',
        ]);

        return back();
    }

    // تعديل لعبة
    public function updateGame(Request $request, Game $game)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'image' => 'nullable|string',
        ]);

        $game->update([
            'name' => $request->name,
            'description' => $request->description,
            'image' => $request->image,
            'slug' => Str::slug($request->name),
            'type' => 'voucher',
        ]);

        return back();
    }

    // حذف لعبة
    public function destroyGame(Game $game)
    {
        $game->delete();
        return back();
    }

    // إضافة عرض جديد
    // إضافة عرض جديد بدون كود
public function storeOffer(Request $request)
{
    $request->validate([
        'game_id' => 'required|exists:games,id',
        'name' => 'required|string|max:255',
        'price' => 'required|numeric|min:0',
        'image' => 'nullable|string',
    ]);

    Offer::create($request->only('game_id','name','price','image'));
    return back();
}

// تعديل عرض بدون كود
public function updateOffer(Request $request, Offer $offer)
{
    $request->validate([
        'name' => 'required|string|max:255',
        'price' => 'required|numeric|min:0',
        'image' => 'nullable|string',
    ]);

    $offer->update($request->only('name','price','image'));
    return back();
}

    // حذف عرض
    public function destroyOffer(Offer $offer)
    {
        $offer->delete();
        return back();
    }

    // ---------------- Voucher codes API existing (تبقى كما هي) ----------------
// جلب الأكواد الخاصة بعرض (قمتَ بإضافتها مسبقاً، تبقى هكذا)
public function getVoucherCodes(Offer $offer)
{
    return response()->json([
        'codes' => $offer->voucherCodes()->get()
    ]);
}

// إضافة كود جديد (تبقى كما هي)
public function storeVoucherCode(Request $request, Offer $offer)
{
    $request->validate([
        'code' => 'required|string|max:255',
    ]);

    $code = $offer->voucherCodes()->create(['code' => $request->code]);

    // لو أردت إرجاع الكود الجديد كـ JSON:
    return response()->json(['code' => $code]);
}

// تعديل و حذف الأكواد تبقى كما كتبتها
public function updateVoucherCode(Request $request, Offer $offer, $codeId)
{
    $request->validate(['code' => 'required|string|max:255']);

    $code = $offer->voucherCodes()->findOrFail($codeId);
    $code->update(['code' => $request->code]);

    return back();
}

public function destroyVoucherCode(Offer $offer, $codeId)
{
    $code = $offer->voucherCodes()->findOrFail($codeId);
    $code->delete();

    return response()->json(['deleted' => true]);
}


// تغيير حالة تفعيل اللعبة
public function toggleGameActive(Game $game)
{
    $game->is_active = !$game->is_active;
    $game->save();

    return back();
}

// تغيير حالة تفعيل العرض
public function toggleOfferActive(Offer $offer)
{
    $offer->is_active = !$offer->is_active;
    $offer->save();

    return back();
}

}