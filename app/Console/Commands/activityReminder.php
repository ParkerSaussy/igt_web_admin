<?php

namespace App\Console\Commands;

use App\Helpers\Helpers;
use App\Models\GuestListModel;
use App\Models\TripActivity;
use Illuminate\Console\Command;

class activityReminder extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:activity-reminder';

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
        $ldate = date('Y-m-d');
        $activitiesStartingSoon = TripActivity::where('is_itineary', 1)
        // for testing purpose ->where('event_date',$ldate)
        ->whereRaw("CONCAT(`event_date`, ' ', `event_time`) = '" . $currentDateTime->addMinutes(30)->format('Y-m-d H:i:s') . "'")
        ->get();
        // print_r($activitiesStartingSoon);
        // exit;
        if($activitiesStartingSoon){
            foreach ($activitiesStartingSoon as $record) {
                 $tripId = $record->trip_id;
                $getrecords = GuestListModel::select('email_id','first_name','u_id')
                ->where('trip_id',$tripId)
                ->where('invite_status','Sent')
                ->get();
                foreach ($getrecords as $guest) {
                
                    if ($guest['u_id'] > 0) {
                        $message =  "Your ".$record->name."activity is starting soon!";
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
                                'activityId' => $record['id'],
                                'tripId' => $tripId,
                                'type' => 'activity',
                                // Other key-value pairs
                            ],
                        ];
                        $sendNotification = Helpers::sendnotification($notificationData);
                    }
                }
            }
        }
    }
}
