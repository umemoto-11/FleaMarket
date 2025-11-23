@extends('layouts.auth')

@section('css')
<link rel="stylesheet" href="{{ asset('css/trade_show.css') }}">
@endsection

@section('content')
<div class="container-fluid p-0">
    <div class="row g-0 vh-100">
        <div class="col-auto trade-sidebar p-3">
            <h5 class="fw-bold mb-2">その他の取引画面</h5>
            <ul class="list-group">
                @foreach($otherTrades as $t)
                    <a href="{{ route('trade.show', $t->id) }}"
                    class="list-group-item {{ $t->id == $trade->id ? 'active' : '' }}">
                        {{ $t->item->name }}
                    </a>
                @endforeach
            </ul>
        </div>
        <div class="col trade-main-wrapper">
            <div class="trade-main">
                <div class="trade-header">
                    <div class="d-flex align-items-center gap-2">
                        @php
                            $partnerIcon = optional($partner->profile)->icon;
                        @endphp
                        <img src="{{ $partnerIcon ? asset('storage/' . $partnerIcon) : 'data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///ywAAAAAAQABAAACAUwAOw==' }}"
                            class="rounded-circle" width="50" height="50">
                        <h4 class="mb-0">「{{ $partner->name }}」 さんとの取引</h4>
                    </div>
                    @if($trade->status === 'chatting' && $trade->buyer_id === auth()->id())
                        <button class="btn btn-complete" id="complete-trade-btn" data-bs-toggle="modal" data-bs-target="#reviewModal">
                            取引を完了する
                        </button>
                    @endif
                </div>
                <div class="trade-item">
                    @php
                        $itemImage = optional($trade->item)->image;
                    @endphp
                    <div class="d-flex">
                        <img src="{{ $itemImage ? asset('storage/' . $itemImage) : 'data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///ywAAAAAAQABAAACAUwAOw==' }}"
                            class="item-image">
                        <div class="ms-3">
                            <h5 class="item-name">{{ $trade->item->name }}</h5>
                            <p class="item-price">¥{{ number_format($trade->item->price) }}</p>
                        </div>
                    </div>
                </div>
                <div class="chat-box">
                    @foreach($trade->messages as $msg)
                        @php
                            $userIcon = optional($msg->user->profile)->icon;
                        @endphp
                        @if($msg->user_id === auth()->id())
                            <div class="chat-message self" data-message-id="{{ $msg->id }}">
                                <div class="d-flex align-items-start flex-row-reverse">
                                    <img src="{{ $userIcon ? asset('storage/' . $userIcon) : 'data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///ywAAAAAAQABAAACAUwAOw==' }}"
                                        class="chat-avatar ms-2">
                                    <div class="chat-bubble-wrapper">
                                        <div class="chat-user-info text-end">
                                            <strong>{{ $msg->user->name }}</strong>
                                        </div>
                                        <div class="chat-bubble my-message">
                                            @if($msg->image)
                                                <img src="{{ asset('storage/' . $msg->image) }}" class="chat-image mb-1">
                                            @endif
                                            {{ $msg->message }}
                                        </div>
                                        <div class="chat-actions text-end">
                                            <button type="button" class="edit-message-btn btn btn-link p-0">編集</button>
                                            <form action="{{ route('trade.message.delete', $msg->id) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button class="delete-message-btn btn btn-link p-0">削除</button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @else
                            <div class="chat-message partner" data-message-id="{{ $msg->id }}">
                                <div class="d-flex align-items-start">
                                    <img src="{{ $userIcon ? asset('storage/' . $userIcon) : 'data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///ywAAAAAAQABAAACAUwAOw==' }}"
                                        class="chat-avatar me-2">
                                    <div class="chat-bubble-wrapper">
                                        <div class="chat-user-info">
                                            <strong>{{ $msg->user->name }}</strong>
                                        </div>
                                        <div class="chat-bubble partner-message">
                                            @if($msg->image)
                                                <img src="{{ asset('storage/' . $msg->image) }}" class="chat-image mb-1">
                                            @endif
                                            {{ $msg->message }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                    @endforeach
                </div>
                <div class="chat-form-wrapper">
                    <div class="error mb-2" id="chat-error">
                        @error('message')
                            {{ $message }}
                        @enderror
                        @error('image')
                            {{ $message }}
                        @enderror
                    </div>
                    <div class="image-preview mb-2 d-flex gap-2" id="image-preview"></div>
                    <form action="{{ route('trade.message.store', $trade->id) }}" method="POST" id="chat-form" enctype="multipart/form-data" class="d-flex gap-2 align-items-end">
                        @csrf
                        <input type="hidden" name="edit_message_id" id="edit-message-id">
                        <input type="text" name="message" id="chat-input" class="form-control" placeholder="取引メッセージを記入してください" value="{{ old('message') }}">
                        <div class="chat-image-upload-wrapper">
                            <label for="chat-image" class="chat-image-btn">画像を追加</label>
                            <input type="file" id="chat-image" name="image" accept=".jpeg,.jpg,.png" class="chat-image-input">
                        </div>
                        <button type="submit" id="send-btn" class="btn btn-send">
                            <img src="{{ asset('img/e99395e98ea663a8400f40e836a71b8c4e773b01.jpg') }}" alt="送信">
                        </button>
                        <button type="button" id="update-btn" class="btn btn-primary d-none">更新</button>
                        <button type="button" id="cancel-btn" class="btn btn-light d-none">キャンセル</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@php
    $partnerIcon = optional($partner->profile)->icon;
    $showReviewModal = ($trade->status === 'completed')
                        && ($trade->buyer_id !== auth()->id())
                        && !\App\Models\TradeReview::where('trade_id', $trade->id)
                                                    ->where('reviewer_id', auth()->id())
                                                    ->exists();
@endphp
<div class="modal fade" id="reviewModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('trade.review.store', $trade->id) }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">取引が完了しました。</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <label class="modal-label">今回の取引相手はどうでしたか？</label>
                    <div id="star-rating" class="d-flex gap-1" style="font-size: 32px; cursor: pointer;">
                        @for ($i = 1; $i <= 5; $i++)
                            <span class="star" data-value="{{ $i }}" style="color: #ccc;">★</span>
                        @endfor
                    </div>
                    <input type="hidden" name="rating" id="rating-value" required>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-primary" type="submit">送信する</button>
                </div>
            </form>
        </div>
    </div>
</div>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const stars = document.querySelectorAll('#star-rating .star');
        const ratingValue = document.getElementById('rating-value');
        stars.forEach(star => {
            star.addEventListener('click', function() {
                let value = this.dataset.value;
                ratingValue.value = value;
                stars.forEach(s => {
                    s.style.color = s.dataset.value <= value ? '#FFD700' : '#ccc';
                });
            });
        });

        const chatForm = document.getElementById('chat-form');
        const chatInput = document.getElementById('chat-input');
        const editMessageId = document.getElementById('edit-message-id');
        const sendBtn = document.getElementById('send-btn');
        const updateBtn = document.getElementById('update-btn');
        const cancelBtn = document.getElementById('cancel-btn');

        document.querySelectorAll('.edit-message-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const bubble = this.closest('.chat-message').querySelector('.chat-bubble');
                chatInput.value = bubble.textContent.trim();
                editMessageId.value = this.closest('.chat-message').dataset.messageId;
                sendBtn.classList.add('d-none');
                updateBtn.classList.remove('d-none');
                cancelBtn.classList.remove('d-none');
            });
        });

        cancelBtn.addEventListener('click', function() {
            chatInput.value = '';
            editMessageId.value = '';
            sendBtn.classList.remove('d-none');
            updateBtn.classList.add('d-none');
            cancelBtn.classList.add('d-none');
        });

        updateBtn.addEventListener('click', function() {
            if(!editMessageId.value) return;
            const formData = new FormData();
            formData.append('message', chatInput.value);
            formData.append('_token', '{{ csrf_token() }}');
            fetch(`/trade/message/${editMessageId.value}/edit`, { method:'POST', body:formData })
                .then(res => location.reload());
        });

        const chatImage = document.getElementById('chat-image');
        const preview = document.getElementById('image-preview');
        chatImage.addEventListener('change', function() {
            preview.innerHTML = '';
            const file = this.files[0];
            if(file) {
                const img = document.createElement('img');
                img.src = URL.createObjectURL(file);
                img.style.maxWidth = '150px';
                img.style.maxHeight = '150px';
                preview.appendChild(img);
            }
        });
    });

    document.addEventListener('DOMContentLoaded', function() {
        var showReviewModal = @json($showReviewModal);
        if (showReviewModal) {
            var reviewModal = new bootstrap.Modal(document.getElementById('reviewModal'));
            reviewModal.show();
        }
    });
</script>

@endsection

