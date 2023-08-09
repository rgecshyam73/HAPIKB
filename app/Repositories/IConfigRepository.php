<?php


namespace App\Repositories;


interface IConfigRepository
{
    public function getValue($id);

    public function getCurrencybyCode($code);

    public function getLangbyCode($code);
}