<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ConfigurationCommandRequest extends FormRequest
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
            'min_air_temp' => ['numeric', 'nullable'],
            'min_wind_speed' => ['numeric', 'nullable'],
            'max_soil_temp' => ['numeric', 'nullable'],
            'max_soil_humidity' => ['numeric', 'nullable'],
            'opening_command' => ['integer', 'nullable', 'in:1'],
            'closing_command' => ['integer', 'nullable', 'in:2'],
            'filling_command' => ['integer', 'nullable', 'in:3'],
            'emptying_command' => ['integer', 'nullable', 'in:4'],
            'remote_mode' => ['integer', 'nullable', 'in:0,1'],
        ];
    }
}
