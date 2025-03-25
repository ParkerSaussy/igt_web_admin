<?php

namespace App\Http\Services\V1\Auth;

use App\Helpers\Helpers;
use App\Http\Helpers\Helper;
use App\Models\GuestListModel;
use App\Models\TripActivity;
use App\Models\Like;
use App\Models\TripDocument;
use Illuminate\Support\Str;
use DB;
use Carbon\Carbon;

class TripActivityServices
{
    public function addEditActivity($authToken, $json)
    {
        $userId = Helpers::getUserIdFromToken($authToken);
        $array = [
            'user_id' => $userId,
            'trip_id' => $json['tripId'],
            'activity_type' => $json['activityType'],
            'name' => $json['name'],
            'event_date' => $json['date'],
            'event_time' => Carbon::parse($json['time'])->format('H:i:s'),
'utc_time' => $json['utcTime'],
            'checkout_time' => $json['checkoutTime'],
            'url' => $json['url'],
            'cost' => $json['cost'],
            'address' => $json['address'],
            'discription' => $json['description'],
            'spent_hours' => $json['spentHours'],
            'capacity_per_room' => $json['capacityPerRoom'],
            'number_of_nights' => $json['numberOfNights'],
            'average_nightly_cost' => $json['averageNightlyCost'],
            'room_number' => $json['roomNumber'],
            'departure_date' => $json['departureFlightDate'],
            'arrival_flight_number' => $json['arrivalFlightNumber'],
            'departure_flight_number' => $json['departureFlightNumber'],
        ];

        if ($json['activityId'] == '') {
            $data = TripActivity::create($array);
        } else {
            $data = TripActivity::where('id', $json['activityId'])->update($array);
        }

        if ($data) {
            $responseData = [];
            if ($json['activityId'] == '') {
                return Helpers::success('Data inserted successfully', $responseData, $authToken);
            } else {
                return Helpers::success('Data updated successfully', $responseData, $authToken);
            }
        } else {
            return Helpers::error('Failed to update data', 200);
        }
    }

    public function likeDislikeIdeas($authToken, $json)
    {
        $userId = Helpers::getUserIdFromToken($authToken);
        $likeOrDisLikeData = Like::where('activity_id', $json['activityId'])
            ->where('user_id', $userId)
            ->first();

        $liketArray = [
            'activity_id' => $json['activityId'],
            'user_id' => $userId,
            'like_or_dislike' => $json['likeOrDislike'],
        ];
        if ($likeOrDisLikeData) {
            $delete = Like::where('activity_id', $json['activityId'])
                ->where('user_id', $userId)
                ->delete();
        }

        $insertData = Like::create($liketArray);

        $likeCount = Like::select('like_or_dislike')
            ->where('activity_id', $json['activityId'])
            ->where('like_or_dislike', 1)
            ->count();
        $disLikeCount = Like::select('like_or_dislike')
            ->where('activity_id', $json['activityId'])
            ->where('like_or_dislike', 0)
            ->count();
        $count = ['like' => $likeCount, 'disLike' => $disLikeCount];
        if ($insertData) {
            $responseData = [];
            return Helpers::success('Data updated successfully', $count, $authToken);
        } else {
            return Helpers::error('Failed to update data', 200);
        }
    }

