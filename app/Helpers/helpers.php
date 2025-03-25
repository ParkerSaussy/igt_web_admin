<?php

namespace App\Helpers;

use App\Mail\OtpEmail;
use App\Models\GuestListModel;
use App\Models\Notification;
use App\Models\Token;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Log;

class Helpers
{
    /**
     * Send a successful response with a message and optional data
     *
     * @param string $message
     * @param array $data
     * @param string $authToken
     * @return \Illuminate\Http\JsonResponse
     */
    public static function success($message, $data = [], $authToken)
    {
        $result = [
            'meta' => [
                'authToken' => $authToken,
                'success' => true,
                'message' => $message,
            ],
        ];
        $result += ['data' => $data];

        // if (!empty($data)) {
        //     $result += ['data' => $data];
        // }
        // if (empty($data)) {
        //     $result += ['data' => json_decode("{}")];
        // }

        return response()->json($result);
    }

    /**
     * Send a successful JSON response without an authentication token.
     *
     * This function constructs a JSON response with a success status and a message,
     * optionally including additional data, and returns it as a response.
     *
     * @param string $message The message to include in the response.
     * @param array $data Optional data to include in the response.
     * @return \Illuminate\Http\JsonResponse A JSON response indicating success and containing the message and optional data.
     */

    public static function withoutAuthSuccessResponce($message, $data = [])
    {
        $result = [
            'meta' => [
                'authToken' => '',
                'success' => true,
                'message' => $message,
            ],
        ];
        $result += ['data' => $data];
        // if (!empty($data)) {
        //     $result += ['data' => $data];
        // }

        // if (empty($data)) {
        //     $result += ['data' => json_decode("{}")];
        // }

        return response()->json($result);
    }

    /**
     * Construct a JSON response for validation failure.
     *
     * This function constructs a JSON response for when a request fails validation,
     * containing the error message and no data or authentication token.
     *
     * @param string $error The error message to include in the response.
     * @return array A JSON response indicating failure and containing the error message.
     */
    public static function validatorFail($error)
    {
        return [
            'data' => json_decode('{}'),
            'meta' => [
                'authToken' => '',
                'success' => false,
                'message' => $error,
            ],
        ];
    }

    /**
     * Retrieve user data from a given authentication token.
     *
     * This function retrieves the user data associated with the given authentication token,
     * including the user's profile image, plan data (if subscribed), and unread notification count.
     *
     * @param string $authToken The authentication token to retrieve user data for.
     * @return \App\Models\User The user data associated with the given authentication token, or an error response if the user does not exist.
     */
    public static function getUserDataFromId($authToken)
    {
        $imageUrl = config('global.local_image_url');
        $userData = User::join('tbl_auth_token', 'tbl_auth_token.user_id', '=', 'tbl_users.id')
            ->leftJoin('tbl_push_notification', function ($join) {
                $join->on('tbl_push_notification.reciver_id', '=', 'tbl_users.id')->where('tbl_push_notification.is_read', 0); // Add the condition for is_read
            })
            ->leftJoin('tbl_plan', 'tbl_plan.id', 'tbl_users.plan_id')
            ->where('tbl_auth_token.auth_token', $authToken)
            ->select(['tbl_users.*', 'tbl_auth_token.fcm_token', 'tbl_auth_token.auth_token', DB::raw('COUNT(tbl_push_notification.reciver_id) AS notification_count'), 'tbl_plan.duration', 'tbl_plan.price', 'tbl_plan.discounted_price', 'tbl_plan.image'])
            ->first();
        if ($userData->plan_end_date < now()) {
            $userData->plan_id = null;
            $userData->plan_start_date = null;
            $userData->plan_end_date = null;
            $userData->duration = null;
            $userData->price = null;
            $userData->discounted_price = null;
            $userData->image = null;
        } else {
            $userData->image = $imageUrl . $userData->image;
        }

        $profileImage = $userData['profile_image'];
        if ($profileImage != null) {
            $userData['profile_image'] = $imageUrl . $userData['profile_image'];
        }

        if ($userData) {
            return $userData;
        } else {
            return self::error('No data found');
        }
    }

    /**
     * Construct a JSON response for a generic error.
     *
     * This function constructs a JSON response for when an error occurs,
     * containing the error message and no data or authentication token.
     *
     * @param string $message The error message to include in the response.
     * @param int $status_code The HTTP status code to use for the response (default: 200).
     * @return \Illuminate\Http\JsonResponse A JSON response indicating failure and containing the error message.
     */
    public static function error($message, $status_code = 200)
    {
        //Log::info($message . "***********" . $status_code);
        return response()->json(
            [
                'data' => json_decode('{}'),
                'meta' => [
                    'authToken' => '',
                    'message' => $message,
                    'success' => false,
                ],
            ],
            $status_code,
        );
    }

