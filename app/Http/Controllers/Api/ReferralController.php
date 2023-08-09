<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\BonusReferralRequest;
use App\Http\Requests\DownlineRequest;
use App\Http\Requests\ReferralRequest;
use App\Http\Requests\TurnoverRequest;
use App\Models\Db1\Join_referral_com;
use App\Models\Db1\Join_transaction_day;
use App\Models\Db1\Partner_web;
use App\Models\Db1\User_id;
use App\Repositories\IReferralRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Jenssegers\Agent\Agent;

class ReferralController extends Controller
{
    //

    protected $referralRepo;
    protected $agent;

    public function __construct(IReferralRepository $referralRepository)
    {
        $this->referralRepo = $referralRepository;
        $this->agent  = new Agent;
    }

    /**
     * @OA\Post(
     *     path="/api/v2/getReferral",
     *     summary="Check Online Player",
     *     tags={"Referral API"},
     *
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                  @OA\Property(
     *                      property="operatorid",
     *                      description="Operator ID",
     *                      type="integer"
     *                 ),
     *                 @OA\Property(
     *                      property="username",
 *                          description="Username",
     *                      type="string"
     *                 ),
     *                 @OA\Property(
     *                      property="start_date",
     *                      description="Start Date with format Y-m-d",
     *                      type="string"
     *                 ),
     *                 @OA\Property(
     *                      property="end_date",
     *                      description="End Date with format Y-m-d",
     *                      type="string"
     *                 ),
     *                  @OA\Property(
     *                      property="hash",
     *                      description="HASHING with SHA 256 hash(sha256, operatorid . username . start_date . end_date . secret-key)",
     *                      type="string"
     *                 ),
     *                  example={"operatorid": 10066, "username": "NGA_ANAKEMAS2", "start_date": "2019-09-09", "end_date": "2019-09-11", "hash": "3c138d022652310f451509c67776b492d2557b26a9d113187756194b664d83f9"},
     *             )
     *          )
     *      ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="successful operation"
     *     ),
     * )
     */
    public function referral(ReferralRequest $request){
        $webid      =   $request->operatorid;
        $startdate  =   $request->start_date;
        $enddate    =   $request->end_date;
        $player     =   $request->get('player');


        $code   =   0;
        $userId =   $player->user_id;

        $data       = [
            'code'      =>  $code,
            'msg'       =>  (config('message.'.$code)),
            'data'      =>  $this->referralRepo->getReferral($webid, $startdate, $enddate, $userId)
        ];

        return response()->json($data);
    }

    /**
     * @OA\Post(
     *     path="/api/v2/getBonusReferral",
     *     summary="Get Bonus Referral",
     *     tags={"Referral API"},
     *
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                  @OA\Property(
     *                      property="operatorid",
     *                      description="Operator ID",
     *                      type="integer"
     *                 ),
     *                 @OA\Property(
     *                     property="username",
     *                     description="Username",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                      property="start_date",
     *                      description="Start Date with format Y-m-d",
     *                      type="string"
     *                 ),
     *                 @OA\Property(
     *                      property="end_date",
     *                      description="End Date with format Y-m-d",
     *                      type="string"
     *                 ),
     *                 @OA\Property(
     *                      property="status",
     *                      description="Status 0: Unpaid, 1: Paid",
     *                      type="integer"
     *                 ),
     *                  @OA\Property(
     *                      property="hash",
 *                          description="HASHING with SHA 256 hash(sha256, operatorid . username . start_date . end_date . status . secret-key)",
     *                      type="string"
     *                 ),
     *                  example={"operatorid": 10066, "username": "NGA_ANAKEMAS2", "start_date": "2019-09-09", "end_date": "2019-09-11", "status": 1, "hash": "96b70875ab6558b95821257b73bac96a1be4fe0d82c2e65accae7d625a4d68b0"},
     *             )
     *          )
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="successful operation"
     *     ),
     * )
     */
    public function getBonusReferral(BonusReferralRequest $request)
    {
        $webid      =   $request->operatorid;
        $username   =   $request->username;
        $startdate  =   $request->start_date;
        $enddate    =   $request->end_date;
        $status     =   $request->status;
        $player     =   $request->get('player');

        $code   =   0;
        $userId =   $player->user_id;

        $data       = [
            'code'      =>  $code,
            'msg'       =>  (config('message.'.$code)),
            'data'      =>  $this->referralRepo->getBonusReferral($webid, $startdate, $enddate, $status, $userId)
        ];

        return response()->json($data);
    }

