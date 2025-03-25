<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use DB;

class EnsureToken
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        //return $next($request);
        $auth = $request->header('auth');
        // echo $auth; 
        $checkToken = DB::table('tbl_auth_token')->where("auth_token", $auth)->count();
        //print_r($checkToken);
        //exit;
        if ($auth == "" || $checkToken == 0) {
            if ($auth == "") {
                $massage = "Authorization token is required";
            } else {
                $massage = "Token is mismatched";
            }
            return response()
                ->json([
                    'data' => json_decode("{}"), 'meta' => array(
                        'authToken' => "",
                        'success' => false,
                        "message" => $massage,
                    ),

                ], 401);
        } else {
            return $next($request);
        }
    }
}