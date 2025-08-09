@extends('layout.auth')

@section('css')
<link rel="stylesheet" href="{{ asset('css/mylist.css') }}">
@endsection

@section('content')
<div class="content">
    <div class="page_link">
        <a class="recommend" href="/">おすすめ</a>
        <a class="mylist" href="{{ route('mylist') }}">マイリスト</a>
    </div>
    <div class="img__link">
        @if ($items->isEmpty())
        @foreach($items as $item)
        <ul>
            <li>
                <a href="{{ route('admin', $item->id) }}">
                    <img src="{{ asset('storage/' . $item->image) }}" alt="">
                    <div class="link">
                        <span class="link__title">{{ $item->name }}</span>
                    </div>
                </a>
            </li>
        </ul>
        @endforeach
        @endif
    </div>
</div>
@endsection