    /**
     * @OA\Post(
     *     path="/api/v2/getReferralPerDay",
     *     summary="Get Daily Referral",
     *     tags={"Referral API"},
     *
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                  @OA\Property(
     *                      property="operatorid",
     *                      description="Operator ID",
     *                      type="integer"
     *                 ),
     *                 @OA\Property(
     *                      property="username",
     *                      description="Username",
     *                      type="string"
     *                 ),
     *                 @OA\Property(
     *                      property="start_date",
     *                      description="Start Date with format Y-m-d",
     *                      type="string"
     *                 ),
     *                 @OA\Property(
     *                      property="end_date",
     *                      description="End Date with format Y-m-d",
     *                      type="string"
     *                 ),
     *                  @OA\Property(
     *                      property="hash",
     *                      description="HASHING with SHA 256 hash(sha256, operatorid . username . start_date . end_date . secret-key)",
     *                      type="string"
     *                 ),
     *                  example={"operatorid": 10066, "username": "NGA_ANAKEMAS2", "start_date": "2019-09-09", "end_date": "2019-09-11", "hash": "3c138d022652310f451509c67776b492d2557b26a9d113187756194b664d83f9"},
     *             )
     *          )
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="successful operation"
     *     ),
     * )
     */
    public function getReferralPerDay(ReferralRequest $request){
        $webid      =   $request->operatorid;
        $username   =   $request->username;
        $startdate  =   $request->start_date;
        $enddate    =   $request->end_date;
        $hash       =   $request->hash;
        $player     =   $request->get('player');

        $code   =   "0";
        $userId =   $player->user_id;

        $data       = [
            'code'      =>  $code,
            'msg'       =>  (config('message.'.$code)),
            'data'      =>  $this->referralRepo->getDailyReferral($webid, $startdate, $enddate, $userId)
        ];

        return response()->json($data);
    }

    /**
     * @OA\Post(
     *     path="/api/v2/getDownline",
     *     summary="Get Downline",
     *     tags={"Referral API"},
     *
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                  @OA\Property(
     *                      property="operatorid",
     *                      description="Operator ID",
     *                      type="integer"
     *                 ),
     *                 @OA\Property(
     *                      property="username",
     *                      description="Username",
     *                      type="string"
     *                 ),
     *                  @OA\Property(
     *                      property="hash",
     *                      description="HASHING with SHA 256 hash(sha256, operatorid . username . secret-key)",
     *                      type="string"
     *                 ),
     *                  example={"operatorid": 10066, "username": "NGA_ANAKEMAS2", "hash": "1f763342f1c4d5931ef9369de05c60a0d95f6a6c9df04ac26eaf2c3424748191"},
     *             )
     *          )
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="successful operation"
     *     ),
     * )
     */
    public function getDownline(DownlineRequest $request)
    {
        $player     =   $request->get('player');

        $code   =   0;

        $data       = [
            'code'      =>  $code,
            'msg'       =>  (config('message.'.$code)),
            'data'      =>  $this->referralRepo->getDownline($player->user_id)
        ];

        return response()->json($data);
    }

    /**
     * @OA\Post(
     *     path="/api/v2/getTurnover",
     *     summary="Get Turnover",
     *     tags={"Referral API"},
     *
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                  @OA\Property(
     *                      property="operatorid",
     *                      description="Operator ID",
     *                      type="integer"
     *                 ),
     *                 @OA\Property(
     *                      property="username",
     *                      description="Username",
     *                      type="string"
     *                 ),
     *                 @OA\Property(
     *                      property="start_date",
     *                      description="Start Date with format Y-m-d",
     *                      type="string"
     *                 ),
     *                 @OA\Property(
     *                      property="end_date",
     *                      description="End Date with format Y-m-d",
     *                      type="string"
     *                 ),
     *                  @OA\Property(
     *                      property="hash",
     *                      description="HASHING with SHA 256 hash(sha256, operatorid . username . start_date . end_date . secret-key)",
     *                      type="string"
     *                 ),
     *                  example={"operatorid": 10066, "username": "NGA_ANAKEMAS2", "start_date": "2019-09-09", "end_date": "2019-09-11", "hash": "3c138d022652310f451509c67776b492d2557b26a9d113187756194b664d83f9"},
     *             )
     *          )
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="successful operation"
     *     ),
     * )
     */
    public function turnover(TurnoverRequest $request){
        $startdate  =   $request->start_date;
        $enddate    =   $request->end_date;
        $player     =   $request->get('player');
        
        $code   =   0;
        $userId =   $player->user_id;

        $data       = [
            'code'      =>  $code,
            'msg'       =>  (config('message.'.$code)),
            'data'      =>  $this->referralRepo->getTurnover($userId, $startdate, $enddate)
        ];

        return response()->json($data);
    }
}
