<?php


namespace App\Repositories;

use App\Models\Db1\Adm_config;
use App\Models\Db1\Adm_transaction_day;
use App\Models\Db1\Adm_transfer;
use App\Models\Db1\Config_bet_type;
use App\Models\Db1\Config_game;
use App\Models\Db1\Config_subgame;
use App\Models\Db1\Dd_number;
use App\Models\Db1\Ddc_limit;
use App\Models\Db1\Dd_invoice;
use App\Models\Db1\Join_balance;
use App\Models\Db1\Game_round_user;
use App\Models\Db1\Game_transaction;
use App\Models\Db1\Ttg_invoice;
use App\Models\Db1\Ttg_number;
use App\Models\Db1\User_coin;
use Illuminate\Support\Facades\DB;

class TransactionRepository implements ITransactionRepository
{

    public function transfer($dataUser, $cfg_curr, $username, $webid, $web_transferId, $amount, $type, $sym, $blockTfOut = false)
    {
        return DB::transaction(function () use($dataUser,$cfg_curr,$username,$webid,$web_transferId,$amount,$type,$sym,$blockTfOut) {
            $newAmount = $amount;
            if ($type == 2 && $blockTfOut === true) {
                $newAmount = "0";
            }

            try {
                $id_trx = Adm_transfer::insertGetId([
                    'date'              => DB::raw('NOW()'),
                    'user_id'           => $dataUser->user_id,
                    'web_id'            => $webid,
                    'web_transferid'    => $web_transferId,
                    'type'              => $type,
                    'amount'            => $newAmount,
                    'status'            => 1
                ]);
            } catch (\Exception $e) {
                return FALSE;
            }

            if($blockTfOut === false) {
                if ($type == 2) {
                    $updateATD = [
                        'cash_out'       => DB::raw('COALESCE(cash_out,0)+'.$newAmount)
                    ];
                } else {
                    $updateATD = [
                        'cash_in'       => DB::raw('COALESCE(cash_in,0)+'.$newAmount)
                    ];
                }
                
                Adm_transaction_day::updateOrCreate(
                    [
                        'created_date'  => date("Y-m-d"),
                        'user_name'     => strtoupper($username),
                        'curr_id'       => $cfg_curr->curr_id,
                        'web_id'        => $webid,
                        'user_id'       => $dataUser->user_id
                    ],
                    $updateATD
                );

                $updateBalance = @User_coin::where(['user_id'=>$dataUser->user_id,'game_id'=>0])
                ->whereRaw('COALESCE(`coin`,0)'.$sym.$amount.' >= 0')
                ->update([
                    'coin' => DB::raw('COALESCE(`coin`,0)'.$sym.$amount),
                    'valsatu' => DB::raw('SHA2(CONCAT(`user_id`, `coin`), 256)')
                ]);

                if (! $updateBalance) {
                    DB::rollback();
                    return false;
                }
            }

            Join_balance::create([
                'start_datetime'  => DB::raw('NOW()'),
                'datetime'  => DB::raw('NOW()'),
                'user_id'   => $dataUser->user_id,
                'user_name' => strtoupper($username),
                'nickname'  => strtoupper($dataUser->nickname),
                'act'       => $type,
                'amount'    => $newAmount,
                'balance'   => DB::raw('(SELECT `coin` FROM `user_coin` WHERE `user_id` = ' . $dataUser->user_id . ' AND `game_id` = 0)'),
                'trans_id'  => $id_trx,
                'game_id'   => 0,
                'user_ip'   => getIP(),
                'web_id'    => $webid
            ]);

            return $id_trx;
        });
    }

    public function getDetailTransaction($params)
    {
        $data = [];
        $game = Config_game::find($params->game_id);
        if ($game->type == Config_game::TYPE_TOGEL) {
            $data = $this->detailTransactionTG($params, $game);
        } elseif ($game->type == Config_game::TYPE_DINGDONG) {
            $data = $this->detailTransactionDD($params, $game);
        } elseif ($game->type == Config_game::TYPE_CARDGAME) {
            $data = $this->detailTransactionCG($params, $game);
        }

        return $data;
    }

