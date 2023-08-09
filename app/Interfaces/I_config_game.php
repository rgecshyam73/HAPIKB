<?php

namespace App\Interfaces;

interface I_config_game
{

	// TYPE DEFINITION
	const HKB_BALANCE           = 0;
	const TYPE_DINGDONG         = 2;
	const TYPE_CARDGAME         = 3;
	const TYPE_TOGEL            = 5;
	const TYPE_LOTTERY          = 6;
	const TYPE_PRIVATE_DINGDONG = 52;
	const TYPE_PRIVATE_CARD     = 53;
	const TYPE_PRIVATE_TOGEL    = 55;


	//GAME DEFINITION
	const GAMEID_TXH            = 101;
	const GAMEID_TXB            = 102;
	const GAMEID_DMC            = 103;
	const GAMEID_DBC            = 104;
	const GAMEID_DMB            = 105;
	const GAMEID_BJM            = 106;
	const GAMEID_CSA            = 107;
	const GAMEID_MMB            = 108;
	const GAMEID_STN            = 109;
	const GAMEID_OMH            = 110;
	const GAMEID_TKG            = 111;
	const GAMEID_JK             = 112;
	const GAMEID_DMQ            = 113;
	const GAMEID_BCT            = 115;
	
	const GAMEID_TXP            = 151;
	const GAMEID_DCP            = 153;
	const GAMEID_DBP            = 154;
	const GAMEID_DMP            = 155;
	const GAMEID_MMP            = 158;
	
	const GAMEID_SGP            = 201;
	const GAMEID_MC             = 202;
	const GAMEID_SD             = 203;
	const GAMEID_CN             = 204;
	const GAMEID_TW             = 205;
	const GAMEID_HK             = 206;
	
	const GAMEID_JH             = 301;
	const GAMEID_JD             = 302;
	const GAMEID_48D            = 310;
	const GAMEID_36D            = 303;
	const GAMEID_24D            = 304;
	const GAMEID_12D            = 305;
	const GAMEID_SC             = 306;
	const GAMEID_DT             = 307;
	const GAMEID_BR             = 308;
	const GAMEID_PD             = 309;
	
	//	PRIVATE
	const GAMEID_48D_PRIVATE    = 358;
	const GAMEID_36D_PRIVATE    = 351;
	const GAMEID_24D_PRIVATE    = 352;
	const GAMEID_12D_PRIVATE    = 353;
	const GAMEID_SC_PRIVATE     = 354;
	const GAMEID_DT_PRIVATE     = 355;
	const GAMEID_BR_PRIVATE     = 356;
	const GAMEID_PD_PRIVATE     = 357;
	
	//STATUS FIELD DEFINITION
	const STATUS_CLOSE          = 0;
	const STATUS_OPEN           = 1;
	const STATUS_MAINTENANCE    = 2;
	const STATUS_DISABLED       = 3;
	const STATUS_COMINGSOON     = 4;

	// JACKPOT DEFINITION

	const JACKPOT_TYPE_DEACTIVE = 0;
	const JACKPOT_TYPE_ACTIVE   = 1;
	const JACKPOT_TYPE_PERIODIC = 2;

	// NAIK KAN KE STG
}