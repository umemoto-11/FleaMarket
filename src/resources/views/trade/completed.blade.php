<p>{{ $seller->name }} 様</p>

<p>以下の商品について、購入者が取引を完了しましたのでお知らせいたします。</p>

<hr>

<p>■ 商品名：{{ $trade->item->name }}<br>
■ 取引ID：{{ $trade->id }}<br>
■ 購入者：{{ $buyer->name }}<br>
■ 完了日時：{{ now()->format('Y-m-d H:i') }}</p>

<hr>

<p>取引内容をご確認のうえ、必要に応じて評価を行ってください。</p>

<p>※ このメールは自動送信されています。返信は不要です。</p>

<p>{{ config('app.name') }}</p>
