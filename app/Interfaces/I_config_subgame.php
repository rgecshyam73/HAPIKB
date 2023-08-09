<?php

namespace App\Interfaces;

interface I_config_subgame
{
	const SUBGAME_4D3D2D        = 0;
	const SUBGAME_4D            = 1;
	const SUBGAME_3D            = 2;
	const SUBGAME_2D            = 3;
	const SUBGAME_COLOKBEBAS    = 4;
	const SUBGAME_COLOKBEBAS2D  = 5;
	const SUBGAME_COLOKNAGA     = 6;
	const SUBGAME_COLOKJITU     = 7;
	const SUBGAME_TENGAHTEPI    = 8;
	const SUBGAME_DASAR         = 9;
	const SUBGAME_5050          = 10;
	const SUBGAME_50502D        = 42;
	const SUBGAME_SHIO          = 11;
	const SUBGAME_SILANGHOMO    = 12;
	const SUBGAME_KEMBANGKEMPIS = 13;
	const SUBGAME_KOMBINASI     = 14;
	const SUBGAME_NOMOR         = 15;
	const SUBGAME_ROW           = 16;
	const SUBGAME_GROUP         = 17;
	const SUBGAME_DUAL          = 18;
	const SUBGAME_TRIPLE        = 19;
	const SUBGAME_QUAD          = 20;
	const SUBGAME_HEXA          = 21;
	const SUBGAME_DOUBLE        = 22;
	const SUBGAME_TRI           = 45;
	const SUBGAME_ALLTRIPLE     = 23;
	const SUBGAME_SUM           = 24;
	const SUBGAME_MONO          = 25;
	const SUBGAME_LAMBANG       = 26;
	const SUBGAME_ANGKA         = 27;
	const SUBGAME_WARNA 		= 28;
	const SUBGAME_B 			= 30;
	const SUBGAME_S 			= 31;
	const SUBGAME_4A 			= 32;
	const SUBGAME_A 			= 33;
	const SUBGAME_ABC 			= 34;
	const SUBGAME_PAIR          = 35;
	const SUBGAME_KIND          = 36;
	const SUBGAME_FULLHOUSE     = 37;
	const SUBGAME_STRAIGHT      = 38;
	const SUBGAME_FLUSH         = 39;
	const SUBGAME_OCTA          = 40;
	const SUBGAME_QUICKBUY      = 41;

	static function getSubgame($items);
}