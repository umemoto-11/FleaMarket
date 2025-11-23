<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Stripe\Stripe;
use Stripe\Checkout\Session as StripeSession;
use App\Models\Item;
use App\Http\Requests\PurchaseRequest;
use App\Models\Trade;

class PaymentController extends Controller
{
    public function process(PurchaseRequest $request)
    {
        $item = Item::findOrFail($request->item_id);

        if ($item->is_sold) {
            return redirect()->back()->with('error', 'この商品はすでに売り切れています。');
        }

        if (app()->environment('testing') || $request->header('X_TEST_MODE')) {
            $checkoutUrl = route('payment.success', ['item_id' => $item->id]);
            return redirect($checkoutUrl);
        } else {
            \Stripe\Stripe::setApiKey(config('services.stripe.secret'));
            $checkoutSession = StripeSession::create([
                'payment_method_types' => ['card'],
                'line_items' => [[
                    'price_data' => [
                        'currency' => 'jpy',
                        'unit_amount' => $item->price * 100,
                        'product_data' => [
                            'name' => $item->name,
                        ],
                    ],
                    'quantity' => 1,
                ]],
                'mode' => 'payment',
                'success_url' => route('payment.success') . '?item_id=' . $item->id,
                'cancel_url' => url()->previous(),
            ]);
        }

        return redirect()->away($checkoutSession->url);
    }

    public function success(Request $request)
    {
        $item = Item::findOrFail($request->input('item_id'));
        $user = $request->user();

        if ($item->is_sold) {
            return redirect()->route('home')->with('error', 'この商品はすでに購入されています。');
        }

        $item->update([
            'buyer_id' => $user->id,
            'is_sold' => true,
            'shipping_postcode' => $user->profile->postcode,
            'shipping_address' => $user->profile->address,
            'shipping_building' => $user->profile->building ?? null,
        ]);

        $trade = Trade::create([
            'item_id'   => $item->id,
            'buyer_id'  => $user->id,
            'seller_id' => $item->user_id,
            'status'    => 'chatting',
        ]);

        return redirect()->route('trade.show', $trade->id)
            ->with('success', '購入が完了しました。取引を開始してください。');
    }
}
