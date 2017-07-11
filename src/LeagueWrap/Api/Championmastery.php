<?php

namespace LeagueWrap\Api;

use LeagueWrap\Dto\ChampionMasteryList;

class Championmastery extends AbstractApi
{
    /**
     * Valid version for this api call.
     *
     * @var array
     */
    protected $versions = [
        "v3"
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
    protected $defaultRemember = 900;

    /**
     * Endpoint URI fragment. Can change between API versions.
     *
     * @return string
     */
    protected function getEndpointName()
    {
        return "champion-mastery";
    }

    /**
     * @return string domain used for the request
     */
    public function getDomain()
    {
        return "{$this->getRegion()->getStandardizedDomain()}champion-mastery/";
    }

    public function champions($identity)
    {
        $summonerId = $this->extractId($identity);
        $response = $this->request("champion-masteries/by-summoner/{$summonerId}", []);

        $championMasteryList = new ChampionMasteryList($response);
        $this->attachResponse($identity, $championMasteryList, 'championmastery');

        return $championMasteryList;
    }

    public function champion($identity, $championId)
    {
        $summonerId = $this->extractId($identity);
        $response = $this->request("champion-masteries/by-summoner/{$summonerId}/by-champion/{$championId}", []);

        $mastery = new \LeagueWrap\Dto\ChampionMastery($response);
        $this->attachResponse($identity, $mastery, 'championmastery');

        return $mastery;
    }

    public function score($identity)
    {
        $summonerId = $this->extractId($identity);
        $response = $this->request("scores/by-summoner/{$summonerId}", []);

        $score = intval($response);
        $this->attachResponse($identity, $score, 'score');

        return intval($response);
    }
}
