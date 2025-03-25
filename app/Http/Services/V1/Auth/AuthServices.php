<?php

namespace App\Http\Services\V1\Auth;

use App\Helpers\Helpers;
use App\Http\Helpers\Helper;
//use App\Http\Resources\MedlistResource;
use App\Models\User;
use App\Models\Token;
use App\Models\ForgotPassword;
use App\Models\GuestListModel;
use App\Models\Otp;
use Illuminate\Http\Request;
use App\Http\Requests\StoreUser;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use App\Mail\OtpEmail;
use Mail;
use Carbon\Carbon;

class AuthServices
{
    public function storeUser($json)
    {
        $email = $json['email'];
        $authToken = Str::random(48);
        $fcmToken = $json['fcmToken'];
        $deviceId = $json['deviceId'];
        $platform = $json['platform'];
        $otp = rand(1000, 9999);
        $userData = [
            'first_name' => $json['firstName'],
            'last_name' => $json['lastName'],
            'email' => $json['email'],
            'password' => Hash::make($json['password']),
            'country_code' => $json['countryCode'],
            'mobile_number' => $json['mobileNumber'],
            'social_type' => 'email',
            'signin_type' => 'email',
        ];

        DB::beginTransaction();
        $user = User::create($userData);
        $this->updateGuestDetails($user);
        $userId = $user->id;

        $genrateToken = $this->genrateUserToken($userId, $authToken, $fcmToken, $deviceId, $platform);
        //$genrateOtp = $this->genrateUserRegisterOtp($userId, $otp, $authToken, $email);
        //$sendOtp = $this->send_verification_otp($email, $otp);
        $userData = Helpers::getUserDataFromId($authToken);
        if ($userData) {
            // $template = 'registersuccess';
            // $subject = 'Successful Signup Confirmation';
            // $sendOtp = Helpers::sendEmail($json['email'], $userData, $template,$subject);
            DB::commit();
            $responseData = [
                'userData' => $userData,
            ];
            return Helpers::success(__('messages.signup_success'), $responseData, $authToken);
        } else {
            return Helpers::error(__('messages.signup_fail'), 200);
        }
    }
    public function sendOtp($json)
    {
        $reciverType = $json['reciverType'];
        $reciver = $json['reciver'];
        $otpType = $json['otpType'];
        $otp = rand(1000, 9999);
        $today = date('Y-m-d');

        if ($otpType == 'updatemobile') {
            $getCount = Otp::where('reciver', $reciver)
                ->where('otp_type', $otpType)
                ->where(DB::raw('CAST(created_at as date)'), '=', $today)
                ->count();
            if ($getCount < 5) {
                $insertOtp = [
                    'otp' => $otp,
                    'reciver_type' => $reciverType,
                    'reciver' => $reciver,
                    'otp_type' => $otpType,
                ];
                $insertForgotData = Otp::create($insertOtp);
                if ($insertOtp) {
                    $responseData = [
                        'otp' => $otp,
                    ];

                    $accountSid = 'AC4b2e47e1c16cf5aa8c4d0da3d6f64366';
                    $authToken = '4df6c929cbc38887e91bda559cfd2523';
                    $twilioPhoneNumber = '+17068073850';
                    $recipientPhoneNumber = $reciver;
                    $messageBody = ' ItsGoTime - Your verification code is ' . $otp;
                    $ch = curl_init();
                    curl_setopt($ch, CURLOPT_URL, "https://api.twilio.com/2010-04-01/Accounts/$accountSid/Messages.json");
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                    curl_setopt($ch, CURLOPT_POST, 1);
                    curl_setopt($ch, CURLOPT_POSTFIELDS, "To=$recipientPhoneNumber&From=$twilioPhoneNumber&Body=$messageBody");
                    curl_setopt($ch, CURLOPT_USERPWD, "$accountSid:$authToken");
                    $response = curl_exec($ch);
                    if (curl_errno($ch)) {
                        curl_close($ch);
                        return Helpers::error('Somthing went wrong ', 200);
                    } else {
                        curl_close($ch);
                        return Helpers::withoutAuthSuccessResponce(__('messages.sent_success_on_mobile'), $responseData);
                    }
                } else {
                    return Helpers::error('Somthing went wrong ', 200);
                }
            } else {
                return Helpers::error(__('messages.otp_limit_over'), 200);
            }
        } else {
            $query = User::query();
            if ($reciverType == 'email') {
                $query = $query->where('email', $reciver);
            } else {
                $query = $query->whereRaw('CONCAT(`country_code`, `mobile_number`) = ' . $reciver);
            }
            $userexist = $query->first();

            if ($userexist) {
                $getCount = Otp::where('reciver', $reciver)
                    ->where(DB::raw('CAST(created_at as date)'), '=', $today)
                    ->count();

                // $getCount = Otp::where('reciver', $reciver)->where('created_at', $today)->count();
                if ($getCount < 5) {
                    $insertOtp = [
                        'otp' => $otp,
                        'reciver_type' => $reciverType,
                        'reciver' => $reciver,
                        'otp_type' => $otpType,
                    ];
                    $insertForgotData = Otp::create($insertOtp);
                    if ($reciverType == 'email') {
                        if ($insertForgotData) {
                            $responseData = [
                                'otp' => $otp,
                            ];
                            if ($reciverType == 'email' && $otpType == 'verify') {
                                $template = 'sendotp';
                                $subject = 'Welcome to ItsGoTime! Confirm Your Email Address';
                                $sendOtp = Helpers::sendEmail($reciver, $otp, $template, $subject);
                            }
                            if ($reciverType == 'email' && $otpType == 'forgot') {
                                $template = 'forgototp';
                                $subject = 'Reset Password Request For Verify Your Identity with OTP';
                                $sendOtp = Helpers::sendEmail($reciver, $otp, $template, $subject);
                            }
                            return Helpers::withoutAuthSuccessResponce(__('messages.sent_success_on_email'), $responseData);
                        } else {
                            return Helpers::error('Somthing went wrong ', 200);
                        }
                    } else {
                        if ($otp) {
                            $responseData = [
                                'otp' => $otp,
                            ];
                            $accountSid = 'AC4b2e47e1c16cf5aa8c4d0da3d6f64366';
                            $authToken = '4df6c929cbc38887e91bda559cfd2523';
                            $twilioPhoneNumber = '+17068073850';
                            $recipientPhoneNumber = $reciver;
                            $messageBody = 'ItsGoTime - Your verification code is ' . $otp;
                            $ch = curl_init();
                            curl_setopt($ch, CURLOPT_URL, "https://api.twilio.com/2010-04-01/Accounts/$accountSid/Messages.json");
                            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                            curl_setopt($ch, CURLOPT_POST, 1);
                            curl_setopt($ch, CURLOPT_POSTFIELDS, "To=$recipientPhoneNumber&From=$twilioPhoneNumber&Body=$messageBody");
                            curl_setopt($ch, CURLOPT_USERPWD, "$accountSid:$authToken");
                            $response = curl_exec($ch);
                            if (curl_errno($ch)) {
                                return Helpers::error('Somthing went wrong ', 200);
                            } else {
                                // if($reciverType == 'mobile' && $json['otpType'] == 'verify'){
                                //     $template = 'mobileverifysend';
                                //     $subject = 'Mobile Number Verification OTP';
                                //     $sendOtp = Helpers::sendEmail($userexist['email'], $otp, $template,$subject);
                                // }
                                // if($reciverType == "mobile" && $otpType =="forgot"){
                                //     $template = 'forgototp';
                                //     $subject = 'Reset Password Request For ItsGoTime: Verify Your Identity with OTP';
                                //     $sendOtp = Helpers::sendEmail($reciver, $otp, $template,$subject);
                                //    }
                                return Helpers::withoutAuthSuccessResponce(__('messages.sent_success_on_mobile'), $responseData);
                            }
                            curl_close($ch);

                            //return Helpers::withoutAuthSuccessResponce(__('messages.sent_success_on_mobile'), $responseData);
                        } else {
                            return Helpers::error('Somthing went wrong ', 200);
                        }
                    }
                } else {
                    return Helpers::error(__('messages.otp_limit_over'), 200);
                }
            } else {
                return Helpers::error(__('messages.email/mobile_not_registerd'), 200);
            }
        }
    }

