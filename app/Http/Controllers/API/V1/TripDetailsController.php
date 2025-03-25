<?php

namespace App\Http\Controllers\API\V1;

use AshAllenDesign\ShortURL\Classes\Builder;
use App\Http\Requests\TripSaveFinalRequest;
use App\Mail\TripInvitationMail;
use App\Models\User;
use DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use App\Helpers\Helpers;
use Illuminate\Http\Request;
use App\Http\Requests\TripDetailsRequest;
use App\Http\Requests\DatesListRequest;
use App\Http\Requests\CitiesListRequest;
use App\Http\Requests\GuestsListRequest;
use App\Models\TripDetails;
use App\Models\DatesListModel;
use App\Models\CitiesListModel;
use App\Models\GuestListModel;
use App\Models\TripCityPollModel;
use App\Models\TripDatesPollModel;
use App\Models\CityModel;
use Carbon\Carbon;
use App\Models\CoverImage;
use Spatie\IcalendarGenerator\Components\Calendar;
use Spatie\IcalendarGenerator\Components\Event;
use DateTimeInterface;
use DateTime;

class TripDetailsController extends Controller
{
    /**
     * @OA\Put(
     *     path="/api/v1/trip/save-final-trip",
     *     summary="Save final trip",
     *     description="Save final trip",
     *     tags={"Trip"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             required={"tripName", "tripDescription", "itinaryDetails", "responseDeadline", "reminderDays", "tripImgUrl", "tripId", "isTripFinalised", "tripFinalStartDate", "tripFinalEndDate", "tripFinalCityId", "tripFinalizingComments"},
     *             @OA\Property(property="tripName", type="string", example="Trip name"),
     *             @OA\Property(property="tripDescription", type="string", example="Trip description"),
     *             @OA\Property(property="itinaryDetails", type="string", example="Itinary details"),
     *             @OA\Property(property="responseDeadline", type="string", format="date", example="2022-05-20"),
     *             @OA\Property(property="reminderDays", type="integer", example=1),
     *             @OA\Property(property="tripImgUrl", type="string", example="https://example.com/image.jpg"),
     *             @OA\Property(property="tripId", type="integer", example=1),
     *             @OA\Property(property="isTripFinalised", type="integer", example=1),
     *             @OA\Property(property="tripFinalStartDate", type="string", format="date", example="2022-05-20"),
     *             @OA\Property(property="tripFinalEndDate", type="string", format="date", example="2022-05-22"),
     *             @OA\Property(property="tripFinalCityId", type="integer", example=1),
     *             @OA\Property(property="tripFinalizingComments", type="string", example="Trip finalizing comments"),
     *         )
     *     ),
     *     @OA\Response(response=200, description="Successful response"),
     *     @OA\Response(response=400, description="Bad request"),
     *     @OA\Response(response=401, description="Unauthorized"),
     *     @OA\Response(response=404, description="Not found"),
     *     @OA\Response(response=422, description="The given data was invalid"),
     *     security={
     *         {"bearer": {}}
     *     }
     * )
     */
    public function saveFinalTrip(TripSaveFinalRequest $request)
    {
        $json = json_decode(trim($request->getContent()), true);
        $authToken = $request->header('auth');
        $userId = Helpers::getUserIdFromToken($authToken);

        $request->validated();
        $tripId = $json['tripId'];
        $current = Carbon::now();
        $tripData = [
            'trip_name' => $json['tripName'],
            'trip_description' => $json['tripDescription'],
            'itinary_details' => $json['itinaryDetails'],
            'response_deadline' => $json['responseDeadline'],
            'reminder_days' => $json['reminderDays'],
            'trip_img_url' => $json['tripImgUrl'],
            'updated_by' => $userId,
            'trip_finalizing_comment' => $json['tripFinalizingComments'],
            'updated_on' => $current,
        ];

        try {
            DB::beginTransaction();
            //its going to final trip
            if ($json['isTripFinalised'] == 1) {
                $tripData = array_merge($tripData, ['is_trip_finalised' => 1, 'trip_final_start_date' => $json['tripFinalStartDate'], 'trip_final_end_date' => $json['tripFinalEndDate'], 'trip_final_city' => $json['tripFinalCityId'], 'trip_finaled_on' => $current]);
            }
            TripDetails::where('id', $tripId)->update($tripData);
            //check if user purchased plan,
            $hostId = TripDetails::where('id', $tripId)->first()->created_by;
            $planCheck = User::where('id', $hostId)
                ->where('plan_end_date', '>=', now())
                ->first();

            if ($planCheck) {
                TripDetails::where('id', $tripId)
                    ->where(function ($w) {
                        $w->whereNull('paid_plan_type')->orWhere('paid_plan_type', 'normal');
                    })
                    ->Update([
                        'is_paid' => 1,
                        'paid_by' => $hostId,
                        'paid_on' => now(),
                        'paid_plan_type' => 'normal',
                    ]);
            } else {
                TripDetails::where('id', $tripId)
                    ->where('paid_plan_type', '!=', 'single')
                    ->Update([
                        'is_paid' => 0,
                        'paid_by' => $hostId,
                        'paid_on' => now(),
                        'paid_plan_type' => null,
                    ]);
            }
            DB::commit();
            if ($json['isTripFinalised'] == 1) {
                $this->sendFinalTripMail($userId, $tripId);
                return Helpers::success(trans('messages.trip_final_successfully'), TripDetails::find($tripId), $authToken);
            }
            return Helpers::success(trans('messages.trip_updated_successfully'), TripDetails::find($tripId), $authToken);
        } catch (\Exception $e) {
            print_r($e->getMessage());
            DB::rollBack();
            $this->failTripeSave();
        }
    }
    /**
     * Fail trip save
     *
     * This method will rollback the transaction and return error message when trip save or final trip is failed.
     *
     * @return Response
     */
    public function failTripeSave()
    {
        DB::rollback();
        return Helpers::error('Failed to save or final trip.', 200);
    }

    /**
     * Send final trip mail
     *
     * This method will send a mail to all invitees of a trip when trip is finalized.
     * It will also send a notification to all invitees.
     *
     * @param int $userId
     * @param int $tripId
     * @return boolean
     */
    public function sendFinalTripMail($userId, $tripId)
    {

        $data = TripDetails::with('cityNameDetails:id,city_name,time_zone,country_name')
            ->with('user:id,first_name,last_name,email,mobile_number')
            ->with('dates:id,start_date,end_date,comment,trip_id')
            ->where('trip_details.id', $tripId)
            ->get();
        $guestDetail = GuestListModel::where('trip_id', $tripId)
            ->where('invite_status', 'Approved')
            ->where(function ($query) {
                $query->orWhere('u_id', '>', 0);
            })
            ->where('u_id', '!=', $userId)
            ->select('email_id', 'u_id')
            ->get();
        // print_r($guestDetail);
        // exit;


        if ($guestDetail->isEmpty()) {
            return false;
        } else {
            $recipients = $guestDetail->pluck('email_id')->toArray();
            $icsContent = $this->generateICalendarEvents($data);
            $test = '';
            Mail::to($recipients)->send(new TripInvitationMail($test, $icsContent, $data));

            $getTripName = TripDetails::select('trip_name')
                ->where('id', $tripId)
                ->first();

            $message = $getTripName['trip_name'] . ' trip finalized';
            $reciverId = [];
            // Build an array of user IDs
            foreach ($guestDetail as $guest) {
                $reciverId[] = [
                    'userId' => $guest['u_id'],
                ];

                // print_r($$reciverId);
                // exit;
            }
            $data = [
                'type' => 'trip',
                'senderId' => '',
                'reciverId' => $reciverId,
                'title' => 'ItsGoTime',
                'message' => $message,
                'payload' => [
                    'tripId' => $tripId,
                    'type' => 'trip',
                    // Other key-value pairs
                ],
            ];
            $sendNotification = Helpers::sendnotification($data);
            return true;
        }
    }

    /**
     * This function will delete a trip.
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function deleteTrip(Request $request)
    {
        $json = json_decode(trim($request->getContent()), true);
        $authToken = $request->header('auth');
        $userId = Helpers::getUserIdFromToken($authToken);

        $schema = [
            'tripId' => 'required',
        ];

        $errorMessages = [
            'tripId.required' => trans('messages.trip_id_is_required'),
        ];

        $validator = Validator::make($request->all(), $schema, $errorMessages);

        if ($validator->fails()) {
            return Helpers::validatorFail($validator->errors()->first());
        }

        if (Helpers::isAccessible($userId, $json['tripId'], 'isHostOrCoHost')) {
            // Update cohost status
            DB::beginTransaction();
            try {
                $count = TripDetails::where('id', $json['tripId'])->update(['is_deleted' => 1]);
                DB::commit();
                return Helpers::success('Trip deleted successfully', [], $authToken);
            } catch (\Exception $e) {
                DB::rollback();
                return Helpers::error('Trip not deleted successfully', 200);
            }
        } else {
            return Helpers::error('There was something wrong in deletion', 200);
        }
    }

    /**
     * This method will return the default city id.
     * If default city is not already available in database then it will create a new one.
     *
     * @return int
     */
    public function getDefaultCity()
    {
        $getDefualt = CityModel::where('is_default', 1)->first();
        if (!$getDefualt) {
            $defaultCity = [
                'city_name' => 'Default',
                'state' => 'D',
                'state_abbr' => 'D',
                'time_zone' => 'D',
                'country_name' => 'D',
                'is_default' => 1
            ];
            $getDefualt = CityModel::create($defaultCity);
        }
        return $getDefualt->id;
    }


