<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\CoverImage;
use App\Http\Requests\CoverImageFormRequest;

class CoverImageController extends Controller
{
    /**
     * Shows the list of cover images.
     * 
     * This function is used to display the list of cover images.
     * It fetches all the cover images from the database and
     * passes them to the view.
     * 
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data = CoverImage::where('is_deleted', 0)->orderBy('Id', 'Desc')->get();
        return view('Admin.coverimages.allimages', compact('data'));
    }

    /**
     * Displays the form to add a new cover image.
     *
     * This function is used to render the view for adding a new cover image.
     * It returns the view that contains the form for creating a new cover image.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */

    public function create()
    {
        return view('Admin.coverimages.addimage');
    }

    /**
     * Handles the upload and processing of a cropped image.
     *
     * This function receives an image from the request, decodes it from its
     * base64 string format, and saves it as a PNG file in the 'uploads/images'
     * directory. It returns a JSON response indicating the success status of
     * the operation.
     *
     * @param \Illuminate\Http\Request $request The request object containing the image data.
     * @return \Illuminate\Http\JsonResponse A JSON response indicating the success of the upload.
     */

    public function uploadCropImage(Request $request)
    {

        $image = $request->image;

        // list($type, $image) = explode(';', $image);
        // list(, $image)      = explode(',', $image);
        // $image = base64_decode($image);
        // $image_name= time().'.png';
        // $path = public_path('/uploads/images/'.$image_name);

        // file_put_contents($path, $image);
        return response()->json(['status' => true]);
    }

    /**
     * Saves a cropped image to the server and stores its information in the database.
     *
     * This function handles the processing of an image uploaded in base64 string format.
     * It decodes the image, saves it as a PNG file in the 'uploads/coverimages' directory,
     * and stores the image name in the database. The function returns a JSON response
     * indicating the success of the operation, along with a redirect URL.
     *
     * @param \Illuminate\Http\Request $request The request object containing the base64 encoded image data.
     * @return \Illuminate\Http\JsonResponse A JSON response indicating the success of the image upload.
     */

    public function saveImage(Request $request)
    {

        $image = $request->image;

        list($type, $image) = explode(';', $image);
        list(, $image)      = explode(',', $image);
        $image = base64_decode($image);
        $image_name = time() . '.png';
        $path = public_path('/uploads/coverimages/' . $image_name);

        file_put_contents($path, $image);

        $saveFile = new CoverImage;
        $saveFile->image_name = $image_name;
        $saveFile->save();
        //return response()->json(['status'=>true]);
        return response()->json([
            'status' => true,
            'message' => 'Image uploaded successfully!',
            'redirect_url' => route('coverimages') // Replace 'another.page' with the route name of the page you want to redirect to
        ]);
    }

    /**
     * Deletes a cover image.
     *
     * This function handles the request to delete a cover image. It takes an
     * id as an input parameter and updates the corresponding cover image
     * record in the database by setting its 'is_deleted' status to 1. The
     * function returns a JSON response indicating whether the operation was
     * successful or not.
     *
     * @param \Illuminate\Http\Request $request The request object containing the id of the cover image to be deleted.
     * @return \Illuminate\Http\JsonResponse A JSON response indicating the success of the delete operation.
     */
    public function deleteCoverImage(Request $request)
    {

        $id = $request->Id;
        //$isActive = $request->IsActive;
        $updateStatus = CoverImage::where('id', $id)->update(['is_deleted' => 1]);
        if ($updateStatus) {
            $success = 'success';
        } else {
            $success = 'fail';
        }

        return response()->json($success);
    }

    /**
     * Deletes multiple cover images.
     *
     * This function handles the request to delete multiple cover images
     * by updating their 'is_deleted' status. It takes a list of cover image IDs
     * from the request and updates the corresponding cover image records in the
     * database. The function returns a JSON response indicating whether
     * the operation was successful or not.
     *
     * @param \Illuminate\Http\Request $request The request object containing the list of cover image IDs.
     * @return \Illuminate\Http\JsonResponse A JSON response indicating the success of the delete operation.
     */
    public function deleteallimages(Request $request)
    {
        $idsString = $request->input('Id');


        //$isActive = $request->IsActive;
        $updateStatus = CoverImage::whereIn('id', $idsString)->update(['is_deleted' => 1]);
        if ($updateStatus) {
            $success = 'success';
        } else {
            $success = 'fail';
        }

        return response()->json($success);
    }
}
