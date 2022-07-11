<?php

namespace App\Http\Requests;

use App\Models\Comment;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateCommentRequest extends FormRequest
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
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'title' => ['required', 'string'],
            'content' => ['string'],
            'published_at' => ['date'],
            'published' => [Rule::in([Comment::PUBLISHED, Comment::UNPUBLISHED, Comment::PENDING])]
        ];
    }
}
