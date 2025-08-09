@extends('layouts.auth')

@section('css')
<link rel="stylesheet" href="{{ asset('css/purchase.css') }}">
@endsection

@section('content')
<div class="purchase-container">
    <form action="{{ route('payment.process', ['item_id' => $item->id]) }}" method="post">
        @csrf
        <input type="hidden" name="item_id" value="{{ $item->id }}">
        @if (session('error'))
        <div class="alert-danger">
            {{ session('error') }}
        </div>
        @endif
        <div class="product-detail">
            <div class="product-image">
                <img src="{{ asset('storage/' . $item->image ) }}" alt="">
                @if ($item->is_sold)
                <div class="soldout-label">SOLD OUT</div>
                @endif
            </div>
            <div class="product-info">
                <h1>{{ $item->name }}</h1>
                <p class="price">¥ {{ number_format($item->price) }}</p>
            </div>
            <div class="divider"></div>
            <div class="payment-method">
                <h2>支払い方法</h2>
                <select id="payment_method" name="payment_method">
                    <option value="">選択してください</option>
                    <option value="convenience_store">コンビニ払い</option>
                    <option value="credit">カード支払い</option>
                </select>
                <div class="form__error">
                    @error('payment_method')
                    {{ $message }}
                    @enderror
                </div>
            </div>
            <div class="divider"></div>
            <div class="delivery-address">
                <h2>
                    配送先
                    <a href="{{ route('address.update', ['item_id' => $item->id]) }}">変更する</a>
                </h2>
                <p>
                    〒{{ Auth::user()->profile->postcode ?? '未設定' }}<br>
                    {{ Auth::user()->profile->address ?? '' }}{{ Auth::user()->profile->building ?? '' }}
                </p>
                <div class="form__error">
                    @error('address')
                    {{ $message }}
                    @enderror
                </div>
            </div>
            <div class="divider"></div>
        </div>
        <aside class="product-right">
            <div class="summary-box">
                <div class="summary-row">
                    <div class="summary-label">商品代金</div>
                    <div class="summary-value">¥{{ number_format($item->price) }}</div>
                </div>
                <div class="summary-row">
                    <div class="summary-label">支払い方法</div>
                    <div id="selected-payment-method" class="summary-value">コンビニ払い</div>
                </div>

                <script>
                    document.getElementById('payment_method').addEventListener('change', function() {
                        const selectedMethod = this.options[this.selectedIndex].text;

                        document.getElementById('selected-payment-method').textContent = selectedMethod;
                    });
                </script>

            </div>
            <button class="purchase-button" type="submit">購入する</button>
        </aside>
    </form>
</div>
@endsection
