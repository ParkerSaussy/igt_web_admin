<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CityFormRequest extends FormRequest
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
            'cityName' => 'required|unique:tbl_city,city_name',
            'state' => 'required',
            'abbreviation' => 'required',
            //'stateAbbr' => 'required',
            'countryName' => 'required',
            'timeZone' => 'required',
            'status' => 'required',
           
        ];
    }
    public function messages()
    {
        return [
            'cityName.required' => 'City name field is required',
            'state.required' => 'State name field is required',
            'countryName.required' => 'Country name field is required',
            'timeZone.required' => 'Timezone field is required',
            'status.required' => 'Status field is required',
        ];
    }
}
