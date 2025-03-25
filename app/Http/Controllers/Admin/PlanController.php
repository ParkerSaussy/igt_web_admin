<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PlanPurchaseHistoryModel;
use App\Rules\DiscountedPriceLessThanOriginal;
use App\Rules\UniqueTypeForSinglePlan;
use Illuminate\Http\Request;
use App\Models\Plan;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use App\Providers\AppServiceProvider;


class PlanController extends Controller
{
    /**
     * Show the list of plans.
     *
     * This function is used to list all the plans. It fetches all the plans from the database
     * and passes them to the view.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data = Plan::where('is_delete', 0)->orderBy('Id', 'Desc')->get();
        return view('Admin.plan.allPlans', compact('data'));
    }

    /**
     * Displays the add plan page.
     *
     * This function is used to display the add plan page. It simply renders the
     * add plan view.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {

        return view('Admin.plan.addPlan');
    }

    /**
     * Stores a new plan in the database.
     *
     * This function handles the request to create a new plan. It validates the
     * request data, ensuring all required fields are present and meet specific
     * criteria. If the validation fails, it redirects back with the validation
     * errors. If the validation succeeds, it inserts the new plan into the
     * database and redirects to the plan list page with a success message.
     *
     * @param Request $request The request object containing plan data.
     *
     * @return \Illuminate\Http\RedirectResponse
     */

