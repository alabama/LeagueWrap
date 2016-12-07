<?php

namespace LeagueWrap\Dto;

/**
 * List made for multiple RankedStats when calling ranked stats endpoint multiple times
 * (for example for each summoner in current game)
 *
 * @package LeagueWrap\Dto
 */
class RankedStatsList extends AbstractListDto
{
    protected $listKey = '';

    /**
     * @param array $info
     */
    public function __construct(array $info)
    {
        $dtos = [];

        foreach ($info as $playerId => $rankedStats) {
            $dtos[$playerId] = new RankedStats($rankedStats);
        }

        parent::__construct($dtos);
    }

    /**
     * Get the ranked stats for player.
     *
     * @param int $playerStatId
     *
     * @return RankedStats|null
     */
    public function playerStat($playerStatId)
    {
        if (!isset($this->info[$playerStatId])) {
            return null;
        }

        return $this->info[$playerStatId];
    }
}
