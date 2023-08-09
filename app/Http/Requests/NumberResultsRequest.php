<?php

namespace App\Http\Requests;

use App\Models\Views\{ViewPartner,ViewPartnerGame};
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;
use App\Models\Db1\{Adm_config,Config_game};
use Cache;

class NumberResultsRequest extends APIRequest
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
            'type_id'       => 'required',
            'hash'          => 'required',
        ];
    }

    public function messages()
    {
        return [
            'operatorid.required'   => 503,
            'type_id.required'      => 503,
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
                if (! $this->isDDandTGType($this->type_id)) {
                    return $validator->errors()->add('type_id', 503);
                }
                if (! $this->isAnyGameActive()) {
                    return $validator->errors()->add('type_id', 503);
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

        return $this->hash == hash('sha256', $this->operatorid . $this->type_id . $this->game_id . $this->room_id . $this->length . $secret_key);
    }

    private function isAnyGameActive()
    {
        $where   =   ['type'=>$this->type_id,'web_id'=>$this->operatorid];
        if ($this->game_id) {
            $where['game_id']   =   $this->game_id;
        }

        $listGame = Cache::tags(['api', 'viewpartner'])
                    ->remember('listgame_' . $this->operatorid . $this->type_id . $this->game_id, 60*5, function() use ($where){
                        return ViewPartnerGame::where($where)->active3()->get();
                    });//ViewPartnerGame::where($where)->active2()->get();
        $this->request->add(['listGame' => $listGame]);

        return $listGame->count() > 0;
    }
}
