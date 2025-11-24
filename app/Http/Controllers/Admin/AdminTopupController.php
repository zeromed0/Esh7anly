<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Str;
use App\Models\Game;
use App\Models\Offer;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AdminTopupController extends Controller
{
    // صفحة TopUps
    public function index()
    {
        $games = Game::with('offers')->where('type', 'topup')->get();
        return Inertia::render('Admin/TopUps', ['games' => $games]);
    }

    // إضافة لعبة جديدة
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
            'type' => 'topup', // ← يتم تحديد النوع تلقائيًا
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
            'type' => 'topup',
        ]);

        return back();
    }

    // حذف لعبة
    public function destroyGame(Game $game)
    {
        $game->delete();
        return back();
    }

    // ---------- العروض ----------
    public function storeOffer(Request $request)
    {
        $request->validate([
            'game_id' => 'required|exists:games,id',
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'image' => 'nullable|string',
        ]);

        Offer::create($request->only('game_id', 'name', 'price', 'image'));
        return back();
    }

    public function updateOffer(Request $request, Offer $offer)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'image' => 'nullable|string',
        ]);

        $offer->update($request->only('name', 'price', 'image'));
        return back();
    }

    public function destroyOffer(Offer $offer)
    {
        $offer->delete();
        return back();
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