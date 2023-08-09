<?php


namespace App\Repositories;


interface IGameRepository
{
    public function getJackpot();

    public function getNumberResults($webid, $typeid, $gameid, $room, $length, $listGame);

    public function getNumberDetails($gameid, $game, $results);

    public function getGameTurnover($input);
}