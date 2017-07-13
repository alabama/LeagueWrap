<?php

namespace LeagueWrap\Api;

use LeagueWrap\Dto\CurrentGame as CurrentGameDto;
use LeagueWrap\Dto\FeaturedGames as FeaturedGamesDto;

/**
 * Spectator service endpoint.
 */
class Currentgame extends AbstractApi
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
    protected $defaultRemember = 900;

    /**
     * @return string domain used for the request
     */
    public function getDomain()
    {
        return "{$this->getRegion()->getStandardizedDomain()}spectator/";
    }

    /**
     * Gets the current game of summoner.
     *
     * @param \LeagueWrap\Api\Summoner|int $identity
     *
     * @throws \Exception
     * @throws \LeagueWrap\Exception\CacheNotFoundException
     * @throws \LeagueWrap\Exception\InvalidIdentityException
     * @throws \LeagueWrap\Exception\RegionException
     * @throws \LeagueWrap\Response\HttpClientError
     * @throws \LeagueWrap\Response\HttpServerError
     *
     * @return \LeagueWrap\Dto\AbstractDto
     */
    public function currentGame($identity)
    {
        $summonerId = $this->extractId($identity);
        $response = $this->request("active-games/by-summoner/{$summonerId}");
        $game = $this->attachStaticDataToDto(new CurrentGameDto($response));

        $this->attachResponse($identity, $game, 'game');

        return $game;
    }

    /**
     * Requests all featured games.
     *
     * @throws \Exception
     * @throws \LeagueWrap\Exception\CacheNotFoundException
     * @throws \LeagueWrap\Exception\RegionException
     * @throws \LeagueWrap\Response\HttpClientError
     * @throws \LeagueWrap\Response\HttpServerError
     *
     * @return \LeagueWrap\Dto\AbstractDto
     */
    public function featuredGames()
    {
        $response = $this->request("featured-games");

        return $this->attachStaticDataToDto(new FeaturedGamesDto($response));
    }
}
