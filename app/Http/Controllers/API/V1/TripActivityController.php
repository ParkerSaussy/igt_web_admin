<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Carbon\Carbon;
use App\Helpers\Helpers;
use App\Http\Helpers\Helper;
use App\Http\Services\V1\Auth\TripActivityServices;

class TripActivityController extends Controller
{
    /**
     * Add or edit a trip activity.
     *
     * This function validates the incoming request data for adding or editing
     * a trip activity based on the specified activity type, and then delegates
     * the processing to the TripActivityServices.
     *
     * @param Request $request The HTTP request object containing activity data.
     * @param TripActivityServices $tripActivity The service handling the trip activity logic.
     * @return mixed The result of the trip activity operation, or a validation error response.
     */

    public function addEditActivity(Request $request, TripActivityServices $tripActivity)
    {

        $json = json_decode(trim($request->getContent()), true);
        $authToken = $request->header('auth');
        $schema = [
            'activityType' => 'required|in:event,dining,hotel,flight',
            //for hotel
            'tripId' => 'required',
            'name' => 'required',
            'date' => 'required',
            'time' => 'required',
            'utcTime' => 'required',
            'departureFlightDate' => 'required_if:activityType,flight',
            'checkoutTime' => 'required_if:activityType,event|required_if:activityType,hotel|required_if:activityType,fight',
            'description' => 'required',
            'address' => 'required_if:activityType,event|required_if:activityType,hotel|required_if:activityType,dining',
            'cost' => 'required_if:activityType,event|required_if:activityType,hotel|required_if:activityType,dining',
            'spentHours' => 'required_if:activityType,event|required_if:activityType,dining',
            'numberOfNights' => 'required_if:activityType,hotel',
            'averageNightlyCost' => 'required_if:activityType,hotel',
            'capacityPerRoom' => 'required_if:activityType,hotel',
            //'roomNumber' => 'required_if:activityType,hotel', //remove after client feedback
            'departureFlightNumber' => 'required_if:activityType,flight',
            'arrivalFlightNumber' => 'required_if:activityType,flight',
        ];

        $dateMessage = 'date field is required';
        $timeMessage = 'Checkin time field is required';
        $CheckoutTime = 'Checkout time field is required';
        $address = 'Address field is required';
        if ($request['activityType'] == 'flight') {
            $dateMessage = 'Arrival date field is required';
            $timeMessage = 'Arrival time field is required';
            $CheckoutTime = 'Departure time field is required';
        }
        if ($request['activityType'] == 'dining') {
            $timeMessage = 'Time field is required';
            $address = 'Dining location field is required';
        }


        $errorMessages = [
            //for hotel
            'tripId.required' => 'Trip id is required',
            'activityType.required' => 'Activity type field is required.',
            'name.required' => '' . $request['activityType'] . ' name field is required.',
            'date.required' =>  $dateMessage,
            'time.required' => $timeMessage,
            'utcTime.required' => 'Utc time field is required.',
            'departureFlightDate.required_if' => 'Departure flight date field is required.',
            'checkoutTime.required_if' => $CheckoutTime,
            'description.required' => 'Description field is required.',
            'address.required_if' => $address,
            'cost.required_if' => 'Cost field is required.',
            'spentHours.required_if' => 'Spent hours field is required.',
            'numberOfNights.required_if' => 'Number of nights field is required.',
            'averageNightlyCost.required_if' => 'Average nightly cost field is required.',
            'capacityPerRoom.required_if' => 'Capacity per room field is required.',
            //'roomNumber.required_if' => 'Room number field is required.', //due to client feedback
            'departureFlightNumber.required_if' => 'Departure flight number field is required.',
            'arrivalFlightNumber.required_if' => 'Arrival flight number field is required.',

        ];

        $validator = Validator::make($request->all(), $schema, $errorMessages);

        if ($validator->fails()) {
            return Helpers::validatorFail($validator->errors()->first());
        }

        return $data = $tripActivity->addEditActivity($authToken, $json);
    }
    /**
     * Like/Dislike an idea
     *
     * @param Request $request
     * @param TripActivityServices $tripActivity
     * @return mixed
     */
    public function likeDislikeIdeas(Request $request, TripActivityServices $tripActivity)
    {

        $json = json_decode(trim($request->getContent()), true);
        $authToken = $request->header('auth');
        $schema = [
            'activityId' => 'required',
            'likeOrDislike' => 'required',
        ];

        $errorMessages = [
            //for hotel
            'activityId.required' => 'Activity id field is required',
            'likeOrDislike.required' => 'Like/Dislike field is required',

        ];

        $validator = Validator::make($request->all(), $schema, $errorMessages);

        if ($validator->fails()) {
            return Helpers::validatorFail($validator->errors()->first());
        }

        return $tripActivity->likeDislikeIdeas($authToken, $json);
    }

