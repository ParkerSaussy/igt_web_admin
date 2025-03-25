<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\V1\TripDetailsController;
use App\Http\Controllers\API\V1\TimezoneController;
use App\Http\Controllers\API\V1\TripDocumentController;
use App\Http\Controllers\API\V1\TripMemoriesController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::group(['prefix' => 'v1/auth'], function () {
    Route::post('register', 'App\Http\Controllers\API\V1\AuthController@register');
    Route::post('logout', 'App\Http\Controllers\API\V1\AuthController@logout');
    Route::post('signin', 'App\Http\Controllers\API\V1\AuthController@signin');
    Route::post('sendOtp', 'App\Http\Controllers\API\V1\AuthController@sendOtp');
    Route::post('verifyOtp', 'App\Http\Controllers\API\V1\AuthController@verifyOtp');
    Route::post('changePassword', 'App\Http\Controllers\Api\V1\AuthController@changePassword')->middleware('log.route');
    Route::post('uploadImage', 'App\Http\Controllers\Api\V1\AuthController@uploadImage')->middleware('log.route');
    Route::post('editProfile', 'App\Http\Controllers\Api\V1\AuthController@editProfile')->middleware('log.route');
    Route::post('updateMobileNumber', 'App\Http\Controllers\Api\V1\AuthController@updateMobileNumber')->middleware('log.route');
    Route::post('forgotPassword', 'App\Http\Controllers\Api\V1\AuthController@forgotPassword');
    Route::post('resetPassword', 'App\Http\Controllers\Api\V1\AuthController@resetPassword');
    Route::post('getCms', 'App\Http\Controllers\Api\V1\AuthController@getCms');
    Route::post('getEmailForApple', 'App\Http\Controllers\Api\V1\AuthController@getEmailForApple');
    Route::post('getProfile', 'App\Http\Controllers\Api\V1\AuthController@getProfile')->middleware('log.route');
    Route::post('updateUsernames', 'App\Http\Controllers\Api\V1\AuthController@updateUsernames');
    Route::post('updatenotficationStatus', 'App\Http\Controllers\Api\V1\AuthController@updatenotficationStatus')->middleware('log.route');
    
});

Route::group(['prefix' => 'v1/trip'], function () {
    Route::post('createtrip', [TripDetailsController::class,'createTrip'])->middleware('log.route');
    Route::post('adddatestotrip', [TripDetailsController::class,'addDatesToTrip'])->middleware('log.route');
    Route::post('addCitiesToTrip', [TripDetailsController::class,'addCitiesToTrip'])->middleware('log.route');
    Route::post('addGuestToTrip', [TripDetailsController::class,'addGuestToTrip'])->middleware('log.route');
    Route::post('addRemoveCoHost', [TripDetailsController::class,'addRemoveCoHost'])->middleware('log.route');
    Route::post('updateGuestRole', [TripDetailsController::class,'updateGuestRole'])->middleware('log.route');
    Route::post('uploadTripCoverImage', [TripDetailsController::class,'uploadTripCoverImage'])->middleware('log.route');
    Route::post('removeInvitee', [TripDetailsController::class,'removeInvitee'])->middleware('log.route');
    Route::post('sendInvitation', [TripDetailsController::class,'sendInvitation'])->middleware('log.route');
    Route::post('sendInvitationMail', [TripDetailsController::class,'sendInvitationMail'])->middleware('log.route');
    Route::post('getTripsList', [TripDetailsController::class,'getTripsList'])->middleware('log.route');
    Route::post('getTripDetail', [TripDetailsController::class,'getTripDetail'])->middleware('log.route');
    Route::post('addDatePoll', [TripDetailsController::class,'addDatePoll'])->middleware('log.route');
    Route::post('addCityPoll', [TripDetailsController::class,'addCityPoll'])->middleware('log.route');
    Route::post('getTripGuestList', [TripDetailsController::class,'getTripGuestList'])->middleware('log.route');
    Route::post('getDatesPollDetails', [TripDetailsController::class,'getDatesPollDetails'])->middleware('log.route');
    Route::post('getCityPollDetails', [TripDetailsController::class,'getCityPollDetails'])->middleware('log.route');
    Route::post('getcities', [TripDetailsController::class,'getCities']);
    Route::post('getCitiesSearched', [TripDetailsController::class,'getCitiesSearched']);
    Route::get('getcoverimages', [TripDetailsController::class,'getcoverimages'])->middleware('log.route');
    Route::post('saveFinalTrip', [TripDetailsController::class,'saveFinalTrip'])->middleware('log.route');
    Route::post('inserttimezone', [TimezoneController::class,'inserttimezone']);
    Route::post('actionOnInvitation',[TripDetailsController::class,'actionOnInvitation'])->middleware('log.route');
    Route::post('deleteTrip',[TripDetailsController::class,'deleteTrip'])->middleware('log.route');
    Route::post('addDropboxUrl',[TripDetailsController::class,'addDropboxUrl'])->middleware('log.route');
    

    //upload document
    Route::post('uploadtripdocument', [TripDocumentController::class,'uploaddocument'])->middleware('log.route');
    Route::post('gettripdocuments', [TripDocumentController::class,'gettripdocuments'])->middleware('log.route');
    Route::post('deleteTripDocuments', [TripDocumentController::class,'deleteTripDocuments'])->middleware('log.route');

    
    
});

