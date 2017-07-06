<?php

use LeagueWrap\Api;
use Mockery as m;

class ApiRunesTest extends PHPUnit_Framework_TestCase
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
        $oRunesApi = $api->runes();
        $this->assertEquals("v3", $oRunesApi->getVersion());
    }

    public function testRunes()
    {
        $this->client->shouldReceive('baseUrl')->with('https://na1.api.riotgames.com/lol/platform/')
            ->once();
        $this->client->shouldReceive('request')
            ->with('v3/runes/by-summoner/74602', [
                'api_key' => 'key',
            ])->once()
            ->andReturn(file_get_contents('tests/Json/summoner.runes.74602.json'));

        $api = new Api('key', $this->client);
        $runes = $api->runes()->selectVersion('v3')->runePages(74602);
        $this->assertTrue($runes[0] instanceof LeagueWrap\Dto\RunePage);
    }

    public function testRuneArrayAccess()
    {
        $this->client->shouldReceive('baseUrl')->with('https://na1.api.riotgames.com/lol/platform/')
            ->once();
        $this->client->shouldReceive('request')
            ->with('v3/runes/by-summoner/74602', [
                'api_key' => 'key',
            ])->once()
            ->andReturn(file_get_contents('tests/Json/summoner.runes.74602.json'));

        $api = new Api('key', $this->client);
        $runes = $api->runes()->runePages(74602);
        $this->assertTrue($runes[0][30] instanceof LeagueWrap\Dto\Rune);
    }

    public function testRunesGetAccess()
    {
        $this->client->shouldReceive('baseUrl')->with('https://na1.api.riotgames.com/lol/platform/')
            ->once();
        $this->client->shouldReceive('request')
            ->with('v3/runes/by-summoner/74602', [
                'api_key' => 'key',
            ])->once()
            ->andReturn(file_get_contents('tests/Json/summoner.runes.74602.json'));

        $api = new Api('key', $this->client);
        $runesArray = $api->runes()->runePages(74602);
        $this->assertEquals(5317, $runesArray[1]->rune(15)->runeId);
    }

    public function testEmptyRunesRequest()
    {
        $this->client->shouldReceive('baseUrl')->with('https://euw1.api.riotgames.com/lol/platform/')
            ->once();
        $this->client->shouldReceive('request')
            ->with('v3/runes/by-summoner/87119303', [
                'api_key' => 'key',
            ])->once()
            ->andReturn(file_get_contents('tests/Json/summoner.runes.empty.json'));
        $api = new Api('key', $this->client);
        $runesArray = $api->runes()->setRegion('euw')->runePages(87119303);
        $this->assertTrue(is_array($runesArray));
        $this->assertCount(2, $runesArray);
        $this->assertInstanceOf(\LeagueWrap\Dto\RunePage::class, $runesArray[0]);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testInvalidSummonerId()
    {
        $api = new Api('key', $this->client);
        $runesArray = $api->runes()->setRegion('euw')->runePages([87119303]);
    }
}
