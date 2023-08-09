<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\BetDetailsRequest;
use App\Http\Requests\OutstandingBetDetailsRequest;
use App\Http\Requests\OutstandingBetRequest;
use App\Http\Requests\WinloseRequest;
use App\Repositories\IBetRepository;
use App\Http\Controllers\Controller;
use Jenssegers\Agent\Agent;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;

class BetController extends Controller
{
    //
    protected $agent;
    protected $betRepo;

    public function __construct(IBetRepository $betRepository)
    {
        $this->agent  = new Agent;
        $this->betRepo = $betRepository;
        LaravelLocalization::setLocale('en');
    }

    /**
     * @OA\Post(
     *     path="/api/v2/getBetDetails",
     *     summary="Bet Details",
     *     tags={"Bet API"},
     *
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                  @OA\Property(
     *                     property="operatorid",
     *                     type="integer"
     *                 ),
     *                 @OA\Property(
     *                     property="start_time",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="end_time",
     *                     type="string"
     *                 ),
     *                  @OA\Property(
     *                     property="hash",
     *                     type="string"
     *                 ),
     *                 example={"operatorid": 10066, "start_time": "2019-09-11", "end_time": "2019-09-11", "hash": "72ca307d16e7d91159ad6f797b95d0a2ea7aa4d6f836bf055c03f457dae88d71"},
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
    public function betDetails(BetDetailsRequest $request) {
        $webid      =   $request->operatorid;
        $starttime  =   $request->start_time;
        $endtime    =   $request->end_time;

        $dataResults = $this->betRepo->betDetails($webid, $starttime, $endtime);
        $code = 0;

        $data       = [
            'code'      =>  $code,
            'msg'       =>  (config('message.'.$code)),
            'data'      =>  $dataResults
        ];

        return response()->json($data);
    }

    /**
     * @OA\Post(
     *     path="/api/v2/getCurrentOutstandingBet",
     *     summary="Current Outstanding Bet",
     *     tags={"Bet API"},
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
     *                      property="game_id",
     *                      description="Game ID",
     *                      type="integer"
     *                 ),
     *                  @OA\Property(
     *                      property="hash",
     *                      description="HASHING with SHA 256 hash(sha256, operatorid . game_id . secret-key)",
     *                      type="string"
     *                 ),
     *                  example={"operatorid": 10066, "game_id": 201, "hash": "8e0ac2151c66e52178c7e6ee5e034d3d32f997a041bbca99df92ebe54bcef128"},
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
    public function outstandingBet(OutstandingBetRequest $request){
        $webid      =   $request->operatorid;
        $game       =   $request->get('game');

        $code = 0;
        $dataInvoice = $this->betRepo->OutstandingBet($webid, $game);

        $data = [
            'code'  =>  $code,
            'msg'   =>  (config('message.'.$code)),
            'data'  =>  $dataInvoice
        ];

        return response()->json($data);
    }

    /**
     * @OA\Post(
     *     path="/api/v2/getCurrentOutstandingBetDetail",
     *     summary="Current Outstanding Bet Details",
     *     tags={"Bet API"},
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
     *                      property="game_id",
     *                      description="Game ID",
     *                      type="integer"
     *                 ),
     *                @OA\Property(
     *                    property="subgame_id",
     *                    description="Sub Game ID",
     *                    type="integer"
     *                ),
 *                  @OA\Property(
 *                     property="hash",
 *                     description="HASHING with SHA 256 hash(sha256, operatorid . game_id . subgame_id . secret-key)",
 *                     type="string"
 *                 ),
     *                  example={"operatorid": 10066, "game_id": 201, "subgame_id": 1, "hash": "4abb27415e875171115236ba440d2142cfc5d446944000225096a018f6a36141"},
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
    public function outstandingBetDetails(OutstandingBetDetailsRequest $request){
        $webid      =   $request->operatorid;
        $gameid     =   $request->game_id;
        $subgameid  =   $request->subgame_id;
        $roomid     =   $request->room_id;
        $game       =   $request->get('game');

        $code = 0;

        $dataInvoiceDetails = $this->betRepo->OutstandingBetDetails($webid, $game, $gameid, $subgameid, $roomid);

        $data = [
            'code'  =>  $code,
            'msg'   =>  (config('message.'.$code)),
            'data'  =>  $dataInvoiceDetails
        ];

        return response()->json($data);
    }

    public function winlose(WinloseRequest $request)
    {
        $startdate  =   $request->start_date;
        $enddate    =   $request->end_date;
        $player     =   $request->get('player');

        $dataTurnover = [];
        $code   =   0;
        $userId =   $player->user_id;

        $dataTurnover = $this->betRepo->getWinLosePlayer($userId, $startdate, $enddate);

        $data       = [
            'code'      =>  $code,
            'msg'       =>  (config('message.'.$code)),
            'data'      =>  $dataTurnover
        ];

        return response()->json($data);
    }
}
