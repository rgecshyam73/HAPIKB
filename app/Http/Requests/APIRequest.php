<?php

namespace App\Http\Requests;

use DB;
use App\Models\Views\{ViewPartner,ViewPartnerGame,ViewUser};
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use App\Models\Db1\{
    Adm_config,
    Config_curr,
    Config_game,
    Config_lang,
    Ddc_room,
    User_block,
    User_block_trf_out,
};
use App\Repositories\{
    ConfigRepository,
    GameRepository,
    IConfigRepository,
    IGameRepository,
    IPartnerRepository,
    IUserRepository,
    PartnerRepository,
    UserRepository
};
use Illuminate\Support\Facades\Log;

class APIRequest extends FormRequest
{
    protected $configRepo;
    protected $partnerRepo;
    protected $userRepo;
    protected $gameRepo;

    public function __construct(
        IConfigRepository $configRepo,
        IPartnerRepository $partnerRepo,
        IUserRepository $userRepo,
        IGameRepository $gameRepo
    )
    {
        $this->configRepo = $configRepo;
        $this->partnerRepo = $partnerRepo;
        $this->userRepo = $userRepo;
        $this->gameRepo = $gameRepo;

        // add timeout
        set_time_limit(45);
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

    public function isMaintenanceAPI()
    {
        $maintenanceAPI = $this->configRepo->getValue(Adm_config::API_STATUS);

        return $maintenanceAPI == "0";
    }

    public function isValidClient()
    {
        $partner = $this->partnerRepo->getPartner($this->operatorid);
        $this->request->add(['partner' => $partner]);

        if ($partner) {
            return $partner->status != 0;
        }
        
        return false;
    }

    public function isValidUsername()
    {
        $player = $this->userRepo->getUserbyUsername($this->username);
        $this->request->add(['player' => $player]);

        if ($player) {
            return $player;
        }

        return false;
    }

    public function isValidGame()
    {
        $game = $this->gameRepo->getPartnerGame($this->operatorid , $this->game_id);
        $this->request->add(['game' => $game]);

        if ($game) {
            return $game->status_game != 0 && $game->status_partner != 0;
        }

        return false;
    }

    public function isValidCurrency()
    {
        $currency = $this->configRepo->getCurrencybyCode($this->currency);
        $this->request->add(['currenc' => $currency]);

        if ($currency) {
            return $currency->status > 0;
        }

        return false;
    }

    public function isValidLanguage()
    {
        $lang = $this->configRepo->getLangbyCode($this->language);
        $this->request->add(['lang' => $lang]);

        if ($lang) {
            return $lang->status > 0;
        }

        return false;
    }

    public function isValidPrefix()
    {
        $partner = $this->get('partner');
        $userPrefix = strtolower(explode("_", $this->username)[0]);

        return strtolower($partner->prefix) == $userPrefix;
    }

    public function isValidStatusPlayer()
    {
        $player = $this->get('player');
        $userBlock = User_block::where('user_id',$player->user_id)->where('type_id', 0)->get('type_id');
        $block = 1;
        if(count($userBlock) > 0 ) {
            $block = 0;
        } 
        $pass = $player->status == 2 && $block == 0 ? false : true;

        return $pass;
    }

    public function isUnverifiedStatusPlayer()
    {
        $player = $this->get('player');

        return $player->status == 3;
    }

    public function isValidCurrencyPlayer()
    {
        $player = $this->get('player');

        return $player->currency == $this->currency;
    }

    public function isValidGameRoomDD()
    {
        $game = $this->get('game');
        if ($game->type==Config_game::TYPE_DINGDONG) {
            $checkRoom = $this->gameRepo->isValidRoomid($this->game_id , $this->room_id);

            return $checkRoom > 0;
        }

        return true;
    }

    public function isTGType()
    {
        $game = $this->get('game');

        return $game->type == Config_game::TYPE_TOGEL;
    }

    public function isDDandTGType($type = false)
    {
        if (! $type) {
            $game = $this->get('game');
            $type = $game->type;
        }

        return in_array($type,[Config_game::TYPE_TOGEL, Config_game::TYPE_DINGDONG]);
    }

    public function isOnlineDB()
    {
        try {            
            if(!(DB::connection()->getPdo())) {
                throw new Exception('failed get database connection');
            }
        } catch (\Exception $e) {
            return false;
        }

        return true;
    }

    public function isBlockTransferOut()
    {
        try {            
            $player = $this->get('player');

            $block = User_block_trf_out::where('user_name', $player->username)->where('deleted_date', '>=', date('Y-m-d H:i:s'))->first();
        } catch (\Exception $e) {
            return false;
        }

        $this->request->add(['block_transfer_out' => @$block !== null]);

        return $block !== null;
    }
}
