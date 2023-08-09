<?php

namespace App\Http\Controllers\Api;

use DB;
use App\Http\Requests\CountOnlinePlayerRequest;
use App\Http\Requests\GetTableNameRequest;
use App\Http\Requests\IsOnlinePlayerRequest;
use App\Http\Requests\MarketTimeRequest;
use App\Http\Requests\NumberDetailsRequest;
use App\Http\Requests\NumberResultsRequest;
use App\Http\Requests\AllNumberResultsRequest;
use App\Http\Requests\TotalBalancePlayerRequest;
use App\Http\Requests\TotalJackpotAmountRequest;
use App\Http\Requests\UpdatePlayerDataRequest;
use App\Models\Db1\Config_bet_type;
use App\Models\Db1\Config_game;
use App\Models\Db1\Config_subgame;
use App\Models\Db1\Dd_number;
use App\Models\Db1\Ddc_limit;
use App\Models\Db1\Ddc_room;
use App\Models\Db1\Game_table;
use App\Models\Db1\Join_langtable;
use App\Models\Db1\Ttg_closedtime;
use App\Models\Db1\Txh_table;
use App\Models\Db1\User_active;
use App\Models\Db1\User_id;
use App\Models\Views\ViewUser;
use App\Models\Views\ViewPartnerGame;
use App\Repositories\IGameRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\GameTurnoverRequest;
use App\Models\Db1\Join_referral_com;
use Illuminate\Support\Facades\Cache;


class GameController extends Controller
{
    //
    protected $gameRepo;

    public function __construct(IGameRepository $gameRepository)
    {
        $this->gameRepo = $gameRepository;
    }

    /**
     * @OA\Post(
     *     path="/api/v2/getJackpot",
     *     summary="Get Jackpot",
     *     tags={"Game API"},
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
     *                     property="currency",
     *                     description="Currency can be idr",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="hash",
     *                     description="HASHING with SHA 256 hash(sha256, operatorid . username . currency . secret-key)",
     *                     type="string"
     *                 ),
     *                 example={"operatorid": 10066, "username": "NGA_ANAKEMAS2", "currency": "idr", "hash": "b6dede2dee47de03171afbfabea005d3d3d28de967b4b3f41ec1d6b07a5b5560"}
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="successful operation",
     *          @OA\MediaType(
     *              mediaType="application/json",
     *              @OA\Schema(
     *                 @OA\Property(
     *                     property="code",
     *                     type="integer"
     *                 ),
     *                 @OA\Property(
     *                     property="msg",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="amount",
     *                     type="integer"
     *                 ),
     *                  example={"code": 0,"msg": "Success","amount": 1000}
     *              )
     *          )
     *     ),
     * )
     */
    public function jackpot(TotalJackpotAmountRequest $request) {
        $allSqltable = Config_game::where(['type'=>Config_game::TYPE_CARDGAME,'jackpot_type'=>1])->select('sqltable','conversion')->get();
        $allJackpot = 0;
        foreach ($allSqltable as $data) {
            $sumKJack = Txh_table::from($data['sqltable'].'_table')->sum('jackpot');
            $allJackpot += $sumKJack;
        }

        $code   = 0;
        $amount = $allJackpot;

        $data = array(
            'code'      => $code,
            'msg'       => (config('message.'.$code)),
            'amount'    => ($amount)
        );

        return response()->json($data);
    }

