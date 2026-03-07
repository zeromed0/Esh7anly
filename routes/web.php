<?php

use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Illuminate\Http\Request;

use App\Http\Controllers\Admin\AdminAuthController;
use App\Http\Controllers\Admin\AdminTopupController;
use App\Http\Controllers\Admin\AdminOrderController;
use App\Http\Controllers\Admin\AdminVoucherController;
use App\Http\Controllers\Admin\AdminWalletVoucherController;
use App\Http\Controllers\Admin\AdminUserController;
use App\Http\Controllers\Admin\AdminLoginController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\AdminSettingsController;

// User Controllers
use App\Http\Controllers\User\DashboardController;
use App\Http\Controllers\User\TopupController;
use App\Http\Controllers\User\VoucherController;
use App\Http\Controllers\User\WalletController;
use App\Http\Controllers\User\TransactionController;
use App\Http\Controllers\User\ProfileController;

// الصفحة الرئيسية
Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

// ============================
// 📦 Routes خاصة بالمستخدم (User Panel)
// ============================
Route::middleware(['auth', 'verified'])->prefix('user')->name('user.')->group(function () {


    // 🏠 Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->name('dashboard');

        Route::middleware(['auth','not.banned'])->group(function () {

    // 🔝 TopUp
    Route::get('/topup', [TopupController::class, 'index'])->name('topup');
    Route::post('/topup', [TopupController::class, 'store'])->name('topup.store');

    // 🎟️ Vouchers Routes
Route::get('/vouchers', [VoucherController::class, 'index'])->name('vouchers.index');
Route::post('/vouchers', [VoucherController::class, 'store'])->name('vouchers.store');

    // 💳 Wallet
    Route::get('/wallet', [WalletController::class, 'index'])->name('wallet.index');
    Route::post('/wallet/redeem', [WalletController::class, 'redeemVoucher']);

    // صفحة المعاملات (transactions)
    Route::get('/transactions', [TransactionController::class, 'index'])->name('transactions.index');

    // 👤 Profile
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile.index');
    Route::post('/profile/avatar', [ProfileController::class, 'uploadAvatar'])
        ->name('profile.avatar');
    Route::post('/profile/identity', [ProfileController::class, 'uploadIdentity'])
    ->name('profile.identity');
        // Update name / basic profile
    Route::patch('/user/profile', [ProfileController::class, 'updateProfile'])->name('profile.update');

    // Change password
    Route::patch('/user/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');
});

    

    Route::post('/set-language', function (Request $request) {
    $locale = $request->locale;
    if (in_array($locale, ['EN', 'AR'])) {
        session(['locale' => $locale]);
        app()->setLocale($locale);
    }
    return back(); // إعادة المستخدم لنفس الصفحة
});

});

// ============================
// 🎮 Route عام للـ Dashboard ليتوافق مع Ziggy (حل نهائي للخطأ)
// ============================
Route::middleware(['auth', 'verified'])->get('/dashboard', [DashboardController::class, 'index'])
    ->name('dashboard');

// ============================
// 🔐 Auth routes (Login / Register / Logout)
// ============================

