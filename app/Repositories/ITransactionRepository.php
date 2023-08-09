<?php


namespace App\Repositories;


interface ITransactionRepository
{
    public function transfer($dataUser,$cfg_curr,$username,$webid,$web_transferId,$amount,$type,$sym);

    public function getDetailTransaction($params);
}