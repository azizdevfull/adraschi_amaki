<?php

namespace App\Http\Requests;

use Carbon\Carbon;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
class StoreUserRequest extends FormRequest
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
        User::whereNull('phone_verified_at')
        ->where('created_at', '<=', Carbon::now()->subMinute(5))
        ->delete();

        return [
            'first_name' => 'required|string|min:3|max:255',
            'last_name' => 'required|string|min:3|max:255',
            'username' => 'required|string|min:3|max:255|unique:users',
            'phone' => ['required','string','unique:users'],
            'password' => 'required|string|min:6|confirmed',
        ];
    }
}