    public function updateGuestDetails($userData)
    {
        $guest = GuestListModel::where('email_id', $userData->email)->update([
            'first_name' => $userData->first_name,
            'last_name' => $userData->last_name,
            'u_id' => $userData->id,
            'phone_number' => $userData->country_code . $userData->mobile_number,
        ]);
    }

    public function signIn($json)
    {
        $firstName = $json['firstName'];
        $lastName = $json['lastName'];
        $email = $json['email'];
        $password = $json['password'];
        $signInType = $json['signInType'];
        $deviceId = $json['deviceId'];
        $platform = $json['platform'];
        $authToken = Str::random(48);
        $fcmToken = $json['fcmToken'];
        $otp = rand(1000, 9999);
        if ($signInType == 'social') {
            $socialId = $json['socialId'];
            $socialType = $json['socialType'];
            $emailexist = User::where('email', $email)->count();

            if ($emailexist == 0) {
                //$checkType = User::where('email', $email)->where('social_id', $socialId)->where('social_type', $socialType)->count();
                //if ($checkType == 0) {

                $socialData = [
                    'first_name' => $json['firstName'],
                    'last_name' => $json['lastName'],
                    'email' => $json['email'],
                    'social_id' => $json['socialId'],
                    'social_type' => $socialType,
                    'is_email_verify' => 1,
                    'is_active' => 1,
                    'signin_type' => 'social',
                ];
                DB::beginTransaction();
                $saveSocialInfo = User::create($socialData);
                $this->updateGuestDetails($saveSocialInfo);
                $userId = $saveSocialInfo->id;
                //insert Token
                $genrateToken = $this->genrateUserToken($userId, $authToken, $fcmToken, $deviceId, $platform);
                //genrate otp
                //$genrateOtp = $this->genrateUserRegisterOtp($userId, $otp, $authToken, $email);
                $updateSignInType = User::where('id', $userId)->update(['signin_type' => 'social']);
                $userData = Helpers::getUserDataFromId($authToken);
                if ($userData) {
                    DB::commit();
                    $responseData = [
                        'userData' => $userData,
                    ];
                    return Helpers::success(__('messages.signin_success'), $responseData, $authToken);
                } else {
                    return Helpers::error(__('messages.signin_fail'), 200);
                }
                // } else {

                // }
            } else {
                //check already exist account is social type or not
                $user = User::where('email', $email)->first();
                if ($user->social_type != $socialType) {
                    return Helpers::error(__('messages.email_taken'), 200);
                } else {
                    $userId = $user->id;
                    $activeStatus = $user->is_active;
                    if ($activeStatus == 1) {
                        $genrateToken = $this->genrateUserToken($userId, $authToken, $fcmToken, $deviceId, $platform);
                        $updateSignInType = User::where('id', $userId)->update(['signin_type' => 'social']);
                        $userData = Helpers::getUserDataFromId($authToken);
                        $responseData = [
                            'userData' => $userData,
                        ];
                        return Helpers::success(__('messages.signin_success'), $responseData, $authToken);
                    } else {
                        return Helpers::error(__('messages.deactivated_account'), 200);
                    }
                }
            }
        } else {
            if ($json['mobileNumber'] || $json['countryCode']) {
                $checkEmail = User::where('country_code', $json['countryCode'])
                    ->where('mobile_number', $json['mobileNumber'])
                    ->first();
            } else {
                $checkEmail = User::where('email', $email)->first();
            }

            if ($checkEmail) {
                if($checkEmail->social_type != 'email'){
                    return Helpers::error('Email is associated with '.$checkEmail->social_type.' login.', 200);
                }
                if ($checkEmail->is_active == 1) {
                    if (Hash::check($password, $checkEmail->password)) {
                        $userId = $checkEmail->id;
                        $genrateToken = $this->genrateUserToken($userId, $authToken, $fcmToken, $deviceId, $platform);
                        $userData = Helpers::getUserDataFromId($authToken);
                        $responseData = [
                            'userData' => $userData,
                        ];
                        return Helpers::success(__('messages.signin_success'), $responseData, $authToken);
                    } else {
                        return Helpers::error(__('messages.invalid_email_password'), 200);
                    }
                } else {
                    return Helpers::error(__('messages.deactivated_account'), 200);
                }
            } else {
                return Helpers::error(__('messages.email/mobile_not_registerd'), 200);
            }
        }
    }

