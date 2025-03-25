<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TripDetails;
use App\Models\CitiesListModel;

class TripController extends Controller
{
  /**
   * Displays all trips.
   *
   * Retrieves all trip records from the database. Each record is
   * eager loaded with the user and the count of guests associated
   * with the trip. The function then renders the all trips page with
   * the trip data.
   *
   * @return \Illuminate\Http\Response
   */
  public function index()
  {

    $data = TripDetails::with('user:id,first_name,last_name')->withCount('guests')->get();
    // echo "<pre>";
    // print_r($data);
    // exit;

    return view('Admin.trip.allTrips', compact('data'));
  }

  /**
   * Retrieves a trip and its associated data and renders the trip details page.
   * 
   * This function takes a trip ID as an input parameter and retrieves the
   * trip record from the database. The function also eager loads the city
   * associated with the trip, the user who created the trip and all the
   * guests invited to the trip. The function then renders the trip details
   * page with the trip data.
   *
   * @param int $tripId The ID of the trip to be retrieved.
   *
   * @return \Illuminate\Http\Response
   */
  public function tripdetails($tripId)
  {
    $data = TripDetails::with('city:city_id,trip_id,city_name')
      ->with('user:id,first_name,last_name,email,country_code,mobile_number')
      ->with('guests:trip_id,first_name,last_name,email_id,phone_number,invite_status')
      ->where('trip_details.id', $tripId)->get();

    return view('Admin.trip.tripDetails', compact('data'));
  }
}
