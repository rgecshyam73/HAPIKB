<?php

namespace App\Interfaces;

interface I_join_news
{
	/** 
	  *isi sesuai urutan ID
	  *
	*/
	const STATUS_GAME_1_MSG    = 0;
	const SITE_MAINTENANCE_MSG = 1;
	const MARKET_CLOSE_201     = 2;
	const MARKET_CLOSE_202     = 3;
	const MARKET_CLOSE_203     = 4;
	const MARKET_CLOSE_204     = 5;
	const MARKET_CLOSE_205     = 6;
	const MARKET_CLOSE_206     = 7;
	const MARKET_CLOSE_303     = 13;
	const MARKET_CLOSE_304     = 16;
	const MARKET_CLOSE_305     = 17;
	const MARKET_CLOSE_306     = 18;
	const MARKET_CLOSE_307     = 19;
	const MARKET_CLOSE_308     = 20;
	const MARKET_CLOSE_309     = 21;
	const STATUS_GAME_0_MSG    = 8;
	const STATUS_GAME_2_MSG    = 9;
	const STATUS_GAME_4_MSG    = 10;
	const SUBGAME_CLOSE_MSG    = 11;



	const CURRENCY_MSG    = 14;
	const PARTNERGAME_MSG    = 15;

	static function setMarketClose($items);
	static function setGameStatus($items, $game_id);
}