@component('mail::message')

{{ $seller->name }} 様

以下の商品について、購入者が取引を完了しましたのでお知らせいたします。

---

■ 商品名：{{ $trade->item->name }}
■ 取引ID：{{ $trade->id }}
■ 購入者：{{ $buyer->name }}
■ 完了日時：{{ now()->format('Y-m-d H:i') }}

---

取引内容をご確認のうえ、必要に応じて評価を行ってください。

※ このメールは自動送信されています。返信は不要です。

{{ config('app.name') }}
@endcomponent