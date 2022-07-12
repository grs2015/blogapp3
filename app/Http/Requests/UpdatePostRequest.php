<?php

namespace App\Http\Requests;

use App\Models\Post;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Http\FormRequest;

class UpdatePostRequest extends FormRequest
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
        $commonRules = [
            'title' => ['string', 'required'],
            'meta_title' => ['string'],
            'summary' => ['string'],
            'content' => ['string'],
            'time_to_read' => ['integer', 'between:1, 100'],
            'hero_image' => ['image', 'mimes:jpeg,png,jpg,gif,svg', 'max:2048'],
            'images' => ['array'],
            'tags' => ['array'],
            'categories' => ['array', 'required'],
        ];

        if (Auth::user()->hasRole('author')) {
            return [
                ...$commonRules,
                'published' => [Rule::in([Post::PENDING, Post::DRAFT])]
            ];
        }

        return [
            ...$commonRules,
            'published' => [Rule::in([Post::PUBLISHED, Post::UNPUBLISHED, Post::PENDING, Post::DRAFT])],
            'favorite' => [Rule::in([Post::FAVORITE, Post::NONFAVORITE])]
        ];
    }
}
