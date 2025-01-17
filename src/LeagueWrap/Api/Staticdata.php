<?php

namespace LeagueWrap\Api;

use LeagueWrap\Dto\StaticData\Champion as staticChampion;
use LeagueWrap\Dto\StaticData\ChampionList;
use LeagueWrap\Dto\StaticData\Item as staticItem;
use LeagueWrap\Dto\StaticData\ItemList;
use LeagueWrap\Dto\StaticData\Mastery as staticMastery;
use LeagueWrap\Dto\StaticData\MasteryList;
use LeagueWrap\Dto\StaticData\Realm as staticRealm;
use LeagueWrap\Dto\StaticData\Rune as staticRune;
use LeagueWrap\Dto\StaticData\RuneList;
use LeagueWrap\Dto\StaticData\SummonerSpell as staticSummonerSpell;
use LeagueWrap\Dto\StaticData\SummonerSpellList;

class Staticdata extends AbstractApi
{
    /**
     * The locale you want the response in. By default it is not
     * passed (null).
     *
     * @var string
     */
    protected $locale = null;

    /**
     * The version of League of Legends that we want data from. By default
     * it is not passed (null).
     *
     * @var string
     */
    protected $DDversion = null;

    /**
     * Valid version for this api call.
     *
     * @var array
     */
    protected $versions = [
        'v3',
    ];

    /**
     * A list of all permitted regions for the Staticdata api call.
     *
     * @var array
     */
    protected $permittedRegions = [
        'br',
        'eune',
        'euw',
        'kr',
        'lan',
        'las',
        'na',
        'oce',
        'ru',
        'tr',
        'jp'
    ];

    /**
     * A list of all calls that require to get the data by Id.
     *
     * @var array
     */
    protected $dataById = ['champions', 'summoner-spells'];

    /**
     * The amount of time we intend to remember the response for.
     *
     * @var int
     */
    protected $defaultRemember = 86400;

    /**
     * Sets the locale the data should be returned in. Null returns
     * the default local for that region.
     *
     * @param string $locale
     *
     * @return $this
     */
    public function setLocale($locale)
    {
        $this->locale = $locale;

        return $this;
    }

    /**
     * Sets the DDversion to be used in the query. Null will return
     * the most recent version.
     *
     * @param string $DDversion
     *
     * @return $this
     */
    public function setDDversion($DDversion = null)
    {
        $this->DDversion = $DDversion;

        return $this;
    }

    /**
     * @return string domain used for the request
     */
    public function getDomain()
    {
        return "{$this->getRegion()->getStandardizedDomain()}static-data/";
    }

    /**
     * Gets all static champion data with the given $data option.
     *
     * @param mixed $data
     *
     * @return ChampionList
     */
    public function getChampions($data = null)
    {
        return $this->getChampion(null, $data);
    }

    /**
     * Gets the static champion data of all champions if $championId is null.
     * If $championI is set it will attempt to get info for that champion only.
     *
     * @param int   $championId
     * @param mixed $data
     *
     * @return ChampionList|Champion
     */
    public function getChampion($championId, $data = null)
    {
        $params = $this->setUpParams('champions', $championId, $data, 'tags', 'tags');
        $array = $this->makeRequest('champions', $championId, $params);

        if ($this->appendId($championId)) {
            return new staticChampion($array);
        } else {
            return new ChampionList($array);
        }
    }

    /**
     * Gets static data on all items.
     *
     * @param mixed $data
     *
     * @return ItemList
     */
    public function getItems($data = null)
    {
        return $this->getItem(null, $data);
    }

    /**
     * Gets the static item data of all items if $itemId is null.
     * If $itemId is set it will attempt to get info for that item only.
     *
     * @param int   $itemId
     * @param mixed $data
     *
     * @return staticItem|ItemList
     */
    public function getItem($itemId, $data = null)
    {
        $params = $this->setUpParams('items', $itemId, $data, 'tags', 'tags');
        $array = $this->makeRequest('items', $itemId, $params);

        if ($this->appendId($itemId)) {
            return new staticItem($array);
        } else {
            return new ItemList($array);
        }
    }

    /**
     * Gets static data on all masteries.
     *
     * @param mixed $data
     *
     * @return MasteryList
     */
    public function getMasteries($data = null)
    {
        return $this->getMastery(null, $data);
    }

