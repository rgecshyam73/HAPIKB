<?php

namespace App\Http\Middleware;

use DB,Closure,Cache;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\Db1\Log_api;
use App\Models\Db1\Partner_web;
use Illuminate\Support\Facades\Log;

class LogAPI2
{
    /**
     * The trusted proxies for this application.
     *
     * @var array
     */
    public function handle($request, Closure $next)
    {
        config(['start_time' => microtime(true)]);
        config(['req_id' => identifier(10)]);
        config(['partner_name' => @$request->operatorid ?: '-']);
        Log::stack(['daily-request'])->info(json_encode(['req', config('req_id'), config('partner_name'), str_replace('/', '~', $request->path()), $request->all()]));
        
        return $next($request);
    }

    public function terminate($request, $response)
    {
        $apiDetail = substr($response->getContent(), 0, 5000);
        Log::stack(['daily-request'])->info(json_encode(['res', config('req_id'), config('partner_name'), str_replace('/', '~', $request->path()), microtime(true) - config('start_time'), $apiDetail]));
    }
}
