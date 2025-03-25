<?php

namespace App\Console\Commands;

use App\Helpers\Helpers;
use App\Mail\OtpEmail;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;

class PlanExpired extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:plan-expired';

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
        $expiryDate = Carbon::now()->addDay(); // Get the expiry date one day from now
        $users = User::where('plan_end_date', $expiryDate)->get();
        // print_r($users);
        // exit;
        if($users){
            foreach ($users as $user) {
               echo $user['id'];
                    $template = 'planExpire';
                    $subject = "Reminder: Plan expire";
                    $emailData = [
                        'firstName' => $user['first_name'],
                        'lastName' => $user['last_name'],
                        'expiryDate' => $user->plan_end_date,
                    ];
                    $sendOtp =  \Illuminate\Support\Facades\Mail::to($user['email'])->send(new OtpEmail($emailData, $template, $subject));
                    if ($user['id'] > 0) {
                   
                        $message = "Reminder: Plan expire";
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
}
