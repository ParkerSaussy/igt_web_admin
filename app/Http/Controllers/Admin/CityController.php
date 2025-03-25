<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\CityFormRequest;
use DB;
use Illuminate\Support\Facades\Validator;
use App\Models\CityModel;
use App\Models\Timezone;
use Illuminate\Support\Facades\Route;

class CityController extends Controller
{
    /**
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     *
     * This function is used to list cities. It takes a search query as an input and
     * returns a paginated list of cities sorted by id in descending order.
     * If search query is present then it will filter the list by city name, state,
     * country name and id.
     */
    public function index(Request $request)
    {

        $pageSize = 100;
        $search = $request->input('search');
        $query = CityModel::query();

        $query->select('id', 'city_name', 'state', 'country_name', 'is_deleted');
        $query->where('is_deleted', 0);
        $query->where('is_default', 0);
        if ($search) {
            $query->where(function ($q) use ($search) {

                $q->where('city_name', 'like', "%$search%")
                    ->orWhere('country_name', 'like', "%$search%")
                    ->orWhere('id', 'like', "%$search%");
            });
        }
        $orderByColumn = 'id';
        $paginatedCities = $query->orderByDesc($orderByColumn)
            ->paginate($pageSize);
        return view('Admin.city.cityList', ['cities' => $paginatedCities, 'search' => $search]);
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     *
     * This function is used to display the add city page. It fetches all the timezones
     * from the database and passes them to the view.
     */
    public function create()
    {
        $getTimezone = Timezone::orderBy('id', 'Desc')->get();
        return view('Admin.city.addCity', compact('getTimezone'));
    }

    /**
     * Stores a new city in the database.
     *
     * This function handles the request to add a new city. It validates the
     * input data, creates a new city record, and attempts to insert it into
     * the database. If the operation is successful, it redirects to the city
     * list page with a success message. Otherwise, it redirects with a failure
     * message.
     *
     * @param CityFormRequest $request The request object containing city data.
     *
     * @return \Illuminate\Http\RedirectResponse
     */

    public function storeCity(CityFormRequest $request)
    {

        $validator = $request->all();
        $data = array(
            'city_name' => $request->cityName,
            'state' => $request->state,
            'state_abbr' => $request->abbreviation,
            'country_name' => $request->countryName,
            'time_zone' => $request->timeZone,
            'is_deleted' => $request->status,
        );

        $insertData = CityModel::create($data);

        if ($insertData) {
            return redirect()->route('allcity')
                ->with('success', 'Data inserted successfully');
        } else {
            return redirect()->route('allcity')
                ->with('fail', 'Failed to insert data.');
        }
    }

    /**
     * Edits a city record in the database.
     *
     * This function handles the request to edit an existing city. It takes an
     * id as an input parameter and retrieves the corresponding city record
     * from the database. It then renders the edit city page with the city
     * data and all the timezones.
     *
     * @param int $id The id of the city to be edited.
     *
     * @return \Illuminate\Http\Response
     */
    public function editcity($id)
    {
        $data = CityModel::find($id);
        $getTimezone = Timezone::orderBy('id', 'Desc')->get();
        return view('Admin.city.editCity', compact('data', 'getTimezone'));
    }

    /**
     * Updates a city record in the database.
     *
     * This function handles the request to edit an existing city. It takes the
     * request data as an input parameter and validates it. If the validation
     * fails, it redirects back with the validation errors. Otherwise, it
     * updates the city record in the database and redirects to the city list
     * page with a success message.
     *
     * @param Request $request The request object containing city data.
     * @param CityModel $city The city model object.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updatecity(Request $request, CityModel $city)
    {
        //$validator = $request->all();
        $data = $request->all();

        $validator = Validator::make($data, [
            'cityName' => 'required|unique:tbl_city,city_name,' . $request['id'],
            'state' => 'required',
            'abbreviation' => 'required',
            'countryName' => 'required',
            'timeZone' => 'required',
            'status' => 'required',

            // Add more validation rules as needed
        ], [
            'cityName.required' => 'City name field is required',
            'countryName.required' => 'Country name field is required',
            'timeZone.required' => 'Timezone field is required',
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
            'city_name' => $request->cityName,
            'state' => $request->state,
            'state_abbr' => $request->abbreviation,
            'country_name' => $request->countryName,
            'time_zone' => $request->timeZone,
            'is_deleted' => $request->status,
        );

        if (CityModel::where('id', '=', $request->id)->update($data)) {
            return redirect()->route('allcity')
                ->with('success', 'City has been updated successfully.');
        } else {
            return redirect()->route('editcity')
                ->with('fail', 'City has been failed to update data.');
        }
    }

    /**
     * Deletes or deactivates a city record.
     *
     * This function handles the request to delete or deactivate a city record
     * by updating its 'is_deleted' status. It takes the city ID and the new
     * 'is_deleted' status from the request and updates the corresponding city
     * record in the database. The function returns a JSON response indicating
     * whether the operation was successful or not.
     *
     * @param Request $request The request object containing the city ID and the new 'is_deleted' status.
     * @return \Illuminate\Http\JsonResponse
     */

    public function deletecity(Request $request)
    {

        $id = $request->Id;
        $isActive = $request->IsActive;


        $updateStatus = CityModel::where('id', $id)->update(['is_deleted' => $isActive]);
        if ($updateStatus) {
            $success = 'success';
        } else {
            $success = 'fail';
        }

        return response()->json($success);
    }

    /**
     * Deletes multiple city records.
     *
     * This function handles the request to delete multiple city records
     * by updating their 'is_deleted' status. It takes a list of city IDs
     * from the request and updates the corresponding city records in the
     * database. The function returns a JSON response indicating whether
     * the operation was successful or not.
     *
     * @param Request $request The request object containing the list of city IDs.
     * @return \Illuminate\Http\JsonResponse
     */
    public function deleteallcity(Request $request)
    {
        $idsString = $request->input('Id');


        //$isActive = $request->IsActive;
        $updateStatus = CityModel::whereIn('id', $idsString)->update(['is_deleted' => 1]);

        if ($updateStatus) {
            $success = 'success';
        } else {
            $success = 'fail';
        }

        return response()->json($success);
    }
}
