<?php

namespace App\Console\Commands;
use App\Helpers\Helpers;
use Illuminate\Console\Command;
use App\Models\TripDetails;
use App\Models\GuestListModel;
use App\Mail\OtpEmail;

use Illuminate\Support\Facades\Mail;

class CheckDeadlines extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:check-deadlines';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $currentDateTime = now();
        try {
        // Query for deadlines that have passed
        $records = TripDetails::whereNotNull('response_deadline')
        ->where('is_trip_finalised', 0)
        ->where('is_deleted', 0)
        ->where('response_deadline', '<', $currentDateTime)
        ->where('deadline_passed_status','!=','done')
        ->get();


       if($records){
            foreach ($records as $deadline) {
                $id = $deadline->id;
               
                $getrecords = GuestListModel::select('email_id','first_name','u_id')
                ->where('trip_id',$id)
                ->where('invite_status','Sent')
                ->get();
                foreach ($getrecords as $guest) {
                
                    $template = 'deadlinepassedreminder';
                    $subject = "Deadline has passed for ".$deadline->trip_name;
                    $emailData = [
                        'firstName' => $guest['first_name'],
                        'tripName' => $deadline->trip_name,
                        'tripDate' => $deadline->response_deadline,
                    ];
                    $sendOtp =  Mail::to($guest['email_id'])->send(new OtpEmail($emailData, $template, $subject));
                    if ($guest['u_id'] > 0) {
                   
                        $message = "Deadline has passed for ".$deadline->trip_name;
                        $reciverId = [
                            [
                                'userId' => $guest['u_id'],
                            ],
                        ];
                        $notificationData = [
                            'type' => 'due_date',
                            'senderId' => '',
                            'reciverId' => $reciverId,
                            'title' => 'ItsGoTime!',
                            'message' => $message,
                            'payload' => [
                                'tripId' => $guest['trip_id'],
                                'type' => 'due_date',
                                // Other key-value pairs
                            ],
                        ];
                        $sendNotification = Helpers::sendnotification($notificationData);
                    }
                }
                $updateTripStatus = TripDetails::where('id',$id)->update(['deadline_passed_status' => "done"]);
            }
           
        }
       
        } catch (\Exception $e) {
            // Handle exceptions gracefully, e.g., log the error
            echo "An error occurred: " . $e->getMessage() . "\n";
        }
    }
}
