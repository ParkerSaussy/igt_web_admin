<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Helpers\Helpers;
use App\Models\YoutubeUrl;

class YoutubeUrlController extends Controller
{
    /**
     * List youtube urls based on the given type
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function youtubeUrls(Request $request)
    {
        $json = json_decode(trim($request->getContent()), true);
        $schema = [
            'type' => 'required',
        ];
        $errorMessages = [
            'type.required' => 'Type filed is required',
        ];
        $validator = Validator::make($request->all(), $schema, $errorMessages);

        if ($validator->fails()) {
            return Helpers::validatorFail($validator->errors()->first());
        }
        $data = YoutubeUrl::where('type', $json['type'])->get();
        if ($data) {
            $authToken = "";
            return Helpers::success('Youtube urls listed successfully', $data, $authToken);
        } else {
            return Helpers::error('No urls found', 200);
        }
    }
}
