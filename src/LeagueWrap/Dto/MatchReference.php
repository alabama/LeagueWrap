<?php

namespace LeagueWrap\Dto;

use LeagueWrap\Enum\MatchmakingQueueEnum;
use LeagueWrap\Enum\SeasonEnum;

/**
 * Class MatchReference
 * This class represents a single match of a match list.
 * Match references hold less data then real Match objects.
 */
class MatchReference extends AbstractDto
{
    public function __construct(array $info)
    {
        if (isset($info['queue'])) {
            $info["queueType"] = MatchmakingQueueEnum::convert($info['queue']);
        }
        if (isset($info['season'])) {
            $info["seasonName"] = SeasonEnum::convert($info['season']);
        }

        parent::__construct($info);
    }
}
