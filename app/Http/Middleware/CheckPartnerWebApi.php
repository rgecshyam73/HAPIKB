<?php

namespace App\Http\Middleware;

use App\Repositories\IPartnerRepository;
use Closure;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

class CheckPartnerWebApi
{
    protected $partnerRepository;
    public function __construct(IPartnerRepository $partnerRepository)
    {
        $this->partnerRepository = $partnerRepository;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (! session('opid')) {
            return redirect()->route('api.login.v2');
        }

        $partner = $this->partnerRepository->getPartnerWeb(session('opid'));
        if($partner != null)
        {
            Session::put('partner_web_data', $partner->first());
        }

        return $next($request);
    }
}
