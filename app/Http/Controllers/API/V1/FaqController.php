<?php

namespace App\Http\Controllers\API\V1;

use App\Helpers\Helpers;
use App\Http\Controllers\Controller;
use App\Models\FaqModel;
use Illuminate\Http\Request;

class FaqController extends Controller
{
    /**
     * List all FAQs.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function faqList(Request $request)
    {
        $authToken = $request->header('auth');
        $getFaqs = FaqModel::where('is_active', 1)->orderBy('id', 'DESC')->get();
        if ($getFaqs) {
            foreach ($getFaqs as $faqs) {
                $responseData[] = [
                    'id' => $faqs->id,
                    'question' => $faqs->type,
                    'answer' => json_decode($faqs->answer),
                    'is_active' => $faqs->reciver_id, // Correct the typo: reciver_id => receiver_id
                    'created_at' => $faqs->created_at,
                    'updated_at' => $faqs->updated_at,
                ];
            }
            $data = array(
                'faqData' => $getFaqs,
            );
            return Helpers::success("Faq data listed successfully", $data, $authToken);
        } else {
            return Helpers::error(__('messages.no_data_found'), 200);
        }
    }
}
