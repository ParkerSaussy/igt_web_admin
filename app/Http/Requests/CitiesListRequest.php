<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use App\Helpers\Helpers;

class CitiesListRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'tripId' => 'required',
            'tripCitiesList'=> 'required|array',
        ];
    }

    public function messages()
    {
        return [
            'tripId.required' => 'Trip id is required.',
            'tripCitiesList.required' => 'Trip cities list is required',
            'tripCitiesList.array' => 'Trip cities List must be an array',
        ];
    }

    public function failedValidation(Validator $validator)
    {

        throw new HttpResponseException(response()->json([
            'data' => json_decode("{}"),
            'meta' => array(
                'authToken' => "",
                'success' => false,
                "message" => $validator->errors()->first(),
            )
        ]));
    }
}
