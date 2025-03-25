<?php

namespace App\Http\Controllers\API\V1;

use App\Helpers\Helpers;
use App\Http\Controllers\Controller;
use App\Models\GuestListModel;
use App\Models\TripActivity;
use App\Models\TripDetails;
use Carbon\Carbon;
use DateTime;
use Illuminate\Http\Request;
use App\Mail\OtpEmail;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Crypt;
use AshAllenDesign\ShortURL\Classes\Builder;

class SchedularController extends Controller
{
    /**
     * This function sends reminders to users about activities that are starting in the next 30 minutes.
     * It fetches all activities that are starting soon and haven't had a notification sent yet.
     * It then iterates through the activities and sends a notification to each guest who has been invited
     * but hasn't been sent a notification yet.
     * After sending the notification, the notification_sent flag is set to true.
     */
    public function activityReminder()
    {
        $currentDateTime = Carbon::now('UTC');
        $actualTime = Carbon::now('UTC');
        $currentDateTime->addMinutes(30)->format('Y-m-d H:i:s');
        //  $time30MinutesFromNow = $currentDateTime->format('H:i:s');
        //  $actualMinute = $actualTime->format('H:i:s');
        //$ldate = Carbon::now();
        $activitiesStartingSoon = TripActivity::with([
            'guests' => function ($query) {
                $query->whereNotIn('invite_status', ['Not Sent', 'Declined']);
            },
        ])
            ->where('is_itineary', 1)
            // ->whereDate('event_date', $currentDateTime->format('Y-m-d'))
            ->whereTime('utc_time', '<=', $currentDateTime)
            ->whereTime('utc_time', '>=', $actualTime)
            ->where('notification_sent', false)
            ->get();

        if ($activitiesStartingSoon) {
            foreach ($activitiesStartingSoon as $record) {
                $tripId = $record->trip_id;
                foreach ($record->guests as $guest) {
                    if ($guest['u_id'] > 0) {
                        $message = 'Your ' . $record->name . ' activity is starting soon!';
                        $reciverId = [
                            [
                                'userId' => $guest['u_id'],
                            ],
                        ];
                        $notificationData = [
                            'type' => 'activity',
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

                        $record->notification_sent = true;
                        $record->save();
                    }
                }
            }
        }
    }

    /**
     * This function sends a reminder to users one day before their plan expires.
     * It fetches all users whose plan is expiring the next day and sends a notification to them.
     * The notification contains the user's first name, last name and the expiry date of their plan.
     */
    public function planExpired()
    {
        $expiryDate = now()
            ->addDay()
            ->startOfDay(); // Get the expiry date one day from now
        $users = User::whereDate('plan_end_date', '=', $expiryDate)->get();

        if ($users) {
            foreach ($users as $user) {
                $user['id'];
                $template = 'planExpire';
                $subject = 'Reminder: Plan expire';
                $emailData = [
                    'firstName' => $user['first_name'],
                    'lastName' => $user['last_name'],
                    'expiryDate' => $user->plan_end_date,
                ];
                $sendOtp = \Illuminate\Support\Facades\Mail::to($user['email'])->send(new OtpEmail($emailData, $template, $subject));
                if ($user['id'] > 0) {
                    $message = 'Reminder: Plan expire';
                    $reciverId = [
                        [
                            'userId' => $user['id'],
                        ],
                    ];
                    $notificationData = [
                        'type' => 'due_date',
                        'senderId' => '',
                        'reciverId' => $reciverId,
                        'title' => 'ItsGoTime!',
                        'message' => $message,
                        'payload' => [
                            'userId' => $user['id'],
                            'type' => 'plan',
                            // Other key-value pairs
                        ],
                    ];
                    $sendNotification = Helpers::sendnotification($notificationData);
                }
            }
        }
    }

    /**
     * Sends a reminder to all users who have trips with deadlines that have passed.
     *
     * The reminder is sent in the form of an email with the trip name and deadline.
     * Additionally, a notification is sent to the user with the same information.
     *
     * @throws \Exception if an error occurs
     */
    public function tripReminders()
    {
        $currentDateTime = now();
        try {
            // Query for deadlines that have passed
            $records = TripDetails::whereNotNull('response_deadline')
                ->where('is_trip_finalised', 0)
                ->where('is_deleted', 0)
                ->where('response_deadline', '<', $currentDateTime)
                ->where('deadline_passed_status', '!=', 'done')
                ->get();
            // print_r($records);
            // exit;

            if ($records) {
                foreach ($records as $deadline) {
                    $id = $deadline->id;

                    $getrecords = GuestListModel::select('email_id', 'first_name', 'u_id')
                        ->where('trip_id', $id)
                        ->where('invite_status', 'Sent')
                        ->get();
                    foreach ($getrecords as $guest) {
                        $carbonDate = Carbon::parse($deadline->response_deadline);
                        $formattedDate = $carbonDate->format('Y-m-d');
                        $template = 'deadlinepassedreminder';
                        $subject = 'Deadline has passed for ' . $deadline->trip_name;
                        $emailData = [
                            'firstName' => $guest['first_name'],
                            'tripName' => $deadline->trip_name,
                            'tripDate' => $formattedDate,
                        ];
                        $sendOtp = Mail::to($guest['email_id'])->send(new OtpEmail($emailData, $template, $subject));
                        if ($guest['u_id'] > 0) {
                            $message = 'Deadline has passed for ' . $deadline->trip_name;
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
                    $updateTripStatus = TripDetails::where('id', $id)->update(['deadline_passed_status' => 'done']);
                }
            }
        } catch (\Exception $e) {
            // Handle exceptions gracefully, e.g., log the error
            echo 'An error occurred: ' . $e->getMessage() . "\n";
        }
    }

    /**
     * Send reminders to guests for trips that have a response deadline coming up.
     *
     * This function runs every hour and checks for trips that have a response deadline
     * within the next 24 hours. It then sends out reminders to guests who have not yet
     * responded to the trip.
     *
     * @throws \Exception
     */
    public function deadlinePassed()
    {
        try {
            $records = TripDetails::whereNotNull('response_deadline')
                ->where('is_trip_finalised', 0)
                ->where('is_deleted', 0)
                //->where('id', 117)
                ->where('response_deadline', '>', now())
                ->get();

            // print_r($records);
            // exit;
            foreach ($records as $record) {
                $id = $record->id;
                echo $previosDate = $record->previous_reminder_date;
                $excludedStatus = ['Approved', 'Declined'];
                $reminderDays = $record->reminder_days;

                $nDate = Carbon::parse($previosDate);
                $reminderDate = $nDate->addDays($reminderDays);
                $dateOnly = $reminderDate->toDateString();

                if (now() >= $reminderDate) {

                    $getrecords = GuestListModel::select('id', 'email_id', 'first_name', 'u_id')
                        ->where('trip_id', $id)
                        ->where('invite_status', 'Sent')
                        ->get();
                    if ($getrecords) {
                        foreach ($getrecords as $guest) {

                            $carbonDate = Carbon::parse($record->response_deadline);
                            $formattedDate = $carbonDate->format('Y-m-d');
                            $encryptedTripId = Crypt::encryptString($record->id);
                            $template = 'deadlinereminder';
                            $subject = 'Reminder: ' . $record->trip_name . '- Deadline is approching on ' . $formattedDate;
                            $guestId = $guest['id'];
                            $encryptedguestId = Crypt::encryptString($guestId);
                            $builder = new Builder();
                            $url = 'https://lesgo.dashtechinc.com/pollweb/' . $encryptedTripId . '/' . $encryptedguestId;
                            $shortURLObject = $builder->destinationUrl($url)->make();
                            $emailData = [
                                'firstName' => $guest['first_name'],
                                'tripName' => $record->trip_name,
                                'tripDate' => $formattedDate,
                                'url' => $shortURLObject->default_short_url,
                            ];
                            $sendOtp = Mail::to($guest['email_id'])->send(new OtpEmail($emailData, $template, $subject));
                            //$sendOtp = Helpers::sendEmail($guest['email_id'], $userData, $template, $subject);
                            // $sendOtp = Helpers::sendEmail($guest['email_id'], $guest['first_name'], $template, $subject);

                            if ($guest['u_id'] > 0) {
                                $message = $record->trip_name . '- Deadline is approching on' . $record->response_deadline . 'please respond.';
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
                            $updateResult = TripDetails::where('id', $record->id)->update(['previous_reminder_date' => $dateOnly]);
                            if ($updateResult) {
                                echo "Reminder date updated successfully for trip ID: {$record->id}\n";
                            } else {
                                echo "Failed to update reminder date for trip ID: {$record->id} {$dateOnly}\n";
                            }
                        }
                    }
                } else {
                    if ($record->last_reminder_sent_status == 0) {

                        $deadLine = Carbon::parse($record->response_deadline)
                            ->toDateString();

                        $carbonDate = Carbon::parse($record->response_deadline);
                        $formattedDate = $carbonDate->format('Y-m-d');

                        if (now()->addHours(8) >= $deadLine) {
                            $getrecords = GuestListModel::select('id', 'email_id', 'first_name', 'u_id')
                                ->where('trip_id', $record->id)
                                ->where('invite_status', 'Sent')
                                ->get();

                            $encryptedTripId = Crypt::encryptString($record->id);
                            $template = 'deadlinereminderbefore8hours';
                            $subject = 'Reminder: ' . $record->trip_name . ' - Deadline is approaching on ' . $formattedDate;
                            foreach ($getrecords as $guest) {
                                $guestId = $guest['id'];
                                $encryptedguestId = Crypt::encryptString($guestId);
                                $builder = new Builder();
                                $url = 'https://lesgo.dashtechinc.com/pollweb/' . $encryptedTripId . '/' . $encryptedguestId;
                                $shortURLObject = $builder->destinationUrl($url)->make();
                                $emailData = [
                                    'firstName' => $guest['first_name'],
                                    'tripName' => $record->trip_name,
                                    'tripDate' => $formattedDate,
                                    'url' => $shortURLObject->default_short_url,
                                ];

                                $sendOtp = Mail::to($guest['email_id'])->send(new OtpEmail($emailData, $template, $subject));
                            }
                            $updateResult = TripDetails::where('id', $record->id)->update(['last_reminder_sent_status' => 1]);
                        }
                    }
                }
            }
        } catch (\Exception $e) {
            // Handle exceptions gracefully, e.g., log the error
            echo 'An error occurred: ' . $e->getMessage() . "\n";
        }
    }

    //     public function deadlinePassed()
    // {
    //     try {
    //         $records = TripDetails::whereNotNull('response_deadline')
    //             ->where('is_trip_finalised', 0)
    //             ->where('is_deleted', 0)
    //             ->where('response_deadline', '>', now())
    //             ->get();

    //         foreach ($records as $record) {
    //             $reminderDate = Carbon::parse($record->previous_reminder_date)
    //                 ->addDays($record->reminder_days)
    //                 ->toDateString();

    //            // $now = "2024-01-04 00:00:00";
    //             if (now() >= $reminderDate) {

    //                 $getrecords = GuestListModel::select('id','email_id', 'first_name', 'u_id')
    //                     ->where('trip_id', $record->id)
    //                     ->where('invite_status', 'Sent')
    //                     ->get();

    //                 $encryptedTripId = Crypt::encryptString($record->id);

    //                 $timeDifferenceInHours = now()->diffInHours($reminderDate);

    //                 $template = ($timeDifferenceInHours < 8) ? 'deadlinereminderbefore8hours' : 'deadlinereminder';
    //                 $subject = "Reminder: " . $record->trip_name . " - Deadline is approaching on" . $record->response_deadline;

    //                 foreach ($getrecords as $guest) {
    //                     $guestId = $guest['id'];
    //                     $encryptedguestId = Crypt::encryptString($guestId);
    //                     $builder = new Builder();
    //                     $url= 'https://lesgo.dashtechinc.com/pollweb/' . $encryptedTripId . '/' . $encryptedguestId;
    //                     $shortURLObject = $builder->destinationUrl($url)->make();
    //                     $emailData = [
    //                         'firstName' => $guest['first_name'],
    //                         'tripName' => $record->trip_name,
    //                         'tripDate' => $record->response_deadline,
    //                         'url' => $shortURLObject->default_short_url
    //                     ];

    //                     $sendOtp =  Mail::to($guest['email_id'])->send(new OtpEmail($emailData, $template, $subject));
    //                 }

    //                 $updateResult = TripDetails::where('id', $record->id)->update(['previous_reminder_date' => $reminderDate]);

    //                 if ($updateResult) {
    //                     echo "Reminder date updated successfully for trip ID: {$record->id}\n";
    //                 } else {
    //                     echo "Failed to update reminder date for trip ID: {$record->id}\n";
    //                 }
    //             }else{
    //                 if($record->last_reminder_sent_status == 0){
    //                     $deadLine = Carbon::parse($record->response_deadline)
    //                     ->addDays(1)
    //                     ->toDateString();

    //             if(now()->addHours(8) >=  $deadLine){
    //                 $getrecords = GuestListModel::select('id','email_id', 'first_name', 'u_id')
    //                 ->where('trip_id', $record->id)
    //                 ->where('invite_status', 'Sent')
    //                 ->get();

    //                 $encryptedTripId = Crypt::encryptString($record->id);
    //                 $template = 'deadlinereminderbefore8hours';
    //                 $subject = "Reminder: " . $record->trip_name . " - Deadline is approaching on" . $record->response_deadline;
    //                 foreach ($getrecords as $guest) {
    //                     $guestId = $guest['id'];
    //                     $encryptedguestId = Crypt::encryptString($guestId);
    //                     $builder = new Builder();
    //                     $url= 'https://lesgo.dashtechinc.com/pollweb/' . $encryptedTripId . '/' . $encryptedguestId;
    //                     $shortURLObject = $builder->destinationUrl($url)->make();
    //                     $emailData = [
    //                         'firstName' => $guest['first_name'],
    //                         'tripName' => $record->trip_name,
    //                         'tripDate' => $record->response_deadline,
    //                         'url' => $shortURLObject->default_short_url
    //                     ];

    //                     $sendOtp =  Mail::to($guest['email_id'])->send(new OtpEmail($emailData, $template, $subject));

    //                 }
    //                     $updateResult = TripDetails::where('id', $record->id)->update(['last_reminder_sent_status' =>1]);
    //             }
    //         }

    //     }

    //         }
    //     } catch (\Exception $e) {
    //         // Handle exceptions gracefully, e.g., log the error
    //         echo "An error occurred: " . $e->getMessage() . "\n";
    //     }
    // }
}