    /**
     * This method will create a new trip
     * 
     * @param TripDetailsRequest $request
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function createTrip(TripDetailsRequest $request)
    {
        $json = json_decode(trim($request->getContent()), true);
        $authToken = $request->header('auth');
        $userId = Helpers::getUserIdFromToken($authToken);
        $todaytDate = date('Y-m-d');
        $request->validated();

        $tripData = [
            'trip_name' => $json['tripName'],
            'trip_description' => $json['tripDescription'],
            'itinary_details' => $json['itinaryDetails'],
            'response_deadline' => $json['responseDeadline'],
            'reminder_days' => $json['reminderDays'],
            'trip_img_url' => $json['tripImgUrl'],
            'previous_reminder_date' => $todaytDate,
            'created_by' => $userId,
            'trip_dates_list' => $json['tripDatesList'],
            'trip_cities_list' => $json['tripCitiesList'],
        ];

        foreach ($request->tripDatesList as $dates) {
            $schema = [
                'startDate' => 'required|date_format:Y/m/d H:i:s',
                'endDate' => 'required|date_format:Y/m/d H:i:s',
                'comment' => 'nullable',
            ];

            $errorMessages = [
                'startDate.required' => trans('messages.start_date_is_required'),
                'startDate.date' => trans('messages.start_date_must_be_date'),
                'endDate.required' => trans('messages.end_date_is_required'),
                'endDate.date' => trans('messages.end_date_must_be_date'),
            ];

            $validator = Validator::make($dates, $schema, $errorMessages);

            if ($validator->fails()) {
                return Helpers::validatorFail($validator->errors()->first());
            }
        }

        // Check cities validation
        foreach ($request->tripCitiesList as $cities) {
            $schema = [
                'cityId' => 'required',
            ];

            $errorMessages = [
                'cityId.required' => trans('messages.city_id_is_required'),
            ];

            $validator = Validator::make($cities, $schema, $errorMessages);

            if ($validator->fails()) {
                return Helpers::validatorFail($validator->errors()->first());
            }
        }
        DB::beginTransaction();
        try {
            $tripId = TripDetails::create($tripData)->id;
            //add default date option
            $currentDateTime = Carbon::now()->format('Y/m/d H:i:s');
            $datesData = [
                'trip_id' => $tripId,
                'start_date' => $currentDateTime,
                'end_date' => $currentDateTime,
                'comment' => 'Default',
                'is_default' => 1
            ];
            DatesListModel::create($datesData);

            //add dates
            if (!$this->addDatesInDB($json['tripDatesList'], $tripId)) {
                $this->failTripeCreation();
            }
            //add default city option
            $defaultCityId = $this->getDefaultCity();
            $citiesData = [
                'trip_id' => $tripId,
                'city_id' => $defaultCityId,
            ];
            CitiesListModel::create($citiesData);
            //add cities
            if (!$this->addCitiesInDB($json['tripCitiesList'], $tripId)) {
                $this->failTripeCreation();
            }
            // add host to guest table
            // Check guest validation

            $user = Helpers::getUserDataFromUserId($userId);
            $guestDetail[] = [
                'tripId' => $tripId,
                'firstName' => $user['first_name'],
                'lastName' => $user['last_name'],
                'emailId' => $user['email'],
                'phone' => $user['country_code'] . $user['mobile_number'],
                'role' => 'Host',
                'isCoHost' => 0,
                'inviteStatus' => 'Approved',
                'uId' => $userId,
            ];
            [$isSuccess, $successCount, $failCount] = $this->addGuestInDB($guestDetail);
            if (!$isSuccess) {
                $this->failTripeCreation();
            }

            //check if user purchased plan,
            $planCheck = User::where('id', $userId)
                ->where('plan_end_date', '>=', now())
                ->first();
            if ($planCheck) {
                TripDetails::where('id', $tripId)->Update([
                    'is_paid' => 1,
                    'paid_by' => $userId,
                    'paid_on' => now(),
                    'paid_plan_type' => 'normal',
                ]);
            }

            DB::commit();
            return Helpers::success(trans('messages.trip_created_successfully'), TripDetails::find($tripId), $authToken);
        } catch (\Exception $e) {
            print_r($e->getMessage());
            DB::rollBack();
            $this->failTripeCreation();
        }
    }


    /**
     * Revert the database to its previous state when an error occurs while creating a trip.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function failTripeCreation()
    {
        DB::rollback();
        return Helpers::error(trans('messages.failed_to_create_trip'), 200);
    }

    // addDatesToTrip - This function is helpful for adding array of dates to perticuler trip using post method in api.
    public function addDatesToTrip(DatesListRequest $request)
    {
        $json = json_decode(trim($request->getContent()), true);
        $authToken = $request->header('auth');

        foreach ($json['tripDatesList'] as $dates) {
            $dates = array_merge($dates, ['tripId' => $json['tripId']]);

            $schema = [
                'tripId' => 'required|integer',
                'startDate' => 'required|date_format:Y/m/d H:i:s',
                'endDate' => 'required|date_format:Y/m/d H:i:s',
                'comment' => 'nullable',
            ];

            $errorMessages = [
                'tripId.required' => trans('messages.trip_id_is_required'),
                'tripId.integer' => trans('messages.trip_id_must_be_an_integer'),
                'startDate.required' => trans('messages.start_date_is_required'),
                'startDate.date' => trans('messages.start_date_must_be_date'),
                'endDate.required' => trans('messages.end_date_is_required'),
                'endDate.date' => trans('messages.end_date_must_be_date'),
            ];

            $validator = Validator::make($dates, $schema, $errorMessages);

            if ($validator->fails()) {
                return Helpers::validatorFail($validator->errors()->first());
            }
        }

        if ($this->addDatesInDB($json['tripDatesList'], $json['tripId'])) {
            $this->sendDateNotification($json);
            return Helpers::success(trans('messages.date_added_successfully'), '', $authToken);
        } else {
            return Helpers::error(trans('messages.failed_to_add_dates'), 200);
        }
    }

    /**
     * Send a notification to all users who are invited to a trip when a new date is added to the trip.
     *
     * @param array $json The JSON payload containing the trip ID and the new date information.
     * @return boolean True on success, false on failure.
     */
    public function sendDateNotification($json)
    {
        try {
            $guestDetail = GuestListModel::where('trip_id', $json['tripId'])
                ->where('u_id', '>', 0)
                ->where('is_deleted', false)
                ->get();

            //print_r($guestDetail); exit;

            if ($guestDetail) {
                $getTripName = TripDetails::select('trip_name')
                    ->where('id', $json['tripId'])
                    ->first();
                $message = $getTripName->trip_name . '- New Date added.';
                $reciverId = [];
                // Build an array of user IDs
                foreach ($guestDetail as $guest) {
                    $reciverId[] = [
                        'userId' => $guest['u_id'],
                    ];
                }
                $data = [
                    'type' => 'add_date',
                    'senderId' => '',
                    'reciverId' => $reciverId,
                    'title' => 'ItsGoTime',
                    'message' => $message,
                    'payload' => [
                        'tripId' => $json['tripId'],
                        'type' => 'add_date',
                        // Other key-value pairs
                    ],
                ];

                $sendNotification = Helpers::sendnotification($data);
                return true;
            }
        } catch (\Exception $e) {
            return false;
        }
    }


    /**
     * Add an array of dates to the database for a specific trip.
     *
     * This method begins a database transaction and iterates over each date in the
     * provided array. It creates a new record in the DatesListModel with the trip ID,
     * start date, end date, and optional comment. If any exception occurs during the
     * process, the transaction is rolled back.
     *
     * @param array $datesArray An array of dates, each containing 'startDate', 'endDate', and 'comment'.
     * @param int $tripId The ID of the trip to which the dates belong.
     * @return bool True on successful insertion of dates, false on failure.
     */

    public function addDatesInDB($datesArray, $tripId)
    {
        DB::beginTransaction();
        try {
            foreach ($datesArray as $datesList) {
                $datesData = [
                    'trip_id' => $tripId,
                    'start_date' => $datesList['startDate'],
                    'end_date' => $datesList['endDate'],
                    'comment' => $datesList['comment'],
                ];
                DatesListModel::create($datesData);
            }
            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollback();
            return false;
        }
    }


    /**
     * Adds an array of cities to the database for a specific trip.
     *
     * This method accepts a JSON payload in the request body with the following structure:
     * {
     *     "tripId": integer,
     *     "tripCitiesList": array of objects each containing 'cityId' and 'comment'
     * }
     *
     * It validates the incoming request data for adding cities to a trip based on the
     * specified trip ID. It then delegates the processing to the addCitiesInDB method.
     *
     * @param CitiesListRequest $request The HTTP request object containing the cities data in JSON format.
     *
     * @return \Illuminate\Http\JsonResponse A JSON response containing the result of adding cities.
     */
    public function addCitiesToTrip(CitiesListRequest $request)
    {
        $json = json_decode(trim($request->getContent()), true);
        $authToken = $request->header('auth');

        foreach ($json['tripCitiesList'] as $cities) {
            $cities = array_merge($cities, ['tripId' => $json['tripId']]);

            $schema = [
                'tripId' => 'required|integer',
                'cityId' => 'required',
            ];

            $errorMessages = [
                'tripId.required' => trans('messages.trip_id_is_required'),
                'cityId.required' => trans('messages.city_id_is_required'),
            ];

            $validator = Validator::make($cities, $schema, $errorMessages);

            if ($validator->fails()) {
                return Helpers::validatorFail($validator->errors()->first());
            }
        }

        if ($this->addCitiesInDB($json['tripCitiesList'], $json['tripId'])) {
            return Helpers::success(trans('messages.cities_added_successfully'), '', $authToken);
        } else {
            return Helpers::error(trans('messages.failed_to_add_cities'), 200);
        }
    }


