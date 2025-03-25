<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\StoreUser;
use Illuminate\Support\Facades\Validator;
use DB;
use Illuminate\Support\Str;
use App\Mail\OtpEmail;
use App\Mail\EmailVerification;
use Mail;
use Carbon\Carbon;
use App\Helpers\Helpers;
use App\Http\Helpers\Helper;

use App\Http\Services\V1\Auth\AuthServices;
use App\Models\Cms;
use App\Models\Token;
use App\Models\User;

class AuthController extends Controller
{
    /**
     * Registers a new user with the provided details.
     *
     * This function validates the user input, checks for the existence of the mobile number,
     * and registers the user if validation passes and the mobile number is not already used.
     *
     * @param Request $request The HTTP request object containing user details.
     * @param AuthServices $authServices The authentication services for user registration.
     *
     * @return \Illuminate\Http\JsonResponse The response indicating success or failure of registration.
     */

    public function register(Request $request, AuthServices $authServices)
    {
        $json = json_decode(trim($request->getContent()), true);
        $schema = [
            'firstName' => 'required',
            'lastName' => 'required',
            'email' => 'required|email|unique:tbl_users,email',
            'mobileNumber' => 'nullable|sometimes',
            'password' => 'required|min:8',
        ];

        $errorMessages = [
            'firstName.required' => 'Firstname is required.',
            'lastName.required' => 'Lastname is required.',
            'email.required' => 'Email is required.',
            'email.unique' => 'The provided email address is already registered. Please use a different email or sign in.',
            'password.required' => 'Password is required.',
            'password.min' => 'Password must be at least 8 characters long.',
        ];

        $validator = Validator::make($request->all(), $schema, $errorMessages);

        if ($validator->fails()) {
            return Helpers::validatorFail($validator->errors()->first());
        }


        //old code with check exist or not
        if ($json['countryCode'] != "" && $json['mobileNumber'] != "") {
            $mobileNumber = $json['countryCode'] . $json['mobileNumber'];
            $query = User::query();
            $query->whereRaw(("CONCAT(`country_code`, `mobile_number`) = " . $mobileNumber));
            $query->where('is_mobile_verify', 1);
            $userexist =  $query->first();
            if ($userexist) {
                return Helpers::error(__('messages.mobile_exists'), 200);
            } else {
                $query = User::query();
                $query->whereRaw(("CONCAT(`country_code`, `mobile_number`) = " . $mobileNumber));
                $query->update(['country_code' => "", "mobile_number" => ""]);
            }
        }

        // if ($json['countryCode'] != "" && $json['mobileNumber'] != "") {
        //     $mobileNumber = $json['countryCode'] . $json['mobileNumber'];
        //     $query = User::query();
        //     $query->whereRaw(("CONCAT(`country_code`, `mobile_number`) = " . $mobileNumber));
        //     $userexist =  $query->first();

        //     if ($userexist) {
        //         $responseData = [];
        //         $authToken = "";
        //         return Helpers::error(__('messages.mobile_exists'),200);
        //     } else {
        //         return $data = $authServices->storeUser($json);
        //     }
        // }

        return $data = $authServices->storeUser($json);
    }

    /**
     * Retrieves the profile of the authenticated user.
     *
     * This function uses the authentication token from the request header to
     * identify the user and fetches their profile data.
     *
     * @param Request $request The HTTP request object containing the auth token in headers.
     * @return \Illuminate\Http\JsonResponse A JSON response containing the user's profile data.
     */

    public function getProfile(Request $request)
    {
        $authToken = $request->header('auth');
        $userData = Helpers::getUserDataFromId($authToken);
        return Helpers::success("", $userData, $authToken);
    }

    /**
     * Authenticates a user.
     *
     * This function accepts a JSON payload in the request body with the following structure:
     * {
     *     "signInType": "email" | "social",
     *     "firstName": string (optional for email signin),
     *     "email": string (optional for social signin),
     *     "password": string (optional for email signin),
     *     "countryCode": string (optional for social signin),
     *     "mobileNumber": string (optional for social signin),
     *     "socialId": string (optional for social signin),
     *     "socialType": string (optional for social signin)
     * }
     *
     * @param Request $request The HTTP request object containing the signin data in JSON format.
     * @param AuthServices $authServices The service class for authentication.
     * @return \Illuminate\Http\JsonResponse A JSON response containing the user's authentication data.
     */
    public function signin(Request $request, AuthServices $authServices)
    {
        $json = json_decode(trim($request->getContent()), true);

        $schema = [
            'signInType' => 'required',
            //'fcmToken' => 'required',
            'firstName' => 'required_if:signInType,social',
            'email' => 'required_if:signInType,social',
            'password' => 'required_if:signInType,email',
            'countryCode' => 'required_without:email',
            'mobileNumber' => 'required_without:email',
            'socialId' => 'required_if:signInType,social',
            'socialType' => 'required_if:signInType,social',
        ];

        $errorMessages = [
            'signInType.required' => 'Signin Type is required.',
            'firstName.required' => 'First Name is required.',
            'email.required_if' => 'Email is required.',
            'fcmToken.required' => 'fcm Token is required.',
        ];

        $validator = Validator::make($request->all(), $schema, $errorMessages);

        if ($validator->fails()) {
            return Helpers::validatorFail($validator->errors()->first());
        }

        return $data = $authServices->signIn($json);
    }

