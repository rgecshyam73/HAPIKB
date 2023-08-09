<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\CheckTransferRequest;
use App\Http\Requests\InvoiceTogelRequest;
use App\Http\Requests\InvoiceTogelPerUsernameRequest;
use App\Http\Requests\TotalJackpotAmountRequest;
use App\Http\Requests\TransactionResultsRequest;
use App\Http\Requests\TransferRequest;
use App\Models\Db1\Adm_config;
use App\Models\Db1\Adm_transaction_day;
use App\Models\Db1\Adm_transfer;
use App\Models\Db1\Config_bet_type;
use App\Models\Db1\Config_game;
use App\Models\Db1\Config_subgame;
use App\Models\Db1\Dd_invoice;
use App\Models\Db1\Dd_number;
use App\Models\Db1\Ddc_limit;
use App\Models\Db1\Ddj_transaction;
use App\Models\Db1\Game_transaction;
use App\Models\Db1\Join_balance;
use App\Models\Db1\Ttg_invoice;
use App\Models\Db1\Ttg_number;
use App\Models\Db1\Txh_table;
use App\Models\Db1\User_coin;
use App\Models\Db1\User_id;
use App\Models\Views\ViewPartner;
use App\Repositories\ITransactionRepository;
use App\ViewModels\TransferViewModel;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Jenssegers\Agent\Agent;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;

class TransactionController extends Controller
{
    protected $agent;
    protected $transactionRepo;

    public function __construct(ITransactionRepository $transactionRepository)
    {
        $this->agent  = new Agent;
        $this->transactionRepo = $transactionRepository;
        LaravelLocalization::setLocale('en');
    }

