@extends('layouts.auth')

@section('css')
<link rel="stylesheet" href="{{ asset('css/index.css') }}">
@endsection

@section('content')
<div class="content">
    <div class="page_link-wrapper">
        <div class="page_link">
            <div class="tab_links">
                <a class="{{ request('tab') !== 'mylist' ? 'active' : '' }}" href="{{ route('home')}}">おすすめ</a>
                <a class="{{ request('tab') === 'mylist' ? 'active' : '' }}" href="{{ route('home', ['tab' => 'mylist']) }}">マイリスト</a>
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
                <div class="soldout-label">Sold</div>
                @endif
            </a>
        </li>
    </ul>
    @endforeach
</div>
@endsection