    public function storeplan(Request $request)
    {

        $data = $request->all();

        $validator = Validator::make($data, [
            'type' => [
                'required',
                Rule::unique('tbl_plan', 'type')->where(function ($query) {
                    // Check the value of the "type" field
                    return $query->where('type', 'single');
                }),
            ],
            'name' => 'required|unique:tbl_plan,name',
            //'description' => 'required',
            'price' => 'required',
            'discounted_price' => [
                'required',
                'numeric',
                function ($attribute, $value, $fail) use ($request) {
                    $originalPrice = $request->input('price');

                    if ($value >= $originalPrice) {
                        $fail('The discounted price must be less than the actual price.');
                    }
                },
            ],
            //'discounted_price' => ['required', 'numeric', new DiscountedPriceLessThanOriginal],
            'duration' => 'required_if:type,normal|gt:0',
            'status' => 'required',
            //'image' => 'image|mimes:jpeg,png,jpg|max:2048',
            'apple_pay_key' => 'required',


            // Add more validation rules as needed
        ], [
            'type.required' => 'Type field is required',
            'type.unique' => 'The single trip plan already created.',
            'name.required' => 'Name field is required',
            //'description.required' => 'Description field is required',
            'price.required' => 'Price field is required',
            'discounted_price.required' => 'Discounted price field is required',
            'duration.required' => 'Duration field is required',
            'duration.gt' => 'The duration value should be greater than 0.',
            'status.required' => 'Status field is required',
            'apple_pay_key.required' => 'Apple pay key field is required',
            //'image.max'=> 'Max file allow size is 2048',

            // Add more custom error messages as needed
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $validator = $request->all();


        $data = array(
            'type' => $request->type,
            'name' => $request->name,
            'description' => html_entity_decode($request->description),
            'price' => $request->price,
            'discounted_price' => $request->discounted_price,
            'duration' => $request->duration,
            'image' => $request->image,
            'is_active' => $request->status,
            'apple_pay_key' => $request->apple_pay_key
        );

        $insertData = Plan::create($data);

        if ($insertData) {
            return redirect()->route('allplans')
                ->with('success', 'Data inserted successfully');
        } else {
            return redirect()->route('allplans')
                ->with('fail', 'Failed to insert data.');
        }

        //return view('Admin.plan.addPlan');
    }

    /**
     * Edits a plan record in the database.
     *
     * This function handles the request to edit an existing plan. It takes an
     * id as an input parameter and retrieves the corresponding plan record
     * from the database. It then renders the edit plan page with the plan data.
     *
     * @param Request $request The request object containing id of the plan to be edited.
     *
     * @return \Illuminate\Http\Response
     */
    public function editplan(Request $request)
    {
        $id = $request->id;
        $data = Plan::where('id', $id)->first();
        // print_r($data);
        // exit;
        return view('Admin.plan.editPlan', compact('data'));
    }

    /**
     * Updates a plan record in the database.
     *
     * This function handles the request to edit an existing plan. It takes an
     * id as an input parameter and retrieves the corresponding plan record
     * from the database. It then validates the request data, ensuring all
     * required fields are present and meet specific criteria. If the validation
     * fails, it redirects back with the validation errors. If the validation
     * succeeds, it updates the plan record in the database and redirects to the
     * plan list page with a success message.
     *
     * @param Request $request The request object containing the plan data to be updated.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateplan(Request $request)
    {
        $id = $request->id;

        $data = $request->all();

        $validator = Validator::make($data, [
            'type' => 'required',
            'name' => 'required|unique:tbl_plan,name,' . $id,
            'description' => 'required',
            'price' => 'required',
            'discounted_price' => [
                'required',
                'numeric',
                function ($attribute, $value, $fail) use ($request) {
                    $originalPrice = $request->input('price');

                    if ($value >= $originalPrice) {
                        $fail('The discounted price must be less than the actual price.');
                    }
                },
            ],
            'duration' => 'required_if:type,normal|gt:0',
            'status' => 'required',
            //'image' => 'image|mimes:jpeg,png,jpg|max:2048',
            'apple_pay_key' => 'required',

            // Add more validation rules as needed
        ], [
            'type.required' => 'Type field is required',
            'name.required' => 'Name field is required',
            'description.required' => 'Description field is required',
            'price.required' => 'Price field is required',
            'discounted_price.required' => 'Discounted price field is required',
            'duration.required' => 'Duration field is required',
            'duration.gt' => 'The duration value should be greater than 0.',
            'status.required' => 'Status field is required',
            'apple_pay_key.required' => 'Apple pay key field is required',

            // Add more custom error messages as needed
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $validator = $request->all();


        // if ($request->hasFile('image')) {

        //     $image = $request->image;
        //     $getImage = $request->file('image');
        //     $imageName = time() . '.' . $getImage->extension();
        //     $image->move(public_path('uploads/images'), $imageName);
        // } elseif ($request->has('old_image')) {
        //     $imageName = $request->old_image; // Use the old image name
        // } else {
        //     $imageName = "default_plan.png";
        // } 


        $data = array(
            'type' => $request->type,
            'name' => $request->name,
            'description' => html_entity_decode($request->description),
            'price' => $request->price,
            'discounted_price' => $request->discounted_price,
            'duration' => $request->duration,
            'image' =>  $request->image,
            'is_active' => $request->status,
            'apple_pay_key' => $request->apple_pay_key
        );
        $updateData = Plan::where('id', $id)->update($data);

        if ($updateData) {
            return redirect()->route('allplans')
                ->with('success', 'Data updated successfully');
        } else {
            return redirect()->route('allplans')
                ->with('fail', 'Failed to update data.');
        }
    }

    /**
     * Deletes a plan record from the database.
     *
     * This function handles the request to delete a plan. It takes an id as an
     * input parameter and updates the corresponding plan record in the
     * database by setting its 'is_delete' and 'is_active' status to 1 and 0 respectively.
     * The function returns a JSON response indicating whether the operation was
     * successful or not.
     *
     * @param \Illuminate\Http\Request $request The request object containing the id of the plan to be deleted.
     * @return \Illuminate\Http\JsonResponse A JSON response indicating the success of the delete operation.
     */
    public function deletePlan(Request $request)
    {
        $id = $request->Id;
        //$isActive = $request->IsActive;
        $updateStatus = Plan::where('id', $id)->update(['is_delete' => 1, 'is_active' => 0]);
        if ($updateStatus) {
            $success = 'success';
        } else {
            $success = 'fail';
        }

        return response()->json($success);
    }

    /**
     * Changes the status of a plan record in the database.
     *
     * This function takes the plan ID and its new status as input parameters and
     * updates the corresponding plan record in the database. It returns a JSON
     * response indicating whether the operation was successful or not.
     *
     * @param \Illuminate\Http\Request $request The request object containing the plan ID and its new status.
     * @return \Illuminate\Http\JsonResponse A JSON response indicating the success of the update operation.
     */
    public function changePlanStatus(Request $request)
    {

        $id = $request->Id;
        $isActive = $request->IsActive;
        $updateStatus = Plan::where('id', $id)->update(['is_active' => $isActive]);
        if ($updateStatus) {
            $success = 'success';
        } else {
            $success = 'fail';
        }

        return response()->json($success);
    }
    /**
     * Deletes multiple plan records.
     *
     * This function handles the request to delete multiple plan records
     * by updating their 'is_delete' status. It takes a list of plan IDs
     * from the request and updates the corresponding plan records in the
     * database. The function returns a JSON response indicating whether
     * the operation was successful or not.
     *
     * @param \Illuminate\Http\Request $request The request object containing the list of plan IDs.
     * @return \Illuminate\Http\JsonResponse A JSON response indicating the success of the delete operation.
     */
    public function deleteallplan(Request $request)
    {
        $idsString = $request->input('Id');


        //$isActive = $request->IsActive;
        $updateStatus = Plan::whereIn('id', $idsString)->update(['is_delete' => 1]);
        if ($updateStatus) {
            $success = 'success';
        } else {
            $success = 'fail';
        }

        return response()->json($success);
    }
}