    /**
     * Gets the mastery data of all masteries if $masteryId is null.
     * If $masteryId is a set it will attempt to get info for that mastery only.
     *
     * @param int   $masteryId
     * @param mixed $data
     *
     * @return MasteryList|staticMastery
     */
    public function getMastery($masteryId, $data = null)
    {
        $params = $this->setUpParams('masteries', $masteryId, $data, 'tags', 'tags');
        $array = $this->makeRequest('masteries', $masteryId, $params);

        if ($this->appendId($masteryId)) {
            return new staticMastery($array);
        } else {
            return new MasteryList($array);
        }
    }

    /**
     * Gets static data on all runes.
     *
     * @param mixed $data
     *
     * @return RuneList
     */
    public function getRunes($data = null)
    {
        return $this->getRune('all', $data);
    }

    /**
     * Gets the rune data of all runes if $runeId is null.
     * If $runeId is set it will attempt to get info for that rune only.
     *
     * @param int   $runeId
     * @param mixed $data
     *
     * @return RuneList|staticRune
     */
    public function getRune($runeId, $data = null)
    {
        $params = $this->setUpParams('rune', $runeId, $data, 'tags', 'tags');
        $array = $this->makeRequest('runes', $runeId, $params);

        if ($this->appendId($runeId)) {
            return new staticRune($array);
        } else {
            return new RuneList($array);
        }
    }

    /**
     * Gets static data on all summoner spells.
     *
     * @param mixed $data
     *
     * @return SummonerSpellList
     */
    public function getSummonerSpells($data = null)
    {
        return $this->getSummonerSpell('all', $data);
    }

    /**
     * Gets the summoner spell data of all spells if $summonerSpellId is null
     * If $summonerSpellId is set it will attept to get info for that spell only.
     *
     * @param int   $summonerSpellId
     * @param mixed $data
     *
     * @return staticSummonerSpell|SummonerSpellList
     */
    public function getSummonerSpell($summonerSpellId, $data = null)
    {
        $params = $this->setUpParams('summoner-spells', $summonerSpellId, $data, 'tags', 'tags');
        $array = $this->makeRequest('summoner-spells', $summonerSpellId, $params);

        if ($this->appendId($summonerSpellId)) {
            return new staticSummonerSpell($array);
        } else {
            return new SummonerSpellList($array);
        }
    }

    /**
     * Get the realm information for the current region.
     *
     * @return staticRealm
     */
    public function getRealm()
    {
        $params = $this->setUpParams();
        $array = $this->makeRequest('realms', null, $params);

        return new staticRealm($array);
    }

    /**
     * Get the version information for the current region.
     *
     * @return array
     */
    public function version()
    {
        $params = $this->setUpParams();

        return $this->makeRequest('versions', null, $params);
    }

    /**
     * Make the request given the proper information.
     *
     * @param string $path
     * @param mixed  $requestId
     * @param array  $params
     *
     * @return array
     */
    protected function makeRequest($path, $requestId, array $params)
    {
        if ($this->appendId($requestId)) {
            $path .= "/$requestId";
        }

        return $this->request($path, $params, true);
    }

    /**
     * Set up the boiler plate for the param array for any
     * static data call.
     *
     * @param string $name      of api call
     * @param mixed  $requestId
     * @param mixed  $data
     * @param string $listData
     * @param string $itemData
     *
     * @return array
     */
    protected function setUpParams($name = '', $requestId = null, $data = null, $listData = '', $itemData = '')
    {
        $params = [];
        if (!is_null($this->locale)) {
            $params['locale'] = $this->locale;
        }
        if (!is_null($this->DDversion)) {
            $params['version'] = $this->DDversion;
        }
        if (!$this->appendId($requestId) && $this->dataById($name)) {
            $params['dataById'] = 'true';
        }
        if (!is_null($data)) {
            if ($this->appendId($requestId)) {
                $params[$itemData] = $data;
            } else {
                $params[$listData] = $data;
            }
        }

        return $params;
    }

    /**
     * Check if we should append the id to the end of the
     * url or not.
     *
     * @param mixed $requestId
     *
     * @return bool
     */
    protected function appendId($requestId)
    {
        if (!is_null($requestId) && $requestId != 'all') {
            return true;
        }

        return false;
    }

    /**
     * Check if we need the data by Id.
     *
     * @param $name string
     *
     * @return bool
     */
    protected function dataById($name)
    {
        return in_array($name, $this->dataById);
    }
}
