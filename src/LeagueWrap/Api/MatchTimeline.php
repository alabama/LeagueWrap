<?php

namespace LeagueWrap\Api;

use LeagueWrap\Dto\MatchTimeline as MatchTimelineDto;

class MatchTimeline extends AbstractApi
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
     * A list of all permitted regions for the Champion api call.
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
        'kr',
        'ru',
        'tr',
        'jp'
    ];

    /**
     * The amount of time we intend to remember the response for.
     *
     * @var int
     */
    protected $defaultRemember = 1800;

    /**
     * @return string domain used for the request
     */
    public function getDomain()
    {
        return "{$this->getRegion()->getStandardizedDomain()}match/";
    }

    public function timeline($matchId)
    {
        $response = $this->request("timelines/by-match/{$matchId}");
        return $this->attachStaticDataToDto(new MatchTimelineDto($response));
    }
}