    public function genrateUserToken($userId, $authToken, $fcmToken, $deviceId, $platform)
    {
        $tokenData = [
            'user_id' => $userId,
            'auth_token' => $authToken,
            'fcm_token' => $fcmToken,
            'device_id' => $deviceId,
            'platform' => $platform,
        ];
        $exists = Token::where('user_id', $userId)->exists();
        if ($exists) {
            $insertToken = Token::where('user_id', $userId)->update($tokenData);
        } else {
            $insertToken = Token::create($tokenData);
        }

        if ($insertToken) {
            return true;
        } else {
            return false;
        }
    }

    public function genrateUserRegisterOtp($userId, $otp, $authToken, $email)
    {
        $otpdata = [
            'user_id' => $userId,
            'otp' => $otp,
            'is_expired' => 0,
            'auth_token' => $authToken,
            'type' => 'email',
            'reciver' => $email,
        ];
        $insertOtp = Otp::create($otpdata);
        if ($insertOtp) {
            return true;
        } else {
            return false;
        }
    }

    public function verifyOtp($authToken, $json)
    {
        //echo $authToken;
        //exit;
        $otp = $json['otp'];
        $type = $json['type'];
        $data = Otp::where('otp', $otp)->first();

        if ($data) {
            $createdAt = $data['created_at'];
            $isExpired = $createdAt->addMinutes(5);
            $currenTimestamp = Carbon::now();

            if ($currenTimestamp <= $isExpired) {
                $userId = $data['user_id'];
                $updateExpired = Otp::where('otp', $otp)
                    ->where('auth_token', $authToken)
                    ->update(['is_expired' => '1']);
                $updateIsActive = User::where('id', $userId)->update(['is_email_verify' => 1]);

                $userData = Helpers::getUserDataFromUserId($userId);

                $responseData = [
                    'userData' => $userData,
                    'meta' => [
                        'authToken' => $data['auth_token'],
                        'success' => true,
                    ],
                ];
                return Helpers::success('Otp Verified Successfully.', $responseData, $authToken);
            } else {
                $updateExpired = Otp::where('otp', $otp)
                    ->where('auth_token', $authToken)
                    ->update(['is_expired' => '1']);
                return Helpers::error('Otp is expired', 200);
            }
        } else {
            return Helpers::error('Otp is not matched ', 200);
        }
    }

