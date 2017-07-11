<?php

namespace LeagueWrap\Api;

use LeagueWrap\Dto\Mastery;
use LeagueWrap\Dto\MasteryPage;

class Masteries extends AbstractApi
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
     * @return string domain used for the request
     */
    public function getDomain()
    {
        return $this->getRegion()->getPlatformDomain();
    }

    /**
     * Gets all the mastery pages of the given user object or id.
     *
     * @param mixed $identities
     *
     * @return array
     */
    public function masteryPages($summonerId)
    {
        if(!(is_numeric($summonerId) && ctype_digit((string)$summonerId))) {
            throw new \InvalidArgumentException(
                "the given summonerId must be an integer ".gettype($summonerId)." given"
            );
        }

        $data = $this->request("masteries/by-summoner/{$summonerId}");
        $masteryPages = [];
        foreach ($data['pages'] as $info) {
            if (!isset($info['masteries'])) {
                // seting the talents to an empty array
                $info['masteries'] = [];
            }

            $masteriesInfo = $info['masteries'];
            unset($info['masteries']);
            $masteryPage = $this->attachStaticDataToDto(new MasteryPage($info));
            // set masterys
            $masteries = [];
            foreach ($masteriesInfo as $mastery) {
                $id = $mastery['id'];
                $mastery = $this->attachStaticDataToDto(new Mastery($mastery));
                $masteries[$id] = $mastery;
            }
            $masteryPage->masteries = $masteries;
            $masteryPages[] = $masteryPage;
        }

        return $masteryPages;
    }
}