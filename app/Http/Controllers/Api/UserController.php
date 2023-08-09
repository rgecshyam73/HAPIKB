<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\CheckBalanceRequest;
use App\Http\Requests\CountOnlinePlayerRequest;
use App\Http\Requests\IsOnlinePlayerRequest;
use App\Http\Requests\LobbyRequest;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\TestRequest;
use App\Http\Requests\TotalBalancePlayerRequest;
use App\Http\Requests\UpdatePlayerDataRequest;
use App\Http\Requests\GetTokenRequest;
use App\Models\Db1\Adm_config;
use App\Models\Db1\Config_bet_type;
use App\Models\Db1\Hkb_token;
use App\Models\Db1\Log_player;
use App\Models\Db1\Partner_web;
use App\Models\Db1\User_active;
use App\Models\Db1\User_coin;
use App\Models\Db1\User_id;
use App\Models\Db1\User_log_type;
use App\Models\Views\ViewUser;
use App\Repositories\IUserRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Jenssegers\Agent\Agent;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;
use DB;
use Cache;
/**
 * @OA\Info(
 *      title="HKB Game API",
 *      description="This is a sample server HKB API server. For this sample, you can use the api key `secret-key` to test the authorization filters. <a class='btn btn-danger' href='/api/auth/logout'>Logout</a>",
 *      version="2.0.0",
 *
 *
 *
 *
 * )
 */
class UserController extends Controller
{
    protected $agent;
    protected $userRepo;

    public function __construct(IUserRepository $userRepo)
    {
        $this->agent  = new Agent;
        $this->userRepo = $userRepo;
        LaravelLocalization::setLocale('en');
    }

    /**
     * @OA\Post(
     *     path="/api/v2/lobby",
     *     summary="Lobby",
     *     tags={"Lobby API"},
     *
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 @OA\Property(
     *                     property="operatorid",
     *                     description="Operator ID",
     *                     type="integer"
     *                 ),
     *                 @OA\Property(
     *                     property="username",
     *                     description="Username with a Partner Web Prefix",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="token",
     *                     description="user token",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="language",
     *                     description="Prefered Language EN | ID",
     *                     type="string"
     *                 ),
     *                 example={"operatorid": 10066, "username": "NGA_ANAKEMAS2", "token": "ZvRisa3pNP01OPksSjIp54X1hTQlYZCQGZcHcUKm", "language": "en"}
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="successful operation"
     *     ),
     * )
     */
    public function lobby(LobbyRequest $request) {
        $code = 0;

        $data = [
            'code'  =>  $code,
            'msg'   =>  (config('message.'.$code))
        ];

        return response()->json($data);
    }

    /**
     * @OA\Post(
     *     path="/api/v2/register",
     *     summary="Register User",
     *     tags={"Lobby API"},
     *
     *     @OA\Response(
     *         response=200,
     *         description="successful operation"
     *     ),
     * )
     */
    public function register(RegisterRequest $request) {
        $webid      = $request->operatorid;
        $username   = $request->username;
        $fullname   = $request->fullname;
        $referral   = $request->referral;
        $email      = $request->email;
        $dataUser   = $this->userRepo->getUserByUsername($username);
        $cfg_curr   = $request->get('currenc');
        $cfg_lang   = $request->get('lang');

        $userId = @$dataUser->user_id;
        $curr_id = $cfg_curr->curr_id;
        $lang_id = $cfg_lang->lang_id;
        $code   = 0;

        if(($referral=="" || $referral=="null" || $referral==" ") && !$userId){
            $this->userRepo->setRegisterUser($username,"user",$webid,$curr_id,$lang_id,$fullname,$email);
        } elseif ($referral) {
            $referralId = $this->userRepo->getValueFromUserByUsername($referral);
            $userStatus = @$dataUser->status;
            if ($userStatus==3) {
                $this->userRepo->setStatusUserToActive($userId, $fullname, $email);
            }

            if (!$referralId && !$userId) {
                // daftar referral
                $userIdParent   = $this->userRepo->setRegisterUser($referral,"partner",$webid,$curr_id,$lang_id,"","");
                // daftar user
                $this->userRepo->setRegisterUser($username,$userIdParent,$webid,$curr_id,$lang_id,$fullname,$email);
            } elseif (!$referralId && $userId) {
                // daftar referral
                $userIdParent   = $this->userRepo->setRegisterUser($referral,"partner",$webid,$curr_id,$lang_id,"","");

                $this->userRepo->setReferralToUser($userId, $userIdParent);
            } elseif ($referralId && !$userId) {
                // daftar user
                $this->userRepo->setRegisterUser($username,$referralId,$webid,$curr_id,$lang_id,$fullname,$email);
            } else {
                if ($userStatus==3) {
                    $this->userRepo->setReferralToUserWithoutStatus($userId, $referralId);
                } else {
                    $code   = 312;
                }
            }
        } else {
            $dataUser->refresh();
            $userStatus = $dataUser->status;
            if ($userStatus==3) {
                $this->userRepo->setStatusUserToActive($userId, $fullname, $email);
            } else {
                $code   = 312;
            }
        }

        $data = [
            'code'  =>  $code,
            'msg'   =>  (config('message.'.$code))
        ];

        return response()->json($data);
    }

