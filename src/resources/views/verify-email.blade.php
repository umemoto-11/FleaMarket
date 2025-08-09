@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/verify-email.css') }}">
@endsection

@section('content')
<div class="verify__content">
    <p class="verify-message">
        登録していただいたメールアドレスに認証メールを付しました。<br>
        メール認証を完了してください。
    </p>
    <form action="{{ route('verification.notice') }}" method="get">
        <div class="verify__button">
            <button class="verify__button-submit" type="submit">認証はこちらから</button>
        </div>
    </form>
    <form id="resend-form" action="{{ route('verification.send') }}" method="post">
        @csrf
        <div class="resend__link">
            <a href="#" onclick="event.preventDefault(); document.getElementById('resend-form').submit();">認証メールを再送する</a>
        </div>
    </form>
    @if (session('message'))
    <div class="verify__success">
        {{ session('message') }}
    </div>
    @endif
</div>
@endsection