// صفحات تسجيل الدخول الخاصة بالأدمن
// ✅ تسجيل الدخول كأدمن
Route::get('/admin/login', [AdminAuthController::class, 'showLoginForm'])->name('admin.login');
Route::post('/admin/login', [AdminAuthController::class, 'login']);
Route::post('/admin/logout', [AdminAuthController::class, 'logout'])->name('admin.logout');


Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {


    Route::get('/dashboard', fn() => Inertia::render('Admin/Dashboard'))->name('admin.dashboard');

    // Orders
    Route::get('/orders', [AdminOrderController::class, 'index'])->name('admin.orders.index');
    Route::post('/orders/{id}/status', [AdminOrderController::class, 'updateStatus'])->name('admin.orders.updateStatus');

    // Top Ups
    Route::get('/topups', [AdminTopupController::class, 'index'])->name('topups');
 // ---------- الألعاب ----------
    // إضافة لعبة جديدة
    Route::post('/topups', [AdminTopupController::class, 'storeGame'])->name('topups.storeGame');

    // تعديل لعبة
    Route::put('/topups/game/{game}', [AdminTopupController::class, 'updateGame'])->name('topups.updateGame');

    // حذف لعبة
    Route::delete('/topups/game/{game}', [AdminTopupController::class, 'destroyGame'])->name('topups.destroyGame');

    Route::put('/topups/game/{game}/toggle', [AdminTopupController::class, 'toggleGame']);
Route::put('/topups/offer/{offer}/toggle', [AdminTopupController::class, 'toggleOffer']);

    // ---------- العروض ----------
    // إضافة عرض جديد
    Route::post('/topups/offer', [AdminTopupController::class, 'storeOffer'])->name('topups.storeOffer');

    // تعديل عرض
    Route::put('/topups/offer/{offer}', [AdminTopupController::class, 'updateOffer'])->name('topups.updateOffer');

    // حذف عرض
    Route::delete('/topups/offer/{offer}', [AdminTopupController::class, 'destroyOffer'])->name('topups.destroyOffer');
     // 🔹 صفحة الفوتشرز
    Route::get('/vouchers', [AdminVoucherController::class, 'index'])->name('vouchers');

    // 🔹 عمليات الألعاب (Games)
    Route::post('/vouchers', [AdminVoucherController::class, 'storeGame']); // إضافة لعبة
    Route::put('/vouchers/game/{game}', [AdminVoucherController::class, 'updateGame']); // تعديل لعبة
    Route::delete('/vouchers/game/{game}', [AdminVoucherController::class, 'destroyGame']); // حذف لعبة

    // 🔹 عمليات العروض (Offers)
    Route::post('/vouchers/offer', [AdminVoucherController::class, 'storeOffer']); // إضافة عرض
    Route::put('/vouchers/offer/{offer}', [AdminVoucherController::class, 'updateOffer']); // تعديل عرض
    Route::delete('/vouchers/offer/{offer}', [AdminVoucherController::class, 'destroyOffer']); // حذف عرض

    Route::post('/vouchers/offer/{offer}/codes', [AdminVoucherController::class, 'storeVoucherCode']);
Route::put('/vouchers/offer/{offer}/codes/{code}', [AdminVoucherController::class, 'updateVoucherCode']);
Route::delete('/vouchers/offer/{offer}/codes/{code}', [AdminVoucherController::class, 'destroyVoucherCode']);
Route::get('/vouchers/offer/{offer}/codes', [AdminVoucherController::class, 'getVoucherCodes']);
    
     // الألعاب
Route::post('/vouchers/game/{game}/toggle-active', [AdminVoucherController::class, 'toggleGameActive']);

// العروض
Route::post('/vouchers/offer/{offer}/toggle-active', [AdminVoucherController::class, 'toggleOfferActive']);

    
Route::get('/wallet-vouchers', [AdminWalletVoucherController::class, 'index']);
Route::post('/wallet-vouchers', [AdminWalletVoucherController::class, 'store']);
Route::delete('/wallet-vouchers/{id}', [AdminWalletVoucherController::class, 'destroy']);
// Users
    Route::get('/users', [AdminUserController::class, 'index']);

    Route::post('/user/{user}/toggle-ban', [AdminUserController::class, 'toggleBan']);
    Route::post('/user/{user}/toggle-premium', [AdminUserController::class, 'togglePremium']);
    Route::post('/user/{user}/update-balance', [AdminUserController::class, 'updateBalance']);
    Route::post('/user/{user}/update-note', [AdminUserController::class, 'updateNote']);

    Route::get('/user/{user}/activity', [AdminUserController::class, 'activity']);

    Route::get('/settings', [AdminSettingsController::class, 'index']);
    Route::post('/settings', [AdminSettingsController::class, 'update']);

});




require __DIR__.'/auth.php';