    /**
     * Retrieve the details of a specific trip activity.
     *
     * This function validates the incoming request data for retrieving
     * activity details based on the specified trip ID and type. It then
     * delegates the processing to the TripActivityServices.
     *
     * @param Request $request The HTTP request object containing activity detail data.
     * @param TripActivityServices $tripActivity The service handling the trip activity logic.
     * @return mixed The result of the activity detail retrieval, or a validation error response.
     */

    public function getActivityDetail(Request $request, TripActivityServices $tripActivity)
    {
        $json = json_decode(trim($request->getContent()), true);
        $authToken = $request->header('auth');
        $schema = [
            'tripId' => 'required',
            'type' => 'required|in:others,ideas,itineary',
        ];

        $errorMessages = [
            //for hotel
            'tripId.required' => 'Trip id field is required',
            'type.required' => 'Type field is required',

        ];

        $validator = Validator::make($request->all(), $schema, $errorMessages);

        if ($validator->fails()) {
            return Helpers::validatorFail($validator->errors()->first());
        }
        return $tripActivity->getActivityDetail($authToken, $json);
    }

    /**
     * Set an activity as an itineary or not.
     *
     * This function validates the incoming request data for setting an activity as
     * an itineary or not based on the specified trip ID, activity ID and type. It
     * then delegates the processing to the TripActivityServices.
     *
     * @param Request $request The HTTP request object containing activity detail data.
     * @param TripActivityServices $tripActivity The service handling the trip activity logic.
     * @return mixed The result of setting an activity as an itineary, or a validation error response.
     */
    public function makeItineary(Request $request, TripActivityServices $tripActivity)
    {
        $json = json_decode(trim($request->getContent()), true);
        $authToken = $request->header('auth');
        $schema = [
            'activityId' => 'required',
            'isItineary' => 'required',
            'tripId' => 'required',
        ];

        $errorMessages = [
            //for hotel
            'activityId.required' => 'Activity id field is required',
            'isItineary.required' => 'Is iternary field is required',
            'tripId.required' => 'Trip id field is required',

        ];

        $validator = Validator::make($request->all(), $schema, $errorMessages);

        if ($validator->fails()) {
            return Helpers::validatorFail($validator->errors()->first());
        }
        return $tripActivity->makeItineary($authToken, $json);
    }

    /**
     * Delete a trip activity.
     *
     * This function validates the incoming request data for deleting
     * an activity based on the specified activity ID. It then delegates
     * the processing to the TripActivityServices.
     *
     * @param Request $request The HTTP request object containing activity detail data.
     * @param TripActivityServices $tripActivity The service handling the trip activity logic.
     * @return mixed The result of deleting an activity, or a validation error response.
     */
    public function deleteActivity(Request $request, TripActivityServices $tripActivity)
    {
        $json = json_decode(trim($request->getContent()), true);
        $authToken = $request->header('auth');
        $schema = [
            'activityId' => 'required',
        ];

        $errorMessages = [
            //for hotel
            'activityId.required' => 'Activity id field is required',

        ];

        $validator = Validator::make($request->all(), $schema, $errorMessages);

        if ($validator->fails()) {
            return Helpers::validatorFail($validator->errors()->first());
        }
        return $tripActivity->deleteActivity($authToken, $json);
    }
}