    private function detailTransactionTG($params, $game)
    {
        $data = [];
        $period = $params->period;
        $transid = $params->trans_id;
        $bettype = $params->bet_type_id;
        $lastPeriod = Ttg_number::from($game->sqltable . '_number')->where('iscount',1)->max('period')+1;
        $db = ($lastPeriod - $period >= 1 ? env('DATABASE_NAME2','dev_hkbgame_db2.'): '');
        $old = ($lastPeriod - $period >= 1 ? '_old': '');
        $ttg = $game->jackpot_type==Config_game::JACKPOT_TYPE_DEACTIVE ? 'ttg': 'ttglive';
        $invoicetable = $db.$game->sqltable.'_invoice'.$old;
        $conn = 'mysql';

        if (in_array($bettype, [Config_bet_type::BET_TYPE_4D_1_WEEKLY, Config_bet_type::BET_TYPE_4D_2_WEEKLY, Config_bet_type::BET_TYPE_4D_3_WEEKLY, Config_bet_type::BET_TYPE_4D_STARTER_WEEKLY, Config_bet_type::BET_TYPE_4D_CONSOLATION_WEEKLY, Config_bet_type::BET_TYPE_3D_WEEKLY, Config_bet_type::BET_TYPE_2D_WEEKLY, Config_bet_type::BET_TYPE_4D_1_MONTHLY, Config_bet_type::BET_TYPE_4D_2_MONTHLY, Config_bet_type::BET_TYPE_4D_3_MONTHLY, Config_bet_type::BET_TYPE_4D_STARTER_MONTHLY, Config_bet_type::BET_TYPE_4D_CONSOLATION_MONTHLY, Config_bet_type::BET_TYPE_3D_MONTHLY, Config_bet_type::BET_TYPE_2D_MONTHLY]) || in_array($bettype,[Config_bet_type::BET_TYPE_4D3D2D,Config_bet_type::BET_TYPE_4D,Config_bet_type::BET_TYPE_3D_BELAKANG,Config_bet_type::BET_TYPE_3D_DEPAN,Config_bet_type::BET_TYPE_2D_BELAKANG,Config_bet_type::BET_TYPE_QUICK_BET,Config_bet_type::BET_TYPE_4D_BOLAK_BALIK,Config_bet_type::BET_TYPE_BB_CAMPURAN,Config_bet_type::BET_TYPE_4D_SET,Config_bet_type::BET_TYPE_QUICK_2D,Config_bet_type::BET_TYPE_2D_TENGAH,Config_bet_type::BET_TYPE_2D_DEPAN])) {
            $tablebet       =   $db.$ttg.'_betnumber'.$old;
            $result         =   Ttg_invoice::on($conn)->from($invoicetable.' as inv')
            ->join($tablebet.' as bet','bet.invoice_id','=','inv.id')
            ->join('config_bet_type as cbt','cbt.bet_type_id','=','bet.bet_type_id')
            ->where('inv.id',$transid)
            ->where('bet.game_id',$game->game_id)
            ->orderBy('cbt.subgame_id')->orderBy('bet.bet')
            ->selectRaw('inv.id, inv.status, inv.date, bet.bet, bet.amount, bet.disc, bet.prize as prize, 0 as kei, bayar(bet.amount,bet.disc) as bayar, menangTG(bet.amount, bet.disc, bet.prize, 1) as win_amount, IF(INSTR(cbt.desc,"Posisi")>0,CONCAT(LEFT(cbt.desc,3)," ",cbt.name),cbt.desc) as info, cbt.name as posisi, cbt.subgame_id, cbt.bet_type_id ')
            ->get();
        } elseif ($bettype==Config_bet_type::BET_TYPE_COLOKBEBAS) {
            $tablebet       =   $db.$ttg.'_betbebas'.$old;
            $result         =   Ttg_invoice::on($conn)->from($invoicetable.' as inv')
            ->join($tablebet.' as bet','bet.invoice_id','=','inv.id')
            ->where('inv.id',$transid)
            ->where('bet.game_id',$game->game_id)
            ->orderBy('bet.bet')
            ->selectRaw('inv.id, inv.status, inv.date, bet.bet, bet.amount, bet.disc, bet.prize as prize, 0 as kei, bayar(bet.amount,bet.disc) as bayar, menangTG(bet.amount, bet.disc, bet.prize, 2) as win_amount, "Bebas" as info, '.Config_subgame::SUBGAME_COLOKBEBAS.' as subgame_id, '.Config_bet_type::BET_TYPE_COLOKBEBAS.' as bet_type_id ')
            ->get();
        } elseif ($bettype==Config_bet_type::BET_TYPE_COLOKBEBAS2D) {
            $tablebet       =   $db.$ttg.'_betbebas2d'.$old;
            $result         =   Ttg_invoice::on($conn)->from($invoicetable.' as inv')
            ->join($tablebet.' as bet','bet.invoice_id','=','inv.id')
            ->where('inv.id',$transid)
            ->where('bet.game_id',$game->game_id)
            ->orderBy('bet.bet')
            ->selectRaw('inv.id, inv.status, inv.date, bet.bet, bet.amount, bet.disc, bet.prize as prize, 0 as kei, bet.bet2, bayar(bet.amount,bet.disc) as bayar, menangTG(bet.amount, bet.disc, bet.prize, 2) as win_amount, "Bebas 2D" as info, '.Config_subgame::SUBGAME_COLOKBEBAS2D.' as subgame_id, '.Config_bet_type::BET_TYPE_COLOKBEBAS2D.' as bet_type_id ')
            ->get();
        } elseif ($bettype==Config_bet_type::BET_TYPE_COLOKNAGA) {
            $tablebet       =   $db.$ttg.'_betnaga'.$old;
            $result         =   Ttg_invoice::on($conn)->from($invoicetable.' as inv')
            ->join($tablebet.' as bet','bet.invoice_id','=','inv.id')
            ->where('inv.id',$transid)
            ->where('bet.game_id',$game->game_id)
            ->orderBy('bet.bet')
            ->selectRaw('inv.id, inv.status, inv.date, bet.bet, bet.amount, bet.disc, bet.prize as prize, 0 as kei, bet.bet2, bet.bet3, bayar(bet.amount,bet.disc) as bayar, menangTG(bet.amount, bet.disc, bet.prize, 2) as win_amount, "Naga" as info, '.Config_subgame::SUBGAME_COLOKNAGA.' as subgame_id, '.Config_bet_type::BET_TYPE_COLOKNAGA.' as bet_type_id ')
            ->get();
        } elseif ($bettype==Config_bet_type::BET_TYPE_COLOKJITU) {
            $tablebet       =   $db.$ttg.'_betjitu'.$old;
            $result         =   Ttg_invoice::on($conn)->from($invoicetable.' as inv')
            ->join($tablebet.' as bet','bet.invoice_id','=','inv.id')
            ->join('config_bet_type as cbt','cbt.bet_type_id','=','bet.bet_type_id')
            ->where('inv.id',$transid)
            ->where('bet.game_id',$game->game_id)
            ->orderBy('bet.bet')
            ->selectRaw('inv.id, inv.status, inv.date, bet.bet, bet.amount, bet.disc, bet.prize as prize, 0 as kei, bayar(bet.amount,bet.disc) as bayar, menangTG(bet.amount, bet.disc, bet.prize, 2) as win_amount, cbt.desc as info, cbt.name as posisi, '.Config_subgame::SUBGAME_COLOKJITU.' as subgame_id, '.Config_bet_type::BET_TYPE_COLOKJITU.' as bet_type_id ')
            ->get();
        } elseif ($bettype==Config_bet_type::BET_TYPE_TENGAHTEPI) {
            $tablebet       =   $db.$ttg.'_bettepi'.$old;
            $result         =   Ttg_invoice::on($conn)->from($invoicetable.' as inv')
            ->join($tablebet.' as bet','bet.invoice_id','=','inv.id')
            ->join('config_bet_type as cbt','cbt.bet_type_id','=','bet.bet_type_id')
            ->where('inv.id',$transid)
            ->where('bet.game_id',$game->game_id)
            ->selectRaw('inv.id, inv.status, inv.date, cbt.name as bet, bet.amount, bet.disc, 1 as prize, bet.kei as kei, bayarTG(bet.amount,bet.disc,bet.kei) as bayar, menangTG(bet.amount, bet.disc, bet.kei, 3) as win_amount, cbt.desc as info, '.Config_subgame::SUBGAME_TENGAHTEPI.' as subgame_id, '.Config_bet_type::BET_TYPE_TENGAHTEPI.' as bet_type_id ')
            ->get();
        } elseif ($bettype==Config_bet_type::BET_TYPE_DASAR) {
            $tablebet       =   $db.$ttg.'_betdasar'.$old;
            $result         =   Ttg_invoice::on($conn)->from($invoicetable.' as inv')
            ->join($tablebet.' as bet','bet.invoice_id','=','inv.id')
            ->join('config_bet_type as cbt','cbt.bet_type_id','=','bet.bet_type_id')
            ->where('inv.id',$transid)
            ->where('bet.game_id',$game->game_id)
            ->selectRaw('inv.id, inv.status, inv.date, cbt.name as bet, bet.amount, bet.disc, 1 as prize, bet.kei as kei, bayarTG(bet.amount,bet.disc,bet.kei) as bayar, menangTG(bet.amount, bet.disc, bet.kei, 3) as win_amount, cbt.desc as info, '.Config_subgame::SUBGAME_DASAR.' as subgame_id, '.Config_bet_type::BET_TYPE_DASAR.' as bet_type_id ')
            ->get();
        } elseif ($bettype==Config_bet_type::BET_TYPE_5050) {
            $tablebet       =   $db.$ttg.'_bet50'.$old;
            $result         =   Ttg_invoice::on($conn)->from($invoicetable.' as inv')
            ->join($tablebet.' as bet','bet.invoice_id','=','inv.id')
            ->join('config_bet_type as cbt','cbt.bet_type_id','=','bet.bet_type_id')
            ->where('inv.id',$transid)
            ->where('bet.game_id',$game->game_id)
            ->selectRaw('inv.id, inv.status, inv.date, cbt.name as bet, bet.amount, bet.disc, 1 as prize, bet.kei as kei, bayarTG(bet.amount,bet.disc,bet.kei) as bayar, menangTG(bet.amount, bet.disc, bet.kei, 3) as win_amount, cbt.desc as info, '.Config_subgame::SUBGAME_5050.' as subgame_id, '.Config_bet_type::BET_TYPE_5050.' as bet_type_id ')
            ->get();
        } elseif ($bettype==Config_bet_type::BET_TYPE_50502D) {
            $tablebet       =   $db.$ttg.'_bet502d'.$old;
            $result         =   Ttg_invoice::on($conn)->from($invoicetable.' as inv')
            ->join($tablebet.' as bet','bet.invoice_id','=','inv.id')
            ->join('config_bet_type as cbt','cbt.bet_type_id','=','bet.bet_type_id')
            ->where('inv.id',$transid)
            ->where('bet.game_id',$game->game_id)
            ->selectRaw('inv.id, inv.status, inv.date, cbt.name as bet, bet.amount, bet.disc, 1 as prize, bet.kei as kei, bayarTG(bet.amount,bet.disc,bet.kei) as bayar, menangTG(bet.amount, bet.disc, bet.kei, 4) as win_amount, cbt.desc as info, '.Config_subgame::SUBGAME_50502D.' as subgame_id, '.Config_bet_type::BET_TYPE_50502D.' as bet_type_id ')
            ->get();
        } elseif ($bettype==Config_bet_type::BET_TYPE_SHIO) {
            $tablebet       =   $db.$ttg.'_betshio'.$old;
            $result         =   Ttg_invoice::on($conn)->from($invoicetable.' as inv')
            ->join($tablebet.' as bet','bet.invoice_id','=','inv.id')
            ->join('config_bet_type as cbt','cbt.bet_type_id','=','bet.bet_type_id')
            ->where('inv.id',$transid)
            ->where('bet.game_id',$game->game_id)
            ->selectRaw('inv.id, inv.status, inv.date, CONCAT(cbt.value,"_",cbt.name) as bet, bet.amount, bet.disc, bet.prize as prize, 0 as kei, bayar(bet.amount,bet.disc) as bayar, menangTG(bet.amount, bet.disc, bet.prize, 2) as win_amount, cbt.desc as info, '.Config_subgame::SUBGAME_SHIO.' as subgame_id, '.Config_bet_type::BET_TYPE_SHIO.' as bet_type_id ')
            ->get();
        } elseif ($bettype==Config_bet_type::BET_TYPE_SILANGHOMO) {
            $tablebet       =   $db.$ttg.'_betsilang'.$old;
            $result         =   Ttg_invoice::on($conn)->from($invoicetable.' as inv')
            ->join($tablebet.' as bet','bet.invoice_id','=','inv.id')
            ->join('config_bet_type as cbt','cbt.bet_type_id','=','bet.bet_type_id')
            ->where('inv.id',$transid)
            ->where('bet.game_id',$game->game_id)
            ->selectRaw('inv.id, inv.status, inv.date, cbt.name as bet, bet.amount, bet.disc, 1 as prize, bet.kei as kei, bayarTG(bet.amount,bet.disc,bet.kei) as bayar, menangTG(bet.amount, bet.disc, bet.kei, 3) as win_amount, cbt.desc as info, '.Config_subgame::SUBGAME_SILANGHOMO.' as subgame_id, '.Config_bet_type::BET_TYPE_SILANGHOMO.' as bet_type_id ')
            ->get();
        } elseif ($bettype==Config_bet_type::BET_TYPE_KEMBANGKEMPIS) {
            $tablebet       =   $db.$ttg.'_betkembang'.$old;
            $result         =   Ttg_invoice::on($conn)->from($invoicetable.' as inv')
            ->join($tablebet.' as bet','bet.invoice_id','=','inv.id')
            ->join('config_bet_type as cbt','cbt.bet_type_id','=','bet.bet_type_id')
            ->where('inv.id',$transid)
            ->where('bet.game_id',$game->game_id)
            ->selectRaw('inv.id, inv.status, inv.date, cbt.name as bet, bet.amount, bet.disc, 1 as prize, bet.kei as kei, bayarTG(bet.amount,bet.disc,bet.kei) as bayar, menangTG(bet.amount, bet.disc, bet.kei, 3) as win_amount, cbt.desc as info, '.Config_subgame::SUBGAME_KEMBANGKEMPIS.' as subgame_id, '.Config_bet_type::BET_TYPE_KEMBANGKEMPIS.' as bet_type_id ')
            ->get();
        } elseif ($bettype==Config_bet_type::BET_TYPE_KOMBINASI) {
            $tablebet       =   $db.$ttg.'_betkombinasi'.$old;
            $result         =   Ttg_invoice::on($conn)->from($invoicetable.' as inv')
            ->join($tablebet.' as bet','bet.invoice_id','=','inv.id')
            ->join('config_bet_type as cbt','cbt.bet_type_id','=','bet.bet')
            ->join('config_bet_type as cbt2','cbt2.bet_type_id','=','bet.bet2')
            ->where('inv.id',$transid)
            ->where('bet.game_id',$game->game_id)
            ->selectRaw('inv.id, inv.status, inv.date, cbt.name as bet, cbt2.name as bet2, bet.amount, bet.disc, bet.prize as prize, 0 as kei, bayar(bet.amount,bet.disc) as bayar, menangTG(bet.amount, bet.disc, bet.prize, 2) as win_amount, cbt.desc as info, '.Config_subgame::SUBGAME_KOMBINASI.' as subgame_id, '.Config_bet_type::BET_TYPE_KOMBINASI.' as bet_type_id ')
            ->get();
        } elseif ($bettype==Config_bet_type::BET_TYPE_QUICKBUY) {
            $tablebet       =   $db.$ttg.'_betnumber'.$old;
            $result1         =   Ttg_invoice::on($conn)->from($invoicetable.' as inv')
            ->join($tablebet.' as bet','bet.invoice_id','=','inv.id')
            ->join('config_bet_type as cbt','cbt.bet_type_id','=','bet.bet_type_id')
            ->where('inv.id',$transid)
            ->where('bet.game_id',$game->game_id)
            ->orderBy('cbt.subgame_id')->orderBy('bet.bet')
            ->selectRaw('inv.id, inv.status, inv.date, bet.bet, bet.amount, bet.disc, bet.prize as prize, 0 as kei, "-" as bet2, "-" as bet3, "-" as posisi, bayar(bet.amount,bet.disc) as bayar, menangTG(bet.amount, bet.disc, bet.prize, 1) as win_amount, IF(INSTR(cbt.desc,"Posisi")>0,CONCAT(LEFT(cbt.desc,3)," ",cbt.name),cbt.desc) as info, cbt.subgame_id, cbt.bet_type_id ')
            ->get();

            $tablebet       =   $db.$ttg.'_betbebas'.$old;
            $result2         =   Ttg_invoice::on($conn)->from($invoicetable.' as inv')
            ->join($tablebet.' as bet','bet.invoice_id','=','inv.id')
            ->where('inv.id',$transid)
            ->where('bet.game_id',$game->game_id)
            ->orderBy('bet.bet')
            ->selectRaw('inv.id, inv.status, inv.date, bet.bet, bet.amount, bet.disc, bet.prize as prize, 0 as kei, "-" as bet2, "-" as bet3, "-" as posisi, bayar(bet.amount,bet.disc) as bayar, menangTG(bet.amount, bet.disc, bet.prize, 2) as win_amount, "Bebas" as info, '.Config_subgame::SUBGAME_COLOKBEBAS.' as subgame_id, '.Config_bet_type::BET_TYPE_COLOKBEBAS.' as bet_type_id ')
            ->get();

            $tablebet       =   $db.$ttg.'_betbebas2d'.$old;
            $result3         =   Ttg_invoice::on($conn)->from($invoicetable.' as inv')
            ->join($tablebet.' as bet','bet.invoice_id','=','inv.id')
            ->where('inv.id',$transid)
            ->where('bet.game_id',$game->game_id)
            ->orderBy('bet.bet')
            ->selectRaw('inv.id, inv.status, inv.date, bet.bet, bet.amount, bet.disc, bet.prize as prize, 0 as kei, bet.bet2, "-" as bet3, "-" as posisi, bayar(bet.amount,bet.disc) as bayar, menangTG(bet.amount, bet.disc, bet.prize, 2) as win_amount, "Bebas 2D" as info, '.Config_subgame::SUBGAME_COLOKBEBAS2D.' as subgame_id, '.Config_bet_type::BET_TYPE_COLOKBEBAS2D.' as bet_type_id ')
            ->get();

            $tablebet       =   $db.$ttg.'_betnaga'.$old;
            $result4         =   Ttg_invoice::on($conn)->from($invoicetable.' as inv')
            ->join($tablebet.' as bet','bet.invoice_id','=','inv.id')
            ->where('inv.id',$transid)
            ->where('bet.game_id',$game->game_id)
            ->orderBy('bet.bet')
            ->selectRaw('inv.id, inv.status, inv.date, bet.bet, bet.amount, bet.disc, bet.prize as prize, 0 as kei, bet.bet2, bet.bet3, "-" as posisi, bayar(bet.amount,bet.disc) as bayar, menangTG(bet.amount, bet.disc, bet.prize, 2) as win_amount, "Naga" as info, '.Config_subgame::SUBGAME_COLOKNAGA.' as subgame_id, '.Config_bet_type::BET_TYPE_COLOKNAGA.' as bet_type_id ')
            ->get();

            $tablebet       =   $db.$ttg.'_betjitu'.$old;
            $result5         =   Ttg_invoice::on($conn)->from($invoicetable.' as inv')
            ->join($tablebet.' as bet','bet.invoice_id','=','inv.id')
            ->join('config_bet_type as cbt','cbt.bet_type_id','=','bet.bet_type_id')
            ->where('inv.id',$transid)
            ->where('bet.game_id',$game->game_id)
            ->orderBy('bet.bet')
            ->selectRaw('inv.id, inv.status, inv.date, bet.bet, bet.amount, bet.disc, bet.prize as prize, 0 as kei, "-" as bet2, "-" as bet3, bayar(bet.amount,bet.disc) as bayar, menangTG(bet.amount, bet.disc, bet.prize, 2) as win_amount, cbt.desc as info, cbt.name as posisi, '.Config_subgame::SUBGAME_COLOKJITU.' as subgame_id, '.Config_bet_type::BET_TYPE_COLOKJITU.' as bet_type_id ')
            ->get();

            $tablebet       =   $db.$ttg.'_bet50'.$old;
            $result6         =   Ttg_invoice::on($conn)->from($invoicetable.' as inv')
            ->join($tablebet.' as bet','bet.invoice_id','=','inv.id')
            ->join('config_bet_type as cbt','cbt.bet_type_id','=','bet.bet_type_id')
            ->where('inv.id',$transid)
            ->where('bet.game_id',$game->game_id)
            ->selectRaw('inv.id, inv.status, inv.date, cbt.name as bet, bet.amount, bet.disc, 1 as prize, bet.kei as kei, "-" as bet2, "-" as bet3, "-" as posisi, bayarTG(bet.amount,bet.disc,bet.kei) as bayar, menangTG(bet.amount, bet.disc, bet.kei, 3) as win_amount, cbt.desc as info, '.Config_subgame::SUBGAME_5050.' as subgame_id, '.Config_bet_type::BET_TYPE_5050.' as bet_type_id ')
            ->get();

            $tablebet       =   $db.$ttg.'_bet502d'.$old;
            $result7         =   Ttg_invoice::on($conn)->from($invoicetable.' as inv')
            ->join($tablebet.' as bet','bet.invoice_id','=','inv.id')
            ->join('config_bet_type as cbt','cbt.bet_type_id','=','bet.bet_type_id')
            ->where('inv.id',$transid)
            ->where('bet.game_id',$game->game_id)
            ->selectRaw('inv.id, inv.status, inv.date, cbt.name as bet, bet.amount, bet.disc, 1 as prize, bet.kei as kei, "-" as bet2, "-" as bet3, "-" as posisi, bayarTG(bet.amount,bet.disc,bet.kei) as bayar, menangTG(bet.amount, bet.disc, bet.kei, 3) as win_amount, cbt.desc as info, '.Config_subgame::SUBGAME_50502D.' as subgame_id, '.Config_bet_type::BET_TYPE_50502D.' as bet_type_id ')
            ->get();

            $tablebet       =   $db.$ttg.'_betshio'.$old;
            $result8         =   Ttg_invoice::on($conn)->from($invoicetable.' as inv')
            ->join($tablebet.' as bet','bet.invoice_id','=','inv.id')
            ->join('config_bet_type as cbt','cbt.bet_type_id','=','bet.bet_type_id')
            ->where('inv.id',$transid)
            ->where('bet.game_id',$game->game_id)
            ->selectRaw('inv.id, inv.status, inv.date, CONCAT(cbt.value,"_",cbt.name) as bet, bet.amount, bet.disc, bet.prize as prize, 0 as kei, "-" as bet2, "-" as bet3, "-" as posisi, bayar(bet.amount,bet.disc) as bayar, menangTG(bet.amount, bet.disc, bet.prize, 2) as win_amount, cbt.desc as info, '.Config_subgame::SUBGAME_SHIO.' as subgame_id, '.Config_bet_type::BET_TYPE_SHIO.' as bet_type_id ')
            ->get();

            $tablebet       =   $db.$ttg.'_bettepi'.$old;
            $result9         =   Ttg_invoice::on($conn)->from($invoicetable.' as inv')
            ->join($tablebet.' as bet','bet.invoice_id','=','inv.id')
            ->join('config_bet_type as cbt','cbt.bet_type_id','=','bet.bet_type_id')
            ->where('inv.id',$transid)
            ->where('bet.game_id',$game->game_id)
            ->selectRaw('inv.id, inv.status, inv.date, cbt.name as bet, bet.amount, bet.disc, 1 as prize, bet.kei as kei, "-" as bet2, "-" as bet3, "-" as posisi, bayarTG(bet.amount,bet.disc,bet.kei) as bayar, menangTG(bet.amount, bet.disc, bet.kei, 3) as win_amount, cbt.desc as info, '.Config_subgame::SUBGAME_TENGAHTEPI.' as subgame_id, '.Config_bet_type::BET_TYPE_TENGAHTEPI.' as bet_type_id ')
            ->get();

            $result = collect(array_merge(
                $result1->toArray(), $result2->toArray(), $result3->toArray(), $result4->toArray(), $result5->toArray(), $result6->toArray(), $result7->toArray(), $result8->toArray(), $result9->toArray()
            ));
        } elseif ($bettype == 0){
            $result = [];
        }

        if (! empty($result)) {
            foreach ($result as $k => $val) {
                $data[$k]['bet'] = $val->bet;
                if (in_array($bettype,[Config_bet_type::BET_TYPE_COLOKBEBAS2D,Config_bet_type::BET_TYPE_COLOKNAGA,Config_bet_type::BET_TYPE_KOMBINASI,Config_bet_type::BET_TYPE_QUICKBUY])) {
                    $data[$k]['bet2'] = $val->bet2;
                }
                if (in_array($bettype,[Config_bet_type::BET_TYPE_COLOKNAGA,Config_bet_type::BET_TYPE_QUICKBUY])) {
                    $data[$k]['bet3'] = $val->bet3;
                }
                if (in_array($bettype,[Config_bet_type::BET_TYPE_COLOKJITU,Config_bet_type::BET_TYPE_QUICKBUY])) {
                    $data[$k]['position'] = $val->posisi;
                }
                $data[$k]['subgame_id'] = str_replace('-','',$val->subgame_id);
                $data[$k]['bet_type_id'] = $val->bet_type_id;
                $data[$k]['discount'] = $val->disc;
                $data[$k]['prize'] = $val->prize;
                $data[$k]['kei'] = $val->kei;
                $data[$k]['tover'] = decimalCurr($val->amount);
                if ($params->act == Adm_config::TYPE_BALANCE_BET) {
                    $data[$k]['net_tover'] = decimalCurr($val->bayar);
                } else {
                    $data[$k]['win'] = decimalCurr($val->win_amount);
                }
                if ($params->act == Adm_config::TYPE_BALANCE_BET) {
                    $data[$k]['win'] = decimalCurr($val->win_amount);
                }
            }
        }

        return $data;
    }