    /**
     * Get the user ID associated with the given authentication token.
     *
     * This function takes an authentication token and returns the associated user ID.
     * If the authentication token is invalid or not found, this function returns 0.
     *
     * @param string $authToken The authentication token to look up.
     * @return int The user ID associated with the given authentication token, or 0 if the token is invalid or not found.
     */
    public static function getUserIdFromToken($authToken)
    {
        $getUserId = User::join('tbl_auth_token', 'tbl_auth_token.user_id', '=', 'tbl_users.id')
            ->where('tbl_auth_token.auth_token', $authToken)
            ->select('tbl_users.id')
            ->first();

        //print_r($getUserId); exit;
        if ($getUserId != null) {
            $userId = $getUserId->id;
            return $userId;
        } else {
            return 0;
        }
    }

    /**
     * Retrieve user data from a given user ID.
     *
     * This function retrieves the user data associated with the given user ID,
     * including the user's profile image, plan data (if subscribed), and unread notification count.
     *
     * @param int $userId The user ID to retrieve user data for.
     * @return \App\Models\User The user data associated with the given user ID, or an error response if the user does not exist.
     */
    public static function getUserDataFromUserId($userId)
    {
        //print_r("getUserDataFromUserId USER ID: ".$userId); exit;
        $imageUrl = config('global.local_image_url');
        $userData = User::join('tbl_auth_token', 'tbl_auth_token.user_id', '=', 'tbl_users.id')
            ->leftJoin('tbl_push_notification', function ($join) {
                $join->on('tbl_push_notification.reciver_id', '=', 'tbl_users.id')->where('tbl_push_notification.is_read', 1); // Add the condition for is_read
            })
            ->where('tbl_users.id', $userId)
            ->select(['tbl_users.*', 'tbl_auth_token.fcm_token', 'tbl_auth_token.auth_token', DB::raw('COUNT(tbl_push_notification.reciver_id) AS notification_count')])
            ->first();

        $profileImage = $userData['profile_image'];
        if ($profileImage != null) {
            $userData['profile_image'] = $imageUrl . $userData['profile_image'];
        }

        if ($userData) {
            return $userData;
        } else {
            return self::error('No data found');
        }
    }

    /**
     * Retrieves the user ID from the given email ID or mobile number.
     *
     * This function takes an email ID and a mobile number as input and returns the associated user ID.
     * The function first checks for the user ID associated with the given email ID.
     * If no user ID is found, it then checks for the user ID associated with the given mobile number
     * by concatenating the country code and mobile number.
     * If a user ID is found, it is returned; otherwise, 0 is returned.
     *
     * @param string $emailId The email ID to retrieve the user ID for.
     * @param string $mobile The mobile number to retrieve the user ID for.
     * @return int The user ID associated with the given email ID or mobile number, or 0 if no user ID is found.
     */
    public static function getUserIdFromUserEmailOrMobile($emailId, $mobile)
    {
        $userData = User::select('id')
            ->where('tbl_users.email', $emailId)
            ->first();

        if ($userData == null && $mobile != "") {
            $userData = User::select('id')
                ->whereRaw('Concat(tbl_users.country_code,tbl_users.mobile_number) = ' . $mobile)
                ->orWhere('tbl_users.mobile_number', $mobile)
                ->first();
        }

        if ($userData) {
            return $userData->id;
        } else {
            return 0;
        }
    }


    /**
     * Check if the user has access to the given trip.
     *
     * This function takes a user ID, a trip ID, and a type as input and returns a boolean indicating
     * whether the user has access to the trip or not.
     * Access is granted if the user is the host of the trip or a co-host of the trip.
     *
     * @param int $userId The user ID to check for access.
     * @param int $tripId The trip ID to check for access.
     * @param string $type The type of access to check for (currently unused).
     * @return bool True if the user has access to the trip, false otherwise.
     */
    public static function isAccessible($userId, $tripId, $type)
    {
        $data = GuestListModel::where('trip_id', $tripId)
            ->where('is_deleted', 0)
            ->where('u_id', $userId)
            ->get(['role', 'is_co_host']);

        if ($data->isEmpty()) {
            return false;
        } else {
            if ($data[0]['role'] == 'Host' || $data[0]['is_co_host'] == 1) {
                return true;
            } else {
                return false;
            }
        }
    }

