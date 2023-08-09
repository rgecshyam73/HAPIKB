<?php

namespace App\Http\Requests;

use App\Models\Views\{ViewPartner,ViewPartnerGame};
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;
use App\Models\Db1\{Adm_config,Config_game};
use Cache;

class AllNumberResultsRequest extends APIRequest
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
            // 'hash'          => 'required',
        ];
    }

    public function messages()
    {
        return [
            // 'hash.required'         => 503,
        ];
    }

    public function withValidator($validator)
    {
        if (! $validator->fails()) {
            $validator->after(function ($validator) {
                if (! $this->isMaintenanceAPI()) {
                    return $validator->errors()->add('maintenance', 501);
                }
                // if (! $this->isValidHash()) {
                //     // return $validator->errors()->add('hash', 500);
                // }
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

    // private function isValidHash()
    // {
    //     return $this->hash == hash('sha256',  'a');
    // }
}
