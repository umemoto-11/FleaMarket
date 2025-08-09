@extends('layouts.app')

@section('header')
<div class="search-form-wrapper">
    <form action="/search" method="get">
        @csrf
        <input class="search-form" type="text" name="keyword" placeholder="なにをお探しですか？" value="{{ session('keyword') }}">
    </form>
</div>
<ul class="header-nav">
    @if (Auth::check())
    <li>
        <form action="/logout" method="post">
            @csrf
            <button class="header__logout-button" type="submit">ログアウト</button>
        </form>
    </li>
    @else
    <li>
        <a class="header__login-button" href="/login">ログイン</a>
    </li>
    @endif
    <li>
        <a class="header__logout-button" href="/mypage">マイページ</a>
    </li>
    <li>
        <form action="/sell" method="get">
            @csrf
            <button class="header-nav__button" type="submit">出品</button>
        </form>
    </li>
</ul>
@endsection