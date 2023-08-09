<?php

namespace App\Http\Requests;

use DB;
use App\Models\Views\ViewPartner;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;
use App\Models\Db1\{Adm_config,Config_game};

class NumberDetailsRequest extends APIRequest
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
            'period'        => 'required',
            'hash'          => 'required',
        ];
    }

    public function messages()
    {
        return [
            'operatorid.required'   => 503,
            'game_id.required'      => 503,
            'period.required'       => 503,
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
                if (! $this->isDDandTGType()) {
                    return $validator->errors()->add('game_id', 503);
                }
                if ($this->room_id) {
                    if (! $this->isValidGameRoomDD()) {
                        return $validator->errors()->add('room_id', 503);
                    }
                }
                if (! $this->isAnyDataResults()) {
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
            'data'      => []
        ];

        throw new HttpResponseException(response()->json($data, 200));
    }

    private function isValidHash()
    {
        $partner = $this->get('partner');
        $secret_key = $partner->keyhash;

        return $this->hash == hash('sha256', $this->operatorid . $this->game_id . $this->period . $this->room_id . $secret_key);
    }

    private function isAnyDataResults(){
        $game = $this->get('game');

        $where   =   ['period'=>$this->period];
        if ($game->type==Config_game::TYPE_DINGDONG) {
            if($this->room_id) {
                $where['room_id'] = $this->room_id;
            }
        }

        $results = DB::table($game->sqltable.'_number')->where($where)->get();
        $this->request->add(['results' => $results]);

        return $results->count() > 0;
    }
}
