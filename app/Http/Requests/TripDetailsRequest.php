<?php

namespace App\Http\Requests;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use App\Helpers\Helpers;

class TripDetailsRequest extends FormRequest
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
       
        //return $this->getCustomRules($this->input('class'));
        return [
            'tripName' => 'required',
            'tripDescription' => 'required',
            'itinaryDetails' => 'nullable',
            'responseDeadline' => 'required|date_format:Y/m/d H:i:s',
            'reminderDays' => 'required|integer',
            'tripImgUrl' => 'required',
            'tripDatesList'=> 'required|array',
            'tripCitiesList'=> 'required|array',
            'isTripFinalised' => 'nullable',
            'trip_finalizing_comments' => 'nullable',
            'tripFinalStartDate' => 'nullable|date_format:Y/m/d',
            'tripFinalEndDate' => 'nullable|date_format:Y/m/d',
            'tripFinalCity' => 'nullable',
            'tripFinaledOn' => 'nullable',
        ];
    }

    public function messages()
    {
        return [
            'tripName.required' => 'Trip name is required.',
            'tripDescription.required' => 'Trip description is required.',
            'responseDeadline.required' => 'Response deadline is required',
            'responseDeadline.date' => 'Response deadline must be a date',
            'reminderDays.required' => 'Reminder days is required.',
            'reminderDays.integer' => 'Reminder days must be an integer.',
            'tripImgUrl.required' => 'Trip url has already been taken.',
            'tripDatesList.required' => 'Trip dates list is required',
            'tripDatesList.array' => 'Trip dates list must be in array form',
            'tripCitiesList.required' => 'Trip cities list is required',
            'tripCitiesList.array' => 'Trip cities list must be in array form',
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
