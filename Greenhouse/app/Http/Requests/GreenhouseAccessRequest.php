<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GreenhouseAccessRequest extends FormRequest
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
            'air_temperature' => ['required','boolean'],
            'relative_air_humidity' => ['required','boolean'],
            'soil_temperature' => ['required','boolean'],
            'relative_humidity_of_the_soil' => ['required','boolean'],
            'lighting_intensity' => ['required','boolean'],
            'outside_air_temperature' => ['required','boolean'],
            'wind_speed' => ['required','boolean'],
            'water_level' => ['required','boolean'],
            'opening' => ['required','boolean'],
            'closing' => ['required','boolean'],
            'opened' => ['required','boolean'],
            'closed' => ['required','boolean'],
            'filling' => ['required','boolean'],
            'emptying' => ['required','boolean'],
            'full' => ['required','boolean'],
            'empty' => ['required','boolean'],
            'remote_mode' => ['required','boolean'],
        ];
    }
}
