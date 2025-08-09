<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CommentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'item_id' => ['required', 'exists:items,id'],
            'comment' => ['required', 'max:255'],
        ];
    }

    public function messages()
    {
        return [
            'item_id.required' => '商品IDは必須です。',
            'item_id.exists' => '指定された商品が存在しません。',
            'comment.required' => 'コメントは必須です。',
            'comment.max' => 'コメントは最大255文字までです。',
        ];
    }
}