    public function verifyOtpAll($json)
    {
        $reciverType = $json['reciverType'];
        $reciver = $json['reciver'];
        $otp = $json['otp'];
        $checkData = Otp::where('reciver_type', $reciverType)
            ->where('reciver', $reciver)
            ->latest()
            ->first();

        if ($checkData) {
            if ($checkData['otp'] == $otp) {
                $createdAt = $checkData['created_at'];
                //$otp = $checkData['otp'];

                $isExpired = $createdAt->addMinutes(5);
                $currenTimestamp = Carbon::now();
                if ($currenTimestamp <= $isExpired) {
                    if ($reciverType == 'mobile' && $json['otpType'] == 'updatemobile') {
                        $userData = [];
                        return Helpers::withoutAuthSuccessResponce(__('messages.otp_verified'), $userData);
                    }
                    $query = User::query();
                    if ($reciverType == 'email') {
                        $query = $query->where('email', $reciver);
                    } else {
                        $query = $query->whereRaw('CONCAT(`country_code`, `mobile_number`) = ' . $reciver);
                    }
                    $getUserData = $query->first();
                    $userId = $getUserData['id'];

                    if ($reciverType == 'email') {
                        $updateExpired = User::where('id', $userId)->update(['is_email_verify' => 1]);
                    } else {
                        $updateExpired = User::where('id', $userId)->update(['is_mobile_verify' => 1]);
                    }
                    // if($reciverType == 'mobile' && $json['otpType'] == 'updatemobile'){
                    //     $template = 'mobileverifysuccess';
                    //     $subject = 'Mobile Number Verification Successfully';
                    //     $sendOtp = Helpers::sendEmail($getUserData['email'], $getUserData, $template,$subject);
                    // }

                    if ($reciverType == 'email' && $json['otpType'] == 'verify') {
                        $template = 'registersuccess';
                        $subject = 'Successful Signup Confirmation';
                        $sendOtp = Helpers::sendEmail($getUserData['email'], $getUserData, $template, $subject);
                    }

                    $userData = Helpers::getUserDataFromUserId($userId);
                    ///$updateExpired =  ForgotPassword::where('otp', $otp)->where('auth_token', $token)->update(['is_expired' => "1"]);
                    $responseData = [
                        //'otp' => $otp,
                    ];
                    return Helpers::withoutAuthSuccessResponce(__('messages.otp_verified'), $userData);
                } else {
                    return Helpers::error(__('messages.otp_expired'), 200);
                }
            } else {
                return Helpers::error(__('messages.otp_not_match'), 200);
            }
        } else {
            return Helpers::error(__('messages.otp_not_match'), 200);
        }
    }

