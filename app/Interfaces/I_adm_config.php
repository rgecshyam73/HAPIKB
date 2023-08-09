<?php

namespace App\Interfaces;

interface I_adm_config
{
    // INTERFACE ADMCONFIG CONST DIISI ID DARI TABLE ADM_CONFIG
    const USER_ALLOW                            = 19;
    const TABLE_CONFIG_GAME                     = 20;
    const MOBILE_STATUS                         = 26;
    const WAP_STATUS                            = 27;
    const WEB_STATUS                            = 28;
    const NICKNAME_BLOCK                        = 56;
    const USER_ALLOW_MAINTENANCE                = 60;
    const API_STATUS                            = 63;
    const ALLOW_WHITELIST_IP                    = 68;

    // ISI DARI VALUE DITARUH DITABLE MASING-MASING
    const TYPE_BALANCE_TRANSFER_IN                          = 1;
    const TYPE_BALANCE_TRANSFER_OUT                         = 2;
    const TYPE_BALANCE_CORRECTION_PLUS                      = 3;
    const TYPE_BALANCE_CORRECTION_MIN                       = 4;
    const TYPE_REFERRAL                                     = 5;
    const TYPE_BONUS                                        = 6;
    const TYPE_BONUS_EXPIRED                                = 7;
    const TYPE_STATUS_PLAYER                                = 14;
    const TYPE_GAME                                         = 20;
    const TYPE_BALANCE_CANCEL                               = 19;
    const TYPE_BALANCE_BET                                  = 21;
    const TYPE_BALANCE_WIN                                  = 22;
    const TYPE_BALANCE_LOSE                                 = 23;
    const TYPE_BALANCE_BUY_GIFT                             = 24;
    const TYPE_BALANCE_DRAW                                 = 25;
    const TYPE_BALANCE_TOPUP_IN_GAME                        = 26;
    const TYPE_BALANCE_TRANSFER_IN_GAME                     = 27;
    const TYPE_BALANCE_TRANSFER_OUT_GAME                    = 28;
    const TYPE_BALANCE_BUY_JACKPOT                          = 29;
    const TYPE_BALANCE_WIN_REGULAR_JACKPOT                  = 30;
    const TYPE_BALANCE_WIN_MEGA_JACKPOT                     = 31;
    const TYPE_BALANCE_FOLD                                 = 32;
    const TYPE_BALANCE_NONE                                 = 33;
    const TYPE_BALANCE_WINTRIPLE                            = 34;
    const TYPE_BALANCE_WINHALF                              = 35;
    const TYPE_BALANCE_CLAIM_COIN_REWARD                    = 36;
    const TYPE_BALANCE_TRANSFER_OUT_GAME_AUTO               = 48;
    const TYPE_BALANCE_REFUND_FOLD                          = 49;
    const TYPE_BALANCE_REFUND_BUY_JACKPOT                   = 50;
    const TYPE_BALANCE_COINREWARD_CANCEL                    = 119;
    const TYPE_BALANCE_COINREWARD_BET                       = 121;
    const TYPE_BALANCE_COINREWARD_WIN                       = 122;
    const TYPE_BALANCE_COINREWARD_LOSE                      = 123;
    const TYPE_BALANCE_COINREWARD_DRAW                      = 125;
    const TYPE_BALANCE_COINREWARD_TOPUP_IN_GAME             = 126;
    const TYPE_BALANCE_COINREWARD_TRANSFER_IN_GAME          = 127;
    const TYPE_BALANCE_COINREWARD_TRANSFER_OUT_GAME         = 128;
    const TYPE_BALANCE_COINREWARD_BUY_JACKPOT               = 129;
    const TYPE_BALANCE_COINREWARD_WIN_REGULAR_JACKPOT       = 130;
    const TYPE_BALANCE_COINREWARD_WIN_MEGA_JACKPOT          = 131;
    const TYPE_BALANCE_COINREWARD_WINTRIPLE                 = 134;
    const TYPE_BALANCE_COINREWARD_WINHALF                   = 135;
    const TYPE_BALANCE_COINREWARD_TRANSFER_OUT_GAME_AUTO    = 148;
    const TYPE_BALANCE_COINREWARD_REFUND_FOLD               = 149;
    const TYPE_BALANCE_COINREWARD_REFUND_BUY_JACKPOT        = 150;

    // CHANNEL
    const CHANNEL_WEB       = 1;
    const CHANNEL_MOBILE    = 2;
    const CHANNEL_WAP       = 3;
    const CHANNEL_ANDROID   = 4;
    const CHANNEL_IOS       = 5;

    // STATUS
    const STATUS_NOT_ACTIVE  = 0;
    const STATUS_ACTIVE      = 1;
    const STATUS_MAINTENANCE = 2;
}