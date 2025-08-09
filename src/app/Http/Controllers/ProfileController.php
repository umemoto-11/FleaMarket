<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $page = $request->query('page');
        $items = collect();

        if ($page === 'buy') {
            $items = $user->purchasedItems ?: collect();
        } elseif ($page === 'sell') {
            $items = $user->listedItems ?: collect();
        } else {
            $items = $user->listedItems;
        }

        $profile = $user->profile;

        return view('profile', compact('profile', 'items', 'page'));
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
