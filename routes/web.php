<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\PostController;
use App\Http\Controllers\Admin\CmsController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\CityController;
use App\Http\Controllers\Admin\TripController;
use App\Http\Controllers\Admin\CoverImageController;
use App\Http\Controllers\Admin\PlanController;
use App\Http\Controllers\Admin\ContactController;
use App\Http\Controllers\Admin\FaqController;
use App\Http\Controllers\Admin\PurchasedPlanController;
use App\Http\Controllers\Admin\PollController;
use App\Http\Controllers\Admin\ShortenedUrlController;
use App\Http\Controllers\Admin\YoutubeUrlController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/


// Route::get('/', function () {
//     return view('signin');
// });

Route::fallback(function () {
    return view('errors.404'); // Assuming 'errors.404' corresponds to your 404 error view file
});

Route::get('403', function () {
    return abort(403);
})->name('403');

Route::get('/', [App\Http\Controllers\Admin\AuthController::class, 'signin'])->name('signin');
Route::post('/login', [App\Http\Controllers\Admin\AuthController::class, 'login']);
Route::get('/forgotemail', [App\Http\Controllers\Admin\AuthController::class, 'forgotemail']);
Route::get('/resetpassword/{token}', [App\Http\Controllers\Admin\AuthController::class, 'resetpassword']);
Route::post('/adminforgotpassword', [App\Http\Controllers\Admin\AuthController::class, 'postEmail']);
Route::post('/resetpostPassword', [App\Http\Controllers\Admin\AuthController::class, 'resetpostPassword']);
Route::get('/short/{shortCode}', [App\Http\Controllers\Admin\ShortenedUrlController::class, 'redirectToOriginalUrl']);
//Web Poll
Route::get('pollweb/{tripid}/{guestid}', [PollController::class, 'pollWeb'])->name('pollWeb');
Route::post('insertWebPoll', [PollController::class, 'insertWebPoll'])->name('insertWebPoll');
Route::post('invitationDeclined', [PollController::class, 'invitationDeclined'])->name('invitationDeclined');

Route::group(['middleware' => 'prevent-back-history'], function () {

    //Auth middleware
    Route::group(['middleware' => 'super-auth'], function () {

        Route::get('/dashboard', [App\Http\Controllers\Admin\DashboardController::class, 'index']);
        Route::get('/logouts', [App\Http\Controllers\Admin\AuthController::class, 'logout']);

        Route::get('/changepassword', [App\Http\Controllers\Admin\AuthController::class, 'changePassword']);
        Route::post('/updatepassword', [App\Http\Controllers\Admin\AuthController::class, 'updatePassword']);
        Route::get('/posts', [PostController::class, 'index'])->name('posts');

        //cms
        Route::get('cmspages', [CmsController::class, 'index'])->name('cmspages');
        Route::get('/editpage/{id}', [CmsController::class, 'editPage'])->name('editPage');
        Route::post('/updatepage', [CmsController::class, 'updatepage'])->name('updatepage');
        //end cms

        //users
        Route::get('users', [UserController::class, 'index'])->name('users');
        Route::get('users/userTrips/{id}', [UserController::class, 'userTrips'])->name('userTrips');
        Route::get('users/userTrips/{id}/{tripid}', [UserController::class, 'userTripDetail'])->name('userTripDetail');
        Route::post('/changeuserstatus', [UserController::class, 'changeUserStatus'])->name('changeuserstatus');

         //city
        Route::get('allcity', [CityController::class, 'index'])->name('allcity');
        Route::get('/addcity', [CityController::class, 'create'])->name('addcity');
        Route::post('/storecity', [CityController::class, 'storecity'])->name('storecity');
        Route::get('/editcity/{id}', [CityController::class, 'editcity'])->name('editcity');
        Route::post('/updatecity', [CityController::class, 'updatecity'])->name('updatecity');
        Route::post('/deletecity', [CityController::class, 'deletecity'])->name('deletecity');
        Route::post('/deleteallcity', [CityController::class, 'deleteallcity'])->name('deleteallcity');

        //Trips
        Route::get('alltrips', [TripController::class, 'index'])->name('alltrips');
        Route::get('/tripdetails/{id}', [TripController::class, 'tripdetails'])->name('tripdetails');

        //Cover image
        Route::get('coverimages', [CoverImageController::class, 'index'])->name('coverimages');
        Route::get('/addimage', [CoverImageController::class, 'create'])->name('addimage');
        //Route::post('/upload-image', [CoverImageController::class, 'uploadCropImage'])->name('uploadCropImage');
        Route::post('/upload-crop-image', [CoverImageController::class, 'uploadCropImage'])->name('croppie.upload-image');
        Route::post('/save-crop-image', [CoverImageController::class, 'saveImage'])->name('croppie.save-image');
        Route::post('/deleteCoverImage', [CoverImageController::class, 'deleteCoverImage'])->name('deleteCoverImage');
        Route::post('/deleteallimages', [CoverImageController::class, 'deleteallimages'])->name('deleteallimages');


        //Plan
        Route::get('allplans', [PlanController::class, 'index'])->name('allplans');
        Route::get('/addplan', [PlanController::class, 'create'])->name('addplan');
        Route::post('/storeplan', [PlanController::class, 'storeplan'])->name('storeplan');
        Route::get('/editplan/{id}', [PlanController::class, 'editplan'])->name('editplan');
        Route::post('/updateplan', [PlanController::class, 'updateplan'])->name('updateplan');
        Route::post('/deletePlan', [PlanController::class, 'deletePlan'])->name('deletePlan');
        Route::post('/deleteallplan', [PlanController::class, 'deleteallplan'])->name('deleteallplan');
        Route::post('/changePlanStatus', [PlanController::class, 'changePlanStatus'])->name('changePlanStatus');
        Route::get('purchasedPlans', [PurchasedPlanController::class, 'index'])->name('index');
       

        //Inquiry
        Route::get('allinquiries', [ContactController::class, 'index'])->name('allinquiries');
        Route::get('/inquryReply/{id}', [ContactController::class, 'inquryReply'])->name('inquryReply');
        Route::post('/updateinquiry', [ContactController::class, 'updateinquiry'])->name('updateinquiry');

        //FAQ
        Route::get('allfaqs', [FaqController::class, 'index'])->name('allfaqs');
        Route::get('addfaq', [FaqController::class, 'create'])->name('create');
        Route::post('/storefaq', [FaqController::class, 'store'])->name('store');
        Route::get('/editfaq/{id}', [FaqController::class, 'editfaq'])->name('editfaq');
        Route::post('/updatefaq', [FaqController::class, 'updatefaq'])->name('updatefaq');
        Route::post('/deletefaq', [FaqController::class, 'deletefaq'])->name('deletefaq');
        Route::post('/deleteallfaq', [FaqController::class, 'deleteallfaq'])->name('deleteallfaq');
        Route::post('/changeFaqStatus', [FaqController::class, 'changeFaqStatus'])->name('changeFaqStatus');

         //FAQ
         Route::get('youtubeurls', [YoutubeUrlController::class, 'index'])->name('youtubeurls');
         Route::post('/updateurl', [YoutubeUrlController::class, 'store'])->name('updateurl');
        
        
         
    });
});