    /**
     * @OA\Post(
     *     path="/api/v2/getInvoiceTogel",
     *     summary="Check Transaction Togel",
     *     tags={"Transaction API"},
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
     *                      property="period",
     *                      description="Game Period",
     *                      type="integer"
     *                 ),
     *                  @OA\Property(
     *                      property="hash",
     *                      description="HASHING with SHA 256 hash(sha256, operatorid . game_id . period . secret-key)",
     *                      type="string"
     *                 ),
     *                  example={"operatorid": 10017, "game_id": 201, "period": 225, "hash": "4ecf5406b0e1d82c6765c19620ca054b00128e1137ac44f9f3be3a4c8a26735c"},
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
    public function invoiceTogel(InvoiceTogelRequest $request)
    {
        $webid      =   $request->operatorid;
        $gameid     =   $request->game_id;
        $period     =   $request->period;
        $game       =   $request->get('game');
        $player     =   $request->get('player');

        $dataInvoiceDetails = [];
        $code = 0;

        $currentPeriod = Ttg_number::from($game->sqltable.'_number as num')
                ->where(function($query) use ($game) {
                    if ($game->jackpot_type==Config_game::JACKPOT_TYPE_PERIODIC) {
                        $query->where('periodical',0);
                    }
                })
                ->orderby('period','DESC')
                ->first()->period+1;

        $db     = ($currentPeriod - $period >= 1 ? config('database.dbName2'): '');
        $old    = ($currentPeriod - $period >= 1 ? '_old': '');

        $listSubGame = config('global.list_subgame')[$game->type][$game->jackpot_type];

        foreach ($listSubGame as $k => $val) {
            $tablebet       =   $db.$val['table_bet'].$old;
            $subgame        =   $val['subgame'];
            $result         =   Ttg_invoice::from($game->sqltable.'_invoice as inv')
                ->join($tablebet.' as bet','bet.invoice_id','=','inv.id')
                ->join('user as ui','ui.user_id','=','inv.user_id')
                ->where('ui.web_id',$webid)
                ->where('bet.game_id',$gameid)
                ->where('inv.period',$period ?: $currentPeriod);

            if ($player) {
                $result = $result->where('inv.user_id',$player->user_id);
            }

            if (! in_array($subgame,[Config_subgame::SUBGAME_COLOKBEBAS,Config_subgame::SUBGAME_COLOKBEBAS2D,Config_subgame::SUBGAME_COLOKNAGA,Config_subgame::SUBGAME_KOMBINASI])) {
                $result = $result->join('config_bet_type as cbt','cbt.bet_type_id','=','bet.bet_type_id')
                    ->where('cbt.subgame_id',$subgame);
            }

            $bayar = "bayar(bet.amount,bet.disc)";
            if (in_array($subgame,[Config_subgame::SUBGAME_TENGAHTEPI,Config_subgame::SUBGAME_DASAR,Config_subgame::SUBGAME_5050,Config_subgame::SUBGAME_SILANGHOMO,Config_subgame::SUBGAME_KEMBANGKEMPIS,Config_subgame::SUBGAME_50502D])) {
                $bayar = "bayarTG(bet.amount,bet.disc,bet.kei)";
            }

            $result         =   $result
            ->selectRaw('SUM(IF(inv.status=21,bet.amount,0)) as beli, SUM(IF(inv.status=21,'.$bayar.',0)) as bayar, SUM(IF(inv.status=22,menangTG(bet.amount,bet.disc,'.$val['prizekei'].','.$val['win_type_id'].'),0)) as menang')
            ->first();

            $dataInvoiceDetails[$k]['name'] = $val['name'];
            $dataInvoiceDetails[$k]['subgame_id'] = $subgame;
            $dataInvoiceDetails[$k]['win'] = $result->menang ?: decimalCurr(0);
            $dataInvoiceDetails[$k]['tover'] = $result->beli ?: decimalCurr(0);
            $dataInvoiceDetails[$k]['net_tover'] = $result->bayar ?: decimalCurr(0);
        }

        $data = [
            'code'  =>  $code,
            'msg'   =>  (config('message.'.$code)),
            'data'  =>  $dataInvoiceDetails
        ];

        return response()->json($data);
    }

    public function transfer(TransferRequest $request) {
        $username       = $request->username;
        $web_transferId = $request->trans_id;
        $amount         = $request->amount ?: 0;
        $dir            = $request->dir;
        $webid          = $request->operatorid;
        $dataUser       = $request->get('player');
        $cfg_curr       = $request->get('currenc');
        $blockTfOut     = $request->get('block_transfer_out') ?: false;
        $id_trx         = "";
        $code = 0;

        $type = 2;
        $sym = "-";
        if ($dir == "1") {
            $type = 1;
            $sym = "+";
        }

        $id_trx = $this->transactionRepo->transfer($dataUser,$cfg_curr,$username,$webid,$web_transferId,$amount,$type,$sym,$blockTfOut);

        if (!$id_trx) {
            $code = 399;
        }

        $data = array(
            'ext_id'    => ($id_trx),
            'code'      => ($code),
            'msg'       => (config('message.'.$code)),
            'amount'    => ""
        );

        return response()->json($data);
    }

    public function check_trans(CheckTransferRequest $request) {
        $webid          = $request->operatorid;
        $web_transferId = $request->trans_id;

        $ext_id = "";
        $code = 0;
        $status = 0;

        $idTrans = Adm_transfer::on('mysql::write')->where(['web_transferid' => $web_transferId,'web_id' => $webid])->value('id');
        if ($idTrans) {
            $status = 1;
            $ext_id = $idTrans;
        }

        $data = array(
            'code'      => ($code),
            'status'    => ($status),
            'msg'       => (config('message.'.$code)),
            'ext_id'    => ($ext_id)
        );

        return response()->json($data);
    }

    /**
     * @OA\Post(
     *     path="/api/v2/getTransResult",
     *     summary="Get Transaction Result",
     *     tags={"Transaction API"},
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
     *                      property="version_key",
     *                      description="Version Key / Transaction ID",
     *                      type="integer"
     *                 ),
     *                  @OA\Property(
     *                      property="hash",
     *                      description="HASHING with SHA 256 hash(sha256, version_key . secret-key)",
     *                      type="string"
     *                 ),
     *                  example={"operatorid": 10017, "version_key": 280556, "hash": "218efdff78f64258466caeea4479c45d25e9d65a98503e3fb1ec8806d9af7811"},
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
    public function transResults(TransactionResultsRequest $request){
        $jBalance       = $request->get('jBalance');
        $data           = $dataku = array();

        $code   =   0;

        foreach ($jBalance as $k => $val) {
            $prefix =   explode('_',$val->user_name)[0];
            $turn_over = $net_turn_over = $val->tover;
            $winloss_amount = $val->amount;

            if($val->game_id >= 200){
                $net_turn_over = $val->amount;
            }
            if($val->game_id >= 200 && $val->status == 22){
                $turn_over = 0;
                $net_turn_over = 0;
            }

            if (in_array($val->status,[Adm_config::TYPE_BALANCE_LOSE,Adm_config::TYPE_BALANCE_BET,Adm_config::TYPE_BALANCE_BUY_GIFT,Adm_config::TYPE_BALANCE_BUY_JACKPOT,Adm_config::TYPE_BALANCE_FOLD])) {
                $winloss_amount = $val->amount * -1;
            }

            if($val->game_id == Config_game::GAMEID_MMB){
                $winloss_amount = $val->amount * 100;
            }

            if($winloss_amount == -0){
                $winloss_amount = $val->amount * -1;
            }

            $dataku[$k]['version_key']      =   $val->id;
            $dataku[$k]['prefix']           =   $prefix;
            $dataku[$k]['user_id']          =   $val->user_id;
            $dataku[$k]['username']         =   $val->user_name;
            $dataku[$k]['nickname']         =   $val->nickname;
            $dataku[$k]['status']           =   $val->act;
            $dataku[$k]['trans_id']         =   $val->trans_id;
            $dataku[$k]['trans_time']       =   $val->start_datetime;
            $dataku[$k]['winloss_time']     =   $val->datetime;
            $dataku[$k]['period']           =   $val->period;
            $dataku[$k]['game_id']          =   $val->game_id;
            $dataku[$k]['winloss_amount']   =   $winloss_amount;
            $dataku[$k]['main_balance']     =   $val->balance;
            $dataku[$k]['game_balance']     =   $val->coin;
            $dataku[$k]['turn_over']        =   $turn_over;
            $dataku[$k]['net_turn_over']    =   $net_turn_over;
            $dataku[$k]['bet_type_id']      =   $val->bet_type_id;
            $dataku[$k]['user_ip']          =   $val->user_ip;
            $dataku[$k]['channel']          =   $val->channel;
            $dataku[$k]['detail']           =   $this->transactionRepo->getDetailTransaction($val);

        }

        $data       = [
            'code'      =>  $code,
            'msg'       =>  (config('message.'.$code)),
            'data'      =>  $dataku
        ];

        return response()->json($data);
    }

    public function invoiceTogelperUsername(InvoiceTogelPerUsernameRequest $request)
    {
        $webid      =   $request->operatorid;
        $gameid     =   $request->game_id;
        $period     =   $request->period;
        $subgameid  =   $request->subgame_id;
        $game       =   $request->get('game');
        $player     =   $request->get('player');

        $dataInvoiceDetails = [];
        $code = 0;

        $currentPeriod = Ttg_number::from($game->sqltable.'_number as num')
                ->where(function($query) use ($game) {
                    if ($game->jackpot_type==Config_game::JACKPOT_TYPE_PERIODIC) {
                        $query->where('periodical',0);
                    }
                })
                ->orderby('period','DESC')
                ->first()->period+1;

        $db     = ($currentPeriod - $period >= 1 ? config('database.dbName2'): '');
        $old    = ($currentPeriod - $period >= 1 ? '_old': '');

        $listSubGame = config('global.list_subgame')[$game->type][$game->jackpot_type];

        foreach ($listSubGame as $val) {
            $tablebet       =   $db.$val['table_bet'].$old;
            $subgame        =   $val['subgame'];
            if ($subgameid == $subgame) {
                $result         =   Ttg_invoice::from($game->sqltable.'_invoice as inv')
                ->join($tablebet.' as bet','bet.invoice_id','=','inv.id')
                ->join('user as ui','ui.user_id','=','inv.user_id')
                ->where('ui.web_id',$webid)
                ->where('bet.game_id',$gameid)
                ->where('inv.period',$period ?: $currentPeriod)
                ->groupBy('inv.user_id');

                if ($player) {
                    $result = $result->where('inv.user_id',$player->user_id);
                }

                if (! in_array($subgame,[Config_subgame::SUBGAME_COLOKBEBAS,Config_subgame::SUBGAME_COLOKBEBAS2D,Config_subgame::SUBGAME_COLOKNAGA,Config_subgame::SUBGAME_KOMBINASI])) {
                    $result = $result->join('config_bet_type as cbt','cbt.bet_type_id','=','bet.bet_type_id')
                        ->where('cbt.subgame_id',$subgame);
                }

                $bayar = "bayar(bet.amount,bet.disc)";
                if (in_array($subgame,[Config_subgame::SUBGAME_TENGAHTEPI,Config_subgame::SUBGAME_DASAR,Config_subgame::SUBGAME_5050,Config_subgame::SUBGAME_SILANGHOMO,Config_subgame::SUBGAME_KEMBANGKEMPIS,Config_subgame::SUBGAME_50502D])) {
                    $bayar = "bayarTG(bet.amount,bet.disc,bet.kei)";
                }

                $result         =   $result
                ->selectRaw('SUM(IF(inv.status=21,bet.amount,0)) as beli, SUM(IF(inv.status=21,'.$bayar.',0)) as bayar, SUM(IF(inv.status=22,menangTG(bet.amount,bet.disc,'.$val['prizekei'].','.$val['win_type_id'].'),0)) as menang, inv.user_id')
                ->get();

                foreach ($result as $key => $value) {
                    $username = User_id::find($value->user_id)->user_name;
                    $dataInvoiceDetails[$key]['user_name'] = $username;
                    $dataInvoiceDetails[$key]['win'] = $value->menang ?: decimalCurr(0);
                    $dataInvoiceDetails[$key]['tover'] = $value->beli ?: decimalCurr(0);
                    $dataInvoiceDetails[$key]['net_tover'] = $value->bayar ?: decimalCurr(0);
                }
            }
        }

        $data = [
            'code'  =>  $code,
            'msg'   =>  (config('message.'.$code)),
            'data'  =>  $dataInvoiceDetails
        ];

        return response()->json($data);
    }
}