    /**
     * This API is used to get the email address of user who signed in using apple signin.
     *
     * @param Request $request The HTTP request object containing the signin data in JSON format.
     * The request data should contain the following:
     * {
     *     "socialId": string (required),
     *     "socialType": string (required)
     * }
     * @return \Illuminate\Http\JsonResponse A JSON response containing the user's email address.
     */
    public function getEmailForApple(Request $request)
    {
        $json = json_decode(trim($request->getContent()), true);

        $schema = [
            'socialId' => 'required',
            'socialType' => 'required',
        ];

        $errorMessages = [
            'socialId.required' => 'Social id is required.',
            'socialType.required' => 'Social type is required.',
        ];

        $validator = Validator::make($request->all(), $schema, $errorMessages);

        if ($validator->fails()) {
            return Helpers::validatorFail($validator->errors()->first());
        }

        //find user by social id and social type
        $user = User::where('social_type', $json['socialType'])->where('social_id', $json['socialId'])->first();
        if ($user) {
            return Helpers::withoutAuthSuccessResponce('User email get successfully', $user);
        } else {
            return Helpers::error('Social email address not found, please try with different type of sign in', 200);
        }
    }

    /**
     * Verify the OTP send to user during registration process.
     *
     * @param Request $request The HTTP request object containing the OTP in JSON format.
     * The request data should contain the following:
     * {
     *     "otp": string (required)
     * }
     * @param AuthServices $authServices The service class for authentication.
     * @return \Illuminate\Http\JsonResponse A JSON response containing the result of OTP verification.
     */
    public function verifyRegisterOtp(Request $request, AuthServices $authServices)
    {
        $json = json_decode(trim($request->getContent()), true);
        $authToken = $request->header('auth');
        $schema = [
            'otp' => 'required',
        ];

        $errorMessages = [
            'otp.required' => 'OTP is required.',
        ];

        $validator = Validator::make($request->all(), $schema, $errorMessages);

        if ($validator->fails()) {
            return Helpers::validatorFail($validator->errors()->first());
        }

        return $verifyOtp = $authServices->verifyOtp($authToken, $json);
    }


    /**
     * Starts the forgot password process.
     *
     * The request data should contain the following:
     * {
     *     "type": string (required) - The type of forgot password process, either 'email' or 'mobile'.
     *     "email": string (required if type is 'email') - The user's email address.
     *     "mobileNumber": string (required if type is 'mobile') - The user's mobile number.
     * }
     *
     * @param Request $request The HTTP request object containing the forgot password data in JSON format.
     * @param AuthServices $authServices The service class for authentication.
     * @return \Illuminate\Http\JsonResponse A JSON response containing the result of forgot password process.
     */
    public function forgotPassword(Request $request, AuthServices $authServices)
    {
        $json = json_decode(trim($request->getContent()), true);
        $type = $json['type'];
        $email = $json['email'];
        $mobileNumber = $json['mobileNumber'];
        $schema = [
            'type' => 'required|in:email,mobile',
            'email' => 'required_if:type,email',
            'mobileNumber' => 'required_if:type,mobile',
        ];
        $errorMessages = [
            'email.required_if' => 'Email field is required',
            'mobileNumber.required_if' => 'Mobile number field is required',
        ];

        $validator = Validator::make($request->all(), $schema, $errorMessages);

        if ($validator->fails()) {
            return Helpers::validatorFail($validator->errors()->first());
        }
        $query = User::query();
        if ($type == 'email') {
            $query = $query->where('email', $email);
        } else {
            $query = $query->whereRaw(("CONCAT(`country_code`, `mobile_number`) = " . $mobileNumber));
        }
        $userexist =  $query->first();

        if ($userexist) {
            return Helpers::withoutAuthSuccessResponce('User data listed successfully', $userexist);
        } else {
            return Helpers::error('User account does not exist.', 200);
        }




        if ($json['countryCode'] && $json['mobileNumber']) {
            return $sendOtpMobile = $authServices->sendForgotOtpMobile($json);
        } else {
            return $sendOtpEmail = $authServices->sendForgotOtpEmail($json);
        }
    }

