<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\CommentRequest;
use App\Models\Comment;

class CommentController extends Controller
{
    public function comment(CommentRequest $request, $item_id)
    {
        $data = $request->validated();

        $user_id = Auth::id();

        $input['user_id'] = $user_id;
        $input['item_id'] = $item_id;

        Comment::create([
            'item_id' => $input['item_id'],
            'user_id' => $input['user_id'],
            'comment' => $data['comment'],
        ]);

        return redirect()->route('admin', ['item_id' => $item_id]);
    }
}