    /**
     * @OA\Post(
     *     path="/api/v2/getNumberResults",
     *     summary="Get Number Results",
     *     tags={"Game API"},
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
     *                     property="type_id",
     *                     description="Type ID",
     *                     type="integer"
     *                 ),
     *                 @OA\Property(
     *                     property="game_id",
     *                     description="Game ID",
     *                     type="integer"
     *                 ),
     *                 @OA\Property(
     *                     property="room_id",
     *                     description="ROOM ID",
     *                     type="integer"
     *                 ),
     *                 @OA\Property(
     *                     property="length",
     *                     description="Length of number Row",
     *                     type="integer"
     *                 ),
     *                 @OA\Property(
     *                      property="hash",
     *                      description="HASHING with SHA 256 hash(sha256, operatorid . type_id . game_id . room_id . length . secret-key)",
     *                      type="string"
     *                 ),
     *                 example={"operatorid": 10066, "type_id": 5, "game_id": 201, "room_id": 1, "length": 10, "hash": "115faeafd0d93d6f8f37b716f4841942cfbcdbfe33a8e3a1473675a0473bc317"}
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
    public function numberResults(NumberResultsRequest $request) {
        $webid          = $request->operatorid;
        $typeid         = $request->type_id;
        $gameid         = $request->game_id;
        $room           = $request->room_id;
        $length         = $request->length ?: 10;
        $listGame       = $request->get('listGame');
        $data           = $dataku = array();

        $code   = 0;
        $ca = 0;

        foreach ($listGame as $lgame) {
            $table = $lgame['sqltable'];
            if ($gameid!="") {
                if ($typeid!=Config_game::TYPE_DINGDONG) {
                    $listRoom = [['room_id'=>1]];
                } elseif ($room!="") {
                    $listRoom = Ddc_room::where(['game_id'=>$gameid,'room_id'=>$room])->get();
                } else {
                    $listRoom = Ddc_room::where('game_id',$gameid)
                    ->leftjoin('ddc_room_share as drs',function($query){
                        return $query->on('drs.room_id','=','ddc_room.room_id');
                    })
                    ->where('status',1)
                    ->where(function($query) use($webid) {
                        $query->where('ddc_room.web_id',0)
                            ->orwhere('ddc_room.web_id',$webid)
                            ->orwhere('drs.web_id',$webid);
                    })
                    ->selectRaw('ddc_room.*')
                    ->get();
                }
            } else {
                if ($typeid!=Config_game::TYPE_DINGDONG) {
                    $listRoom = [['room_id'=>1]];
                } elseif ($room!="") {
                    $roomidx    =   Ddc_room::where('game_id',$lgame['game_id'])->value('room_id');
                    $listRoom = [['room_id'=>$roomidx]];
                } else {
                    $listRoom = Ddc_room::where('game_id',$lgame['game_id'])
                    ->leftjoin('ddc_room_share as drs',function($query){
                        return $query->on('drs.room_id','=','ddc_room.room_id');
                    })
                    ->where('status',1)
                    ->where(function($query) use($webid) {
                        $query->where('ddc_room.web_id',0)
                            ->orwhere('ddc_room.web_id',$webid)
                            ->orwhere('drs.web_id',$webid);
                    })
                    ->selectRaw('ddc_room.*')
                    ->get();
                }
            }
            $ce = 0;
            foreach ($listRoom as $lroom) {
                if ($typeid!=Config_game::TYPE_DINGDONG) {
                    $getNumber = Cache::tags(['api', $table.'_number'])
                    ->rememberForever('numberresulttg_' . $table . $length, function() use ($table,$length){
                        return Dd_number::on('mysql::write')->from($table.'_number')->orderBy('period','DESC')->take($length)->get();
                    });
                } else {
                    $getNumber = Cache::tags(['api', $table.'_number_'.$lroom['room_id']])
                    ->rememberForever('numberresultdd_' . $table . $lroom['room_id'] . $length, function() use ($table,$lroom,$length){
                        return Dd_number::on('mysql::write')->from($table.'_number')->where('room_id',$lroom['room_id'])->orderBy('period','DESC')->take($length)->get();
                    });
                }

                if (count($getNumber)>0) {
                    $dataku[$ca]['game_id']                                = $lgame['game_id'];
                    $dataku[$ca]['game_name']                              = $lgame['game_name'];
                    $dataku[$ca]['dataResults'][$ce]['room_id']            = ($typeid!=Config_game::TYPE_DINGDONG || $lgame['game_id']==Config_game::GAMEID_JH || $lgame['game_id']==Config_game::GAMEID_JD ? "": $lroom['room_id']);
                    $dataku[$ca]['dataResults'][$ce]['room_name']          = ($typeid!=Config_game::TYPE_DINGDONG || $lgame['game_id']==Config_game::GAMEID_JH || $lgame['game_id']==Config_game::GAMEID_JD ? "": $lroom['name']);
                    $dataku[$ca]['dataResults'][$ce]['count']              = sizeof($listRoom);

                    foreach ($getNumber as $k => $lnumber) {
                        $dataku[$ca]['dataResults'][$ce]['results'][$k]['date']      = changeDate($lnumber['datetime']);
                        $dataku[$ca]['dataResults'][$ce]['results'][$k]['period']    = $lnumber['period'];
                        if ($lgame['game_id']==Config_game::GAMEID_JH || $lgame['game_id']==Config_game::GAMEID_JD) {
                            $dataku[$ca]['dataResults'][$ce]['results'][$k]['number1']    = $lnumber['number1'].'-'.$lnumber['symbol1'];
                            $dataku[$ca]['dataResults'][$ce]['results'][$k]['number2']    = $lnumber['number2'].'-'.$lnumber['symbol2'];
                            $dataku[$ca]['dataResults'][$ce]['results'][$k]['number3']    = $lnumber['number3'].'-'.$lnumber['symbol3'];
                            $dataku[$ca]['dataResults'][$ce]['results'][$k]['number4']    = $lnumber['number4'].'-'.$lnumber['symbol4'];
                            $dataku[$ca]['dataResults'][$ce]['results'][$k]['number5']    = $lnumber['number5'].'-'.$lnumber['symbol5'];
                            $dataku[$ca]['dataResults'][$ce]['results'][$k]['number6']    = $lnumber['number6'].'-'.$lnumber['symbol6'];
                        } elseif ($lgame['game_id']==Config_game::GAMEID_PD) {
                            $arrTebak   = getTebakPD('splitNumber',$lnumber['number'],$lnumber['number2'],$lnumber['number3'],$lnumber['number4'],$lnumber['number5']);
                            $dataku[$ca]['dataResults'][$ce]['results'][$k]['number1']    = $arrTebak['number'].'-'.$arrTebak['colour'];
                            $dataku[$ca]['dataResults'][$ce]['results'][$k]['number2']    = $arrTebak['number2'].'-'.$arrTebak['colour2'];
                            $dataku[$ca]['dataResults'][$ce]['results'][$k]['number3']    = $arrTebak['number3'].'-'.$arrTebak['colour3'];
                            $dataku[$ca]['dataResults'][$ce]['results'][$k]['number4']    = $arrTebak['number4'].'-'.$arrTebak['colour4'];
                            $dataku[$ca]['dataResults'][$ce]['results'][$k]['number5']    = $arrTebak['number5'].'-'.$arrTebak['colour5'];
                        } elseif ($lgame['game_id']==Config_game::GAMEID_SC || $lgame['game_id']==Config_game::GAMEID_BR || $typeid!=Config_game::TYPE_DINGDONG) {
                            $dataku[$ca]['dataResults'][$ce]['results'][$k]['number1']    = $lnumber['number'];
                            $dataku[$ca]['dataResults'][$ce]['results'][$k]['number2']    = $lnumber['number2'];
                            $dataku[$ca]['dataResults'][$ce]['results'][$k]['number3']    = $lnumber['number3'];
                        } elseif ($lgame['game_id']==Config_game::GAMEID_DT) {
                            $dataku[$ca]['dataResults'][$ce]['results'][$k]['number1']    = $lnumber['number'].'-Dragon';
                            $dataku[$ca]['dataResults'][$ce]['results'][$k]['number2']    = $lnumber['number2'].'-Tiger';
                        } else {
                            $dataku[$ca]['dataResults'][$ce]['results'][$k]['number1']    = $lnumber['number'];
                        }
                    }
                    $ce++;
                }
            }

            $ca++;
        }

        $data = array(
            'code'      => $code,
            'msg'       => (config('message.'.$code)),
            'data'      => $dataku
        );

        return response()->json($data);
    }

    /**
     * @OA\Post(
     *     path="/api/v2/getNumberDetails",
     *     summary="Get Number Details",
     *     tags={"Game API"},
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
     *                      property="type_id",
     *                      description="Type ID",
     *                      type="integer"
     *                 ),
     *                 @OA\Property(
     *                      property="game_id",
     *                      description="Game ID",
     *                      type="integer"
     *                 ),
     *                 @OA\Property(
     *                      property="room_id",
     *                      description="Room ID",
     *                      type="integer"
     *                 ),
     *                 @OA\Property(
     *                      property="period",
     *                      description="Game Period",
     *                      type="integer"
     *                 ),
     *                  @OA\Property(
     *                      property="hash",
     *                      description="HASHING with SHA 256 hash(sha256, operatorid . type_id . game_id . room_id . period . secret-key)",
     *                      type="string"
     *                 ),
     *                  example={"operatorid": 10066, "type_id": 5, "game_id": 201, "room_id": 1, "period": 73, "hash": "ebf54d4dae6d6aa088a454f63548c383faf67ab15bd614778f0b598a222acd84"}
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
    public function numberDetails(NumberDetailsRequest $request){
        $gameid         = $request->game_id;
        $game           = $request->get('game');
        $results        = $request->get('results');
        $data           = $dataku = array();

        $code = 0;

        foreach ($results as $val) {
            $dataku['number']     = $val->number;

            if ($game->type==Config_game::TYPE_TOGEL) {
                if(strlen($val->number) == 6) {
                    $number = substr($val->number, 2);
                } else {
                    $number = $val->number;
                }
                $detail = dataAnalysisTG($number);
                $shio = getShioNew($number,$val->datetime);
                $getShioName = Config_bet_type::where('value',$shio)->value('name');
                $dataku['as']             = $detail['as'];
                $dataku['kop']            = $detail['kop'];
                $dataku['kepala']         = $detail['kepala'];
                $dataku['ekor']           = $detail['ekor'];
                $dataku['as50']           = $detail['asGG'].'#'.$detail['asBS'];
                $dataku['kop50']          = $detail['kopGG'].'#'.$detail['kopBS'];
                $dataku['kepala50']       = $detail['kepalaGG'].'#'.$detail['kepalaBS'];
                $dataku['ekor50']         = $detail['ekorGG'].'#'.$detail['ekorBS'];
                $dataku['shio']           = $detail['shio'].'_'.ucfirst($getShioName);
                $dataku['silangHomo']     = $detail['homoBelakang'];
                $dataku['tengahTepi']     = $detail['tepi'];
                $dataku['kembangKempis']  = $detail['kembangBelakang'];
                $dataku['dasar']          = $detail['dasGG'].'#'.$detail['dasBS'];
            } elseif ($game->type==Config_game::TYPE_DINGDONG) {
                $warna                  = Config_bet_type::where(['game_id'=>$gameid,'subgame_id'=>Config_subgame::SUBGAME_5050])->get(['name','value']);
                $row                    = Config_bet_type::where(['game_id'=>$gameid,'subgame_id'=>Config_subgame::SUBGAME_ROW])->get(['name','value']);
                $group                  = Config_bet_type::where(['game_id'=>$gameid,'subgame_id'=>Config_subgame::SUBGAME_GROUP])->get(['name','value']);
                if (in_array($gameid,[Config_game::GAMEID_36D,Config_game::GAMEID_24D,Config_game::GAMEID_12D,Config_game::GAMEID_48D,Config_game::GAMEID_36D_PRIVATE,Config_game::GAMEID_24D_PRIVATE,Config_game::GAMEID_12D_PRIVATE,Config_game::GAMEID_48D_PRIVATE])) {
                    $dataku['5050']           = getTebak5050('5050BS',$gameid,$val->number).'#'.getTebak5050('5050GG',$gameid,$val->number).'#'.getTebak5050Warna($val->number,$warna);
                    $dataku['row']            = getTebakRow($val->number,$row);
                    $dataku['group']          = getTebakGroup($val->number,$group);
                } elseif ($gameid==Config_game::GAMEID_SC || $gameid==Config_game::GAMEID_SC_PRIVATE) {
                    $dataku['number2']        = $val->number2;
                    $dataku['number3']        = $val->number3;
                    $dataku['5050']           = getTebak5050('5050BS',$gameid,$val->number,$val->number2,$val->number3).'#'.getTebak5050('5050GG',$gameid,$val->number,$val->number2,$val->number3);
                } elseif ($gameid==Config_game::GAMEID_DT || $gameid==Config_game::GAMEID_DT_PRIVATE) {
                    $dataku['number']         = getTebakDT('angkaDTflip',$val->number).'-Dragon';
                    $dataku['number2']        = getTebakDT('angkaDTflip',$val->number2).'-Tiger';
                    $dataku['5050_1']         = getTebak5050('5050BS',$gameid,$val->number).'#'.getTebak5050('5050GG',$gameid,$val->number).'-Dragon';
                    $dataku['5050_2']         = getTebak5050('5050BS',$gameid,$val->number2).'#'.getTebak5050('5050GG',$gameid,$val->number2).'-Tiger';
                    $dataku['result']         = getTebakDT('gameDT',$val->number,$val->number2);
                } elseif ($gameid==Config_game::GAMEID_BR || $gameid==Config_game::GAMEID_BR_PRIVATE) {
                    $dataku['number2']        = $val->number2;
                    $dataku['number3']        = $val->number3;
                    $dataku['5050']           = getTebak5050('5050BS',$gameid,$val->number,$val->number2,$val->number3).'#'.getTebak5050('5050GG',$gameid,$val->number,$val->number2,$val->number3);
                    $dataku['colour']         = getTebak5050Warna($val->number,$warna).'#'.getTebak5050Warna($val->number2,$warna).'#'.getTebak5050Warna($val->number3,$warna);
                } elseif ($gameid==Config_game::GAMEID_PD || $gameid==Config_game::GAMEID_PD_PRIVATE) {
                    $arrTebak                 = getTebakPD('splitNumber',$val->number,$val->number2,$val->number3,$val->number4,$val->number5);
                    $dataku['number']         = $arrTebak['number'].'#'.$arrTebak['colour'];
                    $dataku['number2']        = $arrTebak['number2'].'#'.$arrTebak['colour2'];
                    $dataku['number3']        = $arrTebak['number3'].'#'.$arrTebak['colour3'];
                    $dataku['number4']        = $arrTebak['number4'].'#'.$arrTebak['colour4'];
                    $dataku['number5']        = $arrTebak['number5'].'#'.$arrTebak['colour5'];
                    $dataku['5050']           = getTebakPD('5050BS',$val->number,$val->number2,$val->number3,$val->number4,$val->number5) . '#' . getTebakPD('5050GG',$val->number,$val->number2,$val->number3,$val->number4,$val->number5) . '#' . getTebakPD('5050Warna',$val->number,$val->number2,$val->number3,$val->number4,$val->number5);
                    $dataku['result']         = getTebakPD('tebakPD',$val->number,$val->number2,$val->number3,$val->number4,$val->number5);
                }
            }
        }

        $data       = [
            'code'      =>  $code,
            'msg'       =>  (config('message.'.$code)),
            'data'      =>  $dataku
        ];

        return response()->json($data);
    }

    /**
     * @OA\Post(
     *     path="/api/v2/getMarketTime",
     *     summary="Get Martket Time",
     *     tags={"Game API"},
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
     *                 @OA\Property(
     *                      property="date",
     *                      description="market time date with format Y-m-d",
     *                      type="string"
     *                 ),
     *                  @OA\Property(
     *                      property="hash",
     *                      description="HASHING with SHA 256 hash(sha256, operatorid . game_id . date . secret-key)",
     *                      type="string"
     *                 ),
     *                  example={"operatorid": 10066, "game_id": 201, "date": "2019-09-11", "hash": "7e4edbfcce95db944beb7eec1bc3232471d946d1f176980e79e8932a2933faae"}
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
    public function getMarketTime(MarketTimeRequest $request){
        $gameid         = $request->game_id;
        $date           = $request->date;
        $game           = $request->get('game');

        $data           = $dataTime =   [];
        $code   =   0;

        $date       =   $date ?: date('Y-m-d');

        if (!$gameid) {
            $game       =   Config_game::where('type',Config_game::TYPE_TOGEL)->get();
            $schedule   =   Ttg_closedtime::where(['day'=>date('w',strtotime($date))])->get();

            foreach ($game as $k => $gme) {
                $dataTime[$k]['game_name']  = $gme->game_name;
                $dataTime[$k]['close']      = $schedule->where('game_id',$gme->game_id)->where('type',0)->first()->time;
                $dataTime[$k]['open']       = $schedule->where('game_id',$gme->game_id)->where('type',1)->first()->time;
            }
        } else {
            $schedule   =   Ttg_closedtime::where(['day'=>date('w',strtotime($date)),'game_id'=>$game->game_id])->get();

            $dataTime[0]['game_name']  =   $game->game_name;
            $dataTime[0]['close']      =   $schedule->where('type',0)->first()->time;
            $dataTime[0]['open']       =   $schedule->where('type',1)->first()->time;
        }

        $data       = [
            'code'      =>  $code,
            'msg'       =>  (config('message.'.$code)),
            'data'      =>  $dataTime
        ];

        return response()->json($data);
    }

    /**
     * @OA\Post(
     *     path="/api/v2/getTableName",
     *     summary="Get Table Name",
     *     tags={"Game API"},
     *
     *     @OA\Response(
     *         response=200,
     *         description="successful operation"
     *     ),
     * )
     */
    public function getTableName(GetTableNameRequest $request){
        $tableid        = $request->table_id;
        $game           = $request->get('game');

        $data           = $dataTable = array();
        $code   =   0;

        if ($game->type==Config_game::TYPE_CARDGAME) {
            if ($game->game_id==Config_game::GAMEID_MMB) {
                $getTable   =   Game_table::from($game->sqltable.'_table as tbl')
                    ->join($game->sqltable.'_room as tr','tr.room_id','=','tbl.room_id')
                    ->where('tbl.table_id',$tableid)
                    ->selectRaw('CONCAT("ROOM",tr.coin,"C ",tbl.number) as name')
                    ->first();

                $dataTable['name']  =   $getTable['name'];
            } else {
                $getTable   =   Join_langtable::where(['table_id'=>$tableid,'game_id'=>$game->game_id])
                    ->join('config_lang as cl','cl.lang_id','=','join_langtable.lang_id')
                    ->select('join_langtable.name','cl.code')->get();

                foreach ($getTable as $tbl) {
                    $dataTable[$tbl['code']]['name']          =   $tbl['name'];
                }
            }

        } elseif ($game->type==Config_game::TYPE_DINGDONG) {
            $getTable   =   Ddc_limit::where('ddc_limit.id',$tableid)
                ->join('ddc_room as dr','dr.room_id','=','ddc_limit.room_id')
                ->selectRaw('CONCAT(dr.name," ",ddc_limit.limit_no) as name')->first();

            $dataTable['name']  =   $getTable['name'];

        }

        if (empty($dataTable)) {
            $code = 503;
        }

        $data       = [
            'code'      =>  $code,
            'msg'       =>  (config('message.'.$code)),
            'data'      =>  $dataTable
        ];

        return response()->json($data, 200, [], JSON_UNESCAPED_UNICODE);
    }

