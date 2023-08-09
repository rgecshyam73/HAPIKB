<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\PartnerAuthRequest;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ApiAuthController extends Controller
{
    //
    public function index(){
        return redirect()->route('api.login.v2');
    }

    public function login() {
        return view('api.api_auth_controller.login');
    }

    public function doLogin(PartnerAuthRequest $request) {
        session(['opid'=>$request->operatorid]);
        return redirect(config('l5-swagger.routes.api'));
    }

   
 
    public function doLogin_apk(PartnerAuthRequest $request) {
        // session(['opid'=>$request->operatorid]);
        // return redirect(config('l5-swagger.routes.api'));

        dd($request);
       // return echo "here.....";
    }

    public function logout() {
        session()->flush();
        return redirect()->route('api.login.v2');
    }

    public function notfound() {
        return abort(404);
    }
}