    /**
     * Adds an array of cities to the database for a specific trip.
     *
     * This method begins a database transaction and iterates over each city in the
     * provided array. It creates a new record in the CitiesListModel with the trip ID,
     * city ID, and optional comment. If any exception occurs during the process, the
     * transaction is rolled back.
     *
     * @param array $citiesArray An array of cities, each containing 'cityId' and 'comment'.
     * @param int $tripId The ID of the trip to which the cities belong.
     * @return bool True on successful insertion of cities, false on failure.
     */
    public function addCitiesInDB($citiesArray, $tripId)
    {
        DB::beginTransaction();
        try {
            try {
                $cityJson = $citiesArray;

                // Extract the city IDs from the JSON data
                $cityIds = [];
                foreach ($citiesArray as $citiesList) {
                    $cityIds[] = $citiesList['cityId'];
                }

                $nonExistingCities = CitiesListModel::whereNotIn('city_id', $cityIds)->get();

                // print_r($existingCities); exit;
                if ($nonExistingCities) {
                    $guestDetail = GuestListModel::where('trip_id', $tripId)
                        ->where('u_id', '>', 0)
                        ->where('is_deleted', false)
                        ->get();

                    if ($guestDetail->count() > 0) {
                        $getTripName = TripDetails::select('trip_name')
                            ->where('id', $tripId)
                            ->first();
                        $message = $getTripName['trip_name'] . '- New city added.';
                        $reciverId = [];

                        // Build an array of user IDs
                        foreach ($guestDetail as $guest) {
                            $reciverId[] = [
                                'userId' => $guest['u_id'],
                            ];
                        }
                        $data = [
                            'type' => 'invite',
                            'senderId' => '',
                            'reciverId' => $reciverId,
                            'title' => 'ItsGoTime!',
                            'message' => $message,
                            'payload' => [
                                'tripId' => $tripId,
                                'type' => 'invite',
                                // Other key-value pairs
                            ],
                        ];
                        $sendNotification = Helpers::sendnotification($data);
                    } else {
                    }
                }
            } catch (\Exception $e) {
            }
            foreach ($citiesArray as $citiesList) {
                $citiesData = [
                    'trip_id' => $tripId,
                    'city_id' => $citiesList['cityId'],
                ];
                CitiesListModel::create($citiesData);
            }
            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollback();
            return false;
        }
    }


    /**
     * Adds a list of guests to a specified trip.
     *
     * This function processes the incoming request to add guests to a trip based on
     * the provided guest information. It validates the guest data, and if valid,
     * adds the guests to the trip. The function then delegates the database insertion
     * to the `addGuestInDB` method.
     *
     * @param GuestsListRequest $request The HTTP request object containing the guests data in JSON format.
     *
     * @return \Illuminate\Http\JsonResponse A JSON response indicating the success or failure of the operation,
     *                                       including the count of successfully added and failed guests.
     */

    public function addGuestToTrip(GuestsListRequest $request)
    {
        $json = json_decode(trim($request->getContent()), true);
        $authToken = $request->header('auth');

        $guestList = [];
        foreach ($json['tripGuestsList'] as $guests) {
            $id = Helpers::getUserIdFromUserEmailOrMobile($guests['emailId'], Helpers::getOnlyDigits($guests['phone']));

            $guests = array_merge($guests, ['uId' => $id]);

            $schema = [
                'tripId' => 'required|integer',
                'firstName' => 'required',
                'emailId' => 'required|email',
                'role' => ['required', 'in:Guest,VIP,Host'],
                'isCoHost' => 'required|boolean',
                'inviteStatus' => ['required', 'in:Not Sent,Sent,Approved,Declined'],
            ];

            $errorMessages = [
                'tripId.required' => trans('messages.trip_id_is_required'),
                'tripId.integer' => trans('messages.trip_id_must_be_an_integer'),
                'firstName.required' => trans('messages.first_name_is_required'),
                'emailId.required' => trans('messages.email_id_is_required'),
                'emailId.email' => trans('messages.invalid_email_id'),
                'role.required' => trans('messages.role_is_required'),
                'role.in' => trans('messages.invalid_role'),
                'isCoHost.required' => trans('messages.co_host_is_required'),
                'isCoHost.is_co_host' => trans('messages.invalid_co_host_status'),
                'inviteStatus.required' => trans('messages.invite_status_required'),
                'inviteStatus.in' => trans('messages.invalid_invite_status'),
            ];

            $validator = Validator::make($guests, $schema, $errorMessages);

            if ($validator->fails()) {
                return Helpers::validatorFail($validator->errors()->first());
            } else {
                $guestList[] = $guests;
            }
        }

        [$isSuccess, $successCount, $failCount] = $this->addGuestInDB($guestList);

        if ($isSuccess) {
            return Helpers::success(trans('messages.no_guest_added_no_guest_failed', ['successCount' => $successCount, 'failCount' => $failCount]), '', $authToken);
        } else {
            return Helpers::error(trans('messages.failed_to_add_guests'), 200);
        }
    }


    /**
     * addGuestInDB
     * @param array $GuestsArray containing guest array
     * @return array contains successCount, failCount
     * @throws \Exception
     */

    public function addGuestInDB($GuestsArray)
    {
        $successCount = 0;
        $failCount = 0;
        DB::beginTransaction();
        try {
            foreach ($GuestsArray as $tempGuest) {
                $guestDetail = [
                    'trip_id' => $tempGuest['tripId'],
                    'first_name' => $tempGuest['firstName'],
                    'last_name' => $tempGuest['lastName'],
                    'email_id' => $tempGuest['emailId'],
                    'phone_number' => Helpers::getOnlyDigits($tempGuest['phone']),
                    'role' => $tempGuest['role'],
                    'is_co_host' => $tempGuest['isCoHost'],
                    'invite_status' => $tempGuest['inviteStatus'],
                    'u_id' => $tempGuest['uId'],
                ];

                $data = GuestListModel::where('trip_id', $tempGuest['tripId'])
                    ->where(function ($w) use ($tempGuest) {
                        $w->where('email_id', $tempGuest['emailId']);
                        if (!empty($tempGuest['phone'])) {
                            $w->orWhere('phone_number', Helpers::getOnlyDigits($tempGuest['phone']));
                        }
                    })
                    ->where('is_deleted', false)
                    ->first();

                if ($data == null) {
                    GuestListModel::create($guestDetail);
                    $successCount = $successCount + 1;
                } else {
                    $failCount = $failCount + 1;
                }
            }
            DB::commit();
            return ['true', $successCount, $failCount];
        } catch (\Exception $e) {
            DB::rollback();
            return ['false', '0', '0'];
        }
    }


    /**
     * @OA\Get(
     *     path="/api/v1/get-cities",
     *     summary="Get cities",
     *     description="Get cities",
     *     tags={"City"},
     *     @OA\Response(response=200, description="Successful response"),
     *     @OA\Response(response=400, description="Bad request"),
     *     @OA\Response(response=401, description="Unauthorized"),
     *     @OA\Response(response=404, description="Not found"),
     *     @OA\Response(response=422, description="The given data was invalid"),
     *     security={
     *         {"bearer": {}}
     *     }
     * )
     */
    public function getCities()
    {
        try {
            $cityList = DB::table('tbl_city')
                ->orderBy('id', 'Desc')
                ->take(50)
                ->get();
            return Helpers::withoutAuthSuccessResponce(trans('messages.cities_found_successfully'), $cityList);
        } catch (\Exception $e) {
            return Helpers::error('Failed to get cities.', 200);
        }
    }
    /**
     * Retrieve a list of cities based on a search query.
     *
     * This method fetches cities from the database whose names match the 
     * provided search text. It returns up to 50 cities that are not marked 
     * as deleted or default, ordering them alphabetically by city name.
     *
     * @param Request $request The HTTP request object containing the search text in JSON format.
     *
     * @return \Illuminate\Http\JsonResponse A JSON response with the list of cities found,
     *                                       or an error message if the operation fails.
     */

    public function getCitiesSearched(Request $request)
    {
        $json = json_decode(trim($request->getContent()), true);
        $searchText = $json['searchText'];
        try {
            $cityList = DB::table('tbl_city')
                ->whereRaw('city_name like ?', ["%$searchText%"])
                ->where('is_deleted', '0')
                ->where('is_default', '0')
                ->orderBy('city_name', 'Asc')
                ->take(50)
                ->get();
            return Helpers::withoutAuthSuccessResponce(trans('messages.cities_found_successfully'), $cityList);
        } catch (\Exception $e) {
            return Helpers::error('Failed to get cities.', 200);
        }
    }


