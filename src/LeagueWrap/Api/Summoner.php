<?php

namespace LeagueWrap\Api;

use LeagueWrap\Dto;
use LeagueWrap\Dto\Mastery;
use LeagueWrap\Dto\MasteryPage;
use LeagueWrap\Dto\Rune;
use LeagueWrap\Dto\RunePage;

class Summoner extends AbstractApi
{
    /**
     * The summoners we have loaded.
     *
     * @var array
     */
    protected $summoners = [];

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
    protected $defaultRemember = 600;

    /**
     * Attempt to get a summoner by key.
     *
     * @param string $key
     *
     * @return object|null
     */
    public function __get($key)
    {
        return $this->get($key);
    }

    /**
     * @return string domain used for the request
     */
    public function getDomain()
    {
        return "{$this->getRegion()->getStandardizedDomain()}summoner/";
    }


    /**
     * Gets the information about the user by the given identification. Requests for SummonerId odr Summoner name.
     * IDs must be of type integer, otherwise, numeric string values will be assumed to be summoner-names.
     *
     * In V3 batch calls are removed!
     *
     * @return object|null
     */
    public function get($key)
    {
        $key = strtolower($key);
        if (isset($this->summoners[$key])) {
            return $this->summoners[$key];
        }
    }

    /**
     * Gets the information about the user by the given identification. IDs must be of type integer, otherwise,
     * numeric string values will be assumed to be names.
     *
     * @param int|string $identities
     *
     * @return Dto\Summoner
     */
    public function info($identity)
    {
        $isNumericIdentity = !is_string($identity) && is_numeric($identity) && ctype_digit((string)$identity);
        $isStringIdentity = is_string($identity);
        if (!$isNumericIdentity && !$isStringIdentity) {
            throw new \InvalidArgumentException(
                "the given identity must be a numeric (summoner_id) or a string (summoner_name) ".gettype($identity)." given"
            );
        }

        if ($isNumericIdentity) {
            // it's the id
            $summoner = $this->infoBySummonerId($identity);
        } else {
            // the summoner name
            $summoner = $this->infoBySummonerName($identity);
        }

        return $summoner;
    }

    /**
     * Gets the information by the summonerid of the summoner.
     *
     * @param integer $summonerId
     *
     * @return Dto\Summoner;
     */
    protected function infoBySummonerId($summonerId)
    {
        $info = $this->request('summoners/'.$summonerId);
        $summoner = $this->attachStaticDataToDto(new Dto\Summoner($info));
        $this->summoners[$summoner->name] = $summoner;
        return $summoner;
    }

    /**
     * Gets the information by the accountid of the summoner.
     *
     * @param $accountId
     *
     * @throws \InvalidArgumentException
     *
     * @return Dto\Summoner;
     */
    public function infoByAccountId($accountId)
    {
        if (!((is_string($accountId) || is_numeric($accountId)) && ctype_digit((string)$accountId))) {
            throw new \InvalidArgumentException(
                "the given accountId must be an integer (accountId) ".gettype($accountId)." given"
            );
        }

        $info = $this->request('summoners/by-account/'.$accountId);
        $summoner = $this->attachStaticDataToDto(new Dto\Summoner($info));
        $this->summoners[$summoner->name] = $summoner;
        return $summoner;
    }

    /**
     * Gets the information by the name of the summoner.
     *
     * @param string $names
     *
     * @return Dto\Summoner;
     */
    protected function infoBySummonerName($summonerName)
    {
        // clean the name
        $summonerName = htmlspecialchars($summonerName);
        $info = $this->request('summoners/by-name/'.$summonerName);
        $summoner = $this->attachStaticDataToDto(new Dto\Summoner($info));
        $this->summoners[$summoner->name] = $summoner;
        return $summoner;
    }
}
