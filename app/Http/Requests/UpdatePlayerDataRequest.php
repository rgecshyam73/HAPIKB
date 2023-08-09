<?php

namespace App\Http\Requests;

use Illuminate\Http\Exceptions\HttpResponseException;

class UpdatePlayerDataRequest extends APIRequest
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
            'language'      => 'required',
            'hash'          => 'required',
        ];
    }

    public function messages()
    {
        return [
            'operatorid.required'   => 503,
            'username.required'     => 503,
            'language.required'     => 503,
            'referral.required'     => 503,
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
                if (! $this->isValidLanguage()) {
                    return $validator->errors()->add('language', 503);
                }
            });
        }
    }

    private function isValidHash()
    {
        $partner = $this->get('partner');
        $secret_key = $partner->keyhash;

        return $this->hash == hash('sha256', $this->operatorid . $this->username . $this->language . $this->fullname . $this->referral . $this->email . $secret_key);
    }
}
