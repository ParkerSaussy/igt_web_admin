<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\Helpers;
use App\Http\Controllers\Controller;
use App\Models\Inquiry;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ContactController extends Controller
{
    /**
     * Displays a list of all inquiries.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $getInquiries = Inquiry::orderBy('id', 'DESC')->get();
        return view('admin.contact.allInquiry', compact('getInquiries'));
    }

    /**
     * Shows the reply form for a specific inquiry.
     * 
     * @param int $id The id of the inquiry to show the reply form for.
     * 
     * @return \Illuminate\Http\Response
     */
    public function inquryReply($id)
    {
        $data = Inquiry::find($id);
        return view('admin.contact.inquryreply', compact('data'));
    }

    /**
     * Updates an inquiry record and sends a reply email to the user.
     * 
     * This function is used to update an existing inquiry record and send a reply email to the user.
     * It takes a request object as an input parameter and validates it.
     * If the validation fails then it redirects back with the validation errors.
     * If the validation succeeds then it updates the inquiry record and sends a reply email to the user.
     * It also sends a notification to the user if the user is logged in.
     * 
     * @param Request $request The request object containing inquiry data.
     * 
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateinquiry(Request $request)
    {
        $data = $request->all();

        $validator = Validator::make($data, [
            'reply' => 'required',


            // Add more validation rules as needed
        ], [
            'reply.required' => 'Reply field is required',


            // Add more custom error messages as needed
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $validator = $request->all();
        $id = $request->id;
        $userId = $request->user_id;
        $oldMessage = $request->old_message;
        $email = $request->email;
        $data = array(
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'old_message' => htmlspecialchars_decode($request->old_message),
            'reply' => html_entity_decode($request->reply)
        );

        $template = 'inquiryreplycontent';
        $subject = 'Your Inquiry Reply';
        $sentMail = Helpers::sendEmail($email, $data, $template, $subject);
        if ($sentMail) {
            $update = Inquiry::where('id', $id)->update(['is_replied' => 1]);
            try {
                if ($userId > 0) {
                    $message = 'Your inquiry has been addressed by the admin';
                    $reciverId = [];
                    // Build an array of user IDs

                    $reciverId[] = [
                        'userId' => $userId,
                    ];

                    $data = [
                        'type' => 'Inquiry',
                        'senderId' => '',
                        'reciverId' => $reciverId,
                        'title' => 'ItsGoTime',
                        'message' => $message,
                        'payload' => [
                            'userId' => $userId,
                            'type' => 'Inquiry',
                            // Other key-value pairs
                        ],
                    ];
                    $sendNotification = Helpers::sendnotification($data);
                    return true;
                }
            } catch (\Exception $e) {
                return false;
            }
            return redirect()->route('allinquiries')
                ->with('success', 'Email sent successfully');
        } else {
            return redirect()->route('allinquiries')
                ->with('fail', 'Failed to sent reply');
        }
    }
}
