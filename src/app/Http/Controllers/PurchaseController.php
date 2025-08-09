<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Item;
use App\Models\Profile;
use App\Http\Requests\AddressRequest;

class PurchaseController extends Controller
{
    public function index($id)
    {
        $item = Item::findOrFail($id);

        return view('purchase', compact('item'));
    }

    public function edit($id)
    {
        $user = Auth::user();
        $item = Item::findOrFail($id);

        return view('address', compact('item', 'user'));
    }

    public function update(AddressRequest $request, $id)
    {
        $item = Item::findOrFail($id);

        Auth::user()->profile()->update([
            'postcode' => $request->postcode,
            'address' => $request->address,
            'building' => $request->building,
        ]);

        return redirect()->route('purchase', ['item_id' => $item->id]);
    }
}