    public function getActivityDetail($authToken, $json)
    {
        $currentDateTime = Carbon::now();

        $query = TripActivity::withCount([
            'likes as like_count' => function ($query) {
                $query->where('like_or_dislike', 1); // 1 represents likes
            },
            'likes as dislike_count' => function ($query) {
                $query->where('like_or_dislike', 0); // 0 represents dislikes
            },
        ]);
        $query->join('tbl_users', 'tbl_users.id', 'tbl_trip_activities.user_id');
        $query->addSelect(DB::Raw('Concat(tbl_users.first_name," ",tbl_users.last_name) as createdBy'));
        if ($json['type'] == 'itineary') {
            $query = $query->where('is_itineary', 1);
            $type = [];
            if ($json['filterEventType']) {
                if (in_array('hotel', $json['filterEventType'])) {
                    array_push($type, 'hotel');
                }
                if (in_array('flight', $json['filterEventType'])) {
                    array_push($type, 'flight');
                }
                if (in_array('event', $json['filterEventType'])) {
                    array_push($type, 'event');
                }
                if (in_array('dining', $json['filterEventType'])) {
                    array_push($type, 'dining');
                }
            } else {
                $type = ['hotel', 'flight', 'event', 'dining'];
            }
            $query->whereIn('activity_type', $type);
        } else {
            $query = $query->where('is_itineary', 0);
        }
        if ($json['type'] == 'others') {
            $type = [];
            if ($json['filterEventType']) {
                if (in_array('hotel', $json['filterEventType'])) {
                    array_push($type, 'hotel');
                }
                if (in_array('flight', $json['filterEventType'])) {
                    array_push($type, 'flight');
                }
            } else {
                $type = ['hotel', 'flight'];
            }
            $query->whereIn('activity_type', $type);
            // $query->where(function ($w) {
            //     $w->where('activity_type', 'hotel')->orWhere('activity_type', 'flight');
            // });
        }
        if ($json['type'] == 'ideas') {
            $type = [];
            if ($json['filterEventType']) {
                if (in_array('event', $json['filterEventType'])) {
                    array_push($type, 'event');
                }
                if (in_array('dining', $json['filterEventType'])) {
                    array_push($type, 'dining');
                }
            } else {
                $type = ['event', 'dining'];
            }
            $query->whereIn('activity_type', $type);
            // $query->where(function ($w) {
            //     $w->where('activity_type', 'event')->orWhere('activity_type', 'dining');
            // });
            //$query->orWhere('activity_type', 'dining');
        }

        if ($json['searchText'] != '') {
            $query->where(function ($y) use ($json) {
                $y->where('name', 'LIKE', '%' . $json['searchText'] . '%');
                $y->orWhere('discription', 'LIKE', '%' . $json['searchText'] . '%');
            });
        }

        if ($json['sortBy'] != '') {
            if ($json['sortBy'] == 'hidePast') {
                $query->where('event_date', '>=', $currentDateTime->format('Y-m-d'));
                // $query->orderBy('event_date', 'asc');
                //$query->orderBy('event_time', 'asc');
            }

            if ($json['sortBy'] == 'upcoming') {
                //$query->where('event_date', '>=', $currentDateTime->format('Y-m-d'));
                $query->orderBy('created_at', 'asc');
                $query->orderBy('event_time', 'asc');
                //print($query->toSql()); exit;
            }
        } else {
            $query->orderBy('created_at', 'desc');
        }

        $query->orderBy('id', 'asc');
        $query->where('trip_id', $json['tripId']);
        $data = $query->get();

        if ($data) {
            $responseData = [
                'activityData' => $data,
            ];
            return Helpers::success('Data listed successfully', $responseData, $authToken);
        } else {
            return Helpers::error('No data found', 200);
        }
    }

    public function makeItineary($authToken, $json)
    {
        $userId = Helpers::getUserIdFromToken($authToken);
        $tripId = $json['tripId'];
        $type = 'move_to_itineray';

        if (Helpers::isAccessible($userId, $tripId, $type)) {
            $updateIsIternary = TripActivity::where('id', $json['activityId'])->update(['is_itineary' => $json['isItineary']]);
            if ($updateIsIternary) {
                try {
                    $hostRecords = GuestListModel::select('u_id')
                        ->where(function ($query) use ($userId) {
                            $query->where('is_co_host', 1);
                            $query->orWhere('u_id', '!=', $userId); // Match the requested user's ID
                        })
                        ->where(function ($query) {
                            $query->where('is_co_host', '1')->orWhere('role', 'Host');
                        })
                        ->where('trip_id', $tripId)
                        ->where('is_deleted', 0)
                        ->get();

                    $ownerId = TripActivity::select('user_id')
                        ->where('id', $json['activityId'])
                        ->first();

                    $mergedUserIds = $hostRecords
                        ->pluck('u_id')
                        ->merge([$ownerId->user_id])
                        ->unique()
                        ->values()
                        ->all();

                    $reciverId = array_map(function ($mergedUserIds) {
                        return ['userId' => $mergedUserIds];
                    }, $mergedUserIds);

                    if ($hostRecords) {
                        $activityId = $json['activityId'];
                        $getActivityName = TripActivity::select('name')
                            ->where('id', $activityId)
                            ->first();
                        $title = 'ItsGoTime';
                        $type = 'activity';

                        $message = 'Congrats! Activity ' . $getActivityName->name . ' has moved to the trip itinerary';

                        $data = [
                            'type' => $type,
                            'senderId' => '',
                            'reciverId' => $reciverId,
                            'title' => $title,
                            'message' => $message,
                            'payload' => [
                                'tripId' => $json['tripId'],
                                'activityId' => $activityId,
                                'type' => 'activity',
                                // Other key-value pairs
                            ],
                        ];
                        $sendNotification = Helpers::sendnotification($data);
                    }
                } catch (\Exception $e) {
                }
                $responseData = [];
                return Helpers::success('Data updated successfully', $responseData, $authToken);
            } else {
                return Helpers::error('Failed to update data', 200);
            }
        } else {
            return Helpers::error('Failed to update data. No access', 200);
        }
    }

    public function deleteActivity($authToken, $json)
    {
        $userId = Helpers::getUserIdFromToken($authToken);
        $deleteRow = TripActivity::where('id', $json['activityId'])
            ->where('user_id', $userId)
            ->delete(['id' => $json['activityId']]);

        if ($deleteRow) {
            $responseData = [];
            return Helpers::success('Activity deleted successfully', $responseData, $authToken);
        } else {
            return Helpers::error('Failed to delete activity', 200);
        }
    }
}
