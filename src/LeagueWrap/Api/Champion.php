<?php

namespace LeagueWrap\Api;

use LeagueWrap\Dto\Champion as Champ;
use LeagueWrap\Dto\ChampionList;

class Champion extends AbstractApi
{
    /**
     * Do we want to only get the free champions?
     *
     * @param string
     */
    protected $free = 'false';

    /**
     * Valid versions for this api call.
     *
     * @var array
     */
    protected $versions = [
        'v3'
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
        'ru',
        'tr',
        'kr',
    ];

    /**
     * The amount of time we intend to remember the response for.
     *
     * @var int
     */
    protected $defaultRemember = 86400;

    /**
     * Gets all the champions in the given region.
     *
     * @return ChampionList
     */
    public function all()
    {
        $params = [
            'freeToPlay' => $this->free,
        ];

        $info = $this->request($this->getEndpointName(), $params);

        // set up the champions
        $championList = new ChampionList($info);

        return $this->attachStaticDataToDto($championList);
    }

    /**
     * Gets the information for a single champion.
     *
     * @param int $championId
     *
     * @return Champ
     */
    public function championById($championId)
    {
        $info = $this->request($this->getEndpointName().'/'.$championId);

        return $this->attachStaticDataToDto(new Champ($info));
    }

    /**
     * Gets all the free champions for this week.
     *
     * @uses $this->all()
     *
     * @return championList
     */
    public function free()
    {
        $this->free = 'true';
        $championList = $this->all();
        $this->free = 'false';

        return $championList;
    }

    /**
     * Endpoint URI fragment. Can change between API versions.
     *
     * @return string
     */
    protected function getEndpointName()
    {
        if ($this->getVersion() === 'v3') {
            return 'champions';
        }

        return 'champion';
    }

    /**
     * @return string domain used for the request
     */
    public function getDomain()
    {
        // Account for v3 changes
        if ($this->getVersion() === 'v3') {
            return $this->getRegion()->getPlatformDomain();
        }

        return $this->getRegion()->getDefaultDomain();
    }
}