    /**
     * Verifies the OTP send to user during forgot password process.
     *
     * This function accepts a JSON payload in the request body with the following structure:
     * {
     *     "token": string (required)
     *     "otp": string (required)
     * }
     *
     * @param Request $request The HTTP request object containing the OTP in JSON format.
     * @param AuthServices $authServices The service class for authentication.
     * @return \Illuminate\Http\JsonResponse A JSON response containing the result of OTP verification.
     */
    public function verifyForgotOtp(Request $request, AuthServices $authServices)
    {
        $json = json_decode(trim($request->getContent()), true);
        $schema = [
            //'email' => 'required',
            'token' => 'required',
            'otp' => 'required',
        ];
        $errorMessages = [
            'countryCode.required' => 'Token code is required',
            'otp.required' => 'Otp number is required',
        ];
        $validator = Validator::make($request->all(), $schema, $errorMessages);

        if ($validator->fails()) {
            return Helpers::validatorFail($validator->errors()->first());
        }

        //return $verifyOtp = $authServices->verifyForgotOtp($json);
    }

    /**
     * Resets the password for a user.
     *
     * This function accepts a JSON payload in the request body with the following structure:
     * {
     *     "userId": string (required)
     *     "password": string (required)
     * }
     *
     * @param Request $request The HTTP request object containing the password in JSON format.
     * @param AuthServices $authServices The service class for authentication.
     * @return \Illuminate\Http\JsonResponse A JSON response containing the result of password reset.
     */
    public function resetPassword(Request $request, AuthServices $authServices)
    {

        $json = json_decode(trim($request->getContent()), true);
        $schema = [
            //'email' => 'required',
            'userId' => 'required',
            'password' => 'required|min:8',
        ];
        $errorMessages = [
            'userId.required' => 'UserId is required',
            'password.required' => 'Password is required',
        ];

        $validator = Validator::make($request->all(), $schema, $errorMessages);

        if ($validator->fails()) {
            return Helpers::validatorFail($validator->errors()->first());
        }

        return $verifyOtp = $authServices->resertForgotPassword($json);
    }

    /**
     * Returns a CMS page by type.
     *
     * This function accepts a JSON payload in the request body with the following structure:
     * {
     *     "type": string (required)
     * }
     *
     * @param Request $request The HTTP request object containing the CMS type in JSON format.
     *
     * @return \Illuminate\Http\JsonResponse A JSON response containing the result of CMS page retrieval.
     */
    public function getCms(Request $request)
    {
        $json = json_decode(trim($request->getContent()), true);
        $schema = [
            'type' => 'required',
        ];
        $errorMessages = [
            'type.required' => 'Cms type is required',
        ];
        $validator = Validator::make($request->all(), $schema, $errorMessages);

        if ($validator->fails()) {
            return Helpers::validatorFail($validator->errors()->first());
        }
        $getCmsData = Cms::where('type', $json['type'])->first();
        if ($getCmsData) {
            $cmsData = array(
                'cmsData' => $getCmsData,
            );
            return Helpers::withoutAuthSuccessResponce(__('messages.cms_success'), $cmsData);
        } else {
            return Helpers::error(__('messages.no_data_found'), 200);
        }
    }

    /**
     * Sends an OTP to a user.
     *
     * This function accepts a JSON payload in the request body with the following structure:
     * {
     *     "reciverType": string (required)
     *     "reciver": string (required)
     *     "otpType": string (required)
     * }
     *
     * @param Request $request The HTTP request object containing the OTP data in JSON format.
     * @param AuthServices $authServices The service class for authentication.
     * @return \Illuminate\Http\JsonResponse A JSON response containing the result of OTP sending.
     */
    public function sendOtp(Request $request, AuthServices $authServices)
    {
        $json = json_decode(trim($request->getContent()), true);
        $schema = [
            'reciverType' => 'required',
            'reciver' => 'required',
            'otpType' => 'required',
        ];
        $errorMessages = [
            'reciverType.required' => 'Reciver type is required',
            'reciver.required' => 'Reciver field is required',
            'otpType.required' => 'Otp type is required',
        ];
        $validator = Validator::make($request->all(), $schema, $errorMessages);

        if ($validator->fails()) {
            return Helpers::validatorFail($validator->errors()->first());
        }
        return $sendOtp = $authServices->sendOtp($json);
    }

