<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Stripe\Stripe;
use Stripe\Checkout\Session as StripeSession;
use App\Models\Item;
use App\Http\Requests\PurchaseRequest;

class PaymentController extends Controller
{
    public function process(PurchaseRequest $request)
    {
        $item = Item::findOrFail($request->item_id);

        if ($item->is_sold) {
            return redirect()->back()->with('error', 'この商品はすでに売り切れています。');
        }

        Stripe::setApiKey(config('services.stripe.secret'));

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

        return redirect($checkoutSession->url);
    }

    public function success(Request $request)
    {
        $item = Item::find($request->input('item_id'));

        if ($item) {
            $item->update([
                'buyer_id' => auth()->id(),
                'is_sold' => true
            ]);
        }

        return redirect()->route('home');
    }
}
