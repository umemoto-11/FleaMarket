@extends('layouts.auth')

@section('css')
<link rel="stylesheet" href="{{ asset('css/exhibition.css') }}">
@endsection

@section('content')
<div class="content">
    <div class="heading">
        <h1>商品の出品</h1>
    </div>
    <form action="/sell" method="post" enctype="multipart/form-data">
        @csrf
        <div class="form__group">
            <div class="form__group-title">
                <span class="form__label--item">商品画像</span>
            </div>
            <div class="form__group-content">
                <div class="image-upload" id="image-upload">
                    <img id="preview" src="" alt="">
                    <input id="image" type="file" name="image" accept="image/*" onchange="previewImage(this)" value="{{ old('image') }}">
                    <span class="upload-label">
                        画像を選択する
                    </span>
                </div>
                <div class="form__error">
                    @error('image')
                    {{ $message }}
                    @enderror
                </div>
            </div>

            <script>
                function previewImage(input) {
                    var preview = document.getElementById('preview');
                    var imageUpload = document.getElementById('image-upload');

                    if (input.files && input.files[0]) {
                        var fileReader = new FileReader();
                        fileReader.onload = function (e) {
                            preview.src = e.target.result;
                            imageUpload.classList.add('has-image');
                        };
                        fileReader.readAsDataURL(input.files[0]);
                    } else {
                        preview.src = '';
                        imageUpload.classList.remove('has-image');
                    }
                }
            </script>

        </div>
        <div class="subtitle">
            <p class="section-title">商品の詳細</p>
        </div>
        <div class="form__group">
            <div class="form__group-title">
                <span class="form__label--item">カテゴリー</span>
            </div>
            <div class="form__group-content">
                @foreach ($categories as $category)
                <input type="checkbox" id="category-{{ $category->id }}" name="category_ids[]" value="{{ $category->id }}">
                <label for="category-{{ $category->id }}">
                    {{ $category->name }}
                </label>
                @endforeach
                <div class="form__error">
                    @error('category_ids')
                    {{ $message }}
                    @enderror
                </div>
            </div>
        </div>
        <div class="form__group">
            <div class="form__group-title">
                <span class="form__label--item">商品の状態</span>
            </div>
            <div class="form__group-content">
                <select name="condition" id="">
                    <option value="">選択してください</option>
                    @foreach (\App\Enums\Condition::cases() as $condition)
                    <option value="{{ $condition->value }}">{{ $condition->label() }}</option>
                    @endforeach
                </select>
                <div class="form__error">
                    @error('condition')
                    {{ $message }}
                    @enderror
                </div>
            </div>
        </div>
        <div class="subtitle">
            <p class="section-title">商品名と説明</p>
        </div>
        <div class="form__group">
            <div class="form__group-title">
                <span class="form__label--item">商品名</span>
            </div>
            <div class="form__group-content">
                <div class="form__input-text">
                    <input type="text" name="name" value="{{ old('name') }}">
                </div>
                <div class="form__error">
                    @error('name')
                    {{ $message }}
                    @enderror
                </div>
            </div>
        </div>
        <div class="form__group">
            <div class="form__group-title">
                <span class="form__label--item">ブランド名</span>
            </div>
            <div class="form__group-content">
                <div class="form__input-text">
                    <input type="text" name="brand" value="{{ old('brand') }}">
                </div>
            </div>
        </div>
        <div class="form__group">
            <div class="form__group-title">
                <span class="form__label--item">商品の説明</span>
            </div>
            <div class="form__group-content">
                <div class="form__input-text">
                    <textarea type="textarea" name="description" value="{{ old('description') }}"></textarea>
                </div>
                <div class="form__error">
                    @error('description')
                    {{ $message }}
                    @enderror
                </div>
            </div>
        </div>
        <div class="form__group">
            <div class="form__group-title">
                <span class="form__label--item">販売価格</span>
            </div>
            <div class="form__group-content">
                <div class="form__input-text price-input">
                    <input type="number" name="price" min="0" step="1" value="{{ old('price') }}">
                </div>
                <div class="form__error">
                    @error('price')
                    {{ $message }}
                    @enderror
                </div>
            </div>
        </div>
        <div class="form__button">
            <button class="form__button-submit" type="submit">出品する</button>
        </div>
    </form>
</div>
@endsection