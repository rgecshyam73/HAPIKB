<?php

namespace App\Http\Requests;

use Illuminate\Http\Exceptions\HttpResponseException;

class TestRequest extends APIRequest
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
            'email'    => 'email',
        ];
    }

    public function messages()
    {
        return [
            'email.email' => 503,
        ];
    }

    public function withValidator($validator)
    {
        if (! $validator->fails()) {
            $validator->after(function ($validator) {
                if ($this->email && ! $this->validateEmail()) {
                    return $validator->errors()->add('email', 444);
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

    private function validateEmail()
    {
        return filter_var($this->email,FILTER_VALIDATE_EMAIL);
    }
}
