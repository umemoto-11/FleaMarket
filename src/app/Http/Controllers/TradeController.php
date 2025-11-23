<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Trade;
use App\Models\TradeMessage;
use App\Models\Item;
use App\Http\Requests\StoreTradeMessageRequest;

class TradeController extends Controller
{
    public function show(Trade $trade)
    {
        $trade->load([
            'item',
            'buyer',
            'seller',
            'messages.user',
        ]);

        if ($trade->buyer_id !== Auth::id() && $trade->seller_id !== Auth::id()) {
            abort(403, 'この取引を見る権限がありません。');
        }

        $partner = $trade->buyer_id === Auth::id()
            ? $trade->seller
            : $trade->buyer;

        $otherTrades = Trade::where(function ($q) {
                $q->where('buyer_id', Auth::id())
                    ->orWhere('seller_id', Auth::id());
            })
            ->where('id', '!=', $trade->id)
            ->with('item')
            ->get();

        TradeMessage::where('trade_id', $trade->id)
            ->where('user_id', '!=', Auth::id())
            ->where('is_read', false)
            ->update([
                'is_read' => true,
                'read_at' => now(),
            ]);

        return view('trade.show', compact(
            'trade',
            'partner',
            'otherTrades'
        ));
    }

    public function storeMessage(StoreTradeMessageRequest $request, $id)
    {
        $trade = Trade::findOrFail($id);

        if ($trade->buyer_id !== Auth::id() && $trade->seller_id !== Auth::id()) {
            abort(403);
        }

        $data = [
            'trade_id' => $trade->id,
            'user_id'  => Auth::id(),
            'message'  => $request->message,
        ];

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('chat', 'public');
            $data['image'] = $path;
        }

        TradeMessage::create($data);

        return back()->with('success', 'メッセージを送信しました。');
    }

    public function updateMessage(Request $request, $message_id)
    {
        $message = TradeMessage::with('trade')->findOrFail($message_id);

        if ($message->user_id !== Auth::id()) {
            abort(403);
        }

        $trade = $message->trade;
        if ($trade->buyer_id !== Auth::id() && $trade->seller_id !== Auth::id()) {
            abort(403);
        }

        $request->validate([
            'message' => 'required|string|max:2000'
        ]);

        $message->update([
            'message' => $request->message,
            'is_edited' => true,
        ]);

        return back()->with('success', 'メッセージを編集しました。');
    }

    public function deleteMessage($message_id)
    {
        $message = TradeMessage::with('trade')->findOrFail($message_id);

        if ($message->user_id !== Auth::id()) {
            abort(403);
        }

        $trade = $message->trade;
        if ($trade->buyer_id !== Auth::id() && $trade->seller_id !== Auth::id()) {
            abort(403);
        }

        $message->delete();

        return back()->with('success', 'メッセージを削除しました。');
    }
}
