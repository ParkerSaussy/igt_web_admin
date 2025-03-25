<?php

namespace App\Http\Controllers\API\V1;

use App\Helpers\Helpers;
use App\Http\Controllers\Controller;
use App\Models\Inquiry;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ContactController extends Controller
{
    /**
     * Adds a new inquiry.
     *
     * This function handles the addition of a new inquiry submitted by the user.
     * It validates the request, saves the inquiry data to the database, and sends a confirmation email.
     *
     * @param Request $request The HTTP request object containing the inquiry data.
     * @return \Illuminate\Http\JsonResponse A JSON response indicating success or failure of the inquiry submission.
     */
    public function addInquiry(Request $request)
    {
        $authToken = $request->header('auth');
        $json = json_decode(trim($request->getContent()), true);
        $userId = Helpers::getUserIdFromToken($authToken);

        $schema = [
            'firstName' => 'required',
            'email' => 'required|email',
            'message' => 'required',
        ];
        $errorMessages = [
            'firstName.required' => 'Firstname filed is required',
            'email.required' => 'Email filed is required',
            'message.required' => 'Message filed is required'
        ];
        $validator = Validator::make($request->all(), $schema, $errorMessages);

        if ($validator->fails()) {
            return Helpers::validatorFail($validator->errors()->first());
        }

        $data = array(
            'first_name' => $json['firstName'],
            'email' => $json['email'],
            'message' => $json['message'],
            'is_replied' => 0,
            'user_id' => $userId,
        );

        $insertInquiry = Inquiry::create($data);
        if ($insertInquiry) {
            $template = 'inquirysent';
            $subject = 'Inquiry Submitted Successfully';
            $sendOtp = Helpers::sendEmail($json['email'], $data, $template, $subject);
            $responseData = [];
            return Helpers::success("Inquiry sent successfully!", $responseData, $authToken);
        } else {
            return Helpers::error("Failed to sent inquiry!", 200);
        }
    }
}
