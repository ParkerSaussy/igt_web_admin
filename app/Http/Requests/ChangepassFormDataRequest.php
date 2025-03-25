<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ChangepassFormDataRequest extends FormRequest
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
            'opassword' => 'required',
            'npassword' => 'required|min:8',
            'cpassword' => 'required|min:8|same:npassword',
        ];
    }
    public function messages()
    {
        return [
            'opassword.required' => 'Old password is required',
            'npassword.required' => 'New password is required',
            'npassword.min' => 'Password length is minimum 8 characters',
            'cpassword.required' => 'Confirm password is required',
            'cpassword.min' => 'Confirm password length is minimum 8 characters',
            'cpassword.same' => 'The confirm password and new password must be same',
        ];
    }
}