    /**
     * @OA\Post(
     *     path="/api/v1/add-remove-co-host",
     *     summary="Add/Remove Co Host",
     *     description="Add/Remove Co Host",
     *     tags={"Trip"},
     *     @OA\RequestBody(
     *         description="Add/Remove Co Host",
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="tripId", type="integer", example=1),
     *             @OA\Property(property="guestId", type="integer", example=1),
     *             @OA\Property(property="isCoHost", type="boolean", example=true)
     *         )
     *     ),
     *     @OA\Response(response=200, description="Successful response"),
     *     @OA\Response(response=400, description="Bad request"),
     *     @OA\Response(response=401, description="Unauthorized"),
     *     @OA\Response(response=404, description="Not found"),
     *     @OA\Response(response=422, description="The given data was invalid"),
     *     security={
     *         {"bearer": {}}
     *     }
     * )
     */
    public function addRemoveCoHost(Request $request)
    {
        $json = json_decode(trim($request->getContent()), true);
        $authToken = $request->header('auth');
        $userId = Helpers::getUserIdFromToken($authToken);

        $schema = [
            'tripId' => 'required',
            'guestId' => 'required',
            'isCoHost' => 'required|boolean',
        ];

        $errorMessages = [
            'tripId.required' => trans('messages.trip_id_is_required'),
            'guestId.required' => trans('messages.guest_id_required'),
            'isCoHost.required' => trans('messages.co_host_is_required'),
            'isCoHost.boolean' => trans('messages.invalid_co_host_status'),
        ];

        $validator = Validator::make($request->all(), $schema, $errorMessages);

        if ($validator->fails()) {
            return Helpers::validatorFail($validator->errors()->first());
        }

        if (Helpers::isAccessible($userId, $json['tripId'], 'isHostOrCoHost')) {
            // Update cohost status
            DB::beginTransaction();
            try {
                $count = GuestListModel::where('id', $json['guestId'])->update(['is_co_host' => $json['isCoHost']]);
                DB::commit();
                if ($count > 0) {
                    return Helpers::success(trans('messages.co_host_role_changed_successfully'), '', $authToken);
                } else {
                    return Helpers::error(trans('messages.guest_not_available'), 200);
                }
            } catch (\Exception $e) {
                DB::rollback();
                return Helpers::error(trans('messages.failed_to_change_role'), 200);
            }
        } else {
            return Helpers::error(trans('messages.failed_to_update_data_no_access'), 200);
        }
    }


    /**
     * This function will update the role of guest in a trip.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateGuestRole(Request $request)
    {
        $json = json_decode(trim($request->getContent()), true);
        $authToken = $request->header('auth');
        $userId = Helpers::getUserIdFromToken($authToken);

        $schema = [
            'tripId' => 'required',
            'guestId' => 'required',
            'isCoHost' => 'required|boolean',
            'role' => ['required', 'in:Guest,VIP,Host'],
        ];

        $errorMessages = [
            'tripId.required' => trans('messages.trip_id_is_required'),
            'guestId.required' => trans('messages.guest_id_required'),
            'role.required' => trans('messages.role_is_required'),
            'role.in' => trans('messages.invalid_role'),
            'isCoHost.required' => trans('messages.co_host_is_required'),
            'isCoHost.boolean' => trans('messages.invalid_role'),
        ];

        $validator = Validator::make($request->all(), $schema, $errorMessages);

        if ($validator->fails()) {
            return Helpers::validatorFail($validator->errors()->first());
        }

        if (Helpers::isAccessible($userId, $json['tripId'], 'isHostOrCoHost')) {
            // Update cohost status
            DB::beginTransaction();
            try {
                $count = GuestListModel::where('id', $json['guestId'])->update(['role' => $json['role'], 'is_co_host' => $json['isCoHost']]);
                DB::commit();
                try {
                    $guestDetail = GuestListModel::where('id', $json['guestId'])->first();

                    if ($guestDetail['u_id'] > 0) {
                        $GetTripName = TripDetails::select('trip_name')
                            ->where('id', $json['tripId'])
                            ->first();
                        $tripName = $GetTripName['trip_name'];
                        $message = ' Your are now ' . $json['role'] . ' in the ' . $GetTripName['trip_name'] . ' trip.';
                        $reciverId = [
                            [
                                'userId' => $guestDetail['u_id'],
                            ],
                        ];
                        $data = [
                            'type' => 'remove_invite',
                            'senderId' => '',
                            'reciverId' => $reciverId,
                            'title' => 'ItsGoTime',
                            'message' => $message,
                            'payload' => [
                                'tripId' => $json['tripId'],
                                'type' => 'roll_change',
                                // Other key-value pairs
                            ],
                        ];
                        $sendNotification = Helpers::sendnotification($data);
                    }
                } catch (\Exception $e) {
                }

                return Helpers::success(trans('messages.invitee_role_changed_successfully'), '', $authToken);
                // if ($count > 0) {
                //     return Helpers::success('Invitee role changed successfully.', "", $authToken);
                // } else {
                //     return Helpers::error(trans('messages.invitee_not_available'), 200);
                // }
            } catch (\Exception $e) {
                DB::roleback();
                return Helpers::error(trans('messages.failed_to_chnage_role'), 200);
            }
        } else {
            return Helpers::error(trans('messages.failed_to_update_data_no_access'), 200);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/v1/trip/upload-cover-image",
     *     summary="Upload trip cover image",
     *     description="Upload trip cover image",
     *     tags={"Trip"},
     *     @OA\RequestBody(
     *         description="Upload trip cover image",
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 type="object",
     *                 required={"coverImage"},
     *                 @OA\Property(
     *                     property="coverImage",
     *                     type="string",
     *                     format="binary",
     *                     description="Trip cover image"
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(response=200, description="Successful response"),
     *     @OA\Response(response=400, description="Bad request"),
     *     @OA\Response(response=401, description="Unauthorized"),
     *     @OA\Response(response=404, description="Not found"),
     *     @OA\Response(response=422, description="The given data was invalid"),
     *     security={
     *         {"bearer": {}}
     *     }
     * )
     */
    public function uploadTripCoverImage(Request $request)
    {
        $authToken = $request->header('auth');
        $schema = [
            'coverImage' => 'required|image',
        ];
        $errorMessages = [
            'coverImage.required' => trans('messages.cover_image_field_is_required'),
            'coverImage.image' => trans('messages.invalid_file'),
        ];
        $validator = Validator::make($request->all(), $schema, $errorMessages);

        if ($validator->fails()) {
            return Helpers::validatorFail($validator->errors()->first());
        }

        $imageUrl = config('global.cover_images');

        $image = $request->coverImage;
        $getImage = $request->file('coverImage');
        $imageName = time() . '.' . $getImage->extension();
        if ($image->move(public_path('uploads/coverimages'), $imageName)) {
            $responseData = [
                'coverImage' => $imageUrl . $imageName,
            ];
            return Helpers::success(trans('messages.cover_image_uploaded_successfully'), $responseData, $authToken);
        } else {
            return Helpers::error(trans('messages.failed_to_upload_image'), 200);
        }
    }



    /**
     * Removes an invitee from a trip, including associated date and city polls.
     * 
     * This function validates the request to ensure that the required parameters
     * 'tripId' and 'guestId' are present. It checks if the user has the necessary
     * access rights to remove the invitee. If the invitee is successfully removed,
     * it sends a notification to the invitee about their removal from the trip.
     * It also deletes any associated date and city polls.
     *
     * @param Request $request The HTTP request object containing 'tripId' and 'guestId'.
     * 
     * @return \Illuminate\Http\JsonResponse A JSON response indicating success or failure.
     */

    public function removeInvitee(Request $request)
    {
        $json = json_decode(trim($request->getContent()), true);
        $authToken = $request->header('auth');
        $userId = Helpers::getUserIdFromToken($authToken);

        $schema = [
            'tripId' => 'required',
            'guestId' => 'required',
        ];

        $errorMessages = [
            'tripId.required' => trans('messages.trip_id_is_required'),
            'guestId.required' => trans('messages.guest_id_required'),
        ];

        $validator = Validator::make($request->all(), $schema, $errorMessages);

        if ($validator->fails()) {
            return Helpers::validatorFail($validator->errors()->first());
        }

        if (Helpers::isAccessible($userId, $json['tripId'], 'isHostOrCoHost')) {
            // Update cohost status
            DB::beginTransaction();
            try {
                $guestDetail = GuestListModel::where('id', $json['guestId'])->first();
                $count = GuestListModel::where('id', $json['guestId'])->Delete();
                TripDatesPollModel::where('guest_id', $json['guestId'])->Delete();
                TripCityPollModel::where('guest_id', $json['guestId'])->Delete();
                DB::commit();
                if ($guestDetail) {
                    if ($guestDetail['u_id'] > 0) {
                        $GetTripName = TripDetails::select('trip_name')
                            ->where('id', $json['tripId'])
                            ->first();
                        $tripName = $GetTripName['trip_name'];
                        $message = 'You are removed from the ' . $tripName . ' trip.';
                        $reciverId = [
                            [
                                'userId' => $guestDetail['u_id'],
                            ],
                        ];
                        $data = [
                            'type' => 'remove_invite',
                            'senderId' => '',
                            'reciverId' => $reciverId,
                            'title' => 'ItsGoTime',
                            'message' => $message,
                            'payload' => [
                                'tripId' => $json['tripId'],
                                'type' => 'remove_invite',
                                // Other key-value pairs
                            ],
                        ];
                        $sendNotification = Helpers::sendnotification($data);
                    }
                    return Helpers::success('Invitee removed successfully.', '', $authToken);
                } else {
                    return Helpers::error(trans('messages.invitee_not_available'), 200);
                }
            } catch (\Exception $e) {
                DB::rollback();
                return Helpers::error(trans('messages.failed_to_remove_invitee'), 200);
            }
        } else {
            return Helpers::error(trans('messages.failed_to_remove_invitee_no_access'), 200);
        }
    }


