<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use App\Models\Db1\IpWhitelist;
use Closure,Cache;
use Illuminate\Support\Facades\Log;

class WarnNonWhitelistIp
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $ip = getIP();
        $opid = session('opid') ?? $request->input('operatorid');
        $apiFullUrl = substr($request->fullUrlWithQuery($request->all()), 0, 5000);

        $whitelists = Cache::tags(['api', 'ipwhitelist'])
        ->remember('ipwhitelist', 60*60*24, function(){
            return IpWhitelist::query()->where('app_type', 'API')->get();
        });

        if($whitelists->where('ip', $ip)->count() == 0)
        {
            config(['ip_confirmed' => 0]);
            Log::warning('Non Whitelist IP: '. $ip  .', Partner : ' . $opid . ', trying to access URL : '. $apiFullUrl);
        } else {
            config(['ip_confirmed' => 1]);
        }

        return $next($request);
    }
}