Route::group(['prefix' => 'v1/activity'], function () {
    Route::post('addEditActivity', 'App\Http\Controllers\API\V1\TripActivityController@addEditActivity')->middleware('log.route');
    Route::post('likeDislikeIdeas', 'App\Http\Controllers\API\V1\TripActivityController@likeDislikeIdeas')->middleware('log.route');
    Route::post('getActivityDetail', 'App\Http\Controllers\API\V1\TripActivityController@getActivityDetail')->middleware('log.route');
    Route::post('makeItineary', 'App\Http\Controllers\API\V1\TripActivityController@makeItineary')->middleware('log.route');
    Route::post('deleteActivity', 'App\Http\Controllers\API\V1\TripActivityController@deleteActivity')->middleware('log.route');
});


Route::group(['prefix' => 'v1/memories'], function () {
    Route::post('getActivityName', 'App\Http\Controllers\API\V1\TripMemoriesController@getActivityName')->middleware('log.route');
    Route::post('addMemory', 'App\Http\Controllers\API\V1\TripMemoriesController@addMemory')->middleware('log.route');
    Route::post('memoryListing', 'App\Http\Controllers\API\V1\TripMemoriesController@memoryListing')->middleware('log.route');
    Route::post('deleteMemory', 'App\Http\Controllers\API\V1\TripMemoriesController@deleteMemory')->middleware('log.route');
 
});

Route::group(['prefix' => 'v1/plan'], function () {
    Route::post('getPlans', 'App\Http\Controllers\API\V1\PlanController@getPlans')->middleware('log.route');
    Route::post('purchasePlan', 'App\Http\Controllers\API\V1\PlanController@purchasePlan')->middleware('log.route');
});

Route::group(['prefix' => 'v1/contact'], function () {
    Route::post('addInquiry', 'App\Http\Controllers\API\V1\ContactController@addInquiry')->middleware('log.route');
});

Route::group(['prefix' => 'v1/faq'], function () {
    Route::get('faqList', 'App\Http\Controllers\API\V1\FaqController@faqList')->middleware('log.route');
});

Route::group(['prefix' => 'v1/notification'], function () {
    Route::post('send', 'App\Http\Controllers\API\V1\NotificationController@sendNotification')->middleware('log.route');
    Route::post('get', 'App\Http\Controllers\API\V1\NotificationController@getNotification')->middleware('log.route');
    Route::post('delete', 'App\Http\Controllers\API\V1\NotificationController@delete')->middleware('log.route');
});

Route::group(['prefix' => 'v1/expense'], function () {
    Route::post('addExpense', 'App\Http\Controllers\API\V1\ExpenseController@addExpense')->middleware('log.route');
    Route::post('getActivities', 'App\Http\Controllers\API\V1\ExpenseController@getActivities')->middleware('log.route');
    Route::post('getResolutions', 'App\Http\Controllers\API\V1\ExpenseController@getResolutions')->middleware('log.route');
    Route::post('payExpense', 'App\Http\Controllers\API\V1\ExpenseController@payExpense')->middleware('log.route');
    Route::post('expReport', 'App\Http\Controllers\API\V1\ExpenseController@expReport')->middleware('log.route');
});

Route::group(['prefix' => 'v1/web'], function () {
    Route::post('getTripDetailsWeb', 'App\Http\Controllers\API\V1\TripDetailsController@getTripDetailsWeb');
    Route::post('getDatesPollDetailsWeb', 'App\Http\Controllers\API\V1\TripDetailsController@getDatesPollDetailsWeb');
    Route::post('getCityPollDetailsWeb', 'App\Http\Controllers\API\V1\TripDetailsController@getCityPollDetailsWeb');
    Route::post('addDatePollWeb', 'App\Http\Controllers\API\V1\TripDetailsController@addDatePollWeb');
    Route::post('addCityPollWeb', 'App\Http\Controllers\API\V1\TripDetailsController@addCityPollWeb');
    Route::post('actionOnInvitationWeb', 'App\Http\Controllers\API\V1\TripDetailsController@actionOnInvitationWeb');
});

Route::group(['prefix' => 'v1/walkthrough'], function () {
    Route::post('youtubeUrls', 'App\Http\Controllers\API\V1\YoutubeUrlController@youtubeUrls');
    
});

Route::group(['prefix' => 'v1/job'], function () {
    Route::get('activityReminder', 'App\Http\Controllers\API\V1\SchedularController@activityReminder');
    Route::get('planExpired', 'App\Http\Controllers\API\V1\SchedularController@planExpired');
    Route::get('deadlinePassed', 'App\Http\Controllers\API\V1\SchedularController@deadlinePassed');
    Route::get('tripReminders', 'App\Http\Controllers\API\V1\SchedularController@tripReminders');
    
});


