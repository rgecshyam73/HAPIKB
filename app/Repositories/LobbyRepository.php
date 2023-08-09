<?php


namespace App\Repositories;


use App\Models\Db1\Log_player;
use App\Models\Db1\Partner_web;
use App\Models\Db1\User_active;
use App\Models\Db1\User_coin;
use App\Models\Db1\User_id;
use App\Models\Db1\User_log_type;
use App\Models\Views\ViewUser;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Jenssegers\Agent\Agent;

class LobbyRepository implements ILobbyRepository
{
    protected $agent;

    public function __construct()
    {
        $this->agent  = new Agent;
    }

    public function setRegisterUser($username, $reffId, $webid, $curr_id, $lang_id, $fullname, $email)
    {
        $status     = ($reffId=="partner" ? 3: 1);
        $reffId     = ($reffId=="partner" || $reffId=="user" ? 0: $reffId);
        $insertUser = FALSE;

        try {
            DB::transaction(function() use ($username, $reffId, $webid, $curr_id, $lang_id, $fullname, $email, $status, &$insertUser){
                $insertUser = @User_id::insertGetId([
                    'user_name'     => strtoupper($username),
                    'user_pass'     => hash("sha256", bcrypt($username . date("dmYhis")) . '8080'),
                    'fullname'      => $fullname,
                    'email'         => $email,
                    'curr_id'       => $curr_id,
                    'lang_id'       => $lang_id,
                    'country_code'  => 'id',
                    'status'        => $status,
                    'ref_id'        => $reffId,
                    'joindate'      => DB::raw('NOW()'),
                    'web_id'        => $webid,
                    'last_login'    => DB::raw('NOW()'),
                    'last_ip'       => getIP(),
                    'xtransfer'     => 0
                ]);
            }, 5);

        } catch (\Exception $e) {
            $insertUser = FALSE;
        }

        if ($insertUser) {
            DB::transaction(function() use ($insertUser){
                User_coin::insert([
                    'user_id'   => $insertUser,
                    'game_id'   => 0,
                    'coin'      => 0,
                    'valsatu'   => DB::raw('SHA2(CONCAT(`user_id`, `coin`), 256)')
                ]);
            }, 5);


            $logid = User_log_type::where('user_log_name','register')->where('user_type','PL')->where('log_isactive',1)->value('log_id');
            $subwebname = Partner_web::where('web_id',$webid)->value('name');
            if ($this->agent->isMobile()) {
                $channel = 2;
            } else {
                $channel = 1;
            }

            DB::transaction(function() use ($logid, $insertUser, $channel, $subwebname, $username, $webid){
                Log_player::insert([
                    'log_id'    => $logid,
                    'log_desc'  => '-',
                    'log_date'  => DB::raw('NOW()'),
                    'log_ip'    => getIP(),
                    'channel'   => $channel,
                    'web_name'  => strtoupper($subwebname),
                    'user_name' => strtoupper($username),
                    'user_id'   => $insertUser,
                    'web_id'    => $webid
                ]);
            });


            return $insertUser;
        }

        return $insertUser;
    }

    public function logout()
    {
        $webid = session("operatorid");
        $username = session("username");
        $user = Auth::user();

        if($user != null)
        {
            $logid = User_log_type::where('user_log_name','logout')->where('user_type','PL')->where('log_isactive',1)->value('log_id');
            $subwebname = Partner_web::where('web_id',$webid)->value('name');
            if ($this->agent->isMobile()) {
                $channel = 2;
            } else {
                $channel = 1;
            }
            Log_player::insert([
                'log_id'    => $logid,
                'log_desc'  => '-',
                'log_date'  => DB::raw('NOW()'),
                'log_ip'    => getIP(),
                'channel'   => $channel,
                'web_name'  => strtoupper($subwebname),
                'user_name' => strtoupper($username),
                'user_id'   => $user->user_id,
                'web_id'    => $webid
            ]);

            User_active::where('user_id',$user->user_id)->delete();
            Auth::logout();
            session()->flush();
        }

    }

    public function getMarketTime($gameid, $date)
    {
        // TODO: Implement getMarketTime() method.
    }

    public function getTableName($tableid, $game)
    {
        // TODO: Implement getTableName() method.
    }

    public function getOnlinePlayerCount($gameId, $webid)
    {
        // TODO: Implement getOnlinePlayerCount() method.
    }

    public function checkIsOnlinePlayer($player)
    {
        // TODO: Implement checkIsOnlinePlayer() method.
    }

    public function updatePlayerSetting($webid, $fullname, $referral, $email, $player, $cfg_lang)
    {
        // TODO: Implement updatePlayerSetting() method.
    }

    public function getUserByUsername($username)
    {
        return ViewUser::where('username', $username)->first();
    }

    public function setReferralToUser($userId, $userIdParent)
    {
        return User_id::where('user_id',$userId)->update(['status'=>1,'ref_id'=>$userIdParent]);
    }

    public function setReferralToUserWithoutStatus($userId, $referralId)
    {
        return User_id::where('user_id',$userId)->update(['ref_id'=>$referralId]);
    }
}