<?php


namespace App\Repositories;


use App\Models\Db1\Join_referral_com;
use App\Models\Db1\Join_transaction_day;
use App\Models\Db1\Partner_web;
use App\Models\Db1\User_id;

class ReferralRepository implements IReferralRepository
{

    public function getReferral($webid, $startDate, $endDate, $userId)
    {
        $date   =   Partner_web::where('web_id',$webid)->value('pay_ref_date');
        $date   =   $date=="0000-00-00" ? date('Y-m-d'): $date;

        $referral = Join_referral_com::where('join_referral_com.ref_id',$userId)
            ->join('user as ui','ui.user_id','=','join_referral_com.user_id')
            ->whereBetween('created_date',[$startDate,$endDate])
            //->whereDate('created_date', '<', $date)
            ->groupBy('created_date')
            ->groupBy('join_referral_com.user_id')
            ->orderBy('created_date','DESC')
            ->selectRaw('created_date, user_name, COALESCE(SUM(turnover),0) as turnover, COALESCE(SUM(amount),0) as komisi')
            ->get();

        return $referral->map(function($ref) use ($date) {
            return [
                'date' => $ref->created_date,
                'username' => $ref->user_name,
                'turnover' => $ref->turnover,
                'bonus_referral' => $ref->komisi,
                'status' => $ref->created_date < $date ? 1: 0
            ];
        })->all();
    }

    public function getDailyReferral($webid, $startDate, $endDate, $userId)
    {
        $date   =   Partner_web::where('web_id',$webid)->value('pay_ref_date');
        $date   =   $date=="0000-00-00" ? date('Y-m-d'): $date;

        $referral = Join_referral_com::where('join_referral_com.ref_id',$userId)
            ->join('user as ui','ui.user_id','=','join_referral_com.user_id')
            ->whereBetween('created_date',[$startDate,$endDate])
            ->groupBy('created_date')
            ->groupBy('join_referral_com.user_id')
            ->orderBy('created_date','DESC')
            ->selectRaw('created_date, user_name, COALESCE(SUM(turnover),0) as turnover, COALESCE(SUM(amount),0) as komisi')
            ->get();

        return $referral->map(function($ref) use ($date) {
            return [
                'date' => $ref->created_date,
                'username' => $ref->user_name,
                'turnover' => $ref->turnover,
                'bonus_referral' => $ref->komisi,
                'status' => $ref->created_date < $date ? 1: 0
            ];
        })->all();
    }

    public function getBonusReferral($webid, $startDate, $endDate, $status, $userId)
    {
        $date   =   Partner_web::where('web_id',$webid)->value('pay_ref_date');
        $date   =   $date=="0000-00-00" ? date('Y-m-d'): $date;

        $referral = Join_referral_com::where('join_referral_com.ref_id',$userId)
            ->join('user as ui','ui.user_id','=','join_referral_com.user_id')
            ->whereBetween('created_date',[$startDate,$endDate])
            ->where(function($query) use($date,$status) {
                if ($status==0) {
                    $query->whereDate('created_date', '>=', $date);
                } elseif ($status==1) {
                    $query->whereDate('created_date', '<', $date);
                }
            })
            ->groupBy('join_referral_com.user_id')
            ->orderBy('created_date','DESC')
            ->selectRaw('user_name, COALESCE(SUM(turnover),0) as turnover, COALESCE(SUM(amount),0) as komisi')
            ->get();

        return $referral->map(function($ref) {
            return [
                'username' => $ref->user_name,
                'turnover' => $ref->turnover,
                'bonus_referral' => $ref->komisi
            ];
        })->all();
    }

    public function getDownline($uplineUserId)
    {
        $downlines = User_id::where('ref_id',$uplineUserId)->select('user_name','nickname','joindate')->get();
        return $downlines->map(function($downline){
            return [
                'username' => $downline->user_name,
                'nickname' => $downline->nickname,
                'join_date' => $downline->joindate
            ];
        });
    }

    public function getTurnover($userId, $startDate, $endDate)
    {
        $turnover   = Join_transaction_day::where('user_id',$userId)
            ->whereBetween('created_date',[$startDate,$endDate])
            ->where('tover','>','0')
            ->groupby('created_date')
            ->orderby('created_date','DESC')
            ->selectRaw('created_date, SUM(tover) AS tover')
            ->get();

        return $turnover->map(function($turnover){
            return [
                'username' => $turnover->created_date,
                'amount' => $turnover->tover
            ];
        });
    }

    public function getWinlose($player, $startDate, $endDate)
    {
        return true;
    }
}