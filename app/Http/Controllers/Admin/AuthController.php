<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use DB;
use Illuminate\Support\Facades\Hash;
use Session;
use Mail;
use App\Http\Requests\LoginFormDataRequest;
use App\Http\Requests\ForgotFormDataRequest;
use App\Http\Requests\ChangepassFormDataRequest;
use Illuminate\Support\Facades\Validator;
use SecureString;


class AuthController extends Controller
{



    /**
     * This function is used to display the signin page.
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function signin()
    {
        return view('Admin.signin');
    }


    /**
     * This function is used to handle the admin login
     * @param LoginFormDataRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function login(LoginFormDataRequest $request)
    {

        $validator = $request->all();

        $email = $request->email;
        $password = $request->password;


        $userList = array(
            'email' => $email,
            'password' => $password,

        );
        $token = Str::random(20);
        $userData = DB::table('mainadmin')->where(['email' => $email])->first();


        if ($userData) {
            if ($userData->IsActive == 0) {
                return redirect('/')->with('Fail', 'Your account is deactivated, Kindly contact to administrator');
            }
            //echo "Yes"; exit;
            if (Hash::check($password, $userData->password)) {

                Session::put('name', $userData->name);
                Session::put('email', $email);
                Session::put('id', $userData->id);
                $userToken = array(
                    'authToken' => $token,
                    'updated_at' => date('Y-m-d H:i:s'),
                );
                DB::table('mainadmin')->where(['email' => $userData->email])->update($userToken);
                $InsertAuthToken = array(
                    'MainAdminId' => $userData->id,
                    'AuthToken' => $token,
                );
                DB::table('useraccesstoken')->insert($InsertAuthToken);
                $loggedUserAuth = Session::put('auth', $token);


                return redirect('/dashboard')->with('success', 'Thank you for contacting us!');
            } else {

                return redirect('/')->with('Fail', 'Email/Password is incorrect.');
            }
        } else {

            return redirect('/')->with('adminEmail', 'Email/Password is incorrect.');
        }
    }
    // Close


    /**
     * Logs out the user.
     *
     * This function is used to remove the users authentication token from the
     * database and to flush the session.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function logout()
    {
        $email = Session::get('email');
        $id = Session::get('id');

        $userLogout = array(
            'authToken' => "",
        );
        $updateData = DB::table('mainadmin')->where(['email' => $email])->update($userLogout);

        //update user access token to IsActive 0
        $useraccessToken = array(
            'IsActive' => 0,
        );

        $updateData = DB::table('useraccesstoken')->where('MainAdminId', $id)->update($useraccessToken);

        Session::flush();

        return redirect('/');
    }


    /**
     * Shows the forgot password form.
     *
     * This function is used to display the forgot password page to the user.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function forgotemail()
    {

        return view('Admin.forgotpassword');
    }


    /**
     * Sends the forgot password email.
     *
     * This function is used to send the forgot password email to the user.
     * It validates the email address and checks if the email address exists in the database.
     * If the email address exists then it generates a token and inserts it into the mainadminforgottoken table.
     * Then it sends an email to the user with the token.
     *
     * @param Request $request The request object.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postEmail(Request $request)
    {

        $validator = $request->validate(
            [
                'email' => 'required|regex:/^([a-z0-9\+_\-]+)(\.[a-z0-9\+_\-]+)*@([a-z0-9\-]+\.)+[a-z]{2,6}$/ix',
            ],
            [
                'email.required' => 'Please enter email',
                'email.regex' => 'Please enter valid email',
            ]
        );

        $email = $request->email;
        $emailData = DB::table('mainadmin')->where(['email' => $email])->first();
        if ($emailData) {


            $token = Str::random(16);

            $adminForgotToken = array(

                "AdminId" => $emailData->id,
                "Token" => $token
            );

            DB::table('mainadminforgottoken')->insert($adminForgotToken);

            Mail::send('Admin.emails.forgotemail', ['token' => $adminForgotToken['Token']], function ($message) use ($request) {
                $message->to($request->email);
                $message->subject('Reset Password Notification');
            });


            return redirect('forgotemail')->with('message', 'We have e-mailed your password reset link!');
        } else {
            return redirect('forgotemail')->with('failed', 'Email not found');
        }
    }


    /**
     * This function is used to reset password.
     * It validates the token and checks if the token exists in the mainadminforgottoken table.
     * If the token exists then it returns the reset password page.
     * If the token does not exists then it shows the token expired page.
     *
     * @param string $token The token which is used to reset the password.
     *
     * @return \Illuminate\Http\Response
     */
    public function resetpassword($token)
    {

        //echo $token; exit;
        $updatePassword = DB::table('mainadminforgottoken')->where('Token', $token)->where('IsExpired', false)->get();
        if (!$updatePassword->isEmpty()) {
            return view('Admin.resetpassword', ['token' => $token]);
        } else {
            return view('Admin.tokenexpired');
        }
    }


    /**
     * Resets the password of the user.
     *
     * This function is used to reset the password of the user.
     * It takes a token as an input and validates it.
     * If the token is valid then it updates the password of the user.
     * If the token is invalid then it shows the token expired page.
     *
     * @param ForgotFormDataRequest $request The request object.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function resetpostPassword(ForgotFormDataRequest $request)
    {
        $validator = $request->all();

        $adminToken = $request->token;

        $adminForgotTokenData = DB::table('mainadminforgottoken')
            ->where('Token', $adminToken)
            ->where('IsExpired', false)
            ->first();

        if ($adminForgotTokenData) {
            $userPass = array(
                'password' => Hash::make($request->newpassword)
            );

            $emailData = DB::table('mainadmin')->where('id', $adminForgotTokenData->AdminId)->update($userPass);


            $adminForgotToken = array(
                "IsExpired" => true
            );

            $insertAdminData = DB::table('mainadminforgottoken')->where('AdminId', $adminForgotTokenData->AdminId)->update($adminForgotToken);


            return redirect('/')->with('password', 'Your password has been changed!');
        } else {
            return back()->with('failed', 'Link is expired');
        }
    }

    /**
     * This function is used to show the change password page.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function changePassword()
    {
        return view('Admin.changepassword');
    }


    /**
     * Updates the user's password.
     *
     * This function validates the current password and updates it with a new password if valid.
     * It checks if the old password matches the stored password, and if so, updates the password.
     * If the password is successfully updated, it redirects with a success message.
     * Otherwise, it redirects with an appropriate error message.
     *
     * @param ChangepassFormDataRequest $request The request object containing password data.
     * @return \Illuminate\Http\RedirectResponse
     */

    public function updatePassword(ChangepassFormDataRequest $request)
    {
        $loggedUserId = Session::get('email');
        $validation = $request->all();
        if (!$validation) {
            return redirect('/changepassword');
        } else {

            $oPassword = $request->opassword;
            $nPassword = $request->npassword;
            $cPassword = $request->cpassword;
            $userData = DB::table('mainadmin')->where(['email' => $loggedUserId])->first();

            $password = $userData->password;
            $userpassdata = [
                'password' => Hash::make($nPassword)
            ];

            if (Hash::check($oPassword, $password)) {
                $userInfo = DB::table('mainadmin')->where(['email' => $loggedUserId])->update($userpassdata);
                if ($userInfo) {
                    return redirect('/changepassword')->with('success', 'Password updated successfully.');
                } else {
                    return redirect('/changepassword')->with('msg', 'Password does not updated.');
                }
            } else {
                return redirect('/changepassword')->with('msg', 'Old password does not match.');
            }
        }
    }
}
