<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Helpers\Helpers;
use App\Http\Services\V1\Auth\TripActivityServices;
use App\Models\TripActivity;
use App\Models\TripMemory;
use Illuminate\Support\Str;


class TripMemoriesController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/v1/get-activity-name",
     *     summary="Get activity names",
     *     description="Get activity names",
     *     tags={"Trip"},
     *     @OA\RequestBody(
     *         description="Get activity names",
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="tripId", type="integer", example=1),
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
    public function getActivityName(Request $request)
    {
        $authToken = $request->header('auth');
        $json = json_decode(trim($request->getContent()), true);
        $schema = [
            'tripId' => 'required',
        ];
        $errorMessages = [
            'tripId.required' => 'Trip id filed is required',
        ];
        $validator = Validator::make($request->all(), $schema, $errorMessages);

        if ($validator->fails()) {
            return Helpers::validatorFail($validator->errors()->first());
        }
        $userId = Helpers::getUserIdFromToken($authToken);
        $staticImagePath  = config('global.trip_document_images');
        $data = TripActivity::select('id', 'name')->where('trip_id', $json['tripId'])->orderBy('created_at', 'Desc')->get();

        if ($data) {
            return Helpers::success('Activity names listed successfully', $data, $authToken);
        } else {
            return Helpers::error('No activity found', 200);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/v1/add-memory",
     *     summary="Add trip memory",
     *     description="Add trip memory",
     *     tags={"Trip"},
     *     @OA\RequestBody(
     *         description="Add trip memory",
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="tripId", type="integer", example=1),
     *             @OA\Property(property="caption", type="string", example="Caption"),
     *             @OA\Property(property="location", type="string", example="Location"),
     *             @OA\Property(property="activityName", type="string", example="Activity name"),
     *             @OA\Property(property="image", type="file", example="image.jpg"),
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
    public function addMemory(Request $request)
    {
        $authToken = $request->header('auth');
        $json = json_decode(trim($request->getContent()), true);
        $schema = [
            'tripId' => 'required',
            //'caption' => 'required',
            //'location' => 'required',
            'activityName' => 'required',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ];
        $errorMessages = [
            'tripId.required' => 'Trip id field is required',
            //'caption.required' => 'Caption field is required',
            //'location.required' => 'Location field is required',
            'activityName.required' => 'Activity name field is required',
            'image.required' => 'Image field is required',
        ];
        $validator = Validator::make($request->all(), $schema, $errorMessages);

        if ($validator->fails()) {
            return Helpers::validatorFail($validator->errors()->first());
        }
        $userData = Helpers::getUserDataFromId($authToken);


        $createdBy = $userData['id'];

        $image = $request->image;
        $getImage = $request->file('image');
        $imageName = Str::random(10) . time() . '.' . $getImage->extension();
        $image->move(public_path('uploads/tripmemory'), $imageName);

        $tripMemoryData = array(
            'trip_id' => $request->tripId,
            'created_by' => $createdBy,
            'activity_name' => $request->activityName,
            'caption' => $request->caption,
            'location' =>  $request->location,
            'image' => $imageName,
        );
        $staticImagePath  = config('global.trip_memory_images');
        $createMemory = TripMemory::create($tripMemoryData);
        $tripMemoryData['image'] = $staticImagePath . $imageName;
        $responceData = [];
        if ($createMemory) {
            return Helpers::success('Trip memory inserted successfully', $tripMemoryData, $authToken);
        } else {
            return Helpers::error('Failed to insert memory', 200);
        }
    }

    /**
     * Lists all the memories for a specific trip.
     *
     * This method validates the incoming request to ensure that the 'tripId'
     * field is present. It fetches all the memories associated with the
     * specified trip along with their activity names, formats the image URL,
     * and returns them in a JSON response.
     *
     * @param Request $request The HTTP request object containing 'tripId'.
     *
     * @return \Illuminate\Http\JsonResponse A JSON response containing the trip memories.
     */

    public function memoryListing(Request $request)
    {
        $authToken = $request->header('auth');
        $json = json_decode(trim($request->getContent()), true);
        $schema = [
            'tripId' => 'required',
        ];
        $errorMessages = [
            'tripId.required' => 'Trip id filed is required',
        ];
        $validator = Validator::make($request->all(), $schema, $errorMessages);

        if ($validator->fails()) {
            return Helpers::validatorFail($validator->errors()->first());
        }
        $memoryList = TripMemory::with('getActivityName:id,name')->where('trip_id', $json['tripId'])->orderBy('created_at', 'DESC')->get();
        $staticImagePath  = config('global.trip_memory_images');
        $data = collect($memoryList)->map(function ($item) use ($staticImagePath) {
            $item['image'] = $staticImagePath . $item['image'];
            return $item;
        })->all();

        if ($data) {
            return Helpers::success('Trip memories listed successfully', $data, $authToken);
        } else {
            return Helpers::error('No memories found', 200);
        }
    }

    /**
     * Delete trip memory.
     *
     * This method validates the incoming request to ensure that the 'memoryId'
     * field is present and is an array of existing memory ids associated with
     * the specified trip. It checks if the currently logged in user is the
     * owner of the memories or if the user has the permission to delete the
     * memories. If the user is authorized, it deletes the memories and returns
     * a success response. If the user is not authorized, it returns an error
     * response.
     *
     * @param Request $request The HTTP request object containing 'memoryId' and 'tripId'.
     *
     * @return \Illuminate\Http\JsonResponse A JSON response containing the result of the deletion.
     */
    public function deleteMemory(Request $request)
    {
        $authToken = $request->header('auth');
        $json = json_decode(trim($request->getContent()), true);
        $schema = [
            'memoryId' => 'required|array',
            'tripId' => 'required',
            'memoryId.*' => 'exists:tbl_trip_memory,id', // Assuming 'records' is your table name
        ];
        $errorMessages = [
            'memoryId.required' => 'Trip id filed is required',
        ];
        $validator = Validator::make($request->all(), $schema, $errorMessages);

        if ($validator->fails()) {
            return Helpers::validatorFail($validator->errors()->first());
        }
        $ids = $request->input('memoryId', []);
        $userData = Helpers::getUserDataFromId($authToken);
        $userId = $userData['id'];
        $type = "";
        $tripId = $request->tripId;

        $isOwnerUser = TripMemory::where('created_by', $userId)->whereIn('id', $ids);

        if ($isOwnerUser) {
            $role = true;
        } else {
            $role = Helpers::isAccessible($userId, $tripId, $type);
        }

        if ($role) {
            $delete = TripMemory::whereIn('id', $ids)->delete();
            if ($delete) {
                $responceData = [];
                return Helpers::success('Trip memory deleted successfully', $responceData, $authToken);
            } else {
                return Helpers::error('Failed to delete memory', 200);
            }
        } else {
            return Helpers::error('You are not authorized person to delete this record.', 200);
        }
    }

    //main end
}
