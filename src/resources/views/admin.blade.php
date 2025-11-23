@extends('layouts.auth')

@section('css')
<link rel="stylesheet" href="{{ asset('css/admin.css') }}">
@endsection

@section('content')
<div class="item-detail-container">
    <div class="item-detail-left">
        <div class="item-image-wrapper">
            <img src="{{ asset('storage/' . $item->image ) }}" alt="">
            <input type="hidden" name="id" value="{{ $item['id'] }}">
            @if ($item->is_sold)
            <div class="soldout-label">Sold</div>
            @endif
        </div>
    </div>
    <div class="item-detail-right">
        <h1 class="title">{{ $item->name }}</h1>
        <span class="brand">{{ $item->brand }}</span>
        <span class="price">¥{{ number_format($item->price) }}(税込)</span>
        <div class="icon-utilities">
            @php
            $isLiked = Auth::check() && Auth::user()->hasLiked($item->id);
            @endphp
            <div class="icon-block">
                <button class="like-button {{ $isLiked ? 'liked' : '' }}" data-item-id="{{ $item->id }}" data-csrf="{{ csrf_token() }}">
                <i class="fa {{ $isLiked ? 'fa-solid' : 'fa-regular' }} fa-star"></i>
                </button>
                <span class="likes-count">{{ $item->liked_users_count ?? 0 }}</span>
            </div>

            <script>
                document.addEventListener('DOMContentLoaded', function () {
                    document.querySelectorAll('.like-button').forEach(button => {
                        button.addEventListener('click', function () {
                            const itemId = this.dataset.itemId;
                            const token = this.dataset.csrf;
                            const btn = this;
                            const icon = btn.querySelector('i');
                            const countSpan = btn.nextElementSibling;

                            fetch(`/item/${itemId}/like`, {
                                method: 'POST',
                                headers: {
                                    'X-CSRF-TOKEN': token,
                                    'Accept': 'application/json',
                                    'Content-Type': 'application/json',
                                },
                                body: JSON.stringify({})
                            })
                            .then(response => {
                                if (!response.ok) {
                                    throw new Error('Network response was not ok');
                                }
                                return response.json();
                            })
                            .then(data => {
                                console.log('Like response:', data);
                                if (data.status === 'added') {
                                    btn.classList.add('liked');
                                    icon.classList.remove('fa-regular');
                                    icon.classList.add('fa-solid');
                                } else if (data.status === 'removed') {
                                    btn.classList.remove('liked');
                                    icon.classList.remove('fa-solid');
                                    icon.classList.add('fa-regular');
                                }
                                if (countSpan) {
                                    countSpan.textContent = `${data.likes_count}`;
                                }
                            })
                            .catch(error => {
                                console.error('Fetch error:', error);
                            });
                        });
                    });
                });
            </script>

            <div class="icon-block">
                <i class="fa-regular fa-comment"></i>
                <span class="comment-count">{{ $item->comments->count() }}</span>
            </div>
        </div>
        <form action="{{ route('purchase', ['item_id' => $item->id]) }}" method="get">
            @csrf
            <div class="form__button">
                <button class="form__button-submit" type="submit">購入手続きへ</button>
            </div>
        </form>
        <div class="form__group">
            <h2 class="sub-title">商品説明</h2>
            <p>{{ $item->description }}</p>
        </div>
        <div class="form__group">
            <h2 class="sub-title">商品の情報</h2>
            <div class="item-info-row">
                <div class="item-info-label">カテゴリー</div>
                <div class="item-info-values">
                    @foreach($item->categories as $category)
                    <div class="category-tag">{{ $category->name }}</div>
                    @endforeach
                </div>
            </div>
            <div class="item-info-row">
                <div class="item-info-label">商品の状態</div>
                <div class="item-info-values">
                    <div class="item-info-value">{{ $item->condition->label() }}</div>
                </div>
            </div>
        </div>
        <div class="comments-section">
            <h2>コメント({{ $item->comments->count() }})</h2>
            @forelse ($item->comments ?? [] as $comment)
            <div class="comment">
                <div class="comment-utilities">
                    <img class="comment-image" src="{{ !empty($comment->user->profile->image)
                    ? asset('storage/' . $comment->user->profile->image)
                    : 'data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///ywAAAAAAQABAAACAUwAOw==' }}" alt="">
                    <span class="comment-name">{{ $comment->user->name }}</span>
                </div>
                <div class="comment-body">
                    <p>{{ $comment->comment }}</p>
                </div>
            </div>
            @empty
            <p>こちらにコメントが入ります。</p>
            @endforelse
        </div>
        <div class="form__group">
            <form action="{{ route('comment', ['item_id' => $item->id]) }}" method="post">
                @csrf
                <div class="form__group-title">
                    <span class="form__label--item">商品へのコメント</span>
                </div>
                <div class="form__group-content">
                    <textarea type="comment" name="comment">{{ old('message') }}</textarea>
                    <input type="hidden" name="item_id" value="{{ $item->id }}">
                    <div class="comment-error">
                        @error('comment')
                        {{ $message }}
                        @enderror
                    </div>
                </div>
                <div class="comment__button">
                    <button class="comment__button-submit" type="submit">コメントを送信する</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection