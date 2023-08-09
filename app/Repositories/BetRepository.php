<?php


namespace App\Repositories;


use App\Models\Db1\Config_game;
use App\Models\Db1\Dd_invoice;
use App\Models\Db1\Ddc_room;
use App\Models\Db1\Join_balance;
use App\Models\Db1\Join_transaction_day;
use App\Models\Db1\Ttg_invoice;
use App\Models\Views\ViewInvoice;
use Illuminate\Support\Facades\DB;

class BetRepository implements IBetRepository
{

    public function betDetails($webid, $startTime, $endTime)
    {
        $results = Join_balance::query()
            ->where('web_id',$webid)
            ->where('datetime','>=',$startTime)
            ->where('datetime','<=',$endTime)
            ->get();

        $results = $results->map(function($result, $key){
            $prefix = explode('_',$result->user_name)[0];
            $period = explode('-',$result->period)[0];
            $dataResults = [];
            $dataResults['id']              =    $key+1;
            $dataResults['version_id']      =    $result->id;
            $dataResults['prefix']          =    $prefix;
            $dataResults['user_id']         =    $result->user_id;
            $dataResults['user_name']       =    $result->user_name;
            $dataResults['nickname']        =    $result->nickname;
            $dataResults['status']          =    $result->act;
            $dataResults['trans_id']        =    $result->trans_id;
            $dataResults['bet_time']        =    $result->start_datetime;
            $dataResults['winloss_time']    =    $result->datetime;
            $dataResults['period']          =    $period;
            $dataResults['game_id']         =    $result->game_id;
            $dataResults['winloss_amount']  =    $result->amount;
            $dataResults['main_balance']    =    $result->balance;
            $dataResults['game_balance']    =    $result->coin;
            $dataResults['turnover']        =    $result->tover;
            $dataResults['bet_type_id']     =    $result->bet_type_id;
            $dataResults['user_ip']         =    $result->user_ip;
            $dataResults['channel']         =    $result->channel;
            return $result;
        });

        return $results->toArray();
    }

    public function OutstandingBet($webid, $game)
    {
        $dataInvoice = [];
        if ($game->type == Config_game::TYPE_TOGEL) {
            $getPeriod = DB::table($game->sqltable.'_number')->orderBy('period','DESC')->first()->period+1;

            $invoices = $this->getTogelInvoices($game->sqltable, $webid, $getPeriod);

            for ($k=0; $k < 1; $k++) {
                $dataInvoice[$k]['period'] = $getPeriod;
                $dataInvoice[$k]['dataResults'] = array();
                $dataInvoice[$k]['dataResults'] = $invoices->map(function($inv){
                    $invoice = [];
                    $invoice['subgame_id'] = $inv->subgame_id;
                    $invoice['total_invoice'] = $inv->totalInvoice;
                    $invoice['total_amount'] = $inv->totalAmount;
                    return $invoice;
                })->toArray();
            }
        } elseif ($game->type == Config_game::TYPE_DINGDONG) {
            $ddroom = Ddc_room::select('room_id')->where('game_id',$game->game_id)->get();
            foreach ($ddroom as $k => $room) {

                $getPeriod = DB::table($game->sqltable.'_number')->where('room_id',$room->room_id)->orderBy('period','DESC')->first()->period+1;

                $invoices = $this->getDingdongInvoices($game->sqltable, $webid, $getPeriod, $room->room_id);

                $dataInvoice[$k]['period'] = $getPeriod;
                $dataInvoice[$k]['room_id'] = $room->room_id;
                $dataInvoice[$k]['dataResults'] = $invoices->map(function($inv){
                    $invoice = [];
                    $invoice['subgame_id'] = $inv->subgame_id;
                    $invoice['total_invoice'] = $inv->totalInvoice;
                    $invoice['total_amount'] = $inv->totalAmount;
                })->toArray();
            }
        }

        return $dataInvoice;
    }

    public function OutstandingBetDetails($webid, $game, $gameId, $subgameid, $roomid)
    {
        $dataInvoiceDetails = [];
        if ($game->type == Config_game::TYPE_TOGEL) {
            $getPeriod = DB::table($game->sqltable.'_number')->orderBy('period','DESC')->first()->period+1;

            $invoice = Ttg_invoice::from($game->sqltable.'_invoice as i')
                ->join('rv_invoice as rvi','rvi.invoice_id','i.id')
                ->join('config_bet_type as cbt','cbt.bet_type_id','rvi.bet_type_id')
                ->join('user as ui','i.user_id','ui.user_id')
                ->where('i.period',$getPeriod)
                ->where('cbt.subgame_id',$subgameid)
                ->where('ui.web_id',$webid)
                ->selectRaw('i.id, i.date, cbt.bet_type_id, ui.user_name, COALESCE(rvi.bet,"-") AS bet, COALESCE(rvi.bet2,"-") AS bet2, COALESCE(rvi.bet3,"-") AS bet3, rvi.amount, rvi.disc, rvi.prize, bayar(rvi.amount,rvi.disc) AS net_amount')
                ->get();

            $dataInvoiceDetails['period'] = $getPeriod;
            $dataInvoiceDetails['dataResults'] = array();


            foreach ($invoice as $i => $inv) {
                $dataInvoiceDetails['dataResults'][$i]['invoice_id'] = $inv->id;
                $dataInvoiceDetails['dataResults'][$i]['date'] = $inv->date;
                $dataInvoiceDetails['dataResults'][$i]['bet_type_id'] = $inv->bet_type_id;
                $dataInvoiceDetails['dataResults'][$i]['username'] = $inv->user_name;
                $dataInvoiceDetails['dataResults'][$i]['bet'] = $inv->bet;
                $dataInvoiceDetails['dataResults'][$i]['bet2'] = $inv->bet2;
                $dataInvoiceDetails['dataResults'][$i]['bet3'] = $inv->bet3;
                $dataInvoiceDetails['dataResults'][$i]['amount'] = $inv->amount;
                $dataInvoiceDetails['dataResults'][$i]['disc'] = $inv->disc;
                $dataInvoiceDetails['dataResults'][$i]['prize'] = "";
                $dataInvoiceDetails['dataResults'][$i]['kei'] = "";
                if (in_array($subgameid,[8,9,10,12,13,42])) {
                    $dataInvoiceDetails['dataResults'][$i]['kei'] = $inv->prize;
                } else {
                    $dataInvoiceDetails['dataResults'][$i]['prize'] = $inv->prize;
                }
                $dataInvoiceDetails['dataResults'][$i]['net_amount'] = $inv->net_amount;
            }
        } elseif ($game->type == Config_game::TYPE_DINGDONG) {
            $getPeriod = DB::table($game->sqltable.'_number')->where('room_id',$roomid)->orderBy('period','DESC')->first()->period+1;

            $selectRawDD = 'cbt.bet_type_id, ui.user_name, ui.nickname, GROUP_CONCAT(bd.bet) AS bet, "" AS bet2, "" AS bet3, bayar(b.amount,b.disc) AS bayar';
            if (in_array($gameId,[Config_game::GAMEID_SC, Config_game::GAMEID_SC_PRIVATE])) {
                $selectRawDD = 'cbt.bet_type_id, ui.user_name, ui.nickname, bd.bet, bd.bet2, "" AS bet3, bayar(b.amount,b.disc) AS bayar';
            } else if (in_array($gameId,[Config_game::GAMEID_BR, Config_game::GAMEID_BR_PRIVATE])) {
                $selectRawDD = 'cbt.bet_type_id, ui.user_name, ui.nickname, bd.bet, bd.bet2, bd.bet3, bayar(b.amount,b.disc) AS bayar';
            } else if (in_array($gameId,[Config_game::GAMEID_DT, Config_game::GAMEID_DT_PRIVATE])) {
                $selectRawDD = 'cbt.bet_type_id, ui.user_name, ui.nickname, IF(bd.bet2!="",CONCAT(bd.bet2,"-",bd.bet),bd.bet) AS bet, "" AS bet2, "" AS bet3, bayar(b.amount,b.disc) AS bayar';
            }

            $invoice = Dd_invoice::from($game->sqltable.'_invoice as i')
                ->join($game->sqltable.'_bet as b','b.invoice_id','i.id')
                ->join($game->sqltable.'_bet_detail as bd','bd.bet_id','b.id')
                ->join('config_bet_type as cbt','cbt.bet_type_id','b.bet_type_id')
                ->join('user as ui','i.user_id','ui.user_id')
                ->join('ddc_limit as dl','dl.id','i.limit_id')
                ->where('dl.room_id',$roomid)
                ->where('i.period',$getPeriod)
                ->where('cbt.subgame_id',$subgameid)
                ->where('ui.web_id',$webid)
                ->groupBy('bd.bet_id')
                ->selectRaw($selectRawDD)
                ->get();

            $dataInvoiceDetails['period'] = $getPeriod;
            $dataInvoiceDetails['dataResults'] = array();

            foreach ($invoice as $i => $inv) {
                $dataInvoiceDetails['dataResults'][$i]['bet_type_id'] = $inv->bet_type_id;
                $dataInvoiceDetails['dataResults'][$i]['username'] = $inv->user_name;
                $dataInvoiceDetails['dataResults'][$i]['bet'] = $inv->bet;
                $dataInvoiceDetails['dataResults'][$i]['bet2'] = $inv->bet2;
                $dataInvoiceDetails['dataResults'][$i]['bet3'] = $inv->bet3;
                $dataInvoiceDetails['dataResults'][$i]['amount'] = $inv->bayar;
            }
        }
        
        return $dataInvoiceDetails;
    }

    public function getTogelInvoices($gameTableName, $webId, $period)
    {
        return ViewInvoice::from('rv_invoice as rvi')
            ->join($gameTableName.'_invoice as i','rvi.invoice_id','i.id')
            ->join('config_bet_type as cbt','cbt.bet_type_id','rvi.bet_type_id')
            ->join('user as ui','i.user_id','ui.user_id')
            ->where('i.period',$period)
            ->where('ui.web_id',$webId)
            ->selectRaw('cbt.subgame_id, COUNT(invoice_id) as totalInvoice, SUM(bayarTG(amount,disc,prize)) as totalAmount')
            ->groupBy('cbt.subgame_id')
            ->get();
    }

    public function getDingdongInvoices($gameTableName, $webId, $period, $room_id)
    {
        return Dd_invoice::query()->from($gameTableName.'_invoice as i')
            ->join($gameTableName.'_bet as b','b.invoice_id','i.id')
            ->join('ddc_limit as dl','dl.id','i.limit_id')
            ->join('config_bet_type as cbt','cbt.bet_type_id','b.bet_type_id')
            ->join('user as ui','i.user_id','ui.user_id')
            ->where('i.period',$period)
            ->where('dl.room_id',$room_id)
            ->where('ui.web_id',$webId)
            ->selectRaw('cbt.subgame_id, COUNT(b.id) as totalInvoice, SUM(bayar(amount,disc)) as totalAmount')
            ->groupBy('cbt.subgame_id')
            ->get();
    }

    public function getWinLosePlayer($userId, $startdate, $enddate)
    {
        $dataTurnover = [];
        $turnover   = Join_transaction_day::query()
        ->where('user_id',$userId)
        ->whereBetween('created_date',[$startdate,$enddate])
        ->where('tover','>','0')
        ->groupby('created_date')->groupby('game_id')
        ->orderby('created_date','DESC')
        ->selectRaw('created_date, game_id, SUM(tover) AS tover, SUM(win) AS win, SUM(lose) AS lose')
        ->get();
        
        foreach ($turnover as $k => $trv) {
            $dataTurnover[$k]['date']       = $trv['created_date'];
            $dataTurnover[$k]['game_id']    = $trv['game_id'];
            $dataTurnover[$k]['turnover']   = $trv['tover'];
            $dataTurnover[$k]['win']        = $trv['win'];
            $dataTurnover[$k]['lose']       = $trv['lose'];
        }

        return $dataTurnover;
    }
}