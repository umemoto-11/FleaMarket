<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Item;

class SearchController extends Controller
{
    public function search(Request $request)
    {
        session(['keyword' => $request->input('keyword')]);

        return redirect()->route('home');
    }
}
