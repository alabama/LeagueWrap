<?php

namespace LeagueWrap;

class Region
{
    /**
     * The region that this object represents.
     *
     * @param string
     */
    protected $region;

    /**
     * Platform ids platform ids for regions
     *
     * @param array
     */
    protected $platformIds = [
        'na'   => 'NA1',
        'euw'  => 'EUW1',
        'br'   => 'BR1',
        'lan'  => 'LA1',
        'las'  => 'LA2',
        'oce'  => 'OC1',
        'eune' => 'EUN1',
        'tr'   => 'TR1',
        'ru'   => 'RU',
        'kr'   => 'KR',
        'jp'   => 'JP1',
    ];

    /**
     * @return string platform id for the selected version
     */
    protected function getPlatformId()
    {
        if (array_key_exists($this->region, $this->platformIds)) {
            return $this->platformIds[$this->region];
        } else {
            return strtoupper($this->region);
        }
    }

    /**
     * v3 domains, see: https://discussion.developer.riotgames.com/articles/652/riot-games-api-v3.html
     * https://{platform}.api.riotgames.com/{game}/{service}/{version}/{resource}
     */
    protected $v3StandardizedDomain = 'https://{platform}.api.riotgames.com/lol/';
    protected $v3PlatformDomain = 'https://{platform}.api.riotgames.com/lol/platform/';


    /**
     * @param $region
     */
    public function __construct($region)
    {
        $this->region = strtolower($region);
    }

    /**
     * Returns the region that was passed in the constructor.
     *
     * @return string
     */
    public function getRegion()
    {
        return $this->region;
    }

    /**
     * Standardized domain across all v3 endpoints.
     */
    public function getStandardizedDomain()
    {
        return str_replace('{platform}', strtolower($this->getPlatformId()), $this->v3StandardizedDomain);
    }

    /**
     * New format according to v3 spec of the API.
     *
     * @return string
     */
    public function getPlatformDomain()
    {
        return str_replace('{platform}', strtolower($this->getPlatformId()), $this->v3PlatformDomain);
    }

    /**
     * Determines whether the given region is locked out.
     *
     * @param array $regions
     *
     * @return bool
     */
    public function isLocked(array $regions)
    {
        if (count($regions) == 0) {
            // no regions are locked from this call.
            return true;
        }

        foreach ($regions as $region) {
            if ($this->region == strtolower($region)) {
                // the region is fine
                return false;
            }
        }

        // the region was not found
        return true;
    }
}
