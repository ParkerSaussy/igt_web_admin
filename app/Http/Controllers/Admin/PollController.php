<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DatesListModel;
use App\Models\GuestListModel;
use App\Models\TripDetails;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;

class PollController extends Controller
{

    /**
     * Retrieves trip details, date poll details and city poll details from the API endpoint and
     * renders the poll page with the data.
     *
     * @param string $tripid The encrypted trip ID.
     * @param string $guestid The encrypted guest ID.
     *
     * @return \Illuminate\Http\Response
     */
    public function pollWeb($tripid, $guestid)
    {
        $tripId =  Crypt::decryptString($tripid);
        $guestId =  Crypt::decryptString($guestid);
        $totalGuest = GuestListModel::where('trip_id', $tripId)
            ->whereNot('role', 'Host')
            ->where('is_deleted', 0)
            ->get(); //without host

        $url = 'https://lesgo.dashtechinc.com/api/v1/web/getTripDetailsWeb';
        $dateListUrl = 'https://lesgo.dashtechinc.com/api/v1/web/getDatesPollDetailsWeb';
        $cityListUrl = 'https://lesgo.dashtechinc.com/api/v1/web/getCityPollDetailsWeb'; // Replace with your API endpoint URL
        $postData = [
            'tripId' =>  $tripId,
            'guestId' =>  $guestId,
            // Add more key-value pairs as needed
        ];


        $jsonData = json_encode($postData);
        // Initialize a cURL session
        $ch = curl_init($url);

        // Set cURL options for the POST request
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // Return the response as a string
        curl_setopt($ch, CURLOPT_POST, true); // Set the request method to POST
        curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData); // Set the POST data
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json', // Set the content type to JSON
        ]);

        // Execute the cURL request
        $tripData = curl_exec($ch);


        if ($tripData === false) {
            echo 'cURL Error: ' . curl_error($ch);
        } else {
            $tripData = json_decode($tripData, true);
            $ch1 = curl_init($dateListUrl);
            curl_setopt($ch1, CURLOPT_RETURNTRANSFER, true); // Return the response as a string
            curl_setopt($ch1, CURLOPT_POST, true); // Set the request method to POST
            curl_setopt($ch1, CURLOPT_POSTFIELDS, $jsonData); // Set the POST data
            curl_setopt($ch1, CURLOPT_HTTPHEADER, [
                'Content-Type: application/json', // Set the content type to JSON
            ]);
            $dateList = curl_exec($ch1);
            $dateList = json_decode($dateList, true);
            // echo "<pre>";
            // print_r($dateList);
            // exit;

            $ch2 = curl_init($cityListUrl);
            curl_setopt($ch2, CURLOPT_RETURNTRANSFER, true); // Return the response as a string
            curl_setopt($ch2, CURLOPT_POST, true); // Set the request method to POST
            curl_setopt($ch2, CURLOPT_POSTFIELDS, $jsonData); // Set the POST data
            curl_setopt($ch2, CURLOPT_HTTPHEADER, [
                'Content-Type: application/json', // Set the content type to JSON
            ]);
            $cityList = curl_exec($ch2);
            $cityList = json_decode($cityList, true);
            //   echo "<pre>";
            //     print_r($cityList);
            //     exit;


            return view('Admin.poll', compact('tripData', 'dateList', 'totalGuest', 'cityList'));
        }

        // Close the cURL session
    }
    /**
     * Inserts guest poll details into the database.
     *
     * This function handles the request to insert guest poll details into the
     * database. It takes the trip ID, guest ID, selected city IDs and selected
     * date IDs as inputs and sends a POST request to the API endpoint with the
     * data. The function then returns a JSON response indicating whether the
     * operation was successful or not.
     *
     * @param Request $request The request object containing the trip ID, guest ID,
     *                         selected city IDs and selected date IDs.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function insertWebPoll(Request  $request)
    {
        $guestId = Crypt::decryptString($request->input('guestId'));
        $statusChangeUrl = 'https://lesgo.dashtechinc.com/api/v1/web/actionOnInvitationWeb';
        $cityPostUrl = 'https://lesgo.dashtechinc.com/api/v1/web/addCityPollWeb'; // Replace with your API endpoint URL
        $datePostUrl = 'https://lesgo.dashtechinc.com/api/v1/web/addDatePollWeb'; // Replace with your API endpoint URL

        $selectedCityIds = $request->input('city_ids');
        $selectedDateIds = $request->input('date_ids');

        $statusChangeData = [
            'tripId' =>  Crypt::decryptString($request->input('tripId')),
            'guestId' => $guestId,
            'status' => "Approved"
            // Add more key-value pairs as needed
        ];

        $postData = [
            'tripId' =>  Crypt::decryptString($request->input('tripId')),
            'guestId' => $guestId,
            'tripCityListId' => $selectedCityIds
            // Add more key-value pairs as needed
        ];
        $datesData = [
            'tripId' =>  Crypt::decryptString($request->input('tripId')),
            'guestId' => $guestId,
            'tripDatesListId' => $selectedDateIds

        ];

        //city Start

        $status = curl_init($statusChangeUrl);
        $statusChangedJson = json_encode($statusChangeData);
        curl_setopt($status, CURLOPT_RETURNTRANSFER, true); // Return the response as a string
        curl_setopt($status, CURLOPT_POST, true); // Set the request method to POST
        curl_setopt($status, CURLOPT_POSTFIELDS, $statusChangedJson); // Set the POST data
        curl_setopt($status, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json', // Set the content type to JSON
        ]);
        $statusReturn = curl_exec($status);
        $returnResponse = json_decode($statusReturn);


        //city End
        // Assuming $statusReturn contains the JSON response
        $response = json_decode($statusReturn);

        if ($response && isset($response->meta) && isset($response->meta->success)) {
            $success = $response->meta->success;


            if ($success == true) {
                //city Start
                $ch = curl_init($cityPostUrl);
                $jsonData = json_encode($postData);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // Return the response as a string
                curl_setopt($ch, CURLOPT_POST, true); // Set the request method to POST
                curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData); // Set the POST data
                curl_setopt($ch, CURLOPT_HTTPHEADER, [
                    'Content-Type: application/json', // Set the content type to JSON
                ]);
                $city = curl_exec($ch);

                $ch2 = curl_init($datePostUrl);
                $dateData = json_encode($datesData);

                curl_setopt($ch2, CURLOPT_RETURNTRANSFER, true); // Return the response as a string
                curl_setopt($ch2, CURLOPT_POST, true); // Set the request method to POST
                curl_setopt($ch2, CURLOPT_POSTFIELDS, $dateData); // Set the POST data
                curl_setopt($ch2, CURLOPT_HTTPHEADER, [
                    'Content-Type: application/json', // Set the content type to JSON
                ]);

                $dates = curl_exec($ch2);
                //end date
                return response()->json(['success' => $response]);
            } else {
            }
        } else {
            return response()->json(['success' => $response]);
        }

        //date start




    }

    /**
     * Handles the request to decline an invitation.
     *
     * This function takes a request object as an input parameter and
     * decrypts the guest ID and trip ID from the request. It then sends
     * a POST request to the API endpoint to update the invitation status
     * to 'Declined'. The function returns a JSON response containing the
     * API response.
     *
     * @param \Illuminate\Http\Request $request The request object containing the guest ID and trip ID.
     * @return \Illuminate\Http\JsonResponse A JSON response containing the API response.
     */
    public function invitationDeclined(Request  $request)
    {

        $guestId = Crypt::decryptString($request->input('guestId'));
        $statusChangeUrl = 'https://lesgo.dashtechinc.com/api/v1/web/actionOnInvitationWeb';


        $statusChangeData = [
            'tripId' =>  Crypt::decryptString($request->input('tripId')),
            'guestId' => $guestId,
            'status' => 'Declined'
            // Add more key-value pairs as needed
        ];



        //city Start

        $status = curl_init($statusChangeUrl);
        $statusChangedJson = json_encode($statusChangeData);
        curl_setopt($status, CURLOPT_RETURNTRANSFER, true); // Return the response as a string
        curl_setopt($status, CURLOPT_POST, true); // Set the request method to POST
        curl_setopt($status, CURLOPT_POSTFIELDS, $statusChangedJson); // Set the POST data
        curl_setopt($status, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json', // Set the content type to JSON
        ]);
        $statusReturn = curl_exec($status);
        //$returnResponse = json_decode($statusReturn);


        //city End
        // Assuming $statusReturn contains the JSON response
        $response = json_decode($statusReturn);

        return response()->json(['success' => $response]);
    }
}
