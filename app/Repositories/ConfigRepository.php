<?php

namespace App\Repositories;

use Illuminate\Support\Facades\Cache;

use App\Models\Db1\{Adm_config,Config_curr,Config_lang};

class ConfigRepository implements IConfigRepository
{
    public function getValue($id) 
    {
    	return Cache::tags(['api', 'adm_config', 'adm_config_' . $id])
    	->remember('adm_config_' . $id, 5, function() use ($id){
    		return Adm_config::find($id)->value;
    	});
    }

    public function getCurrencybyCode($code) 
    {
    	return Cache::tags(['api', 'config_curr', 'config_curr_' . $code])
    	->remember('config_curr_' . $code, 5, function() use ($code){
    		return Config_curr::where('code',$code)->first();
    	});
    }

    public function getLangbyCode($code) 
    {
    	return Cache::tags(['api', 'config_lang', 'config_lang_' . $code])
    	->remember('config_lang_' . $code, 5, function() use ($code){
    		return Config_lang::where('code',$code)->first();
    	});
    }
}
