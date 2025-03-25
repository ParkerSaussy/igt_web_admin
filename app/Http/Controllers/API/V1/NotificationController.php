<?php

namespace App\Http\Controllers\API\V1;

use App\Helpers\Helpers;
use App\Http\Controllers\Controller;
use App\Models\Notification;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    /**
     * Sends a notification to the user.
     *
     * This function accepts a JSON payload in the request body with the following structure:
     * {
     *     "title": string (required) - The title of the notification.
     *     "message": string (required) - The message of the notification.
     *     "user_id": integer (required) - The ID of the user to send the notification to.
     * }
     *
     * @param Request $request The HTTP request object containing the notification data in JSON format.
     *
     * @return \Illuminate\Http\JsonResponse A JSON response containing the result of notification sending.
     */
    public function sendNotification(Request $request)
    {
        $json = json_decode(trim($request->getContent()), true);
        $authToken = $request->header('auth');

        $sendNotification = Helpers::sendnotification($json);
        if ($sendNotification) {
            $responseData = [];
            return Helpers::success('Notification sent successfully.', $responseData, $authToken);
        } else {
            return Helpers::error('Failed to sent notification.', 200);
        }
    }

    /**
     * Lists all unread notifications for the user and marks them as read.
     *
     * This function accepts a JSON payload in the request body with the following structure:
     * {
     *     "limit": integer (optional) - The number of notifications to limit the response to.
     * }
     *
     * @param Request $request The HTTP request object containing the notification data in JSON format.
     *
     * @return \Illuminate\Http\JsonResponse A JSON response containing the result of notification listing and marking as read.
     */
    public function getNotification(Request $request)
    {
        // Decode the JSON content from the request
        $json = json_decode(trim($request->getContent()), true);

        // Get the authentication token from the request header
        $authToken = $request->header('auth');

        // Get the user ID from the authentication token using your helper function
        $userId = Helpers::getUserIdFromToken($authToken);

        // Fetch unread notifications for the user
        $notifications = Notification::where('reciver_id', $userId)
            ->orderBy('id', 'DESC')
            ->get();

        // Prepare an array to hold the formatted notification data
        $responseData = [];
        foreach ($notifications as $notification) {
            $responseData[] = [
                'id' => $notification->id,
                'type' => $notification->type,
                'sender_id' => $notification->sender_id,
                'reciver_id' => $notification->reciver_id, // Correct the typo: reciver_id => receiver_id
                'title' => $notification->title,
                'payload' => json_decode($notification->payload), // Assuming payload is stored as JSON string
                'is_read' => $notification->is_read,
                'created_at' => $notification->created_at,
                'updated_at' => $notification->updated_at,
                'message' => $notification->message,
            ];
        }

        // Update the is_read status of fetched notifications to mark them as read
        $updateIsRead = Notification::where('reciver_id', $userId)->update(['is_read' => 1]);

        // Check if notifications were successfully fetched and marked as read
        if ($updateIsRead !== false && !empty($responseData)) {
            return Helpers::success('Notification listed and marked as read successfully.', $responseData, $authToken);
        } else {
            return Helpers::error('No notification found.', 200);
        }
    }

    /**
     * Deletes a notification or all notifications for the user.
     *
     * This function accepts a JSON payload in the request body. If a notification ID is provided,
     * it deletes the specified notification. If no ID is provided, it deletes all notifications for the user.
     * The function returns a success response upon successful deletion, or an error response if deletion fails.
     *
     * @param Request $request The HTTP request object containing the notification data in JSON format.
     * 
     * @return \Illuminate\Http\JsonResponse A JSON response containing the result of the delete operation.
     */

    public function delete(Request $request)
    {
        $json = json_decode(trim($request->getContent()), true);
        $authToken = $request->header('auth');

        // Get the user ID from the authentication token using your helper function
        $userId = Helpers::getUserIdFromToken($authToken);
        $notificationId = $request->id;
        if ($notificationId != "") {
            $delete = Notification::where('id', $notificationId)->delete();
        } else {
            $delete = Notification::where('reciver_id', $userId)->delete();
        }

        if ($delete) {
            $responseData = Helpers::getUserDataFromId($authToken);
            return Helpers::success('Notification deleted successfully.', $responseData, $authToken);
        } else {
            return Helpers::error('Failed to delete notification.', 200);
        }
    }
}