    /**
     * Send an invitation to a guest to join a trip. This function will 
     * validate the request to ensure that the required parameters 'tripId' 
     * and 'guestId' are present. It checks if the user has the necessary 
     * access rights to send the invitation. If the invitation is successfully 
     * sent, it sends a notification to the guest about their invitation. 
     * It also updates the invitation status and last invitation time in the 
     * guest list.
     * 
     * @param Request $request The HTTP request object containing 'tripId' and 'guestId'.
     * 
     * @return \Illuminate\Http\JsonResponse A JSON response indicating success or failure.
     */
    public function sendInvitation(Request $request)
    {
        $json = json_decode(trim($request->getContent()), true);
        $authToken = $request->header('auth');
        $userId = Helpers::getUserIdFromToken($authToken);
        $encryptTripId = $json['tripId'];
        $encryptedTripId = Crypt::encryptString($encryptTripId);
        $schema = [
            'tripId' => 'required',
            'guestId' => 'required',
        ];

        $errorMessages = [
            'tripId.required' => trans('messages.trip_id_is_required'),
            'guestId.required' => trans('messages.guest_id_required'),
        ];

        $validator = Validator::make($request->all(), $schema, $errorMessages);

        if ($validator->fails()) {
            return Helpers::validatorFail($validator->errors()->first());
        }

        if (Helpers::isAccessible($userId, $json['tripId'], 'isHostOrCoHost')) {
            // Update cohost status
            DB::beginTransaction();
            try {
                $count = GuestListModel::where('id', $json['guestId'])
                    ->where('trip_id', $json['tripId'])
                    ->where('is_deleted', 0)
                    ->count();


                if ($count > 0) {
                    $guestDetail = GuestListModel::where('id', $json['guestId'])
                        // ->with(['coHosts' => function ($query) {
                        //         $query->select('first_name as cohostfname', 'last_name as cohostlname');
                        //         // Add any additional conditions if needed
                        //     }])
                        ->where('trip_id', $json['tripId'])
                        ->where('is_deleted', false)
                        ->first();


                    if ($guestDetail) {
                        GuestListModel::where('id', $json['guestId'])
                            ->where('trip_id', $json['tripId'])
                            ->where('is_deleted', false)
                            ->update(['invite_status' => 'Sent', 'last_invitation_time' => Carbon::now()]);
                    }

                    $data = TripDetails::with('city:city_id,trip_id,city_id,city_name')
                        ->with('user:id,first_name,last_name,email,mobile_number')
                        ->with('dates:id,start_date,end_date,comment,trip_id')
                        ->with('coHosts:trip_id,first_name,last_name')
                        ->withCount(['coHosts as cohosts_count'])
                        ->where('trip_details.id', $json['tripId'])
                        ->get();


                    $commaSeparatedNames = '';

                    if (count($data[0]['cohosts']) > 0) {
                        foreach ($data[0]['cohosts'] as $coHostName) {
                            $commaSeparatedNames .= ', ' . ucfirst($coHostName['first_name']) . ' ' . ucfirst($coHostName['last_name']) . '';
                        }
                    }

                    $guestId = $json['guestId'];
                    $encryptedguestId = Crypt::encryptString($guestId);
                    // Add guestId to each item in the collection
                    $data->map(function ($item) use ($encryptedTripId, $encryptedguestId, $guestDetail, $commaSeparatedNames) {
                        $builder = new Builder();
                        $url = 'https://lesgo.dashtechinc.com/pollweb/' . $encryptedTripId . '/' . $encryptedguestId;
                        $shortURLObject = $builder->destinationUrl($url)->make();
                        $item['Url'] = $shortURLObject->default_short_url;
                        $item['guest_name'] = ucfirst($guestDetail['first_name']) . " " . ucfirst($guestDetail['last_name']);
                        $item['commaSeparatedNames'] = $commaSeparatedNames;
                        return $item;
                    });

                    $tripName = $data[0]['trip_name'];
                    $userData = $data->first()->user;
                    $firstName = ucfirst($userData['first_name']);
                    $lastName = ucfirst($userData['last_name']);

                    //co host counts
                    if ($data[0]['cohosts_count'] > 0) {
                        $countMessage = 'and ' . $data[0]['cohosts_count'] . ' more Co Host';
                    } else {
                        $countMessage = '';
                    }

                    if ($guestDetail['u_id'] > 0) {


                        $message = $firstName . ' ' . $lastName . ' ' . $countMessage . ' has invited to ' . $tripName . ' trip!';
                        $reciverId = [
                            [
                                'userId' => $guestDetail['u_id'],
                            ],
                        ];
                        $notificationData = [
                            'type' => 'invite',
                            'senderId' => '',
                            'reciverId' => $reciverId,
                            //'title' => $tripName .' Invitation',
                            'title' => 'ItsGoTime',
                            'message' => $message,
                            'payload' => [
                                'tripId' => $json['tripId'],
                                'type' => 'invite',
                                // Other key-value pairs
                            ],
                        ];
                        $sendNotification = Helpers::sendnotification($notificationData);
                    }

                    $template = 'sendinvitation';
                    //$subject = 'Hey '.ucfirst($guestDetail['first_name']) . ' ' .ucfirst($guestDetail['last_name']) . '! ' .$firstName . ' ' . $lastName . ' '. $countMessage.'  invited you to an event!';
                    $subject =  ucfirst($firstName) . ' ' . ucfirst($lastName) . ' Invited You To An Event!';
                    $sendOtp = Helpers::sendEmail($guestDetail['email_id'], $data, $template, $subject);

                    $mobileNumber = $guestDetail['phone_number'];
                    $url =  $data[0]['Url'];
                    $messageBody = $firstName . ' ' . $lastName . '  invited you to join ' . $data[0]['trip_name'] . '  Can you Go? RSVP on the web :' . $url;
                    $sendTripMessage = Helpers::sendTextMessage($mobileNumber, $messageBody);
                    DB::commit();
                    $guestDetail->increment('no_of_invite_send');

                    return Helpers::success(trans('messages.invitation_sent_successfully'), '', $authToken);
                } else {
                    DB::rollback();
                    return Helpers::error(trans('messages.failed_to_send_invitation_no_guest_found'), 200);
                }
            } catch (\Exception $e) {
                print_r($e->getMessage());

                DB::rollback();
                return Helpers::error(trans('messages.failed_to_send_invitation'), 200);
            }
        } else {
            return Helpers::error(trans('messages.failed_to_send_invitation_no_access'), 200);
        }
    }


    /**
     * Generate an ICS file from the given trip data
     *
     * @param array $data
     * @return string
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
     * Get list of trips for user
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
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
                ->where('trip_details.is_deleted', 0)
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

    /**
     * @OA\Post(
     *     path="/api/v1/trip/details",
     *     tags={"Trip Details"},
     *     summary="Get trip details",
     *     description="Get trip details",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="tripId",
     *                 type="string",
     *                 example="1",
     *                 description="Trip Id"
     *             ),
     *             @OA\Property(
     *                 property="guestId",
     *                 type="string",
     *                 example="1",
     *                 description="Guest Id"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Trip details found",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="status",
     *                 type="string",
     *                 example="success"
     *             ),
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="Trip details found"
     *             ),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 ref="#/components/schemas/TripDetails"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="No trip details found",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="status",
     *                 type="string",
     *                 example="error"
     *             ),
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="No trip details found"
     *             )
     *         )
     *     )
     * )
     */
    public function getTripDetailsWeb(Request $request)
    {
        $json = json_decode(trim($request->getContent()), true);
        $schema = [
            'tripId' => 'required',
            'guestId' => 'required',
        ];

        $errorMessages = [
            'tripId.required' => trans('messages.trip_id_is_required'),
            'guestId.required' => 'Guest Id is required',
        ];

        $validator = Validator::make($request->all(), $schema, $errorMessages);

        if ($validator->fails()) {
            return Helpers::validatorFail($validator->errors()->first());
        }

        $imageUrl = config('global.local_image_url');
        $tripImageUrl = config('global.cover_images');

        $data = TripDetails::where('trip_details.id', $json['tripId'])
            ->join('trip_guests', 'trip_guests.trip_id', '=', 'trip_details.id')
            ->where('trip_guests.id', $json['guestId'])
            ->where('trip_guests.is_deleted', 0)
            ->select('trip_details.*', 'trip_guests.role', 'trip_guests.is_co_host', 'trip_guests.invite_status')
            ->with('hostDetail:id,first_name,last_name,profile_image')
            ->with('premiumPlanBy:id,first_name,last_name,profile_image')
            ->with('cityNameDetails')
            ->first();

        if ($data) {
            $totalGuest = GuestListModel::where('trip_id', $json['tripId'])
                ->where('is_deleted', 0)
                ->count();
            $data['hostDetail']['profile_image'] = $imageUrl . $data['hostDetail']['profile_image'];
            $data['premiumPlanBy']['profile_image'] = $imageUrl . $data['premiumPlanBy']['profile_image'];
            $data['trip_img_url'] = $tripImageUrl . $data['trip_img_url'];
            $data['guest_count'] = $totalGuest;
            return Helpers::success(trans('messages.trip_detail_found'), $data, '');
        } else {
            return Helpers::error(trans('messages.no_trip_detail_found'), 200);
        }
    }

    /**
     * Retrieve detailed information about a specific trip for the authenticated user.
     *
     * This function validates the request to ensure the 'tripId' is provided,
     * and retrieves the trip details, including guest role, co-host status,
     * invite status, host details, and premium plan information. It also
     * appends the profile image URLs and calculates the total number of guests.
     *
     * @param Request $request The HTTP request object containing the 'tripId'.
     *
     * @return \Illuminate\Http\JsonResponse A JSON response containing the trip details
     * if found, or an error message if no details are found.
     */

