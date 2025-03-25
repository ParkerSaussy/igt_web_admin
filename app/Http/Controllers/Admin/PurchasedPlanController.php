<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PlanPurchaseHistoryModel;
use Illuminate\Http\Request;

class PurchasedPlanController extends Controller
{
    /**
     * Retrieves all purchased plans from the database and renders the purchased plan page.
     * 
     * This function is used to list all the purchased plans. It fetches all the purchased plans
     * from the database by joining the plan purchase history, user, trip details and plan tables.
     * The function then passes the data to the view and renders the purchased plan page.
     * 
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data = PlanPurchaseHistoryModel::leftJoin('tbl_users', 'tbl_plan_purchase_history.user_id', '=', 'tbl_users.id')
            ->leftJoin('trip_details', 'tbl_plan_purchase_history.trip_id', '=', 'trip_details.id')
            ->leftJoin('tbl_plan', 'tbl_plan_purchase_history.plan_id', '=', 'tbl_plan.id')
            ->select('tbl_plan_purchase_history.*', 'tbl_users.first_name', 'tbl_users.last_name', 'trip_details.trip_name', 'tbl_plan.name')
            ->get();

        return view('Admin.plan.purchasedPlan', compact('data'));
    }
}
