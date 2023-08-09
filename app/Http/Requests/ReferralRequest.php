<?php

namespace App\Http\Requests;

use Illuminate\Http\Exceptions\HttpResponseException;

class ReferralRequest extends APIRequest
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
            'start_date'    => 'required|regex:/^([0-9]{4})-([0-9]{2})-([0-9]{2})+$/',
            'end_date'      => 'required|regex:/^([0-9]{4})-([0-9]{2})-([0-9]{2})+$/',
            'hash'          => 'required',
        ];
    }

    public function messages()
    {
        return [
            'operatorid.required'   => 503,
            'username.required'     => 503,
            'start_date.required'   => 503,
            'start_date.regex'      => 503,
            'end_date.required'     => 503,
            'end_date.regex'        => 503,
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
                if (! $this->isEndtimeBigthanStarttime()) {
                    return $validator->errors()->add('start_date', 503);
                }
                if (! $this->isTimeRangeExceeded()) {
                    return $validator->errors()->add('start_date', 502);
                }
                if (! $this->isValidUsername()) {
                    return $validator->errors()->add('username', 310);
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
            'data'      => []
        ];

        throw new HttpResponseException(response()->json($data, 200));
    }

    private function isValidHash()
    {
        $partner = $this->get('partner');
        $secret_key = $partner->keyhash;

        return $this->hash == hash('sha256', $this->operatorid . $this->username . $this->start_date . $this->end_date . $secret_key);
    }

    private function isEndtimeBigthanStarttime()
    {
        return strtotime($this->end_date) > strtotime($this->start_date);
    }

    private function isTimeRangeExceeded()
    {
        return (strtotime($this->end_date)-strtotime($this->start_date)) <= 2678400;
    }
}
