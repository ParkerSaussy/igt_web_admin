<?php

namespace App\Http\Requests;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use App\Helpers\Helpers;

class TripSaveFinalRequest extends FormRequest
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
            'tripName' => 'required',
            'tripDescription' => 'required',
            'itinaryDetails' => 'nullable',
            'responseDeadline' => 'required|date_format:Y/m/d H:i:s',
            'reminderDays' => 'required|integer',
            'tripImgUrl' => 'required',
            'isTripFinalised' => 'required',
            'tripFinalizingComments' => 'nullable',
            'tripFinalStartDate' => 'nullable|date_format:Y/m/d H:i:s',
            'tripFinalEndDate' => 'nullable|date_format:Y/m/d H:i:s',
            'tripFinalCityId' => 'nullable'
        ];
    }

    public function messages()
    {
        return [
            'tripId.required' => 'Trip id is required',
            'tripName.required' => 'Trip name is required.',
            'tripDescription.required' => 'Trip description is required.',
            'responseDeadline.required' => 'Response deadline is required',
            'responseDeadline.date' => 'Response deadline must be a date',
            'reminderDays.required' => 'Reminder days is required.',
            'reminderDays.integer' => 'Reminder days must be an integer.',
            'tripImgUrl.required' => 'Trip url has already been taken.',
            'isTripFinalised.required' => 'Trip finalised details required.'
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
