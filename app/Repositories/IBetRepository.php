<?php


namespace App\Repositories;


interface IBetRepository
{
    public function betDetails($webid, $startTime, $endTime);

    public function OutstandingBet($webid, $game);

    public function getTogelInvoices($gameTableName, $webId, $period);

    public function getDingdongInvoices($gameTableName, $webId, $period, $room_id);

    public function OutstandingBetDetails($webid, $game, $gameId, $subgameid, $roomid);

    public function getWinLosePlayer($userId, $startdate, $enddate);
}