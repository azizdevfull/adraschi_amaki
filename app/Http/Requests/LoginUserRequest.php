<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;

class LoginUserRequest extends FormRequest
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
            'login' => [
                'required',
                'string',
                function ($attribute, $value, $fail) {
                    if (!User::where('phone', $value)->orWhere('username', $value)->exists()) {
                        return $fail(__('auth.invalid_input'));
                    }
                },
            ],
            'password' => 'required|string|min:6',
        ];
    }
}
