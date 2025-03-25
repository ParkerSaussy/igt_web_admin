<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\EditPageRequest;
use DB;
use Illuminate\Support\Facades\Validator;
use App\Models\Cms;

class CmsController extends Controller
{
    /**
     * Show the list of CMS pages.
     *
     * This function is used to show the list of CMS pages.
     * It fetches all the CMS pages from the database and
     * passes them to the view.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data = Cms::orderBy('Id', 'Desc')->get();
        return view('Admin.cms.cmslist', compact('data'));
    }

    /**
     * Edits a CMS page.
     *
     * This function is used to edit an existing CMS page.
     * It takes an id as an input parameter and retrieves the
     * corresponding CMS page record from the database. It then
     * renders the edit CMS page with the CMS page data.
     *
     * @param int $id The id of the CMS page to be edited.
     *
     * @return \Illuminate\Http\Response
     */
    public function editPage($id)
    {
        $data = Cms::find($id);
        return view('Admin.cms.editPage', compact('data'));
    }

    /**
     * Updates a CMS page record in the database.
     *
     * This function handles the request to edit an existing CMS page. It takes the
     * request data as an input parameter and validates it. If the validation fails,
     * it redirects back with the validation errors. Otherwise, it updates the CMS
     * page record in the database and redirects to the CMS page list page with a
     * success message.
     *
     * @param EditPageRequest $request The request object containing CMS page data.
     * @param Cms $Cms The CMS page model object.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updatePage(EditPageRequest $request, Cms $Cms)
    {
        //$validator = $request->all();
        $validator = $request->all();
        $data = array(

            'description' => html_entity_decode($request->description)
        );

        if (Cms::where('id', '=', $request->id)->update($data)) {
            return redirect()->route('cmspages')
                ->with('success', 'Cms page has been updated successfully.');
        } else {
            return redirect()->route('cmspages')
                ->with('fail', 'Cms page has been failed to update data.');
        }
    }
}
