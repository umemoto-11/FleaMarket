<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Item;

class SearchController extends Controller
{
    public function search(Request $request)
    {
        // session(['keyword' => $request->input('keyword')]);

        // return redirect()->route('home');

        $keyword = $request->input('keyword');

        $items = Item::where('name', 'like', "%{$keyword}%")->get();

        session(['keyword' => $keyword]);

        return view('index', compact('items', 'keyword'));
    }
}
