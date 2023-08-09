<?php

namespace App\Http\Requests;

use App\Models\Views\{ViewPartner,ViewUser};
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;
use App\Models\Db1\{Adm_config,Hkb_token};

class GetTokenRequest extends APIRequest
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
            'token' => 'required',
            'timestamp' => 'required|regex:/^([0-9]{4})-([0-9]{2})-([0-9]{2})T([0-9]{2}):([0-9]{2}):([0-9]{2})+$/',
            'hash' => 'required',
        ];
    }

    public function messages()
    {
        return [
            'operatorid.required'   => 503,
            'username.required'     => 503,
            'token.required'        => 503,
            'timestamp.required'    => 503,
            'timestamp.regex'       => 503,
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
                if (! $this->isTimeRangeExceeded()) {
                    // return $validator->errors()->add('timestamp', 503);
                }
                /*if (! $this->isAlreadyInsertedToken()) {
                    return $validator->errors()->add('timestamp', 318);
                }*/
                if (! $this->isHaveUserAgent()) {
                    return $validator->errors()->add('hash', 503);
                }
            });
        }
    }

    public function failedValidation($validator){
        $code   = $validator->errors()->first();
        $msg    = config('message.'.$code);
        $data   = [
            'code'  => $code,
            'msg'   => $msg,
            'gt'    => 0,
        ];

        throw new HttpResponseException(response()->json($data, 200));
    }

    private function isValidHash()
    {
        $partner = $this->get('partner');
        $secret_key = $partner->keyhash;

        return $this->hash == hash('sha256', $this->operatorid .  $this->username . $this->token . $this->timestamp . $secret_key);
    }

    private function isAlreadyInsertedToken()
    {
        $checkToken = Hkb_token::where(['web_id'=>$this->operatorid,'username'=>$this->username])->first(['id']);

        if (! empty($checkToken)) {
            return false;
        }

        return true;
    }

    private function isTimeRangeExceeded()
    {
        return (strtotime(date('Y-m-d H:i:s'))-strtotime($this->timestamp)) <= 10;
    }

    private function isHaveUserAgent()
    {
        if (! @$_SERVER['HTTP_USER_AGENT']) {
            return false;
        }

        return true;
    }
}
