<?php

namespace LeagueWrap\Api;

use LeagueWrap\Dto;
use LeagueWrap\Exception\ListMaxException;

class League extends AbstractApi
{
    /**
     * Valid version for this api call.
     *
     * @var array
     */
    protected $versions = [
        'v3',
    ];

    /**
     * A list of all permitted regions for the league api call.
     *
     * @param array
     */
    protected $permittedRegions = [
        'br',
        'eune',
        'euw',
        'lan',
        'las',
        'na',
        'oce',
        'ru',
        'tr',
        'kr',
        'jp'
    ];

    /**
     * The amount of time we intend to remember the response for.
     *
     * @var int
     */
    protected $defaultRemember = 43200;

    /**
     * @return string domain used for the request
     */
    public function getDomain()
    {
        return "{$this->getRegion()->getStandardizedDomain()}league/";
    }

    /**
     * Gets the league information by summoner id or summoner.
     *
     * @param Summoner|int $identities
     * @param bool         $entry
     *
     * @throws ListMaxException
     *
     * @return array
     */
    public function league($identity)
    {
        $summonerId = $this->extractId($identity);
        $summonerLeagues = $this->request("leagues/by-summoner/{$summonerId}");

        $leagues = [];
        foreach ($summonerLeagues as $info) {
            $info["id"] = $summonerId;
            $league = new Dto\League($info);
            $leagues[] = $this->attachStaticDataToDto($league);
        }

        $this->attachResponse($identity, $leagues, 'leagues');

        return $leagues;
    }

    public function position($identity)
    {
        $summonerId = $this->extractId($identity);
        $summonerLeaguesPositions = $this->request("positions/by-summoner/{$summonerId}");

        $leagues = [];
        foreach ($summonerLeaguesPositions as $info) {
            $array["id"] = $summonerId;
            $league = new Dto\League($info);
            $leagues[] = $this->attachStaticDataToDto($league);
        }

        $this->attachResponse($identity, $league, 'leagues');
        return $leagues;
    }

    /**
     * Gets the league information for the challenger teams.
     *
     * @param string $queue     could be: RANKED_SOLO_5x5, RANKED_FLEX_SR, RANKED_FLEX_TT
     *
     * @return array
     */
    public function challenger($queue = 'RANKED_SOLO_5x5')
    {
        $info = $this->request("challengerleagues/by-queue/{$queue}");
        $info['id'] = null;

        return $this->attachStaticDataToDto(new Dto\League($info));
    }

    public function master($queue = 'RANKED_SOLO_5x5')
    {
        $info = $this->request("masterleagues/by-queue/{$queue}");
        $info['id'] = null;

        return $this->attachStaticDataToDto(new Dto\League($info));
    }
}