    /* getOnlyDigits - This function will remove all the spaces, letters and special char.
     *   It will covert string to only numbers and return it.
     * @param $string
     * return String
     */
    public static function getOnlyDigits($string)
    {
        $string = str_replace(' ', '', $string); // Remove all spaces.
        return preg_replace('/[^0-9+\-]/', '', $string); // Removes special chars.
    }

    /**
     * Summary of sendEmail
     * @param mixed $to
     * @param mixed $dynamicData
     * @param mixed $template
     * @param mixed $subject
     * @return bool
     */
    public static function sendEmail($to, $dynamicData, $template, $subject)
    {
        try {
            // print_r($to);
            // exit;
            Mail::to($to)->send(new OtpEmail($dynamicData, $template, $subject));
            //Mail::to($email)->send(new OtpEmail($otp));
            return true;
        } catch (\Exception $ex) {
            return false;
        }
    }

    /**
     * Sends a notification to a list of users.
     * 
     * @param array $data The data for the notification. The following keys are required:
     *   - type: The type of the notification (e.g. 'new_message', 'new_comment', etc.)
     *   - senderId: The ID of the user that is sending the notification
     *   - reciverId: An array of IDs of the users that should receive the notification
     *   - title: The title of the notification
     *   - message: The message of the notification
     *   - payload: An array of additional data that should be sent with the notification
     * 
     * @return bool True if the notification was sent successfully, false otherwise
     */
    public static function sendnotification($data)
    {
        $type = $data['type'];
        $senderId = $data['senderId'];
        $receiverData = $data['reciverId'];
        // print_r($receiverData );
        // exit;


        $title = $data['title'];
        $messageText = $data['message'];
        $payloadText = $data['payload'];

        // Filter receiverData to include only users with push notifications enabled
        $filteredReceiverData = collect($receiverData)->filter(function ($receiver) {
            $userId = $receiver['userId'];
            return User::where('get_push_notfication', 1)
                ->where('id', $userId)
                ->exists();
        });

        // Extract user IDs from the filtered data
        $userIds = $filteredReceiverData->pluck('userId')->toArray();

        if (!empty($userIds)) {
            Notification::insert(
                array_map(function ($userId) use ($type, $senderId, $title, $messageText, $payloadText) {
                    return [
                        'type' => $type,
                        'sender_id' => $senderId,
                        'reciver_id' => $userId,
                        'title' => $title,
                        'message' => $messageText,
                        'payload' => json_encode($payloadText),
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }, $userIds),
            );

            $serverKey = 'AAAAd2hiAsQ:APA91bHUHRb8KgCCnXBo6MKRDWJrJsJK6yMpjFbsOWunvuqQxFwoHCkYxnlCTwwJII5K41VK2hgWknlrEcJs3qJPgn0C56-9j1SJmv2QRfc1m9b9ONZSYRrofxZq4uiKwyfYP1oEvuko';

            $fcmTokens = Token::whereIn('user_id', $userIds)
                ->pluck('fcm_token')
                ->all();

            $data = [
                "registration_ids" => $fcmTokens,
                "notification" => [
                    "title" => $title,
                    "body" => $messageText
                ],
                "data" => $payloadText
            ];
            $encodedData = json_encode($data);


            $headers = [
                'Authorization:key=' . $serverKey,
                'Content-Type: application/json',
            ];

            $ch = curl_init();
            $url = 'https://fcm.googleapis.com/fcm/send';
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
            // Disabling SSL Certificate support temporarly
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $encodedData);
            // Execute post
            $result = curl_exec($ch);
            if ($result === FALSE) {
                return false;
            } else {
                curl_close($ch);
                return true;
            }
        } else {
            return false; // No recipients
        }
    }

