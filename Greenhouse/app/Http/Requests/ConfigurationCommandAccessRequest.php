<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ConfigurationCommandAccessRequest extends FormRequest
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
            'min_air_temp' => ['required', 'boolean'],
            'min_wind_speed' => ['required', 'boolean'],
            'max_soil_temp' => ['required', 'boolean'],
            'max_soil_humidity' => ['required', 'boolean'],
        ];
    }
}
