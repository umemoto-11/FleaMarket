@extends('layouts.auth')

@section('css')
<link rel="stylesheet" href="{{ asset('css/profile.css') }}">
@endsection

@section('content')
<div class="content">
    <form action="/mypage/profile" method="get">
        @csrf
        <div class="profile-utilities">
            @php
            $imagePath = optional($profile)->image;
            @endphp
            <img class="profile-image" src="{{ asset('storage/' . $imagePath) }}" alt="">
            <span class="user_name">{{ optional($profile)->name }}</span>
            <button class="edit__button" type="submit">プロフィールを編集</button>
        </div>
    </form>
    <div class="page_link-wrapper">
        <div class="page_link">
            <div class="tab_links">
                <a class="{{ request('page', 'sell') === 'sell' ? 'active' : '' }}" href="{{ route('mypage', ['page' => 'sell']) }}">出品した商品</a>
                <a class="{{ request('page') === 'buy' ? 'active' : '' }}" href="{{ route('mypage', ['page' => 'buy']) }}">購入した商品</a>
            </div>
        </div>
    </div>
</div>
<div class="image-link">
    @foreach($items as $item)
    <ul>
        <li>
            <a class="item-link" href="{{ route ('admin', $item->id) }}">
                <img src="{{ asset('storage/' . $item->image) }}" alt="">
                <div class="link">
                    <span class="link__title">{{ $item->name }}</span>
                </div>
                @if ($item->is_sold)
                <div class="soldout-label">SOLD OUT</div>
                @endif
            </a>
        </li>
    </ul>
    @endforeach
</div>
@endsection