    public function resertForgotPassword($json)
    {
        $userId = $json['userId'];
        $userData = Helpers::getUserDataFromUserId($userId);
        $oldPassword = $userData['password'];
        if (Hash::check($json['password'], $oldPassword)) {
            return Helpers::error('The new password cannot be the same as the current password.', 200);
        } else {
            $userUpdateData = [
                'password' => Hash::make($json['password']),
            ];
            $updatPassword = User::where('id', $userId)->update($userUpdateData);
        }

        if ($updatPassword) {
            $template = 'resetsuccess';
            $subject = 'Password Reset Successfully';
            $sendOtp = Helpers::sendEmail($userData['email'], $userData, $template, $subject);
            $responseData = [];
            return Helpers::withoutAuthSuccessResponce(__('messages.password_change_success'));
        } else {
            return Helpers::error(__('messages.password_change_fail'), 200);
        }
    }

    public function changePassword($authToken, $json)
    {
        if ($json['socialType'] == 'email') {
            $userData = Helpers::getUserDataFromId($authToken);
            $oldPassword = $userData['password'];

            if (Hash::check($json['oldPassword'], $oldPassword)) {
                if (Hash::check($json['password'], $oldPassword)) {
                    return Helpers::error('The new password cannot be the same as the current password.', 200);
                } else {
                    $password = [
                        'password' => Hash::make($json['password']),
                    ];
                    $responseData = [];
                    $updatePassword = User::where('id', $userData['id'])->update($password);
                    $template = 'changesuccess';
                    $subject = 'Password Change Successfully';
                    $sendOtp = Helpers::sendEmail($userData['email'], $userData, $template, $subject);

                    return Helpers::success(__('messages.change_password_success'), $responseData, $authToken);
                }
            } else {
                return Helpers::error(__('messages.invalid_old_password'), 200);
            }
        } else {
            return Helpers::withoutAuthSuccessResponce('Your change password request is not fulfilled as you are logged in with social media (Google/Apple)');
        }
    }

