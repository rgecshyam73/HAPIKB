<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class PartnerAuth
{
    /**
     * The trusted proxies for this application.
     *
     * @var array
     */
    public function handle($request, Closure $next)
    {
        if (! session('opid')) {
            return redirect()->route('doc.login');
        }

        return $next($request);
    }
}
