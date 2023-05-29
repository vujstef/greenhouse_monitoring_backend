<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ThingSpeakRequest extends FormRequest
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
            'channel_id' => ['required', 'string'],
            'read_key' => ['required', 'string'],
            'write_key' => ['required', 'string'],
        ];
    }
}
