<?php

namespace App\Repositories;

use App\Models\Db1\Partner_web;
use Illuminate\Support\Facades\Cache;

use App\Models\Views\ViewPartner;

class PartnerRepository implements IPartnerRepository
{
    public function getPartner($webid) 
    {
    	return ViewPartner::where('web_id', $webid)->first();
    }

    public function getPartnerWeb($webId)
    {
        return Partner_web::query()
            ->where('web_id', $webId)
            ->get();
    }
}
