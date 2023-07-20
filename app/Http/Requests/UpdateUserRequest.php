<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
class UpdateUserRequest extends FormRequest
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
            'fullname' => 'nullable|string|min:3|max:255',
            'admin_user_category_id' =>['nullable','integer',
            Rule::exists('admin_user_categories', 'id'),
            ],
            'viloyat' => 'nullable|string',
            'rus_viloyat' => 'nullable|string',
            'avatar' => 'nullable|image|max:2048',
            'tuman'=>'nullable|string',
            'rus_tuman'=>'nullable|string',
        ];
    }
}