    /**
     * Verifies an OTP.
     *
     * This function accepts a JSON payload in the request body with the following structure:
     * {
     *     "reciverType": string (required)
     *     "reciver": string (required)
     *     "otpType": string (required)
     *     "otp": string (required)
     * }
     *
     * @param Request $request The HTTP request object containing the OTP data in JSON format.
     * @param AuthServices $authServices The service class for authentication.
     * @return \Illuminate\Http\JsonResponse A JSON response containing the result of OTP verification.
     */
    public function verifyOtp(Request $request, AuthServices $authServices)
    {
        $json = json_decode(trim($request->getContent()), true);
        $schema = [
            'reciverType' => 'required',
            'reciver' => 'required',
            'otpType' => 'required',
            'otp' => 'required',
        ];
        $errorMessages = [
            'reciverType.required' => 'Reciver type is required',
            'reciver.required' => 'Reciver field is required',
            'otpType.required' => 'Otp type is required',
            'otp.required' => 'Otp is required',
        ];
        $validator = Validator::make($request->all(), $schema, $errorMessages);

        if ($validator->fails()) {
            return Helpers::validatorFail($validator->errors()->first());
        }
        return $sendOtp = $authServices->verifyOtpAll($json);
    }

    /**
     * Changes the password of the user.
     *
     * This function accepts a JSON payload in the request body with the following structure:
     * {
     *     "socialType": string (required)
     *     "oldPassword": string (required)
     *     "password": string (required)
     *     "cPassword": string (required)
     * }
     *
     * @param Request $request The HTTP request object containing the password data in JSON format.
     * @param AuthServices $authServices The service class for authentication.
     * @return \Illuminate\Http\JsonResponse A JSON response containing the result of password change.
     */
    public function changePassword(Request $request, AuthServices $authServices)
    {
        $authToken = $request->header('auth');
        $json = json_decode(trim($request->getContent()), true);
        $schema = [
            'socialType' => 'required',
            'oldPassword' => 'required',
            'password' => 'required|min:8',
            'cPassword' => 'required|same:password',

        ];
        $errorMessages = [
            'socialType.required' => 'Social type is required',
            'oldPassword.required' => 'old password is required',
            'password.required' => 'Password is required',
            'cPassword.required' => 'Confirm password is required',
            'cPassword.same' => 'The confirm password field must match password',

        ];
        $validator = Validator::make($request->all(), $schema, $errorMessages);

        if ($validator->fails()) {
            return Helpers::validatorFail($validator->errors()->first());
        }
        return $changePassword = $authServices->changePassword($authToken, $json);
    }
    /**
     * Uploads an image of a user.
     *
     * This function accepts a JSON payload in the request body with the following structure:
     * {
     *     "type": string (required)
     *     "image": file (required)
     * }
     *
     * @param Request $request The HTTP request object containing the image in JSON format.
     * @param AuthServices $authServices The service class for authentication.
     * @return \Illuminate\Http\JsonResponse A JSON response containing the result of image upload.
     */
    public function uploadImage(Request $request, AuthServices $authServices)
    {
        $authToken = $request->header('auth');
        $json = json_decode(trim($request->getContent()), true);
        $schema = [
            'type' => 'required|in:normal,document,chat',
            'image' => 'required|mimes:pdf,doc,docx,png,jpg,jpeg|max:5000',
        ];
        $errorMessages = [
            'type.required' => 'Type field is required',
            'image.required' => 'Image field is required',
        ];
        $validator = Validator::make($request->all(), $schema, $errorMessages);

        if ($validator->fails()) {
            return Helpers::validatorFail($validator->errors()->first());
        }

        return $uploadImage = $authServices->uploadImage($request, $authToken);
    }
    //End Change Profile Image API

    /**
     * Updates the profile of a user.
     *
     * This function accepts a JSON payload in the request body with the following structure:
     * {
     *     "firstName": string (required)
     *     "lastName": string (required)
     * }
     *
     * @param Request $request The HTTP request object containing the profile data in JSON format.
     * @param AuthServices $authServices The service class for authentication.
     * @return \Illuminate\Http\JsonResponse A JSON response containing the result of profile update.
     */
    public function editProfile(Request $request, AuthServices $authServices)
    {
        $authToken = $request->header('auth');

        $json = json_decode(trim($request->getContent()), true);
        $schema = [
            'firstName' => 'required',
            'lastName' => 'required',


        ];
        $errorMessages = [
            'firstName.required' => 'Firstname is required ',
            'lastName.required' => 'Lastname is required ',


        ];
        $validator = Validator::make($request->all(), $schema, $errorMessages);

        if ($validator->fails()) {
            return Helpers::validatorFail($validator->errors()->first());
        }
        return $editProfile = $authServices->editProfile($authToken, $json);
    }

