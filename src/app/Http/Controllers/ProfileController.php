<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Trade;
use App\Models\TradeReview;

class ProfileController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $page = $request->query('page', 'sell');
        $items = collect();

        if ($page === 'sell') {
            $items = $user->listedItems ?: collect();
        }
        elseif ($page === 'buy') {
            $items = $user->purchasedItems ?: collect();
        }
        elseif ($page === 'trades') {
            $userId = $user->id;

            $items = Trade::where(function ($q) use ($userId) {
                        $q->where('buyer_id', $userId)
                            ->orWhere('seller_id', $userId);
                    })
                    ->with([
                        'item',
                        'messages' => function ($q) {
                            $q->latest();
                        }
                    ])
                    ->withCount([
                        'messages as unread_count' => function ($q) use ($userId) {
                            $q->where('user_id', '!=', $userId)
                            ->where('is_read', false);
                        }
                    ])
                    ->get()
                    ->sortByDesc(function ($trade) {
                        return optional($trade->messages->first())->created_at;
                    })
                    ->values();
        }

        $tradesUnreadCount = $items->sum('unread_count');

        $averageRating = TradeReview::where('reviewee_id', $user->id)
                            ->avg('rating');

        $averageRatingRounded = $averageRating ? round($averageRating) : null;

        return view('profile', [
            'profile' => $user->profile,
            'items' => $items,
            'page' => $page,
            'averageRating' => $averageRatingRounded,
            'tradesUnreadCount' => $tradesUnreadCount,
        ]);
    }

    public function edit()
    {
        $user = Auth::user();

        if (!$user->is_profile_setup) {
            return view('profile_edit', [
                'isFirstLogin' => true,
                'user' => $user
            ]);
        } else {
            return view('profile_edit', [
                'isFirstLogin' => false,
                'user' => $user
            ]);
        }
    }

    public function update(Request $request)
    {
        $user = Auth::user();

        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $fileName = $image->getClientOriginalName();
            $path = $image->storeAs('items', $fileName, 'public');
        } else {
            $path = $user->profile->image;
        }

        $user->profile()->update([
            'name' => $request->name,
            'postcode' => $request->postcode,
            'address' => $request->address,
            'building' => $request->building,
            'image' => $path,
        ]);

        return redirect('mypage');
    }
}
