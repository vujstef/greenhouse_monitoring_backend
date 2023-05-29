<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules;

class UpdateUserRequest extends FormRequest
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
            'first_name' => ['string', 'max:255'],
            'last_name' => ['string', 'max:255'],
            'email' => [
                'sometimes',
                'nullable',
                'string',
                'email',
                'max:255',
                Rule::unique('users')->ignore($this->id),
            ],
            'password' => ['nullable', 'confirmed', Rules\Password::defaults()],
        ];
    }
}