    /**
     * Updates the mobile number of a user.
     *
     * This function accepts a JSON payload in the request body with the following structure:
     * {
     *     "countryCode": string (required)
     *     "mobileNumber": string (required)
     * }
     *
     * @param Request $request The HTTP request object containing the mobile number data in JSON format.
     * @param AuthServices $authServices The service class for authentication.
     * @return \Illuminate\Http\JsonResponse A JSON response containing the result of mobile number update.
     */
    public function updateMobileNumber(Request $request, AuthServices $authServices)
    {
        $authToken = $request->header('auth');
        $json = json_decode(trim($request->getContent()), true);
        $schema = [
            'countryCode' => 'required',
            'mobileNumber' => 'required',
        ];
        $errorMessages = [
            'countryCode.required' => 'Country code is required.',
            'mobileNumber.required' => 'Mobile number is required.',
            // 'mobileNumber.unique' => 'Mobile number is already taken.',
        ];
        $validator = Validator::make($request->all(), $schema, $errorMessages);

        if ($validator->fails()) {
            return Helpers::validatorFail($validator->errors()->first());
        }
        return $updateMobile = $authServices->updateMobileNumber($authToken, $json);
    }

    /**
     * Updates the PayPal and Venmo usernames of a user.
     *
     * This function retrieves the user's ID from the authentication token
     * and updates the PayPal and Venmo usernames in the database based on
     * the provided JSON payload. If the update is successful, it returns a
     * success response with the updated user data. Otherwise, it returns an
     * error response indicating a failure to update the data.
     *
     * @param Request $request The HTTP request object containing the usernames in JSON format.
     * @return \Illuminate\Http\JsonResponse A JSON response containing the result of the username update.
     */

    public function updateUsernames(Request $request)
    {
        $authToken = $request->header('auth');
        $json = json_decode(trim($request->getContent()), true);
        $userId = Helpers::getUserIdFromToken($authToken);
        $userData = [
            'paypal_username' => $json['paypalUsername'],
            'venmo_username' => $json['venmoUsername'],
        ];

        $updateData = User::where('id', $userId)->update($userData);
        if ($updateData) {
            $userData = Helpers::getUserDataFromId($authToken);
            $responseData = [
                'userData' => $userData,
            ];
            return Helpers::success("Data updated successfully.", $responseData, $authToken);
        } else {
            return Helpers::error("Failed to update data.", 200);
        }
    }

    /**
     * Updates the notification status of a user.
     *
     * This function retrieves the user's ID from the authentication token
     * and updates the notification status in the database based on
     * the provided JSON payload. If the update is successful, it returns a
     * success response. Otherwise, it returns an error response indicating a failure to update the data.
     *
     * @param Request $request The HTTP request object containing the notification status in JSON format.
     * @param AuthServices $authServices The service class for authentication.
     * @return \Illuminate\Http\JsonResponse A JSON response containing the result of the notification status update.
     */
    public function updatenotficationStatus(Request $request, AuthServices $authServices)
    {
        $authToken = $request->header('auth');
        $json = json_decode(trim($request->getContent()), true);
        $schema = [
            'chatNotification' => 'required',
            'pushNotification' => 'required',
        ];
        $errorMessages = [
            'chatNotification.required' => 'Chat notification field is required.',
            'pushNotification.required' => 'Push notification field is required.',
            // 'mobileNumber.unique' => 'Mobile number is already taken.',
        ];
        $validator = Validator::make($request->all(), $schema, $errorMessages);

        if ($validator->fails()) {
            return Helpers::validatorFail($validator->errors()->first());
        }
        return $updateMobile = $authServices->updateNotificationStatus($authToken, $json);
    }

    /**
     * Logs out the user by invalidating their authentication token.
     *
     * This function retrieves the user's ID from the authentication token,
     * clears the auth and fcm tokens associated with the user in the database,
     * and returns a success response if the operation is successful.
     * If the operation fails, it returns an error response.
     *
     * @param Request $request The request object containing the authentication token.
     * @return \Illuminate\Http\JsonResponse A JSON response indicating the result of the logout operation.
     */

    public function logout(Request $request)
    {
        $authToken = $request->header('auth');
        $userId = Helpers::getUserIdFromToken($authToken);
        $deleteAuthTokens = Token::where('user_id', $userId)->update(['auth_token' => '', 'fcm_token' => '']);
        if ($deleteAuthTokens) {
            return Helpers::withoutAuthSuccessResponce("You have been successfully logged out.", "");
        } else {
            return Helpers::error('Logout failed. Please try again.', 200);
        }

        //Test
    }
}