    /**
     * Send a text message using the Twilio service.
     * 
     * @param string $reciver The recipient's phone number.
     * @param string $messageBody The body of the message.
     * @return boolean True if the message was sent successfully, false otherwise.
     */
    public static function sendTextMessage($reciver, $messageBody)
    {
        try {

            $accountSid = 'AC4b2e47e1c16cf5aa8c4d0da3d6f64366';
            $authToken = '4df6c929cbc38887e91bda559cfd2523';
            $twilioPhoneNumber = '+17068073850';
            $recipientPhoneNumber = $reciver;

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, "https://api.twilio.com/2010-04-01/Accounts/$accountSid/Messages.json");
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, "To=$reciver&From=$twilioPhoneNumber&Body=$messageBody");
            curl_setopt($ch, CURLOPT_USERPWD, "$accountSid:$authToken");
            $response = curl_exec($ch);
            if (curl_errno($ch)) {
                return true;
            } else {

                return false;
            }
        } catch (\Exception $e) {
            return false;
        }
    }
    /**
     * Generate an iCalendar event from given trip data.
     *
     * @param array $data An array of trip data.
     * @return string The generated iCalendar content.
     */
    public function generateICalendarEvents($data)
    {
        $startDateTime = DateTime::createFromFormat('Y-m-d H:i:s', $data[0]['trip_final_start_date']);
        $endDateTime = DateTime::createFromFormat('Y-m-d H:i:s', $data[0]['trip_final_end_date']);
        $calendar = Calendar::create()
            ->name('My Event Calendar')
            ->timezone('America/New_York'); // Set your desired timezone

        $event = Event::create()
            ->name($data[0]['trip_name'])
            ->description($data[0]['trip_description'])
            ->startsAt($startDateTime)
            ->endsAt($endDateTime);

        $calendar->event($event);

        return $calendar->get();
    }

    /**
     * Gets the list of trips for a given user.
     * 
     * The request payload must contain a 'tripType' parameter with a value of either 'upcoming', 'past', or 'all'.
     * The method will return a list of trips that the user is a part of, with the trip details, host details, and city details.
     * 
     * @param Request $request The HTTP request object containing the trip type.
     * 
     * @return \Illuminate\Http\JsonResponse A JSON response containing the list of trips.
     */
    public function getTripsList(Request $request)
    {
        $json = json_decode(trim($request->getContent()), true);
        $authToken = $request->header('auth');
        $userId = Helpers::getUserIdFromToken($authToken);

        $schema = [
            'tripType' => ['required', 'in:upcoming,past,all'],
        ];

        $errorMessages = [
            'tripType.required' => trans('messages.trip_type_is_required'),
            'tripType.in' => trans('messages.invalid_request'),
        ];

        $validator = Validator::make($request->all(), $schema, $errorMessages);

        if ($validator->fails()) {
            return Helpers::validatorFail($validator->errors()->first());
        }

        $imageUrl = config('global.local_image_url');
        $tripImageUrl = config('global.cover_images');
        $tripType = $json['tripType'];
        $query = TripDetails::rightJoin('trip_guests', 'trip_guests.trip_id', '=', 'trip_details.id')->select('trip_details.*', 'trip_guests.role', 'trip_guests.is_co_host');

        $query->where(function ($w) use ($userId, $tripType) {
            $w->where('trip_guests.invite_status', '!=', 'Declined')
                ->where('trip_guests.invite_status', '!=', 'Not Sent')
                ->where('trip_details.is_trip_finalised', 0)
                ->where('trip_guests.u_id', $userId)
                ->where('trip_details.is_deleted', 0)
                ->where('trip_guests.is_deleted', 0);
            if ($tripType == 'upcoming') {
                $w->where(function ($q) {
                    $q->where('is_trip_finalised', 0)->orWhere('trip_final_end_date', '>', Carbon::now());
                });
            }
            if ($tripType == 'past') {
                $w->where('trip_final_end_date', '<', Carbon::now());
            }
        });
        $query->orWhere(function ($w) use ($userId, $tripType) {
            $w->where('trip_guests.invite_status', '=', 'Approved')
                ->where('trip_details.is_trip_finalised', 1)
                ->where('trip_guests.u_id', $userId)
                ->where('trip_guests.is_deleted', 0);
            if ($tripType == 'upcoming') {
                $w->where(function ($q) {
                    $q->where('is_trip_finalised', 0)->orWhere('trip_final_end_date', '>', Carbon::now());
                });
            }
            if ($tripType == 'past') {
                $w->where('trip_final_end_date', '<', Carbon::now());
            }
        });

        $query->with('cityNameDetails:id,city_name,country_name,time_zone');
        $query->orderBy('trip_details.id', 'DESC');
        $data = $query->get();
        $data = $query
            ->with([
                'hostDetail' => function ($temp) use ($imageUrl) {
                    $temp->select(
                        'id',
                        'first_name',
                        'last_name',
                        DB::raw("
                    CASE WHEN profile_image IS NOT NULL THEN CONCAT('$imageUrl', profile_image) ELSE profile_image END as profile_image
                "),
                    );
                },
            ])
            ->get();

        $data = collect($data)
            ->map(function ($item) use ($tripImageUrl) {
                $item['trip_img_url'] = $tripImageUrl . $item['trip_img_url'];
                return $item;
            })
            ->all();

        if ($data) {
            return Helpers::success(trans('messages.trip_list_found'), $data, $authToken);
        } else {
            return Helpers::success(trans('messages.trip_list_found'), [], $authToken);
            //return Helpers::error(trans('messages.no_trip_found'), 200);
        }
    }
}
