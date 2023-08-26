<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProductRequest extends FormRequest
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
            'category_id' => 'required|exists:categories,id',
            'price' => 'required|string',
            'discount' => 'nullable|string',
            'eni' => 'nullable|string',
            'gramm' => 'required|string',
            'boyi' => 'nullable|string',
            'color' => 'required|string|max:255',
            'ishlab_chiqarish_turi' => 'required|exists:ishlab_chiqarish_turis,id',
            'mahsulot_tola_id' => 'required|exists:mahsulot_tolas,id',
            'brand' => 'required',
            'photos' => 'array|max:4',
            'photos.*' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048'
        ];
    }
}
