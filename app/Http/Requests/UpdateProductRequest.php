<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProductRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        return [
            'category_id' => 'exists:categories,id',
            'price' => 'string',
            'discount' => 'nullable|string',
            'eni' => 'nullable|string',
            'gramm' => 'string',
            'boyi' => 'nullable|string',
            'color' => 'string|max:255',
            'ishlab_chiqarish_turi' => 'exists:ishlab_chiqarish_turis,id',
            'mahsulot_tola_id' => 'exists:mahsulot_tolas,id',
            'brand' => 'string',
            'photos' => 'array|max:4',
            'photos.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ];
    }
}
