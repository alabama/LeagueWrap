<?php

namespace LeagueWrap\Dto;

/**
 * Class MatchTeam.
 */
class MatchTeam extends AbstractDto
{
    /**
     * Set up the information about this team.
     *
     * @param array $info
     */
    public function __construct(array $info)
    {
        // set teams (match api)
        if (isset($info['bans'])) {
            $rawBans = $info['bans'];
            $bans = [];
            foreach ($rawBans as $key => $rawBan) {
                $bans[$key] = new Ban($rawBan);
            }
            $info['bans'] = $bans;
        }

        if (isset($info['win'])) {
            $info['win'] = strtolower($info["win"]) == "win";
        }

        parent::__construct($info);
    }

    /**
     * Attempts to get a ban from this team.
     *
     * @param int $banId
     *
     * @return Ban|null
     */
    public function ban($banId)
    {
        if (!isset($this->info['bans'])) {
            // no teams
            return;
        }
        $bans = $this->info['bans'];
        if (isset($bans[$banId])) {
            return $bans[$banId];
        }
    }
}
