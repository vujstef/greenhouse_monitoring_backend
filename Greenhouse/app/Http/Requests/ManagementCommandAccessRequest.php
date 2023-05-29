<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ManagementCommandAccessRequest extends FormRequest
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
            'opening_command' => ['required', 'boolean'],
            'closing_command' => ['required', 'boolean'],
            'filling_command' => ['required', 'boolean'],
            'emptying_command' => ['required', 'boolean'],
            'remote_mode' => ['required', 'boolean'],
        ];
    }
}
