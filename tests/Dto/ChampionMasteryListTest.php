<?php

class ChampionMasteryListTest extends PHPUnit_Framework_TestCase
{
    public function testMasteriesNoResult()
    {
        $masteryList = new \LeagueWrap\Dto\ChampionMasteryList([]);
        $this->assertEquals(0, count($masteryList));
    }

    public function testChampionMastery()
    {
        $content = json_decode(file_get_contents('tests/Json/championmastery.30447079.single.json'), true);
        $masteryList = new \LeagueWrap\Dto\ChampionMasteryList($content);

        $this->assertCount(1, $masteryList);
        $this->assertTrue($masteryList[0] instanceof \LeagueWrap\Dto\ChampionMastery);
        $this->assertEquals($masteryList[0]->championId, 1);
    }

    public function testGetChampionId()
    {
        $content = json_decode(file_get_contents('tests/Json/championmastery.30447079.json'), true);
        $masteryList = new \LeagueWrap\Dto\ChampionMasteryList($content);

        $championMastery = $masteryList->getChampion(1);
        $this->assertTrue($championMastery instanceof \LeagueWrap\Dto\ChampionMastery);
        $this->assertEquals($championMastery->championId, 1);
        $this->assertEquals($championMastery->championPoints, 6484);
    }
}
