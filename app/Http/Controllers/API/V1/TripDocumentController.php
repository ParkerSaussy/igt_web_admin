<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Helpers\Helpers;
use App\Http\Services\V1\Auth\TripActivityServices;
use App\Models\TripDocument;


class TripDocumentController extends Controller
{
    /**
     * Uploads a document for a specific trip.
     *
     * This method handles the uploading of a document for a specified trip.
     * It validates the incoming request to ensure that all required fields
     * ('tripId', 'documentName', 'document', and 'size') are present. If the
     * validation passes, it either creates a new document record or updates
     * an existing one based on the presence of 'documentId'. The method also
     * sends a success or failure response based on the outcome of the document
     * upload process.
     *
     * @param Request $request The HTTP request object containing document data.
     * @param TripActivityServices $tripActivity The service handling trip activities.
     * 
     * @return \Illuminate\Http\JsonResponse A JSON response indicating success or failure.
     */

    public function uploaddocument(Request $request, TripActivityServices $tripActivity)
    {
        $authToken = $request->header('auth');
        $json = json_decode(trim($request->getContent()), true);
        $schema = [
            'tripId' => 'required',
            'documentName' => 'required',
            'document' => 'required',
            'size' => 'required',

        ];
        $errorMessages = [
            'tripId.required' => 'Trip id filed is required',
            'documentName.required' => 'Document name filed is required',
            'document.required' => 'Document filed is required',
            'size.required' => 'Document size filed is required'

        ];
        $validator = Validator::make($request->all(), $schema, $errorMessages);

        if ($validator->fails()) {
            return Helpers::validatorFail($validator->errors()->first());
        }
        $userId = Helpers::getUserIdFromToken($authToken);

        $image = $request->document;


        $data = array(
            'document_name' => $request['documentName'],
            'document' => $image,
            'trip_id' => $request['tripId'],
            'size' => $request['size'],
            'uploaded_by' => $userId
        );
        if ($request->documentId == "") {
            $updateData = TripDocument::create($data);
        } else {
            $updateData = TripDocument::where('id', $request->documentId)->update($data);
        }

        if ($updateData) {
            $responseData = [];
            return Helpers::success('Document has been uploaded successfully', $responseData, $authToken);
        } else {
            return Helpers::error('Failed to upload document', 200);
        }

        //return $data = $tripActivity->uploadTripDocuments($authToken, $request);
    }

    /**
     * Fetches all the documents for a specific trip.
     *
     * This method validates the incoming request to ensure that the 'tripId'
     * field is present. If the validation passes, it fetches all the documents
     * associated with the specified trip and returns them in a JSON response.
     *
     * @param Request $request The HTTP request object containing 'tripId'.
     *
     * @return \Illuminate\Http\JsonResponse A JSON response containing the documents.
     */
    public function gettripdocuments(Request $request)
    {
        $authToken = $request->header('auth');
        $json = json_decode(trim($request->getContent()), true);
        $schema = [
            'tripId' => 'required',
        ];
        $errorMessages = [
            'tripId.required' => 'Trip id filed is required',
        ];
        $validator = Validator::make($request->all(), $schema, $errorMessages);

        if ($validator->fails()) {
            return Helpers::validatorFail($validator->errors()->first());
        }
        $userId = Helpers::getUserIdFromToken($authToken);
        $staticImagePath  = config('global.trip_document_images');
        $data = TripDocument::where('trip_id', $json['tripId'])->where('is_deleted', 0)->orderBy('created_at', 'Desc')->get();
        $data = collect($data)->map(function ($item) use ($staticImagePath) {
            $item['image'] = $staticImagePath . $item['document'];
            return $item;
        })->all();


        if ($data) {
            // $responseData = [];
            return Helpers::success('Document listed successfully', $data, $authToken);
        } else {
            return Helpers::error('No documents found', 200);
        }

        //return $data = $tripActivity->uploadTripDocuments($authToken, $request);
    }

    /**
     * Deletes multiple documents for a specific trip.
     *
     * This method validates the incoming request to ensure that the 'documentId' field is present.
     * If the validation passes, it updates the 'is_deleted' field to 1 for the specified documents
     * and returns a success or failure response based on the outcome of the delete operation.
     *
     * @param Request $request The HTTP request object containing 'documentId'.
     *
     * @return \Illuminate\Http\JsonResponse A JSON response indicating success or failure.
     */
    public function deleteTripDocuments(Request $request)
    {
        $authToken = $request->header('auth');
        $json = json_decode(trim($request->getContent()), true);
        $schema = [
            'documentId.*' => 'required',
        ];
        $errorMessages = [
            'documentId.required' => 'document id filed is required',
        ];
        $validator = Validator::make($request->all(), $schema, $errorMessages);

        if ($validator->fails()) {
            return Helpers::validatorFail($validator->errors()->first());
        }

        $deleteRecords = TripDocument::whereIn('id', $json['documentId'])->update(['is_deleted' => 1]);

        if ($deleteRecords) {
            $responseData = [];
            return Helpers::success('Document deleted successfully', $responseData, $authToken);
        } else {
            return Helpers::error('Failed to delete documents', 200);
        }
    }
}
