<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Trade;
use App\Models\TradeReview;
use Illuminate\Support\Facades\Mail;
use App\Mail\TradeCompletedMail;

class TradeReviewController extends Controller
{
    public function store(Request $request, $trade_id)
    {
        $trade = Trade::with(['item', 'seller', 'buyer'])->findOrFail($trade_id);

        $user = Auth::user();

        if ($trade->buyer_id !== $user->id && $trade->seller_id !== $user->id) {
            abort(403, '評価する権限がありません');
        }

        $exists = TradeReview::where('trade_id', $trade->id)
            ->where('reviewer_id', $user->id)
            ->exists();

        if ($exists) {
            return back()->with('error', 'この取引はすでに評価済みです。');
        }

        if ($user->id === $trade->buyer_id) {
            $targetUserId = $trade->seller_id;

            $trade->update([
                'status' => 'completed',
                'buyer_completed' => true,
            ]);

            $seller = $trade->seller;
            $buyer  = $user;

            Mail::to($seller->email)->send(new TradeCompletedMail($seller, $buyer, $trade));

        } else {
            $targetUserId = $trade->buyer_id;

            $trade->update([
                'seller_completed' => true,
            ]);
        }

        TradeReview::create([
            'trade_id'   => $trade->id,
            'reviewer_id'=> $user->id,
            'reviewee_id'  => $targetUserId,
            'rating'     => $request->rating,
        ]);

        return redirect()->route('home')->with('success', '評価を送信しました！出品一覧に戻ります。');
    }
}
