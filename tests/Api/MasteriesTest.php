<?php

use LeagueWrap\Api;
use Mockery as m;

class ApiMasteriesTest extends PHPUnit_Framework_TestCase
{
    protected $client;

    public function setUp()
    {
        $client = m::mock('LeagueWrap\Client');
        $this->client = $client;
    }

    public function tearDown()
    {
        m::close();
    }

    public function testDefaultVersion()
    {
        $api = new Api('key', $this->client);
        $oMasteriesApi = $api->masteries();
        $this->assertEquals("v3", $oMasteriesApi->getVersion());
    }

    public function testMasteries()
    {
        $this->client->shouldReceive('baseUrl')->with('https://na1.api.riotgames.com/lol/platform/')
            ->once();
        $this->client->shouldReceive('request')
            ->with('v3/masteries/by-summoner/74602', [
                'api_key' => 'key',
            ])->once()
            ->andReturn(file_get_contents('tests/Json/summoner.masteries.74602.json'));

        $api = new Api('key', $this->client);
        $masteries = $api->masteries()->selectVersion('v3')->masteryPages(74602);
        $this->assertTrue($masteries[0] instanceof LeagueWrap\Dto\MasteryPage);
    }

    public function testMasteriesArrayAccess()
    {
        $this->client->shouldReceive('baseUrl')->with('https://na1.api.riotgames.com/lol/platform/')
            ->once();
        $this->client->shouldReceive('request')
            ->with('v3/masteries/by-summoner/74602', [
                'api_key' => 'key',
            ])->once()
            ->andReturn(file_get_contents('tests/Json/summoner.masteries.74602.json'));

        $api = new Api('key', $this->client);
        $masteries = $api->masteries()->masteryPages(74602);
        $this->assertTrue($masteries[0][4342] instanceof LeagueWrap\Dto\Mastery);
    }

    public function testMasteriesArrayOnlyOneMasterySummoner()
    {
        $this->client->shouldReceive('baseUrl')->with('https://br1.api.riotgames.com/lol/platform/')
            ->once();
        $this->client->shouldReceive('request')
            ->with('v3/masteries/by-summoner/401129', [
                'api_key' => 'key',
            ])->once()
            ->andReturn(file_get_contents('tests/Json/summoner.masteries.401129.br.json'));

        $api = new api('key', $this->client);
        $masteries = $api->setRegion('BR')->masteries()->masteryPages(401129);
        $this->assertTrue(is_array($masteries));
    }

    public function testMasteriesSummoner()
    {
        $this->client->shouldReceive('baseUrl')->with('https://na1.api.riotgames.com/lol/platform/')
            ->once();
        $this->client->shouldReceive('request')
            ->with('v3/masteries/by-summoner/74602', [
                'api_key' => 'key',
            ])->once()
            ->andReturn(file_get_contents('tests/Json/summoner.masteries.74602.json'));

        $api = new Api('key', $this->client);
        $masteryArray = $api->masteries()->selectVersion('v3')->masteryPages(74602);
        $this->assertEquals(2, $masteryArray[1]->mastery(4212)->rank);
    }

    public function testEmptyMasteryRequest()
    {
        $this->client->shouldReceive('baseUrl')->with('https://euw1.api.riotgames.com/lol/platform/')
            ->once();
        $this->client->shouldReceive('request')
            ->with('v3/masteries/by-summoner/87119303', [
                'api_key' => 'key',
            ])->once()
            ->andReturn(file_get_contents('tests/Json/summoner.masteries.empty.json'));
        $api = new Api('key', $this->client);
        $masteryArray = $api->masteries()->setRegion('euw')->masteryPages(87119303);
        $this->assertTrue(is_array($masteryArray));
        $this->assertCount(1, $masteryArray);
        $this->assertInstanceOf(\LeagueWrap\Dto\MasteryPage::class, $masteryArray[0]);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testInvalidSummonerId()
    {
        $api = new Api('key', $this->client);
        $masteryArray = $api->masteries()->setRegion('euw')->masteryPages([87119303]);
    }
}
