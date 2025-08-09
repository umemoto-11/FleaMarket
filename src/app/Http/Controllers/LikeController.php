<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Item;

class LikeController extends Controller
{
    public function toggle(Request $request, Item $item)
    {
        $user = Auth::user();

        if ($user->hasLiked($item->id)) {
            $user->likedItems()->detach($item->id);
            $status = 'removed';
        } else {
            $user->likedItems()->attach($item->id);
            $status = 'added';
        }

        $item->loadCount('likedUsers');

        return response()->json([
            'status' => $status,
            'likes_count' => $item->liked_users_count
        ]);
    }
}
