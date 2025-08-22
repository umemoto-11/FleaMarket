<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;
use App\Models\Profile;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\LikeController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\PaymentController;
use App\Http\Requests\ProfileRequest;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/', [ItemController::class, 'index'])->name('home');
Route::get('/search', [SearchController::class, 'search'])->name('search');
Route::get('/item/{item_id}', [ItemController::class, 'show'])->name('admin');

// メール認証関連
Route::get('/email/verify', fn () => view('verify-email'))
    ->middleware('auth')
    ->name('verification.notice');

Route::get('email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
    $request->fulfill();
    return redirect('/first-login');
})->middleware(['auth', 'signed'])->name('verification.verify');

Route::post('email/verification-notification', function (Request $request) {
    $request->user()->sendEmailVerificationNotification();
    return back()->with('message', '確認メールを再送しました。');
})->middleware(['auth', 'throttle:6,1'])->name('verification.send');

// 認証済み & メール認証済みが必要なルート
Route::middleware(['auth', 'verified'])->group(function () {

    // 初回ログイン
    Route::get('/first-login', function () {
        $user = auth()->user();

        if ($user->is_profile_setup) {
            return redirect('/');
        }

        $isFirstLogin = true;
        return view('profile_edit', compact('user', 'isFirstLogin'));
    });

    Route::post('/first-login', function (ProfileRequest $request) {
        $user = auth()->user();

        $image = $request->file('image');
        $path = null;
        if ($image) {
            $fileName = $image->getClientOriginalName();
            $path = $image->storeAs('profiles', $fileName, 'public');
        }

        $profile = Profile::create([
            'name'     => $request->name,
            'postcode' => $request->postcode,
            'address'  => $request->address,
            'building' => $request->building,
            'image'    => $path,
        ]);

        $user->update([
            'profile_id'        => $profile->id,
            'is_profile_setup'  => true,
        ]);

        return redirect('/');
    });

    // いいね & コメント
    Route::post('/item/{item}/like', [LikeController::class, 'toggle'])->name('like');
    Route::post('/item/{item_id}/comment', [CommentController::class, 'comment'])->name('comment');

    // 購入
    Route::get('/purchase/{item_id}', [PurchaseController::class, 'index'])->name('purchase');
    Route::post('/purchase/{item_id}', [PaymentController::class, 'process'])->name('payment.process');
    Route::get('/payment/success', [PaymentController::class, 'success'])->name('payment.success');

    // 住所変更
    Route::get('/purchase/address/{item_id}', [PurchaseController::class, 'edit'])->name('address.edit');
    Route::patch('/purchase/address/{item_id}', [PurchaseController::class, 'update'])->name('address.update');

    // 出品
    Route::get('/sell', [ItemController::class, 'create']);
    Route::post('/sell', [ItemController::class, 'store']);

    // マイページ
    Route::get('/mypage', [ProfileController::class, 'index'])->name('mypage');
    Route::get('/mypage/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/mypage/profile', [ProfileController::class, 'update'])->name('profile.update');
});