    public function getTripDetail(Request $request)
    {
        $json = json_decode(trim($request->getContent()), true);
        $authToken = $request->header('auth');
        $userId = Helpers::getUserIdFromToken($authToken);

        $schema = [
            'tripId' => 'required',
        ];

        $errorMessages = [
            'tripId.required' => trans('messages.trip_id_is_required'),
        ];

        $validator = Validator::make($request->all(), $schema, $errorMessages);

        if ($validator->fails()) {
            return Helpers::validatorFail($validator->errors()->first());
        }
        $imageUrl = config('global.local_image_url');
        $tripImageUrl = config('global.cover_images');

        $data = TripDetails::where('trip_details.id', $json['tripId'])
            ->join('trip_guests', 'trip_guests.trip_id', '=', 'trip_details.id')
            ->where('trip_guests.u_id', $userId)
            ->where('trip_guests.is_deleted', 0)
            ->select('trip_details.*', 'trip_guests.role', 'trip_guests.is_co_host', 'trip_guests.invite_status')
            ->with('hostDetail:id,first_name,last_name,profile_image')
            ->with('premiumPlanBy:id,first_name,last_name,profile_image')
            ->with('cityNameDetails')
            ->first();

        if ($data) {
            $totalGuest = GuestListModel::where('trip_id', $json['tripId'])
                ->where('is_deleted', 0)
                ->count();
            $data['hostDetail']['profile_image'] = $imageUrl . $data['hostDetail']['profile_image'];
            $data['premiumPlanBy']['profile_image'] = $imageUrl . $data['premiumPlanBy']['profile_image'];
            $data['trip_img_url'] = $tripImageUrl . $data['trip_img_url'];
            $data['guest_count'] = $totalGuest;
            return Helpers::success(trans('messages.trip_detail_found'), $data, $authToken);
        } else {
            return Helpers::error(trans('messages.no_trip_detail_found'), 200);
        }
    }

    /**
     * Add date poll for guest in trip
     *
     * This function validates the request to ensure the 'tripId', 'guestId', and 'tripDatesListId' are provided,
     * and then adds the date poll for the guest in the specific trip.
     *
     * @param Request $request The HTTP request object containing the 'tripId', 'guestId', and 'tripDatesListId'.
     *
     * @return \Illuminate\Http\JsonResponse A JSON response containing the success message if poll added successfully,
     * or an error message if something went wrong.
     */
    public function addDatePollWeb(Request $request)
    {
        $json = json_decode(trim($request->getContent()), true);

        $schema = [
            'tripId' => 'required',
            'guestId' => 'required',
            'tripDatesListId' => 'required',
        ];

        $errorMessages = [
            'tripId' => trans('messages.trip_id_is_required'),
            'tripDatesListId.required' => trans('messages.trip_date_id_is_required'),
            'guestId.required' => 'Guest Id is required',
        ];

        $validator = Validator::make($request->all(), $schema, $errorMessages);

        if ($validator->fails()) {
            return Helpers::validatorFail($validator->errors()->first());
        }
        if ($this->addDatePollMethod($json['guestId'], $json['tripDatesListId'])) {
            return Helpers::success(trans('messages.poll_added_successfully'), '', '');
        } else {
            return Helpers::error(trans('messages.something_went_wrong'), 200);
        }
    }

    /**
     * Add date poll for logged in user in trip
     *
     * This function validates the request to ensure the 'tripId', 'tripDatesListId', and 'isSelected' are provided,
     * and then adds the date poll for the logged in user in the specific trip.
     *
     * @param Request $request The HTTP request object containing the 'tripId', 'tripDatesListId', and 'isSelected'.
     *
     * @return \Illuminate\Http\JsonResponse A JSON response containing the success message if poll added successfully,
     * or an error message if something went wrong.
     */
    public function addDatePoll(Request $request)
    {
        $json = json_decode(trim($request->getContent()), true);
        $authToken = $request->header('auth');
        $userId = Helpers::getUserIdFromToken($authToken);

        $schema = [
            'tripId' => 'required',
            'tripDatesListId' => 'required',
            'isSelected' => 'required|boolean',
        ];

        $errorMessages = [
            'tripId' => trans('messages.trip_id_is_required'),
            'tripDatesListId.required' => trans('messages.trip_date_id_is_required'),
            'isSelected.required' => trans('messages.is_selected_is_required'),
            'isSelected.in' => trans('messages.invalid_selection'),
        ];

        $validator = Validator::make($request->all(), $schema, $errorMessages);

        if ($validator->fails()) {
            return Helpers::validatorFail($validator->errors()->first());
        }
        $guestId = GuestListModel::where('trip_id', $json['tripId'])
            ->where('u_id', $userId)
            ->first()->id;
        if ($this->addDatePollMethod($guestId, $json['tripDatesListId'])) {
            return Helpers::success(trans('messages.poll_added_successfully'), '', $authToken);
        } else {
            return Helpers::error(trans('messages.something_went_wrong'), 200);
        }
    }

    /**
     * Add date poll for logged in user in trip
     *
     * This function is used in addDatePoll and addDatePollWeb to add date poll for logged in user in trip.
     * It deletes all the existing polls for the guest and then adds the new poll for the given trip dates list.
     *
     * @param int $guestId The id of the guest.
     * @param array $tripDatesListIdArray The array of trip dates list ids.
     *
     * @return boolean True if poll added successfully, false otherwise.
     */
    public function addDatePollMethod($guestId, $tripDatesListIdArray)
    {
        DB::beginTransaction();
        try {
            TripDatesPollModel::where('guest_id', $guestId)->Delete();
            foreach ($tripDatesListIdArray as $tripDatesListId) {
                $datesPollData = [
                    'trip_dates_list_id' => $tripDatesListId,
                    'guest_id' => $guestId,
                    'is_selected' => 1,
                ];
                TripDatesPollModel::create($datesPollData);
            }
            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            return false;
        }
    }

    /**
     * Add dropbox url for trip
     *
     * This function is used to add the dropbox url for the trip
     *
     * @param Request $request The HTTP request object containing the 'tripId' and 'dropboxUrl'.
     *
     * @return \Illuminate\Http\JsonResponse A JSON response containing the success message if url added successfully,
     * or an error message if something went wrong.
     */
    public function addDropboxUrl(Request $request)
    {
        $json = json_decode(trim($request->getContent()), true);

        $schema = [
            'tripId' => 'required',
            'dropboxUrl' => 'required',
        ];

        $errorMessages = [
            'tripId' => trans('messages.trip_id_is_required'),
            'dropboxUrl.required' => 'Dropbox url is required',
        ];

        $validator = Validator::make($request->all(), $schema, $errorMessages);

        if ($validator->fails()) {
            return Helpers::validatorFail($validator->errors()->first());
        }
        TripDetails::where('id', $json['tripId'])->update([
            'dropbox_url' => $json['dropboxUrl']
        ]);
        return Helpers::success('link has been saved successfully', '', '');
    }

    /**
     * Add city poll for a guest in a trip
     *
     * This function validates the request to ensure that the 'tripId', 'tripCityListId', 
     * and 'guestId' are provided. It then attempts to add a city poll for the guest in the 
     * specified trip using these details.
     *
     * @param Request $request The HTTP request object containing the 'tripId', 'tripCityListId', 
     * and 'guestId'.
     *
     * @return \Illuminate\Http\JsonResponse A JSON response containing a success message if the 
     * poll is added successfully, or an error message if something went wrong.
     */

    public function addCityPollWeb(Request $request)
    {
        $json = json_decode(trim($request->getContent()), true);

        $schema = [
            'tripId' => 'required',
            'tripCityListId' => 'required',
            'guestId' => 'required',
        ];

        $errorMessages = [
            'tripId' => trans('messages.trip_id_is_required'),
            'tripCityListId.required' => trans('messages.trip_city_id_is_required'),
            'guestId.required' => 'Guest Id is required',
        ];

        $validator = Validator::make($request->all(), $schema, $errorMessages);

        if ($validator->fails()) {
            return Helpers::validatorFail($validator->errors()->first());
        }

        if ($this->addCityPollMethod($json['guestId'], $json['tripCityListId'])) {
            return Helpers::success(trans('messages.poll_added_successfully'), '', '');
        } else {
            return Helpers::error(trans('messages.something_went_wrong'), 200);
        }
    }
    /**
     * Add city poll for logged in user in trip.
     *
     * This function validates the request to ensure the 'tripId', 'tripCityListId', and 'isSelected' are provided.
     * It then retrieves the guest ID based on the user ID extracted from the authentication token and the trip ID.
     * The function attempts to add a city poll for the guest in the specified trip using these details.
     *
     * @param Request $request The HTTP request object containing the 'tripId', 'tripCityListId', and 'isSelected'.
     *
     * @return \Illuminate\Http\JsonResponse A JSON response containing a success message if the poll is added
     * successfully, or an error message if something went wrong.
     */

    public function addCityPoll(Request $request)
    {
        $json = json_decode(trim($request->getContent()), true);
        $authToken = $request->header('auth');
        $userId = Helpers::getUserIdFromToken($authToken);

        $schema = [
            'tripId' => 'required',
            'tripCityListId' => 'required',
            'isSelected' => 'required|boolean',
        ];

        $errorMessages = [
            'tripId' => trans('messages.trip_id_is_required'),
            'tripCityListId.required' => trans('messages.trip_city_id_is_required'),
            'isSelected.required' => trans('messages.is_selected_is_required'),
            'isSelected.boolean' => trans('messages.invalid_selection'),
        ];

        $validator = Validator::make($request->all(), $schema, $errorMessages);

        if ($validator->fails()) {
            return Helpers::validatorFail($validator->errors()->first());
        }
        $guestId = GuestListModel::where('trip_id', $json['tripId'])
            ->where('u_id', $userId)
            ->first()->id;
        if ($this->addCityPollMethod($guestId, $json['tripCityListId'])) {
            return Helpers::success(trans('messages.poll_added_successfully'), '', $authToken);
        } else {
            return Helpers::error(trans('messages.something_went_wrong'), 200);
        }
    }

