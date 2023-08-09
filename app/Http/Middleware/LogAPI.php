<?php

namespace App\Http\Middleware;

use DB,Closure;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\Db1\Log_api;

class LogAPI
{
    /**
     * The trusted proxies for this application.
     *
     * @var array
     */
    public function handle($request, Closure $next)
    {
        $apiDetail = substr($request->fullUrlWithQuery($request->all()), 0, 1000);
        Log_api::insert(['web_id'=>$request->operatorid,'direction'=>0,'method'=>$this->getMethod($request->segment(2)),'detail'=>$apiDetail,'created_date'=>DB::raw('NOW()'),'ip'=>getIP()]);

        config(['insert_log' => 1]);
        
        return $next($request);
    }

    public function terminate($request, $response)
    {
        if (config('insert_log') == 1) {
            $apiDetail = substr($response->getContent(), 0, 1000);
            Log_api::insert(['web_id'=>$request->operatorid,'direction'=>1,'method'=>$this->getMethod($request->segment(2)),'detail'=>$apiDetail,'created_date'=>DB::raw('NOW()'),'ip'=>getIP()]);
        }
    }

    private function getMethod($str)
    {
        $method = [
            'balance'       =>  '1',
            'register'      =>  '2',
            'transfer'      =>  '3',
            'check_trans'   =>  '4',
        ];

        return array_key_exists($str, $method) ? $method[$str] : $str;
    }
}
