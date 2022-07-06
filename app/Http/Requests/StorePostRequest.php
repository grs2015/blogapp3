<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePostRequest extends FormRequest
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
            'meta_title' => ['string'],
            'summary' => ['string'],
            'content' => ['string'],
            'time_to_read' => ['integer', 'between:1, 100'],
            'hero_image' => ['image', 'mimes:jpeg,png,jpg,gif,svg', 'max:2048'],
            'images' => ['array'],
            'tags' => ['array'],
            'categories' => ['array', 'required']
        ];
    }
}
