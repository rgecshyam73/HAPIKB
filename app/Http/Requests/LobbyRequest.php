<?php

namespace App\Http\Requests;

use Illuminate\Http\Exceptions\HttpResponseException;

class LobbyRequest extends APIRequest
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
            'operatorid'    => 'required',
            'username'      => 'required',
            'token'         => 'required',
        ];
    }

    public function messages()
    {
        return [
            'operatorid.required'   => 503,
            'username.required'     => 503,
            'token.required'        => 503,
        ];
    }

    public function withValidator($validator)
    {
        if (! $validator->fails()) {
            $validator->after(function ($validator) {
                if (! $this->isValidClient()) {
                    return $validator->errors()->add('operatorid', 311);
                }
                if (! $this->isValidPrefix()) {
                    return $validator->errors()->add('username', 313);
                }
                if (! $this->isValidUsername()) {
                    return $validator->errors()->add('username', 310);
                }
                if (! $this->checkAuthSess()) {
                    return $validator->errors()->add('token', 315);
                }
                if (! $this->checkIsUserBlock()) {
                    return $validator->errors()->add('username', 316);
                }
            });
        }
    }

    public function failedValidation($validator){
        $code   = $validator->errors()->first();
        $msg    = config('message.'.$code);
        $data   = [
            'code'      => $code,
            'msg'       => $msg
        ];

        throw new HttpResponseException(response()->json($data, 200));
    }

    protected function checkAuthSess()
    {
        $partner    = $this->get('partner');
        $myHash     = hash('sha256',$this->operatorid.$this->username.$this->token.$partner->keyhash);
        $linkAuth   = $partner->address.'?token='.$this->token.'&hash='.$myHash.'&operatorid='.$this->operatorid.'&username='.$this->username;

        $data = json_decode(curl_domain($linkAuth), true);
        
        return @$data['code'] == '0';
    }

    public function checkIsUserBlock() 
    {
        $userBlock = $this->userRepo->checkIsUserBlock($this->userRepo->getUserbyUsername($this->username)['user_id']);

        if($userBlock == 0) {
            return false;
        }
        return true; 
    }
}
