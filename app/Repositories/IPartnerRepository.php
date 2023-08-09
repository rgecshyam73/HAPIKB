<?php


namespace App\Repositories;


interface IPartnerRepository
{
    public function getPartner($webid);

    public function getPartnerWeb($webId);
}