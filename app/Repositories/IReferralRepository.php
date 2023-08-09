<?php


namespace App\Repositories;


interface IReferralRepository
{
    public function getReferral($webid, $startDate, $endDate, $userId);

    public function getDailyReferral($webid, $startDate, $endDate, $userId);

    public function getBonusReferral($webid, $startDate, $endDate, $status, $userId);

    public function getDownline($uplineUserId);

    public function getTurnover($userId, $startDate, $endDate);

    public function getWinlose($player, $startDate, $endDate);
}