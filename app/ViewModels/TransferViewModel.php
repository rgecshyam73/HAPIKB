<?php

namespace App\ViewModels;

use Spatie\ViewModels\ViewModel;

class TransferViewModel extends ViewModel
{
    public $ext_id;
    public $code;
    public $msg;
    public $amount;

    public function __construct($ext_id, $code, $msg, $amount)
    {
        //
        $this->ext_id = $ext_id;
        $this->code = $code;
        $this->msg = $msg;
        $this->amount = $amount;
    }
}
