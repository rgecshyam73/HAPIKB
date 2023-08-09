<?php

namespace App\Http\Middleware;

use DB,Closure;
use Illuminate\Http\Request;
use Carbon\Carbon;

class CheckDatabase
{
    /**
     * The trusted proxies for this application.
     *
     * @var array
     */
    public function handle($request, Closure $next)
    {
        try
        {
            DB::connection('mysql')->table(DB::raw('DUAL'))->first([DB::raw(1)]);
        }
        catch(\Exception $e)
        {
            return response()->json([
                'code'      => 399,
                'msg'       => config('message.399')
            ]);
        }
        
        return $next($request);
    }
}
