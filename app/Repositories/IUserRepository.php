<?php


namespace App\Repositories;


interface IUserRepository
{

    public function getUserbyUsername($username);

    public function getValueFromUserByUsername($username,$column);

    public function setReferralToUser($userId, $userIdParent);

    public function setReferralToUserWithoutStatus($userId, $referralId);

    public function setRegisterUser($username,$reffId,$webid,$curr_id,$lang_id,$fullname,$email);

    public function setStatusUserToActive($userId, $fullname, $email);

    public function getAmountLastTransactionBalance($userId);

    public function checkIsUserBlock($userId);

    public function getUserOutstandingBalance($userId);
}