<?php

namespace App\Console\Commands;

use App\Helpers\Helpers;
use App\Models\GuestListModel;
use Date;
use DateInterval;
use DateTime;
use Illuminate\Console\Command;
use App\Models\TripDetails;
use Carbon\Carbon;
use App\Mail\OtpEmail;
use Illuminate\Support\Facades\Mail;

class SendTripReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:send-trip-reminders';

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

        try {
            $records = TripDetails::whereNotNull('response_deadline')
                ->where('is_trip_finalised', 0)
                ->where('is_deleted', 0)
               // ->where('id', 22)
                ->where('response_deadline', '>', now())
                ->get();
                // print_r($records);
                // exit;

        
            foreach ($records as $record) {
                 $id = $record->id;
                $previosDate = $record->previous_reminder_date;
                $excludedStatus = ['Approved', 'Declined'];
                $reminderDays = $record->reminder_days;
              
                $nDate = Carbon::parse($previosDate);
                $reminderDate = $nDate->addDays($reminderDays);
                $dateOnly = $reminderDate->toDateString();
               
                
                if (now() >= $reminderDate) {
                  
                    $getrecords = GuestListModel::select('email_id','first_name','u_id')
                    ->where('trip_id',$id)
                    ->where('invite_status','Sent')
                    ->get();
                    if($getrecords){

                    foreach ($getrecords as $guest) {
                      
                    $template = 'deadlinereminder';
                    $subject = "Reminder: ".$record->trip_name."- Deadline is approching on". $record->response_deadline;
                    $emailData = [
                        'firstName' => $guest['first_name'],
                        'tripName' => $record->trip_name,
                        'tripDate' => $record->response_deadline,
                    ];
                    $sendOtp =  Mail::to($guest['email_id'])->send(new OtpEmail($emailData, $template, $subject));
                    //$sendOtp = Helpers::sendEmail($guest['email_id'], $userData, $template, $subject);
                   // $sendOtp = Helpers::sendEmail($guest['email_id'], $guest['first_name'], $template, $subject);
                  
                   if ($guest['u_id'] > 0) {
                   
                    $message = $record->trip_name."- Deadline is approching on". $record->response_deadline ."please respond.";
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
                            'tripId' => $id,
                            'type' => 'due_date',
                            // Other key-value pairs
                        ],
                    ];
                    $sendNotification = Helpers::sendnotification($notificationData);
                }
                  
                    }
                    

                    $updateResult = TripDetails::where('id', $record->id)->update(['previous_reminder_date' => $dateOnly]);
                    if ($updateResult) {
                        echo "Reminder date updated successfully for trip ID: {$record->id}\n";
                    } else {
                        echo "Failed to update reminder date for trip ID: {$record->id}\n";
                    }
                }
                }
            }
        } catch (\Exception $e) {
            // Handle exceptions gracefully, e.g., log the error
            echo "An error occurred: " . $e->getMessage() . "\n";
        }
    
  
    }
   

    
    
}