    /**
     * Add city poll for the given guest in the specified trip.
     *
     * This function deletes all the existing polls for the guest and then adds the new poll for the given trip city
     * list IDs. It begins a database transaction, attempts to delete the existing polls, adds the new poll, and
     * commits the transaction if successful. If any exception occurs during the process, the transaction is rolled
     * back.
     *
     * @param int $guestId The ID of the guest.
     * @param array $tripCityListIdArray The array of trip city list IDs.
     *
     * @return boolean True if poll added successfully, false otherwise.
     */
    public function addCityPollMethod($guestId, $tripCityListIdArray)
    {
        DB::beginTransaction();
        try {
            TripCityPollModel::where('guest_id', $guestId)->Delete();
            foreach ($tripCityListIdArray as $tripCityListId) {
                $cityPollData = [
                    'trip_city_list_id' => $tripCityListId,
                    'guest_id' => $guestId,
                    'is_selected' => 1,
                ];
                TripCityPollModel::create($cityPollData);
            }
            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            return false;
        }
    }
    /**
     * Updates the invitation status for a guest in a trip. This function validates the request to ensure that the
     * required parameters 'tripId', 'status', and 'guestId' are present. It checks if the guest exists in the
     * guest list and then updates the invitation status and last invitation time in the guest list.
     * It also sends a notification to the guest about the invitation status.
     *
     * @param Request $request The HTTP request object containing 'tripId', 'status', and 'guestId'.
     *
     * @return \Illuminate\Http\JsonResponse A JSON response indicating success or failure.
     */
    public function actionOnInvitationWeb(Request $request)
    {
        $json = json_decode(trim($request->getContent()), true);

        $schema = [
            'tripId' => 'required',
            'status' => 'required|in:Approved,Declined',
            'guestId' => 'required',
        ];

        $errorMessages = [
            'tripId' => trans('messages.trip_id_is_required'),
            'status' => 'Status is required',
            'guestId' => 'Guest Id is required',
        ];

        $validator = Validator::make($request->all(), $schema, $errorMessages);

        if ($validator->fails()) {
            return Helpers::validatorFail($validator->errors()->first());
        }
        $guestListModel = GuestListModel::where('trip_id', $json['tripId'])
            ->where('id', $json['guestId'])
            ->first();
        if ($guestListModel) {
            if ($this->actionOnInvitationMethod($guestListModel, $json)) {
                return Helpers::success('Invitation status updated successfully', '', '');
            } else {
                return Helpers::error('There was something wrong.', 200);
            }
        } else {
            return Helpers::error('There was something wrong.', 200);
        }
    }
    /**
     * Handles the action on a trip invitation for a logged-in user.
     *
     * This function validates the request to ensure the 'tripId' and 'status' 
     * are provided. It checks the guest list model for the user and updates 
     * the invitation status accordingly using the actionOnInvitationMethod. 
     * Returns a success message if the status is updated, or an error message 
     * if something goes wrong.
     *
     * @param Request $request The HTTP request object containing 'tripId' and 'status'.
     *
     * @return \Illuminate\Http\JsonResponse A JSON response indicating success or failure.
     */

    public function actionOnInvitation(Request $request)
    {
        $json = json_decode(trim($request->getContent()), true);
        $authToken = $request->header('auth');
        $userId = Helpers::getUserIdFromToken($authToken);

        $schema = [
            'tripId' => 'required',
            'status' => 'required|in:Approved,Declined',
        ];

        $errorMessages = [
            'tripId' => trans('messages.trip_id_is_required'),
            'status' => 'Status is required',
        ];

        $validator = Validator::make($request->all(), $schema, $errorMessages);

        if ($validator->fails()) {
            return Helpers::validatorFail($validator->errors()->first());
        }
        $guestListModel = GuestListModel::where('trip_id', $json['tripId'])
            ->where('u_id', $userId)
            ->first();
        if ($this->actionOnInvitationMethod($guestListModel, $json)) {
            return Helpers::success('Invitation status updated successfully', '', $authToken);
        } else {
            return Helpers::error('There was something wrong.', 200);
        }
    }

    /**
     * Updates the invitation status of a guest and sends notifications.
     *
     * This function sets the invitation status for the given guest list model
     * based on the provided JSON input. If the status is 'Declined', it deletes
     * associated date and city polls for the guest. It also retrieves the trip
     * owner or co-hosts to send a notification about the change in invitation status.
     *
     * @param object $guestListModel The guest list model object to update.
     * @param array $json An associative array containing the 'status' and 'tripId'.
     *
     * @return bool Returns true if the status is successfully updated and notification sent, false otherwise.
     */

    public function actionOnInvitationMethod($guestListModel, $json)
    {
        $guestListModel->invite_status = $json['status'];
        if ($guestListModel->save()) {
            if ($json['status'] == 'Declined') {
                TripDatesPollModel::where('guest_id', $guestListModel->id)->delete();
                TripCityPollModel::where('guest_id', $guestListModel->id)->delete();
            }
            try {
                // $getTripOwnerId = TripDetails::select('created_by')
                //->where('id', $json['tripId'])
                // ->first();
                $getTripOwnerId = GuestListModel::where('trip_id', $json['tripId'])
                    ->where('is_deleted', 0)
                    ->where('u_id', '!=', 0)
                    ->where(function ($w) {
                        $w->where('role', 'Host')->orWhere('is_co_host', '=', 1);
                    })
                    ->pluck('u_id');
                //test
                if ($getTripOwnerId->count() > 0) {
                    if ($json['status'] == 'Approved') {
                        $type = 'accept_invite';
                        $status = 'accepted';
                        $status1 = 'Accepted';
                    } else {
                        $type = 'reject_invite';
                        $status = 'rejected';
                        $status1 = 'Rejected';
                    }
                    $tripName = TripDetails::select('trip_name')
                        ->where('id', $json['tripId'])
                        ->first();

                    $message = $guestListModel['first_name'] . ' ' . $guestListModel['last_name'] . ' has ' . $status . ' invitation for ' . $tripName['trip_name'];

                    $reciverIds = $getTripOwnerId->toArray(); // Convert the collection to an array

                    $reciverId = array_map(function ($userId) {
                        return ['userId' => $userId];
                    }, $reciverIds);

                    $data = [
                        'type' => $type,
                        'senderId' => '',
                        'reciverId' => $reciverId,
                        //'title' => $status1 . ' Invitation',
                        'title' => 'ItsGoTime!',
                        'message' => $message,
                        'payload' => [
                            'tripId' => $json['tripId'],
                            'type' => $type,
                            // Other key-value pairs
                        ],
                    ];
                    $sendNotification = Helpers::sendnotification($data);
                }
            } catch (\Exception $e) {
            }
            return true;
        } else {
            return false;
        }
    }

    /**
     * Gets the list of guests for a given trip ID.
     *
     * This function retrieves the guest list associated with the given trip ID
     * and returns it as a JSON response. It also concatenates the local image
     * URL with the user's profile image URL if it exists.
     *
     * @param Request $request The HTTP request object containing the trip ID.
     *
     * @return \Illuminate\Http\JsonResponse A JSON response containing the guest list.
     */
    public function getTripGuestList(Request $request)
    {
        $json = json_decode(trim($request->getContent()), true);
        $authToken = $request->header('auth');
        // $userData = Helpers::getUserDataFromId($authToken);

        $schema = [
            'tripId' => 'required',
        ];

        $errorMessages = [
            'tripId.required' => trans('messages.trip_id_is_required'),
        ];

        $validator = Validator::make($request->all(), $schema, $errorMessages);

        if ($validator->fails()) {
            return Helpers::validatorFail($validator->errors()->first());
        }

        $imageUrl = config('global.local_image_url');

        $data = GuestListModel::leftJoin('tbl_users', 'trip_guests.u_id', '=', 'tbl_users.id')
            ->where('trip_id', $json['tripId'])
            ->where('is_deleted', false)
            ->select('trip_guests.*', DB::raw("(case when tbl_users.profile_image is null then '' else CONCAT('$imageUrl', tbl_users.profile_image) end) as profile_picture"))
            ->get();

        if ($data->isEmpty()) {
            return Helpers::error(trans('messages.no_guest_found'), 200);
        } else {
            return Helpers::success(trans('messages.guest_list_found'), $data, $authToken);
        }
    }

    /**
     * Retrieves a list of cover images.
     *
     * This function fetches cover images from the database, appends the static 
     * image path to each image's name, and returns them as a JSON response. 
     * If the retrieval is successful, it returns the images with a success message. 
     * If no images are found or an error occurs, it returns an appropriate error message.
     *
     * @param Request $request The HTTP request object containing headers.
     *
     * @return \Illuminate\Http\JsonResponse A JSON response with the list of cover images or an error message.
     */

    public function getcoverimages(Request $request)
    {
        $authToken = $request->header('auth');
        $staticImagePath = config('global.cover_images');
        try {
            $data = CoverImage::orderBy('id', 'Desc')
                ->where('is_deleted', 0)
                ->get();
            $data = collect($data)
                ->map(function ($item) use ($staticImagePath) {
                    $item['image_name'] = $staticImagePath . $item['image_name'];
                    return $item;
                })
                ->all();
            if ($data) {
                return Helpers::success(trans('messages.cover_images_found_successfully'), $data, $authToken);
            } else {
                return Helpers::error(trans('messages.no_cover_images_found'), 200);
            }
        } catch (\Exception $e) {
            return Helpers::error(trans('messages.failed_to_get_cover_images'), 200);
        }
    }