    private function detailTransactionDD($params, $game)
    {
        $data = [];
        $period = $params->period;
        $limitid = $params->table_id;
        $transid = $params->trans_id;
        $bettype = $params->bet_type_id;
        $limit = Ddc_limit::find($limitid);
        $lastPeriod = Dd_number::from($game->sqltable . '_number')->where('room_id', $limit->room_id)->where('iscount',1)->max('period');

        $dbName = $lastPeriod - $period >= 1 ? env('DATABASE_NAME2','dev_hkbgame_db2.'): '';
        $old = $lastPeriod - $period >= 1 ? '_old': '';

        $invoiceName   = $dbName.$game->sqltable . '_invoice'.$old;
        $betName       = $dbName.$game->sqltable . '_bet'.$old;
        $betDetailName = $dbName.$game->sqltable . '_bet_detail'.$old;

        $result = Dd_invoice::from($invoiceName . ' as inv')
                            ->join($betName . ' as bet','bet.invoice_id','=','inv.id')
                            ->join($betDetailName . ' as betdt','betdt.bet_id','=','bet.id')
                            ->join('config_bet_type as cbt','cbt.bet_type_id','=','bet.bet_type_id')
                            ->where('inv.id',$transid)
                            ->groupBy('betdt.bet_id')
                            ->orderBy('cbt.subgame_id');

        $betSelect = " GROUP_CONCAT(betdt.bet) as bet,NULL as bet2, NULL as bet3,";
        if ($game->game_code == 'SB' || $game->game_code == 'BR') {
            $betSelect = " betdt.bet, betdt.bet2, betdt.bet3,";
        } elseif ($game->game_code == 'DT') {
            $betSelect = " IF(betdt.bet2!='',betdt.bet2,betdt.bet) as bet,betdt.bet as position,NULL as bet2, NULL as bet3,";
        }

        $result = $result->orderBy('betdt.id')->orderBy('betdt.bet')
            ->selectRaw('inv.status, '.$betSelect.' bet.amount, bet.disc, bet.prize, menangDD(bet.amount, bet.disc, bet.prize, bet.bet_type_id,'.$game->game_id.') as win_amount, bet.disc, bayar(bet.amount,bet.disc) as bayar, cbt.desc as info, cbt.subgame_id, bet.bet_type_id')
            ->get();

        if (! empty($result)) {
            foreach ($result as $k => $val) {
                $data[$k]['bet'] = $val->bet;
                $data[$k]['bet2'] = $val->bet2;
                $data[$k]['bet3'] = $val->bet3;

                if ($val->subgame_id == Config_subgame::SUBGAME_5050 || $val->subgame_id == Config_subgame::SUBGAME_ANGKA) {
                    $data[$k]['bet'] = @config('global.bet_trans_array')[$val->bet] ?: $val->bet;
                    $data[$k]['bet2'] = @config('global.bet_trans_array')[$val->bet2] ?: $val->bet2;
                    $data[$k]['bet3'] = @config('global.bet_trans_array')[$val->bet3] ?: $val->bet3;
                }

                if ($game->game_code == 'DT') {
                    $data[$k]['position'] = $val->position;
                } else {
                    $data[$k]['position'] = '-';
                }

                $data[$k]['subgame_id'] = str_replace('-','',$val->subgame_id);
                $data[$k]['bet_type_id'] = $val->bet_type_id;
                $data[$k]['discount'] = $val->disc;
                $data[$k]['prize'] = 1;
                $data[$k]['kei'] = 0;
                if ($val->subgame_id == Config_subgame::SUBGAME_5050) {
                    $data[$k]['kei'] = $val->prize;
                } else {
                    $data[$k]['prize'] = $val->prize;
                }
                $data[$k]['tover'] = decimalCurr($val->amount);
                if ($params->act == Adm_config::TYPE_BALANCE_BET) {
                    $data[$k]['net_tover'] = decimalCurr($val->bayar);
                } else {
                    $data[$k]['win'] = decimalCurr($val->win_amount);
                }
                if ($params->act == Adm_config::TYPE_BALANCE_BET) {
                    $data[$k]['win'] = decimalCurr($val->win_amount);
                }
            }
        }

        return $data;
    }

