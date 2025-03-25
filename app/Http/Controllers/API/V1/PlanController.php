<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Mail\OtpEmail;
use App\Models\GuestListModel;
use App\Models\Plan;
use App\Models\PlanPurchaseHistoryModel;
use App\Models\TripDetails;
use App\Models\User;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Helpers\Helpers;

class PlanController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/v1/get-plans",
     *     summary="Get plans",
     *     description="Get plans",
     *     tags={"Plan"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             required={"type"},
     *             @OA\Property(property="type", type="string", example="normal", enum={"normal", "single"})
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
    public function getPlans(Request $request)
    {
        $authToken = $request->header('auth');
        $json = json_decode(trim($request->getContent()), true);
        $schema = [
            'type' => 'required|in:normal,single',
        ];
        $errorMessages = [
            'type.required' => 'Type id filed is required',
        ];
        $validator = Validator::make($request->all(), $schema, $errorMessages);

        if ($validator->fails()) {
            return Helpers::validatorFail($validator->errors()->first());
        }
        $getPlans = Plan::where('type', $request->type)->where('is_active', 1)->orderBy('duration')->get();

        $imageUrl  = config('global.local_image_url'); // Replace with your image URL

        // Use the map function to modify the collection
        $plansWithImageUrl = $getPlans->map(function ($plan) use ($imageUrl) {
            // Add the image URL to the plan object

            $plan->image_url = $imageUrl . $plan->image;

            return $plan;
        });

        if ($getPlans) {
            return Helpers::success('Plans listed successfully', $getPlans, $authToken);
        } else {
            return Helpers::error('No plans found', 200);
        }
    }


    /**
     * @OA\Post(
     *     path="/api/v1/purchase-plan",
     *     summary="Purchase plan",
     *     description="Purchase plan",
     *     tags={"Plan"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             required={"plan_type", "plan_id", "price", "duration", "trip_id", "transaction_id", "payment_through"},
     *             @OA\Property(property="plan_type", type="string", example="normal", enum={"normal", "single"}),
     *             @OA\Property(property="plan_id", type="integer", example=1),
     *             @OA\Property(property="price", type="number", example=100),
     *             @OA\Property(property="duration", type="integer", example=1),
     *             @OA\Property(property="trip_id", type="integer", example=1),
     *             @OA\Property(property="transaction_id", type="string", example=""),
     *             @OA\Property(property="payment_through", type="string", example="paypal", enum={"paypal", "apple_pay"}),
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
    public function purchasePlan(Request $request)
    {
        $authToken = $request->header('auth');
        $json = json_decode(trim($request->getContent()), true);
        $schema = [
            'plan_type' => 'required|in:normal,single',
            'plan_id' => 'required',
            'price' => 'required',
            'duration' => 'required_if:plan_type,normal',
            'trip_id' => 'required_if:plan_type,single',
            'transaction_id' => 'required',
            'payment_through' => 'required|in:paypal,apple_pay',
        ];
        $errorMessages = [
            'plan_type.required' => 'Plan Type is required',
            'plan_id.required' => 'Plan id is required',
            'price.required' => 'Price is required',
            'duration.required' => 'Diration is required',
            'trip_id.required' => 'Trip id is required',
            'transaction_id.required' => 'Transaction id is required',
            'payment_through.required' => 'Payment through is required',
        ];
        $validator = Validator::make($request->all(), $schema, $errorMessages);

        if ($validator->fails()) {
            return Helpers::validatorFail($validator->errors()->first());
        }

        // Get the user ID from the authentication token using your helper function
        $userId = Helpers::getUserIdFromToken($authToken);
        DB::beginTransaction();
        try {
            //add purchase history
            if ($json['plan_type'] == 'single') {
                PlanPurchaseHistoryModel::create([
                    'user_id' => $userId,
                    'plan_type' => $json['plan_type'],
                    'plan_id' => $json['plan_id'],
                    'price' => $json['price'],
                    'trip_id' => $json['trip_id'],
                    'transaction_id' => $json['transaction_id'],
                    'payment_through' => $json['payment_through'],
                ]);
            } else {
                PlanPurchaseHistoryModel::create([
                    'user_id' => $userId,
                    'plan_type' => $json['plan_type'],
                    'plan_id' => $json['plan_id'],
                    'price' => $json['price'],
                    'duration' => $json['duration'],
                    'transaction_id' => $json['transaction_id'],
                    'payment_through' => $json['payment_through'],
                ]);
            }
            if ($json['plan_type'] == 'single') {
                TripDetails::where('id', $json['trip_id'])->Update([
                    'is_paid' => 1,
                    'paid_by' => $userId,
                    'paid_on' => now(),
                    'paid_plan_type' => $json['plan_type']
                ]);

                $guestDetail = GuestListModel::where('trip_id', $json['trip_id'])->where('u_id', '!=', 0)->get();


                if ($guestDetail) {

                    $GetTripName = TripDetails::select('trip_name')
                        ->where('id', $json['trip_id'])
                        ->first();

                    $message = ' Plan purchased for the ' . $GetTripName['trip_name'] . 'trip.';
                    $reciverId = [];
                    foreach ($guestDetail as $guest) {
                        $reciverId[] = [
                            'userId' => $guest['u_id'],
                        ];
                    }
                    $data = [
                        'type' => 'trip',
                        'senderId' => '',
                        'reciverId' => $reciverId,
                        'title' => 'ItsGoTime',
                        'message' => $message,
                        'payload' => [
                            'tripId' => $json['trip_id'],
                            'type' => 'trip',
                            // Other key-value pairs
                        ],
                    ];
                    $sendNotification = Helpers::sendnotification($data);
                    foreach ($guestDetail as $guest) {
                        $template = 'planPurchased';
                        $subject = "Plan purchased for the " . $GetTripName['trip_name'] . "trip.";
                        $emailData = [
                            'firstName' => $guest['first_name'],
                            'lastName' => $guest->trip_name,
                            'tripName' => $GetTripName['trip_name'],
                        ];
                        $sendOtp =  \Illuminate\Support\Facades\Mail::to($guest['email_id'])->send(new OtpEmail($emailData, $template, $subject));
                    }
                }
            } else {
                User::where('id', $userId)->Update([
                    'plan_id' => $json['plan_id'],
                    'plan_start_date' => now(),
                    'plan_end_date' => now()->addMonths($json['duration'])
                ]);
                TripDetails::Where('created_by', $userId)->where('is_paid', 0)
                    ->where(function ($w) {
                        $w->where('trip_final_end_date', '>', now())->orWhereNull('trip_final_end_date');
                    })->update([
                        'is_paid' => 1,
                        'paid_by' => $userId,
                        'paid_on' => now(),
                        'paid_plan_type' => $json['plan_type']
                    ]);
                try {

                    $userData = User::select('first_name', 'last_name', 'email')->where('id', $userId)->first();
                    $planDetail = Plan::select('name')->where('id', $json['plan_id'])->first();
                    $template = 'planPurchasedForAdmin';
                    $subject = "Plan purchased.";
                    $emailData = [
                        'firstName' => $userData['first_name'],
                        'lastName' => $userData['last_name'],
                        'planName' => $planDetail['name'],
                        'planExpiryDate' => now()->addMonths($json['duration']),
                    ];
                    $sendOtp =  \Illuminate\Support\Facades\Mail::to($userData['email'])->send(new OtpEmail($emailData, $template, $subject));
                } catch (\Exception $e) {
                }
            }

            DB::commit();
            $userData = Helpers::getUserDataFromUserId($userId);
            return Helpers::success('Purchase plan successfully', $userData, $authToken);
        } catch (\Exception $e) {
            DB::rollBack();
            return Helpers::error('Something went wrong, please try again', 200);
        }
    }
}
