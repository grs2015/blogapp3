<?php

namespace App\Http\Requests;

use App\Models\Postmeta;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class UpdatePostmetaRequest extends FormRequest
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
            // 'key' => ['string', 'unique:postmetas,key'],
            'key' => ['string', Rule::unique('postmetas')->ignore($this->getPostmetaId())],
            'content' => ['string']
        ];
    }

    public function getPostmetaId():int
    {
        return $this->route()->parameter('postmeta')->id;
    }
}
