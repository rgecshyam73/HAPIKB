<?php

namespace App\Http\Requests;

use App\Models\Views\ViewPartner;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;
use App\Models\Db1\Adm_config;

class RegisterRequest extends APIRequest
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
            'currency'      => 'required',
            'language'      => 'required',
            'email'         => 'required|email',
            'fullname'      => 'required',
            'hash'          => 'required',
        ];
    }

    public function messages()
    {
        return [
            'operatorid.required'   => 503,
            'username.required'     => 503,
            'currency.required'     => 503,
            'language.required'     => 503,
            'email.required'        => 503,
            'email.email'           => 503,
            'fullname.required'     => 503,
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
                if (! $this->isValidPrefix()) {
                    return $validator->errors()->add('username', 313);
                }
                if (! $this->isValidReferral()) {
                    return $validator->errors()->add('referral', 503);
                }
                if (! $this->isValidCurrency()) {
                    return $validator->errors()->add('currency', 308);
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

        return $this->hash == hash('sha256', $this->operatorid . $this->username . $this->currency . $this->language . stripslashes($this->fullname) . $this->referral . $this->email . $secret_key);
    }

    /* check referral = prefix */
    private function isValidReferral()
    {
        $partner = $this->get('partner');

        return strtoupper($this->referral) != strtoupper($partner->prefix.'_');
    }
}
