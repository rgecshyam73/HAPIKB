<?php

namespace App\Http\Controllers;

use App\Http\Requests\PartnerAuthRequest;
use Illuminate\Http\Request;
use DB;

class DocController extends Controller
{
    public function index() {
        return redirect()->route('doc.functionname',['name'=>'register']);
    }

    public function login() {
        return view('doc.login');
    }

    public function processlogin(PartnerAuthRequest $request) {
        session(['opid'=>$request->operatorid]);
        return redirect()->route('doc.index');
    }

    public function showfunction($name) {
        $partner = DB::table('partner_web')->join('config_curr','config_curr.curr_id','partner_web.curr_id')
        ->where('web_id',session('opid'))
        ->selectRaw('partner_web.*, config_curr.code')
        ->first();
        
        $data = [
            'name'=>$name,
            'partner'=>$partner,
        ];
        return view('doc.function')->with($data);
    }

    public function logout() {
        session()->flush();
        return redirect()->route('doc.index');
    }

    public function testfunction(Request $request) {
        $url = url()->to('api/'.$request->name);
        $params = $request->params;
        $response = json_encode(array());

        if (!in_array($request->name,['register1','transfer1','updatePlayerSetting1'])) {
            $keyhash = DB::table('partner_web')->where('web_id',session('opid'))->first()->keyhash;
            if (!strstr($request->params,'&hash')) {
                $hash = hashParamsAPI($request->name,$request->params.'&keyhash='.$keyhash);
                $params = $request->params.'&hash='.$hash;
            }
            $response = curl_post($url,$params);
        }
        
        $data = array(
            'url' => $url,
            'params' => json_encode(explodeGetParams($params)),
            'reponse' => $response,
        );

        return response()->json($data);
    }
}