    public function uploadImage($request, $authToken)
    {
        $type = $request->type;
        $image = $request->image;

        if ($request->hasFile('image')) {
            $getImage = $request->file('image');
            $imageName = time() . '.' . $getImage->extension();
            if ($type == 'document') {
                $imageUrl = config('global.trip_document_images');
                $image->move(public_path('uploads/tripdocuments'), $imageName);
            } elseif($type == 'chat'){
                $imageUrl = config('global.chat_images');
                $image->move(public_path('uploads/chat'), $imageName);
            }else{
                $imageUrl = config('global.local_image_url');
                $image->move(public_path('uploads/images'), $imageName);
            }

            // Save image information to the database
            $responseData = [
                'image' => $imageUrl . $imageName,
            ];

            return Helpers::success('Image upload successfully', $responseData, $authToken);
        }

        return response()->json(['message' => 'Image upload failed'], 400);
    }

    public function editProfile($authToken, $json)
    {
        $userId = Helpers::getUserIdFromToken($authToken);

        $userData = [
            'profile_image' => $json['profileImage'],
            'first_name' => $json['firstName'],
            'last_name' => $json['lastName'],
            //'paypal_username' => $json['paypalUsername'],
            //'venmo_username' => $json['venmoUsername'],
        ];
        if(isset($json['paypalUsername'])){
            $userData = array_merge($userData,['paypal_username' => $json['paypalUsername']]);
        }
        if(isset($json['venmoUsername'])){
            $userData = array_merge($userData,['venmo_username' => $json['venmoUsername']]);
        }
        $updateUserData = User::where('id', $userId)->update($userData);
        $userData = Helpers::getUserDataFromId($authToken);
        $this->updateGuestDetails($userData);
        $responseData = [
            'userData' => $userData,
        ];
        if ($updateUserData) {
            return Helpers::success(__('messages.profile_success'), $responseData, $authToken);
        } else {
            return Helpers::success(__('messages.profile_fail'), $responseData, $authToken);
        }
    }

    public function updateNotificationStatus($authToken, $json)
    {
        $userId = Helpers::getUserIdFromToken($authToken);

        $userData = [
            'get_chat_notfication' => $json['chatNotification'],
            'get_push_notfication' => $json['pushNotification'],
          
        ];
      
        $updateUserData = User::where('id', $userId)->update($userData);
        $userData = Helpers::getUserDataFromId($authToken);
       
        $responseData = [
            'userData' => $userData,
        ];
        if ($updateUserData) {
            return Helpers::success(__('messages.profile_success'), $responseData, $authToken);
        } else {
            return Helpers::error("Failed to update user status", 200);
        }
    }

    public function updateMobileNumber($authToken, $json)
    {
        $userId = Helpers::getUserIdFromToken($authToken);
        $userData = [
            'country_code' => $json['countryCode'],
            'mobile_number' => $json['mobileNumber'],
            'is_mobile_verify' => 1,
        ];

        $blankOther = [
            'country_code' => '',
            'mobile_number' => '',
            'is_mobile_verify' => 0,
        ];
        //update othe user

        $getotherUserData = User::where('id', '!=', $userId)
            ->where('country_code', $json['countryCode'])
            ->where('mobile_number', $json['mobileNumber'])
            ->get();

        if ($getotherUserData) {
            foreach ($getotherUserData as $ids) {
                $userIds = $ids['id'];
                $updateotherUsers = User::where('id', $userIds)->update($blankOther);
                $deleteAuthTokens = Token::where('user_id', $userIds)->update(['auth_token' => '']);
            }
        }

        $updateUserData = User::where('id', $userId)->update($userData);
        // exit;()
        $userData = Helpers::getUserDataFromId($authToken);
        $this->updateGuestDetails($userData);

        $responseData = [
            'userData' => $userData,
        ];
        if ($updateUserData) {
            $template = 'mobileverifysuccess';
            $subject = 'Mobile Number Verification Successfully';
            $sendOtp = Helpers::sendEmail($userData['email'], $userData, $template, $subject);
            return Helpers::success(__('messages.mobile_success'), $responseData, $authToken);
        } else {
            return Helpers::success(__('messages.fail_to_update_mobile'), $responseData, $authToken);
        }
    }
}