    public function balance(CheckBalanceRequest $request) {
        $username   = $request->username;
        $player     = $request->get('player');
        $blockTfOut = $request->get('block_transfer_out');
        if ($player->status == 3) {
            $code   = 317;
        } elseif ($player->status==1) {
            $lastTransaction = $this->userRepo->getAmountLastTransactionBalance($player->user_id);
            if ($player->balance!=$lastTransaction) {
                $code   = 340;
            } elseif ($player->valsatu !== hash('sha256',$player->user_id.$player->balance)) {
                $code   = 0;//340;
            } else {
                $code   = 0;
            }
        } else {
            $userBlock = $this->userRepo->checkIsUserBlock($player->user_id);
            if ($userBlock != NULL) {
                if ($player->status==2 && $userBlock == 0 || $player->status==0 && $userBlock == 0) {
                    $code            = 0;
                    $player->balance = 0; 
                    $player->status  = 1;
                } else {
                    $code   = 0;
                }
            } else {
                $code   = 0;
            }
        }

        if($blockTfOut) {
            $code    = 0;
            $balance = 0;
        }  else {
            $balance = ($player->balance);
        }

        $outstanding_balance = $this->userRepo->getUserOutstandingBalance($player->user_id);
        $data = array(
            'balance'               => ($balance),
            'outstanding_balance'   => ($outstanding_balance),
            'currency'              => ($player->currency),
            'username'              => ($username),
            'nickname'              => ($player->nickname),
            'fullname'              => ($player->fullname),
            'statususer'            => ($player->status),
            'code'                  => ($code),
            'msg'                   => (config('message.'.$code))
        );
        
        return response()->json($data);
    }

    public function getAllPlayerBalance(TotalBalancePlayerRequest $request){
        $webid          = $request->operatorid;

        $data           = array();
        $totalBalance   = 0;
        $code   =   0;

        $totalBalance   = ViewUser::where('web_id',$webid)->sum('balance');

        $data       = [
            'code'      =>  $code,
            'msg'       =>  (config('message.'.$code)),
            'amount'    =>  ($totalBalance)
        ];

        return response()->json($data);
    }

    public function getOnlinePlayerCount(CountOnlinePlayerRequest $request){
        $webid          = $request->operatorid;
        $channel        = $request->device;

        $data           = array();
        $totalUser      = 0;
        $code           = 0;

        $dataUser       = ['game_id'=>0,'web_id'=>$webid];
        if ($channel) {
            $dataUser['channel'] = $channel;
        }

        $totalUser      = User_active::where($dataUser)
        ->join('user as ui','ui.user_id','=','user_active.user_id')
        ->count();

        $data       = [
            'code'      =>  $code,
            'msg'       =>  (config('message.'.$code)),
            'total'     =>  $totalUser
        ];

        return response()->json($data);
    }

    public function checkPlayerIsOnline(IsOnlinePlayerRequest $request){
        $player         = $request->get('player');

        $data           = array();
        $code           = 0;
        $online         = 'Offline';

        $status         = User_active::where('user_id',$player->user_id)->first()->count();

        if ($status>0) {
            $online     = 'Online';
        }

        $data = [
            'code'      =>  $code,
            'msg'       =>  (config('message.'.$code)),
            'status'    =>  $online
        ];

        return response()->json($data);
    }

