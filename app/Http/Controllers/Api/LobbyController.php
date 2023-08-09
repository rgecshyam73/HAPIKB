<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\LobbyRequest;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\TestRequest;
use App\Models\Db1\Log_player;
use App\Models\Db1\Partner_web;
use App\Models\Db1\User_active;
use App\Models\Db1\User_coin;
use App\Models\Db1\User_id;
use App\Models\Db1\User_log_type;
use App\Models\Views\ViewUser;
use App\Repositories\ILobbyRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Jenssegers\Agent\Agent;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;

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
class LobbyController extends Controller
{
    protected $agent;
    protected $lobbyRepo;

    public function __construct(ILobbyRepository $lobbyRepo)
    {
        $this->agent  = new Agent;
        $this->lobbyRepo = $lobbyRepo;
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
        $partner    = $request->get('partner');

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
        $currency   = $request->currency;
        $lang       = $request->language;
        $fullname   = $request->fullname;
        $referral   = $request->referral;
        $email      = $request->email;
        $hash       = $request->hash;
        $dataUser   = $this->lobbyRepo->getUserByUsername($username);
        $cfg_curr   = $request->get('currenc');
        $cfg_lang   = $request->get('lang');

        $userId = @$dataUser->user_id;
        $curr_id = $cfg_curr->curr_id;
        $lang_id = $cfg_lang->lang_id;
        $code   = 0;

        if(($referral=="" || $referral=="null" || $referral==" ") && !$userId){
            $insertUser     = $this->lobbyRepo->setRegisterUser($username,"user",$webid,$curr_id,$lang_id,$fullname,$email);
        } elseif ($referral) {
            $referralId = $this->lobbyRepo->getUserByUsername($referral);
            $userStatus = @$dataUser->status;
            if ($userStatus==3) {
                User_id::where('user_id',$userId)->update(['status'=>1]);
            }

            if (!$referralId && !$userId) {
                // daftar referral
                $userIdParent   = $this->lobbyRepo->setRegisterUser($referral,"partner",$webid,$curr_id,$lang_id,"","");
                // daftar user
                $insertUser     = $this->lobbyRepo->setRegisterUser($username,$userIdParent,$webid,$curr_id,$lang_id,$fullname,$email);
            } elseif (!$referralId && $userId) {
                // daftar referral
                $userIdParent   = $this->lobbyRepo->setRegisterUser($referral,"partner",$webid,$curr_id,$lang_id,"","");

                $this->lobbyRepo->setReferralToUser($userId, $userIdParent);
            } elseif ($referralId && !$userId) {
                // daftar user
                $insertUser     = $this->lobbyRepo->setRegisterUser($username,$referralId,$webid,$curr_id,$lang_id,$fullname,$email);
            } else {
                if ($userStatus==3) {
                    $this->lobbyRepo->setReferralToUserWithoutStatus($userId, $referralId);
                } else {
                    $code   = 312;
                }
            }
        } else {
            $dataUser->refresh();
            $userStatus = $dataUser->status;
            if ($userStatus==3) {
                User_id::where('user_id',$userId)->update(['status'=>1]);
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
        $this->lobbyRepo->logout();
    }

    public function error(){
        return response()->view('errors.444', ['message' => session()->get('error')], 444);
    }

    public function test(TestRequest $request){
        $data       = [
            'code'      =>  0,
            'msg'       =>  'Success'
        ];

        return response()->json($data);
    }
}
