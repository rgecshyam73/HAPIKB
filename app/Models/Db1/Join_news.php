<?php

namespace App\Models\Db1;

use Illuminate\Database\Eloquent\Model;

use App\Interfaces\I_join_news;
use DB;
class Join_news extends Model implements I_join_news
{
    protected $table = 'join_news';

    static function setMarketClose($items) {
    	return constant('static::MARKET_CLOSE_'.strtoupper($items));
    }

    static function setGameStatus($items , $game_id) {
        return constant('static::STATUS_GAME_' . $items. '_' . $game_id);
    }

    function getNewsValue($news_id) {
        $langid = 101;
    	$querynewsvalue = $this->join('join_news_lang', function($join) use($langid) {
            $join->on('join_news.id', 'join_news_lang.news_id')
                 ->where('join_news_lang.lang_id', $langid);
            })->select('join_news_lang.news_id', 'join_news_lang.value');

        if(is_array($news_id)) {
            $querynewsvalue->whereIn('join_news.id', $news_id);
        }else {
            $querynewsvalue->where('join_news.id', $news_id);
        }

        return $querynewsvalue;
    }
}
