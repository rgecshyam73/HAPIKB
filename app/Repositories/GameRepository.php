<?php


namespace App\Repositories;


use App\Models\Db1\Ddc_room;
use App\Models\Views\ViewPartnerGame;
use Illuminate\Support\Facades\Cache;

class GameRepository implements IGameRepository
{
    public function isValidRoomid($gameid , $roomid)
    {
        return Cache::tags(['api', 'ddc_room', 'room_' . $gameid . '_' . $roomid])
            ->remember('room_' . $gameid . '_' . $roomid, 5, function() use ($gameid , $roomid){
                return Ddc_room::where(['game_id'=>$gameid,'room_id'=>$roomid])->value('room_id');
            });
    }

    public function getPartnerGame($webid , $gameid)
    {
        return Cache::tags(['api', 'rv_partner', 'partner_' . $webid . '_' . $gameid])
            ->remember('partner_' . $webid . '_' . $gameid, 5, function() use ($webid , $gameid){
                return ViewPartnerGame::where(['game_id'=>$gameid,'web_id'=>$webid])->first();
            });
    }


    public function getJackpot()
    {
        // TODO: Implement getJackpot() method.
    }

    public function getNumberResults($webid, $typeid, $gameid, $room, $length, $listGame)
    {
        // TODO: Implement getNumberResults() method.
    }

    public function getNumberDetails($gameid, $game, $results)
    {
        // TODO: Implement getNumberDetails() method.
    }

    public function getGameTurnover($input)
    {
        // TODO: Implement getGameTurnover() method.
    }
}