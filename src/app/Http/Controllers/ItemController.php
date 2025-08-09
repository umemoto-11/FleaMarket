<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use App\Models\Item;
use App\Models\Category;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\ExhibitionRequest;
use App\Http\Requests\CommentRequest;

class ItemController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $tab = $request->query('tab');

        $keyword = session('keyword');

        if ($tab === 'mylist') {
            if ($user) {
                $query = $user->likedItems();
                if (!empty($keyword)) {
                    $query->where('name', 'like', '%' . $keyword . '%');
                }
                $items = $query->get();
            } else {
                $items = collect();
            }
        } else {
            $query = Item::query();
            if (!empty($keyword)) {
                $query->where('name', 'like', '%' . $keyword . '%');
            }

            if (Auth::check()) {
                $query->where('user_id', '!=', Auth::id());
            }

            $items = $query->get();
        }

        return view('index', compact('items', 'tab', 'keyword'));
    }

    public function show($id)
    {
        $item = Item::with('comments.user')->withCount('likedUsers')->findOrFail($id);

        return view('admin', compact('item'));
    }

    public function create()
    {
        $categories = Category::all();

        return view('exhibition', compact('categories'));
    }

    public function store(ExhibitionRequest $request)
    {
        $categories = Category::all();

        $image = $request->file('image');
        $fileName = $image->getClientOriginalName();
        $path = $image->storeAs('items', $fileName, 'public');

        $item = Item::create([
            'name' => $request->input('name'),
            'price' => $request->input('price'),
            'brand' => $request->input('brand'),
            'image' => $path,
            'condition' => $request->input('condition'),
            'description' => $request->input('description'),
            'user_id' => Auth::id(),
        ]);

        $item->categories()->sync($request->input('category_ids', []));

        return redirect('/');
    }

    public function likes()
    {
        $user = Auth::user();
        $items = $user->likeItems()->latest()->get();

        return view('mylist', compact('items'));
    }
}
