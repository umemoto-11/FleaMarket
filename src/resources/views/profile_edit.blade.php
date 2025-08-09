@extends('layouts.auth')

@section('css')
<link rel="stylesheet" href="{{ asset('css/profile_edit.css') }}">
@endsection

@section('content')
<div class="content">
    <div class="heading">
        <h1>プロフィール設定</h1>
    </div>
    <form action="{{ $isFirstLogin ? url('first-login') : route('profile.update') }}" method="post" enctype="multipart/form-data">
        @if (!$isFirstLogin)
            @method('PATCH')
        @endif
        @csrf
        <div class="form__group">
            <div class="form__group-content">
                <div class="profile-image-row">
                    @php
                    $imagePath = optional($user->profile)->image;
                    @endphp
                    @if ($imagePath)
                    <img id="existing-image" class="profile-image" src="{{ asset('storage/' . $imagePath) }}" alt="">
                    @else
                    <div id="existing-image" class="profile-image--placeholder"></div>
                    @endif
                    <img id="preview" class="profile-image preview-hidden" src="" alt="">
                    <label for="file-upload" class="form__file-label">画像を選択する</label>
                    <input id="file-upload" class="form__file-input" type="file" name="image" accept="image/*" onchange="previewImage(this)">
                </div>
                <div class="form__error">
                    @error('image')
                    {{ $message }}
                    @enderror
                </div>
            </div>

            <script>
                function previewImage(input) {
                    const file = input.files[0];
                    if (!file) return;

                    const reader = new FileReader();
                    reader.onload = function(e) {
                        const preview = document.getElementById('preview');
                        const existing = document.getElementById('existing-image');

                        preview.src = e.target.result;
                        preview.classList.remove('preview-hidden');

                        if (existing) {
                            existing.style.display = 'none';
                        }
                    };
                    reader.readAsDataURL(file);
                }
            </script>

        </div>
        <div class="form__group">
            <div class="form__group-title">
                <span class="form__label--item">ユーザー名</span>
            </div>
            <div class="form__group-content">
                <div class="form__input-text">
                    <input type="text" name="name" value="{{ old('name', optional(Auth::user()->profile)->name ?? '') }}">
                    <div class="form__error">
                        @error('name')
                        {{ $message }}
                        @enderror
                    </div>
                </div>
            </div>
        </div>
        <div class="form__group">
            <div class="form__group-title">
                <span class="form__label--item">郵便番号</span>
            </div>
            <div class="form__group-content">
                <div class="form__input-text">
                    <input type="text" name="postcode" value="{{ old('postcode', optional(Auth::user()->profile)->postcode ?? '') }}">
                    <div class="form__error">
                        @error('postcode')
                        {{ $message }}
                        @enderror
                    </div>
                </div>
            </div>
        </div>
        <div class="form__group">
            <div class="form__group-title">
                <span class="form__label--item">住所</span>
            </div>
            <div class="form__group-content">
                <div class="form__input-text">
                    <input type="text" name="address" value="{{ old('address', optional(Auth::user()->profile)->address ?? '') }}">
                    <div class="form__error">
                        @error('address')
                        {{ $message }}
                        @enderror
                    </div>
                </div>
            </div>
        </div>
        <div class="form__group">
            <div class="form__group-title">
                <span class="form__label--item">建物名</span>
            </div>
            <div class="form__group-content">
                <div class="form__input-text">
                    <input type="text" name="building" value="{{ old('building', optional(Auth::user()->profile)->building ?? '') }}">
                    <div class="form__error">
                    </div>
                </div>
            </div>
        </div>
        <div class="form__button">
            <button class="form__button-submit" type="submit">更新する</button>
        </div>
    </form>
</div>
@endsection