    private function detailTransactionCG($params, $game)
    {
        $data = [];
        $period = $params->period;
        $transid = $params->trans_id;

        if (in_array($game->game_id,[Config_game::GAMEID_TXH, Config_game::GAMEID_TXB, Config_game::GAMEID_TXP, Config_game::GAMEID_OMH])) {
            $pokerPrize =   convertAdmCfg(Adm_config::where('name','txt_poker_prize_card')->value('value'));
            $pokerSuit  =   convertAdmCfg(Adm_config::where('name','txt_poker_suit_card')->value('value'));

            $results    =   Game_round_user::from($game->sqltable.'_round_user as gru')
            ->join('user as ui','ui.user_id','gru.user_id')
            ->join($game->sqltable.'_transaction as trx','trx.'.$game->sqltable.'_round_user','gru.id')
            ->join($game->sqltable.'_round as gr','gr.id','gru.round_id')
            ->where(['gru.round_id'=>$transid])
            ->orderBy('gru.sit')
            ->groupBy('trx.'.$game->sqltable.'_round_user')
            ->selectRaw('ui.nickname, CONCAT(gru.card,",",gr.card) as card, gru.sit, SUM(winlose) as winlose')
            ->get();

            foreach ($results as $k => $value) {
                if ($game->game_id == Config_game::GAMEID_OMH) {
                    $detail = getCardPokerDetail($value['card'], $pokerSuit, $pokerPrize, 9);
                } else {
                    $detail = getCardPokerDetail($value['card'], $pokerSuit, $pokerPrize);
                }

                $data[$k]['nickname'] = $value->nickname;
                if ($game->game_id == Config_game::GAMEID_TXB) {
                    $data[$k]['seat'] = $value->sit == 0 ? 'Dealer': 'Player';
                }
                // $data[$k]['seat'] = $value->sit;
                $data[$k]['cards'] = $detail['arrCard'];
                $data[$k]['status'] = $detail['status'];
                $data[$k]['winlose'] = $value->winlose;
            }

            // dd($data);
        } elseif (in_array($game->game_id,[Config_game::GAMEID_DMC, Config_game::GAMEID_DBC, Config_game::GAMEID_DCP, Config_game::GAMEID_DBP])) {
            $cemeType    =   convertAdmCfg(Adm_config::where('name','txt_domino_type_player')->value('value'));
            $cemeValue   =   convertAdmCfg(Adm_config::where('name','txt_domino_value_card')->value('value'));

            $results    =   Game_round_user::from($game->sqltable.'_round_user as gru')
            ->join('user as ui','ui.user_id','gru.user_id')
            ->join($game->sqltable.'_transaction as trx','trx.'.$game->sqltable.'_round_user','gru.id')
            ->where(['gru.round_id'=>$transid])
            ->orderBy('gru.sit')
            ->groupBy('trx.'.$game->sqltable.'_round_user')
            ->selectRaw('ui.nickname, gru.card, gru.sit, SUM(winlose) as winlose')
            ->get();

            foreach ($results as $k => $value) {
                $detail = getCardDominoDetail($value['card'], $cemeType, $cemeValue);

                $data[$k]['nickname'] = $value->nickname;
                // $data[$k]['seat'] = $value->sit;
                $data[$k]['cards'] = $detail['arrCard'];
                // $data[$k]['status'] = $detail['status'];
                $data[$k]['winlose'] = $value->winlose;
            }
        } elseif (in_array($game->game_id,[Config_game::GAMEID_DMB, Config_game::GAMEID_DMQ, Config_game::GAMEID_DMP])) {
            $dominoPrize    =   convertAdmCfg(Adm_config::where('name','txt_domino_prize_card')->value('value'));

            $results    =   Game_round_user::from($game->sqltable.'_round_user as gru')
            ->join('user as ui','ui.user_id','gru.user_id')
            ->join($game->sqltable.'_transaction as trx','trx.'.$game->sqltable.'_round_user','gru.id')
            ->where(['gru.round_id'=>$transid])
            ->orderBy('gru.sit')
            ->groupBy('trx.'.$game->sqltable.'_round_user')
            ->selectRaw('ui.nickname, gru.card, gru.sit, SUM(winlose) as winlose')
            ->get();

            foreach ($results as $k => $value) {
                $detail = getCardDominoDetail($value['card'], $dominoPrize, []);

                $data[$k]['nickname'] = $value->nickname;
                // $data[$k]['seat'] = $value->sit;
                $data[$k]['cards'] = $detail['arrCard'];
                // $data[$k]['status'] = $detail['status'];
                $data[$k]['winlose'] = $value->winlose;
            }

            // dd($data);
        } elseif (in_array($game->game_id,[Config_game::GAMEID_MMB,Config_game::GAMEID_MMP])) {
            $getCard    =   Game_transaction::from($game->sqltable.'_transaction as trx')
            ->join($game->sqltable.'_prize_list as pl','pl.id','=','trx.prize_list_id')
            ->where(['trx.id'=>$transid])
            ->selectRaw('trx.card as handcard, pl.name as status')->first();

            $data['cards']       =   getCardMmbDetail($getCard->handcard)['arrCard'];
            $data['status']     =   $getCard->status;
        } elseif (in_array($game->game_id,[Config_game::GAMEID_BJM])) {
            $results    =   Game_round_user::from($game->sqltable.'_round_user as gru')
            ->join('user as ui','ui.user_id','gru.user_id')
            ->join($game->sqltable.'_transaction as trx','trx.'.$game->sqltable.'_round_user','gru.id')
            ->where(['gru.round_id'=>$transid])
            ->groupBy('trx.'.$game->sqltable.'_round_user')
            ->orderBy('gru.sit')
            ->selectRaw('ui.nickname, CONCAT(gru.card,":",gru.sit) as card, SUM(winlose) as winlose')
            ->get();

            foreach ($results as $k => $value) {
                $detail = getCardBlackjackDetail($value['card']);

                $data[$k]['nickname'] = $value->nickname;
                $data[$k]['seat'] = $detail['status'];
                $data[$k]['cards'] = $detail['arrCard'];
                $data[$k]['winlose'] = $value->winlose;
            }
        } elseif (in_array($game->game_id,[Config_game::GAMEID_CSA])) {

            $results    =   Game_transaction::from($game->sqltable.'_transaction as trx')
            ->join('user as ui','ui.user_id','=','trx.user_id')
            ->where(['trx.csa_round_id'=>$transid])
            ->selectRaw('trx.card, trx.status, trx.winlose, ui.nickname')
            ->get();

            foreach ($results as $k => $value) {
                $detail = getCardCapsaDetail($value['card']);

                $data[$k]['nickname'] = $value->nickname;
                $data[$k]['cards'] = $detail['arrCard'];
                $data[$k]['winlose'] = $value->winlose;
            }
        } elseif (in_array($game->game_id,[Config_game::GAMEID_STN])) {
            $pokerSuit  =   convertAdmCfg(Adm_config::where('name','txt_poker_suit_card')->value('value'));

            $results    =   Game_transaction::from($game->sqltable.'_round_user as gru')
            ->join($game->sqltable.'_transaction as trx','trx.stn_round_user','=','gru.id')
            ->join('user as ui','ui.user_id','=','gru.user_id')
            ->where(['gru.round_id'=>$transid])
            ->groupBy('trx.'.$game->sqltable.'_round_user')
            ->orderBy('gru.sit')
            ->selectRaw('gru.card, SUM(trx.winlose) as winlose, ui.nickname')
            ->get();

            foreach ($results as $k => $value) {
                $detail = getCardSuperTen($value['card'],$pokerSuit);

                $data[$k]['nickname'] = $value->nickname;
                $data[$k]['cards'] = $detail['arrCard'];
                // $data[$k]['value'] = $detail['value'];
                $data[$k]['winlose'] = $value->winlose;
            }
        } elseif (in_array($game->game_id,[Config_game::GAMEID_TKG])) {
            $pokerSuit  =   convertAdmCfg(Adm_config::where('name','txt_poker_suit_card')->value('value'));

            $results    =   Game_round_user::from($game->sqltable.'_round_user as gru')
            ->join('user as ui','ui.user_id','gru.user_id')
            ->join($game->sqltable.'_transaction as trx','trx.'.$game->sqltable.'_round_user','gru.id')
            ->where(['gru.round_id'=>$transid])
            ->orderBy('gru.sit')
            ->groupBy('trx.'.$game->sqltable.'_round_user')
            ->selectRaw('ui.nickname, gru.card, gru.sit, SUM(winlose) as winlose')
            ->get();

            foreach ($results as $k => $value) {
                $detail = getCardThreekings($value['card'],$pokerSuit);

                $data[$k]['nickname'] = $value->nickname;
                $data[$k]['seat'] = $value->sit == 0 ? 'Dealer': 'Player';
                $data[$k]['cards'] = $detail['arrCard'];
                // $data[$k]['value'] = $detail['value'];
                $data[$k]['winlose'] = $value->winlose;
            }
        } elseif (in_array($game->game_id,[Config_game::GAMEID_JK])) {
            $results    =   Game_round_user::from($game->sqltable.'_round_user as gru')
            ->join('user as ui','ui.user_id','gru.user_id')
            ->join($game->sqltable.'_transaction as trx','trx.'.$game->sqltable.'_round_user','gru.id')
            ->where(['gru.round_id'=>$transid])
            ->groupBy('trx.'.$game->sqltable.'_round_user')
            ->orderBy('gru.sit')
            ->selectRaw('ui.nickname, CONCAT(gru.card,",",gru.sit) as card, SUM(winlose) as winlose')
            ->get();

            foreach ($results as $k => $value) {
                $detail = getCardJoker($value['card']);

                $data[$k]['nickname'] = $value->nickname;
                $data[$k]['seat'] = $detail['status'];
                $data[$k]['cards'] = $detail['arrCard'];
                $data[$k]['winlose'] = $value->winlose;
            }
        } elseif (in_array($game->game_id,[Config_game::GAMEID_BCT])) {
            $baccaratType  =   convertAdmCfg(Adm_config::where('name','txt_bct_transaction_type')->value('value'));

            $cards = getCardBaccarat(Game_round_user::from($game->sqltable.'_round')->where('id',$transid)->first()->card);

            $results    =   Game_round_user::from($game->sqltable.'_round_user as gru')
            ->join('user as ui','ui.user_id','gru.user_id')
            ->join($game->sqltable.'_transaction as trx','trx.'.$game->sqltable.'_round_user','gru.id')
            ->where(['gru.round_id'=>$transid])
            ->orderBy('gru.sit')
            // ->groupBy('trx.'.$game->sqltable.'_round_user')
            ->selectRaw('ui.nickname, gru.sit, trx.bet_type, trx.winlose')
            ->get()->groupby(['nickname']);

            foreach ($cards['arrCard'] as $key => $value) {
                $data['result']['status'] = $cards['statusWin'];
                $data['result']['cards'][$key] = $value;
                /*$data['result'][$key]['card'] = $value;
                $data['result'][$key]['value'] = $cards['value'][$key];*/
            }

            $k = 0;
            foreach ($results as $key => $value) {
                $details = array_column(array_map(function($tag) use($baccaratType) {
                    return array(
                        'winlose' => $tag['winlose'],
                        'bet_type_name' => $baccaratType[$tag['bet_type']],
                    );
                }, $value->toArray()),'winlose','bet_type_name');

                $data[$k]['nickname'] = $key;
                $data[$k]['bet_details'] = $details;
                $data[$k]['winlose'] = $value->sum('winlose');
                $k++;
            }
        }

        return $data;
    }
}