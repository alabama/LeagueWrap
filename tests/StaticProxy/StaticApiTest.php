<?php

class StaticProxyStaticApiTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        LeagueWrap\StaticApi::mount();
    }

    /**
     * @expectedException LeagueWrap\Exception\ApiNotLoadedException
     */
    public function testApiNotLoadedException()
    {
        Api::champion();
    }

    public function testSetKey()
    {
        $api = Api::setKey('key');
        $this->assertTrue($api instanceof LeagueWrap\Api);
    }

    public function testChampion()
    {
        Api::setKey('key');
        $champion = Api::champion();
        $this->assertTrue($champion instanceof LeagueWrap\Api\Champion);
    }

    public function testSummoner()
    {
        Api::setKey('key');
        $summoner = Api::summoner();
        $this->assertTrue($summoner instanceof LeagueWrap\Api\Summoner);
    }

    public function testLeague()
    {
        Api::setKey('key');
        $league = Api::league();
        $this->assertTrue($league instanceof LeagueWrap\Api\League);
    }

    public function testStaticData()
    {
        Api::setKey('key');
        $staticData = Api::staticData();
        $this->assertTrue($staticData instanceof LeagueWrap\Api\Staticdata);
    }

    public function testStaticMatch()
    {
        Api::setKey('key');
        $league = Api::match();
        $this->assertTrue($league instanceof LeagueWrap\Api\Match);
    }

    public function testStaticMatchlist()
    {
        Api::setKey('key');
        $league = Api::matchlist();
        $this->assertTrue($league instanceof LeagueWrap\Api\Matchlist);
    }

    public function testStaticMatchtimeline()
    {
        Api::setKey('key');
        $league = Api::matchTimeline();
        $this->assertTrue($league instanceof LeagueWrap\Api\Matchtimeline);
    }

    public function testFresh()
    {
        $api1 = Api::setKey('key');
        Api::fresh();
        $api2 = Api::setKey('key');
        $this->assertNotEquals(spl_object_hash($api1), spl_object_hash($api2));
    }
}
