<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ForgotFormDataRequest extends FormRequest
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
          'newpassword' => 'required|min:8',
          'confirmpassword' => 'required|same:newpassword',
      ];
  }
  public function messages()
  {
   return [
      'newpassword.required' => 'Please enter new password',
      'newpassword.min' => 'New password must be at least 8 characters',
      'confirmpassword.required' => 'Please enter confirm password',
      'confirmpassword.same' => 'The new password and confirm password must be same',
  ];
}
}
