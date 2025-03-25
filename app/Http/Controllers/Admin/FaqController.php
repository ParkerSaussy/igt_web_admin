<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FaqModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class FaqController extends Controller
{
    /**
     * Displays a list of all FAQs.
     * 
     * This function is used to display the list of all FAQs.
     * It fetches all the FAQs from the database and
     * passes them to the view.
     * 
     * @return \Illuminate\Http\Response
     */

    public function index()
    {
        $data = FaqModel::get();
        return view('admin.faq.allFaq', compact('data'));
    }

    /**
     * Display the add FAQ page.
     * 
     * This function renders the view for adding a new FAQ.
     * 
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */

    public function create()
    {
        return view('admin.faq.addfaq');
    }

    /**
     * Inserts a new FAQ into the database.
     * 
     * This function takes a request object as an input parameter and validates it.
     * If the validation fails, it redirects back with the validation errors.
     * If the validation succeeds, it inserts the FAQ into the database and
     * redirects to the FAQ list page with a success message.
     * 
     * @param Request $request The request object containing FAQ data.
     * 
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $data = $request->all();

        $validator = Validator::make($data, [
            'question' => 'required|unique:tbl_faq,question',
            'answer' => 'required',
            'status' => 'required',

            // Add more validation rules as needed
        ], [
            'question.required' => 'Question field is required',
            'answer.required' => 'Answer field is required',
            'status.required' => 'Status field is required',

            // Add more custom error messages as needed
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $validator = $request->all();
        $data = array(
            'question' => $request->question,
            'answer' => html_entity_decode($request->answer),
            'is_active' => $request->status,

        );

        $insertData = FaqModel::create($data);

        if ($insertData) {
            return redirect()->route('allfaqs')
                ->with('success', 'FAQ inserted successfully');
        } else {
            return redirect()->route('allfaqs')
                ->with('fail', 'Failed to insert data.');
        }
    }

    /**
     * Displays the edit FAQ page.
     *
     * This function handles the request to edit an existing FAQ. It takes
     * an id from the request as an input parameter and retrieves the
     * corresponding FAQ record from the database. It then renders the
     * edit FAQ page with the FAQ data.
     *
     * @param Request $request The request object containing the FAQ id.
     * 
     * @return \Illuminate\Http\Response
     */

    public function editfaq(Request $request)
    {
        $id = $request->id;
        $data = FaqModel::where('id', $id)->first();
        // print_r($data);
        // exit;
        return view('Admin.faq.editfaq', compact('data'));
    }

    /**
     * Updates an existing FAQ record in the database.
     *
     * This function takes a request object as an input parameter and validates
     * the FAQ data. If the validation fails, it redirects back with the validation
     * errors. If the validation succeeds, it updates the FAQ in the database and
     * redirects to the FAQ list page with a success message.
     *
     * @param Request $request The request object containing FAQ data and ID.
     * 
     * @return \Illuminate\Http\RedirectResponse
     */

    public function updatefaq(Request $request)
    {
        $id = $request->id;

        $data = $request->all();

        $validator = Validator::make($data, [

            'question' => 'required|unique:tbl_faq,question,' . $id,
            'answer' => 'required',
            'status' => 'required',

            // Add more validation rules as needed
        ], [
            'question.required' => 'Question field is required',
            'answer.required' => 'Answer field is required',
            'status.required' => 'Status field is required',

            // Add more custom error messages as needed
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $validator = $request->all();
        $data = array(
            'question' => $request->question,
            'answer' => html_entity_decode($request->answer),
            'is_active' => $request->status,

        );

        $insertData = FaqModel::where('id', $id)->update($data);

        if ($insertData) {
            return redirect()->route('allfaqs')
                ->with('success', 'FAQ updated successfully');
        } else {
            return redirect()->route('allfaqs')
                ->with('fail', 'Failed to update data.');
        }
    }

    /**
     * Updates the status of a FAQ record in the database.
     *
     * This function takes the FAQ ID and its new status as input parameters and
     * updates the corresponding FAQ record in the database. It returns a JSON
     * response indicating whether the operation was successful or not.
     *
     * @param Request $request The request object containing the FAQ ID and its new status.
     *
     * @return \Illuminate\Http\JsonResponse A JSON response indicating the success of the update operation.
     */
    public function changeFaqStatus(Request $request)
    {

        $id = $request->Id;
        $isActive = $request->IsActive;
        $updateStatus = FaqModel::where('id', $id)->update(['is_active' => $isActive]);
        if ($updateStatus) {
            $success = 'success';
        } else {
            $success = 'fail';
        }

        return response()->json($success);
    }
    /**
     * Deletes a FAQ record.
     *
     * This function handles the request to delete a FAQ record from the database.
     * It takes the FAQ ID from the request, deletes the corresponding FAQ record,
     * and returns a JSON response indicating whether the operation was successful
     * or not.
     *
     * @param \Illuminate\Http\Request $request The request object containing the ID of the FAQ to be deleted.
     * @return \Illuminate\Http\JsonResponse A JSON response indicating the success of the delete operation.
     */

    public function deletefaq(Request $request)
    {
        $id = $request->Id;
        //$isActive = $request->IsActive;
        $updateStatus = FaqModel::where('id', $id)->delete();
        if ($updateStatus) {
            $success = 'success';
        } else {
            $success = 'fail';
        }

        return response()->json($success);
    }

    /**
     * Deletes multiple FAQ records.
     *
     * This function handles the request to delete multiple FAQ records from the database.
     * It takes a list of FAQ IDs from the request and deletes the corresponding FAQ records.
     * The function returns a JSON response indicating whether the operation was successful or not.
     *
     * @param \Illuminate\Http\Request $request The request object containing the list of FAQ IDs.
     * @return \Illuminate\Http\JsonResponse A JSON response indicating the success of the delete operation.
     */

    public function deleteallfaq(Request $request)
    {
        $idsString = $request->input('Id');


        //$isActive = $request->IsActive;
        $updateStatus = FaqModel::whereIn('id', $idsString)->delete();
        if ($updateStatus) {
            $success = 'success';
        } else {
            $success = 'fail';
        }

        return response()->json($success);
    }
}
