<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Plan;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\TripDetails;

class DashboardController extends Controller
{
    /**
     * Display the dashboard with various statistics.
     *
     * This function retrieves and aggregates statistics including the total number
     * of users, verified users, trips, finalized trips, plans, and active plans.
     * It then passes these statistics to the dashboard view.
     *
     * @return \Illuminate\View\View
     */

    public function index()
    {
        $totalUsercount = User::get()->count();
        $totalVerifiedUser = User::where('is_email_verify', 1)->get()->count();
        $totalTrips = TripDetails::get()->count();
        $finalizedTrips = TripDetails::where('is_trip_finalised', 1)->get()->count();
        $totalPlans = Plan::where('is_delete', 0)->get()->count();
        $activePlans = Plan::where('is_active', 1)->get()->count();
        // echo $usercount; exit;

        $datacount = array(
            'totalusercount' => $totalUsercount,
            'verifieduser' => $totalVerifiedUser,
            'totaltrips' => $totalTrips,
            'finalizedtrips' => $finalizedTrips,
            'totalplans' => $totalPlans,
            'activeplans' => $activePlans,

        );

        return view('Admin.dashboard', compact('datacount'));
    }
}
