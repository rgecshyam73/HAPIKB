<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PartnerAuthRequest extends FormRequest
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
            'operatorid'    => 'required|exists:partner_web,web_id',
            'keyhash'       => ['required',Rule::exists('partner_web','keyhash')->where('web_id',$this->operatorid)],
        ];
    }
}
