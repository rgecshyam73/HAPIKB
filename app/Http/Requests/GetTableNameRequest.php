<?php

namespace App\Http\Requests;

use Illuminate\Http\Exceptions\HttpResponseException;
use App\Models\Db1\Config_game;

class GetTableNameRequest extends APIRequest
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
            'game_id'       => 'required',
            'table_id'      => 'required',
            'hash'          => 'required',
        ];
    }

    public function messages()
    {
        return [
            'operatorid.required'   => 503,
            'game_id.required'      => 503,
            'table_id.required'     => 503,
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
                if (! $this->isValidGame()) {
                    return $validator->errors()->add('game_id', 503);
                }
                if (! $this->isDDandCGType()) {
                    return $validator->errors()->add('game_id', 503);
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

        return $this->hash == hash('sha256', $this->operatorid . $this->game_id . $this->table_id . $secret_key);
    }

    private function isDDandCGType()
    {
        $game = $this->get('game');
        return in_array($game->type,[Config_game::TYPE_CARDGAME, Config_game::TYPE_DINGDONG]);
    }
}
