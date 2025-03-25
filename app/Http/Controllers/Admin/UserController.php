<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TripDetails;
use Illuminate\Http\Request;
use App\Models\User;

class UserController extends Controller
{
    /**
     * Displays a list of users with their trip count.
     *
     * This function retrieves all users from the database and
     * includes the count of trips associated with each user.
     * The function then renders the user listing page with the
     * retrieved data.
     *
     * @return \Illuminate\View\View
     */

    public function index()
    {
        $users = User::withCount('trips')->get();
        return view('Admin.users.userListing', compact('users'));
    }

    /**
     * Updates the status of a user in the database.
     *
     * This function takes the user ID and its new status as input parameters and
     * updates the corresponding user record in the database. It returns a JSON
     * response indicating whether the operation was successful or not.
     *
     * @param \Illuminate\Http\Request $request The request object containing the user ID and its new status.
     * @return \Illuminate\Http\JsonResponse A JSON response indicating the success of the update operation.
     */
    public function changeUserStatus(Request $request)
    {

        $id = $request->Id;
        $isActive = $request->IsActive;


        $updateStatus = User::where('id', $id)->update(['is_active' => $isActive]);
        if ($updateStatus) {
            $success = 'success';
        } else {
            $success = 'fail';
        }

        return response()->json($success);
    }

    /**
     * Displays a list of trips for a specific user.
     * 
     * This function takes the user ID as an input parameter and retrieves all trips
     * associated with the user from the database. The function also includes the count
     * of guests associated with each trip. The function then renders the user trips
     * page with the retrieved data.
     * 
     * @param int $id The ID of the user to retrieve trips for.
     * 
     * @return \Illuminate\View\View
     */
    public function userTrips($id)
    {
        $data = TripDetails::with('user:id,first_name,last_name')->withCount('guests')->where('created_by', $id)->get();
        //$data = User::with('trips')->where('id',$id)->get();
        // echo "<pre>";
        // print_r($data);
        // exit;

        return view('Admin.users.userTrips', compact('data'));
    }
    /**
     * Displays trip details for a specific user trip.
     * 
     * This function takes the user ID and trip ID as input parameters and retrieves the
     * trip record from the database. The function also eager loads the city associated
     * with the trip, the user who created the trip and all the guests invited to the trip.
     * The function then renders the trip details page with the trip data.
     * 
     * @param int $id The ID of the user to retrieve trip details for.
     * @param int $tripid The ID of the trip to retrieve.
     * 
     * @return \Illuminate\View\View
     */
    public function userTripDetail($id, $tripid)
    {

        $data = TripDetails::with('city:city_id,trip_id,city_name')
            ->with('user:id,first_name,last_name,email,country_code,mobile_number')
            ->with('guests:trip_id,first_name,last_name,email_id,phone_number,invite_status')
            ->where('trip_details.id', $tripid)->get();
        //$data = User::with('trips')->where('id',$id)->get();
        // echo "<pre>";
        // print_r($data);
        // exit;

        return view('Admin.users.userTripDetail', compact('data', 'id'));
    }
}