    /**
     * Retrieves a list of trip dates for a specific guest.
     *
     * This function validates the request to ensure the 'tripId' and 'guestId' are provided,
     * and then fetches the trip dates for the given guest in the specific trip.
     *
     * @param Request $request The HTTP request object containing the 'tripId', and 'guestId'.
     *
     * @return \Illuminate\Http\JsonResponse A JSON response containing the list of trip dates or an error message.
     */
    public function getDatesPollDetailsWeb(Request $request)
    {
        $json = json_decode(trim($request->getContent()), true);
        $schema = [
            'tripId' => 'required',
            'guestId' => 'required',
        ];

        $errorMessages = [
            'tripId.required' => trans('messages.trip_id_is_required'),
            'guestId.required' => 'Guest Id is required',
        ];

        $validator = Validator::make($request->all(), $schema, $errorMessages);

        if ($validator->fails()) {
            return Helpers::validatorFail($validator->errors()->first());
        }
        $data = $this->getDatesPoll($json['tripId'], $json['guestId']);
        if ($data) {
            return Helpers::success(trans('messages.date_lists_found'), $data, '');
        } else {
            return Helpers::error(trans('messages.no_dates_found'), 200);
        }
    }


    /**
     * Retrieves a list of trip dates for a specific user.
     *
     * This function validates the request to ensure the 'tripId' is provided,
     * and then fetches the trip dates for the given user in the specific trip.
     *
     * @param Request $request The HTTP request object containing the 'tripId'.
     *
     * @return \Illuminate\Http\JsonResponse A JSON response containing the list of trip dates or an error message.
     */
    public function getDatesPollDetails(Request $request)
    {
        $json = json_decode(trim($request->getContent()), true);
        $authToken = $request->header('auth');
        $userId = Helpers::getUserIdFromToken($authToken);

        $schema = [
            'tripId' => 'required',
        ];

        $errorMessages = [
            'tripId.required' => trans('messages.trip_id_is_required'),
        ];

        $validator = Validator::make($request->all(), $schema, $errorMessages);

        if ($validator->fails()) {
            return Helpers::validatorFail($validator->errors()->first());
        }
        $guestId = GuestListModel::where('trip_id', $json['tripId'])
            ->where('u_id', $userId)
            ->first()->id;
        $data = $this->getDatesPoll($json['tripId'], $guestId);
        if ($data) {
            return Helpers::success(trans('messages.date_lists_found'), $data, $authToken);
        } else {
            return Helpers::error(trans('messages.no_dates_found'), 200);
        }
    }

    /**
     * Retrieves a list of trip dates for a specific guest.
     *
     * This function fetches the trip dates for the given guest in the specific trip,
     * and then appends some additional information to the result, such as the total
     * number of VIPs and guests, the number of VIPs and guests who have voted,
     * and the total number of votes.
     *
     * @param int $tripId The ID of the trip.
     * @param int $guestId The ID of the guest.
     *
     * @return array|null The list of trip dates or null if no dates are found.
     */
    public function getDatesPoll($tripId, $guestId)
    {
        $data = DatesListModel::with(['tripDatePolls', 'tripDatePolls.guestDetails.usersDetailProfileImage'])
            ->where('trip_dates_list.trip_id', $tripId)
            ->where('trip_dates_list.is_deleted', 0)
            ->get()
            ->toArray();
        $totalVip = GuestListModel::where('trip_id', $tripId)
            ->where('role', 'VIP')
            ->count();
        $totalGuest = GuestListModel::where('trip_id', $tripId)
            ->whereNot('role', 'Host')
            ->where('is_deleted', 0)
            ->count(); //without host
        $imageUrl = config('global.local_image_url');

        foreach ($data as &$item) {
            $VipVoted = 0;
            $userVoted = 0;
            $userImage = [];
            foreach ($item['trip_date_polls'] as &$itemPoll) {
                if ($itemPoll['guest_details']['role'] == 'VIP') {
                    $VipVoted++;
                }
                if ($itemPoll['guest_details']['id'] == $guestId) {
                    $userVoted = 1;
                }
                if ($itemPoll['guest_details']['users_detail_profile_image']) {
                    array_push($userImage, $imageUrl . $itemPoll['guest_details']['users_detail_profile_image']['profile_image']);
                    $itemPoll['guest_details']['users_detail_profile_image']['profile_image'] = $imageUrl . $itemPoll['guest_details']['users_detail_profile_image']['profile_image'];
                }
                unset($itemPoll);
            }
            $item['vipVoted'] = $VipVoted;
            $item['totalVip'] = $totalVip;
            $item['totalGuest'] = $totalGuest;
            $item['userVoted'] = $userVoted;
            $item['userImage'] = $userImage;
            $item['totalVoted'] = count($item['trip_date_polls']);
            unset($item);
        }
        // Sort by age in ascending order
        array_multisort(array_column($data, 'id'), SORT_DESC, $data);
        return $data;
    }

    /**
     * Get city poll details
     *
     * This function validates the request to ensure the 'tripId' and 'guestId' are provided,
     * and then retrieves the city poll details for the guest in the specific trip.
     *
     * @param Request $request The HTTP request object containing the 'tripId' and 'guestId'.
     *
     * @return \Illuminate\Http\JsonResponse A JSON response containing the city poll details if found,
     * or an error message if something went wrong.
     */
    public function getCityPollDetailsWeb(Request $request)
    {
        $json = json_decode(trim($request->getContent()), true);
        $schema = [
            'tripId' => 'required',
            'guestId' => 'required',
        ];

        $errorMessages = [
            'tripId.required' => trans('messages.trip_id_is_required'),
            'guestId.required' => 'Guest Id is required',
        ];

        $validator = Validator::make($request->all(), $schema, $errorMessages);

        if ($validator->fails()) {
            return Helpers::validatorFail($validator->errors()->first());
        }
        $data = $this->getCityPoll($json['tripId'], $json['guestId']);
        if ($data) {
            return Helpers::success(trans('messages.city_lists_found'), $data, '');
        } else {
            return Helpers::error(trans('messages.no_cities_found'), 200);
        }
    }

    /**
     * Get city poll details
     *
     * This function validates the request to ensure the 'tripId' is provided,
     * and then retrieves the city poll details for the user in the specific trip.
     *
     * @param Request $request The HTTP request object containing the 'tripId'.
     *
     * @return \Illuminate\Http\JsonResponse A JSON response containing the city poll details if found,
     * or an error message if something went wrong.
     */
    public function getCityPollDetails(Request $request)
    {
        $json = json_decode(trim($request->getContent()), true);
        $authToken = $request->header('auth');
        $userId = Helpers::getUserIdFromToken($authToken);

        $schema = [
            'tripId' => 'required',
        ];

        $errorMessages = [
            'tripId.required' => trans('messages.trip_id_is_required'),
        ];

        $validator = Validator::make($request->all(), $schema, $errorMessages);

        if ($validator->fails()) {
            return Helpers::validatorFail($validator->errors()->first());
        }
        $guestId = GuestListModel::where('trip_id', $json['tripId'])
            ->where('u_id', $userId)
            ->first()->id;
        $data = $this->getCityPoll($json['tripId'], $guestId);
        if ($data) {
            return Helpers::success(trans('messages.city_lists_found'), $data, $authToken);
        } else {
            return Helpers::error(trans('messages.no_cities_found'), 200);
        }
    }

    /**
     * Retrieves a list of city polls for a specific guest in a trip.
     *
     * This function fetches city poll details associated with a given trip 
     * and guest, and appends additional information such as the total number 
     * of VIPs, guests, and the number of VIPs and guests who have voted.
     *
     * @param int $tripId The ID of the trip.
     * @param int $guestId The ID of the guest.
     *
     * @return array The list of city poll details with additional information.
     */

    public function getCityPoll($tripId, $guestId)
    {
        $data = CitiesListModel::with(['tripCityPolls', 'tripCityPolls.guestDetails.usersDetailProfileImage', 'cityNameDetails'])
            ->where('trip_city_list.trip_id', $tripId)
            ->where('trip_city_list.is_deleted', 0)
            ->get()
            ->toArray();
        $totalVip = GuestListModel::where('trip_id', $tripId)
            ->where('role', 'VIP')
            ->count();
        $totalGuest = GuestListModel::where('trip_id', $tripId)
            ->whereNot('role', 'Host')
            ->where('is_deleted', 0)
            ->count(); //without host
        $imageUrl = config('global.local_image_url');

        foreach ($data as &$item) {
            $VipVoted = 0;
            $userVoted = 0;
            $userImage = [];
            foreach ($item['trip_city_polls'] as &$itemPoll) {
                if ($itemPoll['guest_details']['role'] == 'VIP') {
                    $VipVoted++;
                }
                if ($itemPoll['guest_details']['id'] == $guestId) {
                    $userVoted = 1;
                }
                if ($itemPoll['guest_details']['users_detail_profile_image']) {
                    array_push($userImage, $imageUrl . $itemPoll['guest_details']['users_detail_profile_image']['profile_image']);
                    $itemPoll['guest_details']['users_detail_profile_image']['profile_image'] = $imageUrl . $itemPoll['guest_details']['users_detail_profile_image']['profile_image'];
                }
                unset($itemPoll);
            }
            $item['vipVoted'] = $VipVoted;
            $item['totalVip'] = $totalVip;
            $item['totalGuest'] = $totalGuest;
            $item['userVoted'] = $userVoted;
            $item['userImage'] = $userImage;
            $item['totalVoted'] = count($item['trip_city_polls']);
            unset($item);
        }
        // Sort by age in ascending order
        array_multisort(array_column($data, 'id'), SORT_DESC, $data);
        return $data;
    }
}
