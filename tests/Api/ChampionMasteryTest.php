<?php

use LeagueWrap\Api;
use Mockery as m;

class ChampionMasteryTest extends PHPUnit_Framework_TestCase
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

    public function testChampions()
    {
        $this->client->shouldReceive('baseUrl')->with('https://euw1.api.riotgames.com/lol/champion-mastery/')
            ->once();
        $this->client->shouldReceive('request')
            ->with('v3/champion-masteries/by-summoner/30447079', [
                'api_key' => 'key',
            ])->once()
            ->andReturn(file_get_contents('tests/Json/championmastery.30447079.json'));

        $api = new Api('key', $this->client);
        $api->setRegion('euw');

        $championMasteries = $api->championMastery()->champions(30447079);
        $this->assertTrue($championMasteries instanceof \LeagueWrap\Dto\ChampionMasteryList);
    }

    public function testChampionId()
    {
        $this->client->shouldReceive('baseUrl')->with('https://euw1.api.riotgames.com/lol/champion-mastery/')
            ->once();
        $this->client->shouldReceive('request')
            ->with('v3/champion-masteries/by-summoner/30447079/by-champion/1', [
                'api_key' => 'key',
            ])->once()
            ->andReturn(file_get_contents('tests/Json/championmastery.30447079.1.json'));

        $api = new Api('key', $this->client);
        $api->setRegion('euw');

        $championMastery = $api->championMastery()->champion(30447079, 1);
        $this->assertTrue($championMastery instanceof \LeagueWrap\Dto\ChampionMastery);
    }

    public function testScore()
    {
        $this->client->shouldReceive('baseUrl')->with('https://euw1.api.riotgames.com/lol/champion-mastery/')
            ->once();
        $this->client->shouldReceive('request')
            ->with('v3/scores/by-summoner/30447079', [
                'api_key' => 'key',
            ])->once()
            ->andReturn(100);

        $api = new Api('key', $this->client);
        $api->setRegion('euw');

        $score = $api->championMastery()->score(30447079);
        $this->assertTrue($score == 100);
    }

    public function testScoreAttachResponse()
    {
        $this->client->shouldReceive('baseUrl')->with('https://na1.api.riotgames.com/lol/champion-mastery/')
            ->once();
        $this->client->shouldReceive('request')
            ->with('v3/scores/by-summoner/74602', [
                'api_key' => 'key',
            ])->once()
            ->andReturn(999);

        $this->client->shouldReceive('baseUrl')->with('https://na1.api.riotgames.com/lol/summoner/')
            ->once();
        $this->client->shouldReceive('request')
            ->with('v3/summoners/by-name/bakasan', [
                'api_key' => 'key',
            ])->once()
            ->andReturn(file_get_contents('tests/Json/summoner.bakasan.json'));

        $api = new Api('key', $this->client);
        $bakasan = $api->summoner()->info('bakasan');
        $api->championMastery()->score($bakasan);
        $this->assertTrue($bakasan->score == 999);
    }

    public function testChampionsAttachResponse()
    {
        $this->client->shouldReceive('baseUrl')->with('https://na1.api.riotgames.com/lol/champion-mastery/')
            ->once();
        $this->client->shouldReceive('request')
            ->with('v3/champion-masteries/by-summoner/74602', [
                'api_key' => 'key',
            ])->once()
            ->andReturn(file_get_contents('tests/Json/championmastery.74602.json'));

        $this->client->shouldReceive('baseUrl')->with('https://na1.api.riotgames.com/lol/summoner/')
            ->once();
        $this->client->shouldReceive('request')
            ->with('v3/summoners/by-name/bakasan', [
                'api_key' => 'key',
            ])->once()
            ->andReturn(file_get_contents('tests/Json/summoner.bakasan.json'));

        $api = new Api('key', $this->client);
        $bakasan = $api->summoner()->info('bakasan');
        $api->championMastery()->champions($bakasan);
        $this->assertTrue($bakasan->championmastery instanceof \LeagueWrap\Dto\ChampionMasteryList);
    }
}
