<?php


namespace App\Repositories;


interface ILobbyRepository
{
    public function getUserByUsername($username);

    public function setReferralToUser($userId, $userIdParent);

    public function setReferralToUserWithoutStatus($userId, $referralId);

    public function setRegisterUser($username,$reffId,$webid,$curr_id,$lang_id,$fullname,$email);

    public function logout();

    public function getMarketTime($gameid, $date);

    public function getTableName($tableid, $game);

    public function getOnlinePlayerCount($gameId, $webid);

    public function checkIsOnlinePlayer($player);

    public function updatePlayerSetting($webid, $fullname, $referral, $email, $player, $cfg_lang);

}