    public function updatePlayerSetting(UpdatePlayerDataRequest $request){
        $webid          = $request->operatorid;
        $fullname       = $request->fullname;
        $referral       = $request->referral;
        $email          = $request->email;
        $player         = $request->get('player');
        $cfg_lang       = $request->get('lang');
        $data           = array();
        $updateData     = array();

        $code   =   0;

        $lang_id    = $updateData['lang_id'] = $cfg_lang->lang_id;
        $ref_id     = User_id::where(['web_id'=>$webid,'user_name'=>$referral])->value('user_id');
        $user_id    = $player->user_id;

        if (isset($fullname)) {
            $updateData['fullname'] = $player->fullname.",".$fullname;
        }

        if (isset($email)) {
            $updateData['email'] = $email;
        }

        if (isset($referral)) {
            $updateData['ref_id'] = $ref_id;
        }

        if (!empty($updateData)) {
            User_id::where('user_id',$user_id)->update($updateData);
        }

        $data       = [
            'code'      =>  $code,
            'msg'       =>  (config('message.'.$code)),
        ];

        return response()->json($data);
    }

    public function getToken(GetTokenRequest $request){
        $webid          = $request->operatorid;
        $username       = $request->username;
        $client_token   = $request->token ?: 0;
        $timestamp      = $request->timestamp;
        $data           = array();

        $code   =   0;
        $updated =  0;
        $token  =   hash('sha256',bcrypt('HKB-API' . $webid . $username . $client_token . $timestamp . '88'));
        $hashUserAgent = hash('sha256',($_SERVER['HTTP_USER_AGENT']));
        $getExistsToken = Hkb_token::on('mysql::write')->where(['web_id'=>$webid,'username'=>$username])->first(['token']);

        if ($getExistsToken) {
            $token =  $getExistsToken->token;
            $updated =  1;
        } else {
            try { 
                $updated = @Hkb_token::insert([
                    'datetime'      =>  DB::raw('NOW()'),
                    'web_id'        =>  $webid,
                    'username'      =>  $username,
                    'token'         =>  $token,
                    'client_token'  =>  $client_token,
                    'agent'         =>  $hashUserAgent
                ]);
            } catch (\Exception $e) {
                // DO NOTHING
            }
        }

        if (!$updated) {
            $code   =   318;
            $token  =   0;
        }

        $data       = [
            'code'  =>  $code,
            'msg'   =>  (config('message.'.$code)),
            'gt'    =>  $token
        ];

        return response()->json($data);
    }

    /**
     * @OA\Get(
     *     path="/api/v2/logout",
     *     summary="Logout User",
     *     tags={"Lobby API"},
     *
     *     @OA\Response(
     *         response=200,
     *         description="successful operation"
     *     ),
     * )
     */
    public function logout() {
        $this->userRepo->logout();
    }

    public function error(){
        return response()->view('errors.444', ['message' => session()->get('error')], 444);
    }

    public function test(TestRequest $request){
        // dump(date('Y-m-d G:i:s'));
        // dump(strtotime(date('Y-m-d G:i:s')));
        // dump(strtotime("2020-10-29 17:12:00"));
        // dump(strtotime(date('Y-m-d G:i:s')) - strtotime("2020-10-29 17:12:00"));
        // dd(getShioNew(1,"2023-01-23 00:01:01"));
        $code = 0;
        $data       = [
            'code'      =>  $code,
            'msg'       =>  (config('message.'.$code)),
            'user_agent' => hash('sha256',($_SERVER['HTTP_USER_AGENT'])),
            'ip'        => getIP()
            // 'chinese_new_year' => getChineseNewYearDate(date('Y')),
            // 'testshio' => Config_bet_type::where('value',getShioNew(1,"2023-01-22 00:01:01"))->value('name')
        ];

        return response()->json($data);
    }

    public function testlbcf(TestRequest $request)
    {
        $code = 0;
        $data       = [
            'code'          =>  $code,
            'msg'           =>  (config('message.'.$code)),
            'maintenance'   =>  Adm_config::find(Adm_config::API_STATUS)->value,
        ];

        return response()->json($data);
    }

    public function testhash(TestRequest $request) {
        $hash = hash('sha256', $request->operatorid .  $request->username . $request->token . $request->timestamp . '543a7b542a1f54d26ead2236b495b640');

        $data = [
            'raw' => $request->operatorid .  $request->username . $request->token . $request->timestamp . '543a7b542a1f54d26ead2236b495b640',
            'hash' => $hash
        ];

        return response()->json($data);
    }

    public function clearCache(Request $request)
    {
        $tag = $request->tag;
        Cache::tags($tag)->flush();
    }

    public function show_apk(Request $request)
    {
        //echo "r u here shyam pandey";
        //dd(ViewUser::all());
        $data = [
            "status" => 1,
            "data" =>$request
        ];
        //$code = 0;

        // $data = [
        //     'code'  =>  $code,
        //     'msg'   =>  (config('message.'.$code))
        // ];

        return response()->json($data);
    }


}
