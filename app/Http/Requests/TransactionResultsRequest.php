<?php

namespace App\Http\Requests;

use Illuminate\Http\Exceptions\HttpResponseException;
use App\Models\Db1\Join_balance;

class TransactionResultsRequest extends APIRequest
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
            'version_key'   => 'required',
            'hash'          => 'required',
        ];
    }

    public function messages()
    {
        return [
            'operatorid.required'   => 503,
            'version_key.required'  => 503,
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
                if (! $this->isAnyData()) {
                    return $validator->errors()->add('period', 503);
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
            'data'      => array()
        ];

        throw new HttpResponseException(response()->json($data, 200));
    }

    private function isValidHash()
    {
        $partner = $this->get('partner');
        $secret_key = $partner->keyhash;

        return $this->hash == hash('sha256', $this->operatorid . $this->version_key . $secret_key);
    }

    private function isAnyData()
    {
        $jBalance       = Join_balance::where(['id'=>$this->version_key,'web_id'=>$this->operatorid])->get();//dd(!$jBalance);

        if ($jBalance->count() === 0) {
            $jBalance       = Join_balance::where(['id'=>substr($this->version_key,0,-3),'web_id'=>$this->operatorid])->get();
        }

        $this->request->add(['jBalance' => $jBalance]);

        return $jBalance->count() > 0;
    }
}
