<?php

namespace App\Http\Middleware;

use App\Models\Db1\IpWhitelist;
use Closure,Cache;
use Illuminate\Support\Facades\Log;
use App\Models\Db1\Adm_config;

class OnlyWhitelistIp
{
    const ALLOW_STATUS = 1;
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $allowWhitelistIP = Cache::tags(['api', 'configipwhitelist'])
        ->remember('configipwhitelist', 60*60*1, function(){
            return Adm_config::find(Adm_config::ALLOW_WHITELIST_IP)->value;
        });

        if ($allowWhitelistIP == self::ALLOW_STATUS && config('ip_confirmed') == 0) {
            return response()->json([
                'code'      => 403,
                'msg'       => config('message.403')
            ]);
        }

        return $next($request);
    }
}
