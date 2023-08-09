<?php

namespace App\Http\Requests;

use App\Models\Views\{ViewPartner,ViewUser};
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;
use App\Models\Db1\{Adm_config,Join_lastorder,Adm_transfer};

class TransferRequest extends APIRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'operatorid' => 'required',
            'username' => 'required',
            'currency' => 'required',
            'dir'   => 'required|between:0,1',
            'amount' => 'required|numeric',
            'trans_id' => 'required|numeric',
            'hash' => 'required',
        ];
    }

    public function messages()
    {
        return [
            'operatorid.required'   => 503,
            'username.required'     => 503,
            'currency.required'     => 503,
            'dir.required'          => 503,
            'dir.between'           => 503,
            'amount.required'       => 503,
            'amount.numeric'        => 503,
            'trans_id.required'     => 503,
            'trans_id.numeric'      => 503,
            'hash.required'         => 503,
        ];
    }

    public function withValidator($validator)
    {
        if (! $validator->fails()) {
            $validator->after(function ($validator) {
                if (! $this->isMaintenanceAPI()) {
                    return $validator->errors()->add('maintenance', 501);
                }
                if (! $this->isValidClient()) {
                    return $validator->errors()->add('operatorid', 311);
                }
                if (! $this->isValidHash()) {
                    return $validator->errors()->add('hash', 500);
                }
                if (! $this->isValidUsername()) {
                    return $validator->errors()->add('username', 310);
                }
                if (! $this->isValidStatusPlayer()) {
                    return $validator->errors()->add('username', 316);
                }
                if (! $this->isValidCurrency()) {
                    return $validator->errors()->add('currency', 308);
                }
                if (! $this->isValidCurrencyPlayer()) {
                    return $validator->errors()->add('currency', 503);
                }
                if (! $this->isAlreadyAcceptedTransaction()) {
                    return $validator->errors()->add('trans_id', 301);
                }
                if (! $this->isValidLastTransaction()) {
                    return $validator->errors()->add('amount', 340);
                }
                if (! $this->isValidValsatu()) {
                    //return $validator->errors()->add('amount', 340);
                }
                if ($this->dir == "0") {
                    if (! $this->isSufficientBalance()) {
                        return $validator->errors()->add('amount', 300);
                    }

                    if (! $this->isBlockTransferOut()) {
                        // return $validator->errors()->add('username', 310);
                    }
                }
            });
        }
    }

    public function failedValidation($validator){
        $code   = $validator->errors()->first();
        $msg    = config('message.'.$code);
        $data   = [
            'code'      => $code,
            'msg'       => $msg,
            'ext_id'    => $code == 319 ? "0" : "",
            'amount'    => ""
        ];

        throw new HttpResponseException(response()->json($data, 200));
    }

    private function isValidHash()
    {
        $partner = $this->get('partner');
        $secret_key = $partner->keyhash;

        return $this->hash == hash('sha256', $this->operatorid . $this->currency . $this->username . $this->trans_id . $this->amount . $this->dir . $secret_key);
    }

    private function isAlreadyAcceptedTransaction()
    {
        $checkTransaction = Adm_transfer::where(['web_transferid'=>$this->trans_id,'web_id'=>$this->operatorid])->first(['id']);

        if (! empty($checkTransaction)) {
            return false;
        }

        return true;
    }

    private function isValidLastTransaction()
    {
        $player = $this->get('player');
        $lastTransaction = Join_lastorder::where('user_id',$player->user_id)->orderby('id','DESC')->value('balance') ?: curr(0,true);

        return $player->balance == $lastTransaction;
    }

    private function isValidValsatu()
    {
        $player = $this->get('player');

        return $player->valsatu === hash('sha256',$player->user_id.$player->balance);
    }

    private function isSufficientBalance()
    {
        $player = $this->get('player');
        $balance = $player->balance - $this->amount;

        return $balance >= 0;
    }
}
