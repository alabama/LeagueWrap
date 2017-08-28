<?php

namespace LeagueWrap\Enum;


class MatchmakingQueueEnum extends BaseEnum
{
    const CUSTOM                            = 0; //    Custom games
    const NORMAL_3x3                        = 8; //    Normal 3v3 games
    const NORMAL_5x5_BLIND                  = 2; //    Normal 5v5 Blind Pick games
    const NORMAL_5x5_DRAFT                  = 14; //    Normal 5v5 Draft Pick games
    const RANKED_SOLO_5x5                   = 4; //    Ranked Solo 5v5 games
    const RANKED_PREMADE_5x5                = 6; //    Ranked Premade 5v5 games
    const RANKED_FLEX_TT                    = 9; //    Used for both historical Ranked Premade 3v3 games and current Ranked Flex Twisted Treeline games
    const RANKED_TEAM_3x3                   = 41; //    Ranked Team 3v3 games
    const RANKED_TEAM_5x5                   = 42; //    Ranked Team 5v5 games
    const ODIN_5x5_BLIND                    = 16; //    Dominion 5v5 Blind Pick games
    const ODIN_5x5_DRAFT                    = 17; //    Dominion 5v5 Draft Pick games
    const BOT_5x5                           = 7; //    Historical Summoner's Rift Coop vs AI games
    const BOT_ODIN_5x5                      = 25; //    Dominion Coop vs AI games
    const BOT_5x5_INTRO                     = 31; //    Summoner's Rift Coop vs AI Intro Bot games
    const BOT_5x5_BEGINNER                  = 32; //    Summoner's Rift Coop vs AI Beginner Bot games
    const BOT_5x5_INTERMEDIATE              = 33; //    Historical Summoner's Rift Coop vs AI Intermediate Bot games
    const BOT_TT_3x3                        = 52; //    Twisted Treeline Coop vs AI games
    const GROUP_FINDER_5x5                  = 61; //    Team Builder games
    const ARAM_5x5                          = 65; //    ARAM games
    const ONEFORALL_5x5                     = 70; //    One for All games
    const FIRSTBLOOD_1x1                    = 72; //    Snowdown Showdown 1v1 games
    const FIRSTBLOOD_2x2                    = 73; //    Snowdown Showdown 2v2 games
    const SR_6x6                            = 75; //    Summoner's Rift 6x6 Hexakill games
    const URF_5x5                           = 76; //    Ultra Rapid Fire games
    const ONEFORALL_MIRRORMODE_5x5          = 78; //    One for All (Mirror mode)
    const BOT_URF_5x5                       = 83; //    Ultra Rapid Fire games played against AI games
    const NIGHTMARE_BOT_5x5_RANK1           = 91; //    Doom Bots Rank 1 games
    const NIGHTMARE_BOT_5x5_RANK2           = 92; //    Doom Bots Rank 2 games
    const NIGHTMARE_BOT_5x5_RANK5           = 93; //    Doom Bots Rank 5 games
    const ASCENSION_5x5                     = 96; //    Ascension games
    const HEXAKILL                          = 98; //    Twisted Treeline 6x6 Hexakill games
    const BILGEWATER_ARAM_5x5               = 100; //    Butcher's Bridge games
    const KING_PORO_5x5                     = 300; //    King Poro games
    const COUNTER_PICK                      = 310; //    Nemesis games
    const BILGEWATER_5x5                    = 313; //    Black Market Brawlers games
    const SIEGE                             = 315; //    Nexus Siege games
    const DEFINITELY_NOT_DOMINION_5x5       = 317; //    Definitely Not Dominion games
    const ARURF_5X5                         = 318; //    All Random URF games
    const ARSR_5x5                          = 325; //    All Random Summoner's Rift games
    const TEAM_BUILDER_DRAFT_UNRANKED_5x5   = 400; //    Normal 5v5 Draft Pick games
    const TEAM_BUILDER_DRAFT_RANKED_5x5     = 410; //    Ranked 5v5 Draft Pick games
    const TEAM_BUILDER_RANKED_SOLO          = 420; //    Ranked Solo games from current season that use Team Builder matchmaking
    const TB_BLIND_SUMMONERS_RIFT_5x5       = 430; //    Normal 5v5 Blind Pick games
    const RANKED_FLEX_SR                    = 440; //    Ranked Flex Summoner's Rift games
    const ASSASSINATE_5x5                   = 600; //    Blood Hunt Assassin games
    const DARKSTAR_3x3                      = 610; //    Dark Star games
    const BOT_TT_3x3_SIMPLE                 = 800; //    Twisted Treeline AI vs AI games
}