    /**
     * @OA\Post(
     *     path="/api/v2/getAllPlayerBalance",
     *     summary="Get All Player Balance",
     *     tags={"Game API"},
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
     *                  @OA\Property(
     *                      property="hash",
     *                      description="HASHING with SHA 256 hash(sha256, operatorid . secret-key)",
     *                      type="string"
     *                 ),
     *                  example={"operatorid": 10066, "hash": "031f1387dae9eff10d4f2d3facecfc33c208209bc49e106d255c67cccd6590b3"}
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

    /**
     * @OA\Post(
     *     path="/api/v2/getOnlinePlayerCount",
     *     summary="Get Online Player",
     *     tags={"Game API"},
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
     *                  @OA\Property(
     *                      property="device",
     *                      description="Active Device 1: Web, 2: Mobile",
     *                      type="integer"
     *                 ),
     *                  @OA\Property(
     *                      property="hash",
     *                      description="HASHING with SHA 256 hash(sha256, operatorid . device . secret-key)",
     *                      type="string"
     *                 ),
     *                  example={"operatorid": 10066, "device":1, "hash": "6210c0f29ee163d429cde1e378766aff2733f8d2997de418193eb9c4ecab5754"},
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


    /**
     * @OA\Post(
     *     path="/api/v2/checkPlayerIsOnline",
     *     summary="Check Online Player",
     *     tags={"Game API"},
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
     *                  @OA\Property(
     *                      property="username",
     *                      description="Username with prefix",
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
     *      ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="successful operation"
     *     ),
     * )
     */
    public function checkPlayerIsOnline(IsOnlinePlayerRequest $request){
        $player         = $request->get('player');

        $data           = array();
        $code           = 0;
        $online         = 'Offline';

        $status         = User_active::where('user_id',$player->user_id)->count();

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


    /**
     * @OA\Post(
     *     path="/api/v2/updatePlayerSetting",
     *     summary="Update Player",
     *     tags={"Game API"},
     *
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                  @OA\Property(
     *                     property="operatorid",
     *                     description="Operator ID",
     *                     type="integer"
     *                 ),
     *                  @OA\Property(
     *                      property="username",
     *                      description="Username with prefix",
     *                      type="string"
     *                 ),
     *                 @OA\Property(
     *                      property="language",
     *                      description="Language id: Indonesia",
     *                      type="string"
     *                 ),
     *                 @OA\Property(
     *                      property="fullname",
     *                      description="Fullname",
     *                      type="string"
     *                 ),
     *                 @OA\Property(
     *                      property="email",
     *                      description="Email",
     *                      type="string"
     *                 ),
     *                  @OA\Property(
     *                     property="hash",
     *                     description="HASHING with SHA 256 hash(sha256, operatorid . username . language . fullname . email . secret-key)",
     *                     type="string"
     *                 ),
     *                  example={"operatorid": 10066, "username": "NGA_ANAKEMAS2", "language": "id", "fullname": "ANAKEMAS", "email": "anakemas@gmail.com",  "hash": "5fa42d95a3d4debe1a2a0bb82f525bca37b5ef2eee457a9923212572d793de96"},
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
    public function updatePlayerSetting(UpdatePlayerDataRequest $request){
        $webid          = $request->operatorid;
        $fullname       = $request->fullname;
        $referral       = $request->referral;
        $email          = $request->email;
        $player         = $request->get('player');
        $cfg_lang       = $request->get('lang');
        $data           = array();

        $code   =   0;

        $lang_id    = $cfg_lang->lang_id;
        $ref_id     = User_id::where(['web_id'=>$webid,'user_name'=>$referral])->value('user_id');
        $user_id    = $player->user_id;

        User_id::where('user_id',$user_id)->update([
            'fullname'  =>  $fullname,
            'email'     =>  $email,
            'lang_id'   =>  $lang_id,
            'ref_id'    =>  $ref_id ?: NULL,
        ]);

        $data       = [
            'code'      =>  $code,
            'msg'       =>  (config('message.'.$code)),
        ];

        return response()->json($data);
    }

    /**
     * @OA\Post(
     *     path="/api/v1/getGameTurnover",
     *     summary="Game Turnover",
     *     tags={"Game API"},
     *
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                  @OA\Property(
     *                     property="operatorid",
     *                     description="Operator ID",
     *                     type="integer"
     *                 ),
     *                  @OA\Property(
     *                      property="username",
     *                      description="Username with prefix",
     *                      type="string"
     *                 ),
     *                 @OA\Property(
     *                      property="create_date",
     *                      description="Start Date with format Y-m-d",
     *                      type="string"
     *                 ),
     *                 @OA\Property(
     *                      property="turnover",
     *                      description="turnover",
     *                      type="string"
     *                 ),
     *                 @OA\Property(
     *                      property="game_id",
     *                      description="game id",
     *                      type="integer"
     *                 ),
     * 
     *                  @OA\Property(
     *                      property="sub_game_id",
     *                      description="sub game id",
     *                      type="integer"
     *                 ),
     *                  @OA\Property(
     *                     property="hash",
     *                     description="HASHING with SHA 256 hash(sha256, operatorid . username . language . fullname . email . secret-key)",
     *                     type="string"
     *                 ),
     *                  example={"operatorid": 10066, "username": "NGA_ANAKEMAS2", "language": "id", "fullname": "ANAKEMAS", "email": "anakemas@gmail.com",  "hash": "5fa42d95a3d4debe1a2a0bb82f525bca37b5ef2eee457a9923212572d793de96"},
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

     public function getGameTurnover(GameTurnoverRequest $request) {
        // $createdate  =   $request->create_date;
        $webid       =   $request->operatorid;
        $gameid      =   $request->game_id;
        $id          =   $request->turnover_id;
        $maxid       =   $id + 1000;
        $code        =   0;

        if($gameid == NULL) {
            if($id == 0) {
                $input  =   DB::table('join_referral_com as jfc')
                            ->join('user as u', 'jfc.user_id', 'u.user_id')
                            ->where('created_date', date('Y-m-d'))
                            ->where('jfc.web_id', $webid)
                            ->limit(100)
                            ->get(['jfc.id as turnover_id','jfc.created_date','u.user_name','jfc.turnover','jfc.game_id','jfc.subgame_id as bet_type_id','jfc.amount_fee as fee']);
            } else {
                $input  =   DB::table('join_referral_com as jfc')
                            ->join('user as u', 'jfc.user_id', 'u.user_id')
                            ->whereBetween('id', [$id, $maxid])
                            ->where('jfc.web_id', $webid)
                            ->limit(100)
                            ->get(['jfc.id as turnover_id','jfc.created_date','u.user_name','jfc.turnover','jfc.game_id','jfc.subgame_id as bet_type_id','jfc.amount_fee as fee']);
            }
        } else {
            if($id == 0) {
                $input  =   DB::table('join_referral_com as jfc')
                            ->join('user as u', 'jfc.user_id', 'u.user_id')
                            ->where('created_date', date('Y-m-d'))
                            ->where('jfc.web_id', $webid)
                            ->where('game_id', $gameid)
                            ->limit(100)
                            ->get(['jfc.id as turnover_id','jfc.created_date','u.user_name','jfc.turnover','jfc.game_id','jfc.subgame_id as bet_type_id','jfc.amount_fee as fee']);
            } else {
                $input  =   DB::table('join_referral_com as jfc')
                            ->join('user as u', 'jfc.user_id', 'u.user_id')
                            ->whereBetween('id', [$id, $maxid])
                            ->where('jfc.web_id', $webid)
                            ->where('game_id', $gameid)
                            ->limit(100)
                            ->get(['jfc.id as turnover_id','jfc.created_date','u.user_name','jfc.turnover','jfc.game_id','jfc.subgame_id as bet_type_id','jfc.amount_fee as fee']);
            }
        }
        $data       = [
            'code'      =>  $code,
            'msg'       =>  (config('message.'.$code)),
            'data'      =>  $input
        ];

        return response()->json($data);
     }

     /**
     * @OA\Post(
     *     path="/api/v1/allnumberResult",
     *     summary="Get All Number Results",
     *     tags={"Game API"},
     *
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="successful operation"
     *     ),
     * )
     */
    public function allnumberResults(AllNumberResultsRequest $request) {
        $typeid         = 5;
        $length         = 15;
        $listGame       = ViewPartnerGame::where('type', $typeid)->groupby('game_code')->get();
        $data           = $dataku = array();
        // dd($listGame);
        $code   = 0;
        $ca = 0;

        foreach ($listGame as $lgame) {
            $table = $lgame['sqltable'];
            $listRoom = [['room_id'=>1]];
            $ce = 0;
            foreach ($listRoom as $lroom) {
                if ($typeid!=Config_game::TYPE_DINGDONG) {
                    $getNumber = Cache::tags(['api', $table.'_number'])
                    ->rememberForever('numberresulttg_' . $table . $length, function() use ($table,$length){
                        return Dd_number::on('mysql::write')->from($table.'_number')->orderBy('period','DESC')->take($length)->get();
                    });
                } else {
                    $getNumber = Cache::tags(['api', $table.'_number_'.$lroom['room_id']])
                    ->rememberForever('numberresultdd_' . $table . $lroom['room_id'] . $length, function() use ($table,$lroom,$length){
                        return Dd_number::on('mysql::write')->from($table.'_number')->where('room_id',$lroom['room_id'])->orderBy('period','DESC')->take($length)->get();
                    });
                }

                if (count($getNumber)>0) {
                    $dataku[$ca]['game_id']                                = $lgame['game_id'];
                    $dataku[$ca]['game_name']                              = $lgame['game_name'];
                    $dataku[$ca]['dataResults'][$ce]['room_id']            = ($typeid!=Config_game::TYPE_DINGDONG || $lgame['game_id']==Config_game::GAMEID_JH || $lgame['game_id']==Config_game::GAMEID_JD ? "": $lroom['room_id']);
                    $dataku[$ca]['dataResults'][$ce]['room_name']          = ($typeid!=Config_game::TYPE_DINGDONG || $lgame['game_id']==Config_game::GAMEID_JH || $lgame['game_id']==Config_game::GAMEID_JD ? "": $lroom['name']);
                    $dataku[$ca]['dataResults'][$ce]['count']              = sizeof($listRoom);

                    foreach ($getNumber as $k => $lnumber) {
                        $dataku[$ca]['dataResults'][$ce]['results'][$k]['date']      = changeDate($lnumber['datetime']);
                        $dataku[$ca]['dataResults'][$ce]['results'][$k]['period']    = $lnumber['period'];
                        if ($lgame['game_id']==Config_game::GAMEID_JH || $lgame['game_id']==Config_game::GAMEID_JD) {
                            $dataku[$ca]['dataResults'][$ce]['results'][$k]['number1']    = $lnumber['number1'].'-'.$lnumber['symbol1'];
                            $dataku[$ca]['dataResults'][$ce]['results'][$k]['number2']    = $lnumber['number2'].'-'.$lnumber['symbol2'];
                            $dataku[$ca]['dataResults'][$ce]['results'][$k]['number3']    = $lnumber['number3'].'-'.$lnumber['symbol3'];
                            $dataku[$ca]['dataResults'][$ce]['results'][$k]['number4']    = $lnumber['number4'].'-'.$lnumber['symbol4'];
                            $dataku[$ca]['dataResults'][$ce]['results'][$k]['number5']    = $lnumber['number5'].'-'.$lnumber['symbol5'];
                            $dataku[$ca]['dataResults'][$ce]['results'][$k]['number6']    = $lnumber['number6'].'-'.$lnumber['symbol6'];
                        } elseif ($lgame['game_id']==Config_game::GAMEID_PD) {
                            $arrTebak   = getTebakPD('splitNumber',$lnumber['number'],$lnumber['number2'],$lnumber['number3'],$lnumber['number4'],$lnumber['number5']);
                            $dataku[$ca]['dataResults'][$ce]['results'][$k]['number1']    = $arrTebak['number'].'-'.$arrTebak['colour'];
                            $dataku[$ca]['dataResults'][$ce]['results'][$k]['number2']    = $arrTebak['number2'].'-'.$arrTebak['colour2'];
                            $dataku[$ca]['dataResults'][$ce]['results'][$k]['number3']    = $arrTebak['number3'].'-'.$arrTebak['colour3'];
                            $dataku[$ca]['dataResults'][$ce]['results'][$k]['number4']    = $arrTebak['number4'].'-'.$arrTebak['colour4'];
                            $dataku[$ca]['dataResults'][$ce]['results'][$k]['number5']    = $arrTebak['number5'].'-'.$arrTebak['colour5'];
                        } elseif ($lgame['game_id']==Config_game::GAMEID_SC || $lgame['game_id']==Config_game::GAMEID_BR || $typeid!=Config_game::TYPE_DINGDONG) {
                            $dataku[$ca]['dataResults'][$ce]['results'][$k]['number1']    = $lnumber['number'];
                            $dataku[$ca]['dataResults'][$ce]['results'][$k]['number2']    = $lnumber['number2'];
                            $dataku[$ca]['dataResults'][$ce]['results'][$k]['number3']    = $lnumber['number3'];
                        } elseif ($lgame['game_id']==Config_game::GAMEID_DT) {
                            $dataku[$ca]['dataResults'][$ce]['results'][$k]['number1']    = $lnumber['number'].'-Dragon';
                            $dataku[$ca]['dataResults'][$ce]['results'][$k]['number2']    = $lnumber['number2'].'-Tiger';
                        } else {
                            $dataku[$ca]['dataResults'][$ce]['results'][$k]['number1']    = $lnumber['number'];
                        }
                    }
                    $ce++;
                }
            }

            $ca++;
        }

        $data = array(
            'code'      => $code,
            'msg'       => (config('message.'.$code)),
            'data'      => $dataku
        );

        return response()->json($data);
    }
}
