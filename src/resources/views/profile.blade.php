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
            <img class="profile-image" src="{{ $imagePath ? asset('storage/' . $imagePath) : 'data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///ywAAAAAAQABAAACAUwAOw==' }}" alt="">
            <div class="profile-info">
                <span class="user_name">{{ optional($profile)->name }}</span>
                @if (!is_null($averageRating) && $averageRating > 0)
                    <div class="average-rating">
                        @for ($i = 1; $i <= 5; $i++)
                            <span class="star" style="color: {{ $i <= $averageRating ? '#FFD700' : '#ccc' }};">★</span>
                        @endfor
                    </div>
                @endif
            </div>
            <button class="edit__button" type="submit">プロフィールを編集</button>
        </div>
    </form>
    <div class="page_link-wrapper">
        <div class="page_link">
            <div class="tab_links">
                <a class="{{ request('page', 'sell') === 'sell' ? 'active' : '' }}" href="{{ route('mypage', ['page' => 'sell']) }}">出品した商品</a>
                <a class="{{ request('page') === 'buy' ? 'active' : '' }}" href="{{ route('mypage', ['page' => 'buy']) }}">購入した商品</a>
                <a class="{{ request('page') === 'trades' ? 'active' : '' }}" href="{{ route('mypage', ['page' => 'trades']) }}">取引中の商品
                    @if($tradesUnreadCount > 0)
                        <span class="tab-unread-badge">{{ $tradesUnreadCount }}</span>
                    @endif
                </a>
            </div>
        </div>
    </div>
</div>
<div class="image-link">
    @foreach($items as $item)
        <ul>
            <li>
                @if($page === 'trades')
                    <a class="item-link" href="{{ route('trade.show', $item->id) }}">
                        <div class="relative-wrapper">
                            <img src="{{ asset('storage/' . $item->item->image) }}" alt="">
                            @if($item->unread_count > 0)
                                <div class="unread-badge">
                                    {{ $item->unread_count }}
                                </div>
                            @endif
                        </div>
                        <div class="link">
                            <span class="link__title">
                                {{ $item->item->name }}
                            </span>
                        </div>
                    </a>
                @else
                    <a class="item-link" href="{{ route('admin', $item->id) }}">
                        <img src="{{ asset('storage/' . $item->image) }}" alt="">
                        <div class="link">
                            <span class="link__title">{{ $item->name }}</span>
                        </div>
                        @if ($item->is_sold)
                            <div class="soldout-label">SOLD OUT</div>
                        @endif
                    </a>
                @endif

            </li>
        </ul>
    @endforeach
</div>
@endsection