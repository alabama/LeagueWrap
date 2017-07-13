<?php

use LeagueWrap\Api;
use Mockery as m;

class ApiLeagueTest extends PHPUnit_Framework_TestCase
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

    public function testLeague()
    {
        $this->client->shouldReceive('baseUrl')->with('https://euw1.api.riotgames.com/lol/league/')
                     ->once();
        $this->client->shouldReceive('request')
                     ->with('v3/leagues/by-summoner/90879750', [
                        'api_key' => 'key',
                     ])->once()
                     ->andReturn(file_get_contents('tests/Json/league.90879750.json'));

        $api = new Api('key', $this->client);
        $api->setRegion("euw");
        $leagues = $api->league()->league(90879750);
        $this->assertTrue($leagues[0] instanceof LeagueWrap\Dto\League);
    }

    public function testLeagueSummoner()
    {
        $this->client->shouldReceive('baseUrl')->with('https://na1.api.riotgames.com/lol/league/')
                     ->once();
        $this->client->shouldReceive('request')
                     ->with('v3/leagues/by-summoner/272354', [
                        'api_key' => 'key',
                     ])->once()
                     ->andReturn(file_get_contents('tests/Json/league.272354.json'));

        $this->client->shouldReceive('baseUrl')->with('https://na1.api.riotgames.com/lol/summoner/')
            ->once();
        $this->client->shouldReceive('request')
                     ->with('v3/summoners/by-name/GamerXz', [
                        'api_key' => 'key',
                     ])->once()
                     ->andReturn(file_get_contents('tests/Json/summoner.gamerxz.json'));

        $api = new Api('key', $this->client);
        $gamerxz = $api->summoner()->selectVersion('v3')->info('GamerXz');
        $api->league()->league($gamerxz);
        $this->assertTrue($gamerxz->league('GamerXz') instanceof LeagueWrap\Dto\League);
    }

    public function testLeagueSummonerEntry()
    {
        $this->client->shouldReceive('baseUrl')->with('https://na1.api.riotgames.com/lol/league/')
                     ->once();
        $this->client->shouldReceive('request')
                     ->with('v3/leagues/by-summoner/272354', [
                        'api_key' => 'key',
                     ])->once()
                     ->andReturn(file_get_contents('tests/Json/league.272354.json'));

        $this->client->shouldReceive('baseUrl')->with('https://na1.api.riotgames.com/lol/summoner/')
            ->once();
        $this->client->shouldReceive('request')
                     ->with('v3/summoners/by-name/GamerXz', [
                        'api_key' => 'key',
                     ])->once()
                     ->andReturn(file_get_contents('tests/Json/summoner.gamerxz.json'));

        $api = new Api('key', $this->client);
        $gamerxz = $api->summoner()->selectVersion('v3')->info('GamerXz');
        $api->league()->league($gamerxz);
        $first = $gamerxz->league('GamerXz')->entry(23855467);
        $this->assertEquals('Shorthop', $first->playerOrTeamName);
    }

    public function testLeagueSummonerEntryMultipleLeague()
    {
        $this->client->shouldReceive('baseUrl')->with('https://euw1.api.riotgames.com/lol/league/')
            ->once();
        $this->client->shouldReceive('request')
            ->with('v3/leagues/by-summoner/23240923', [
                'api_key' => 'key',
            ])->once()
            ->andReturn(file_get_contents('tests/Json/league.23240923.multipleleague.json'));

        $this->client->shouldReceive('baseUrl')->with('https://euw1.api.riotgames.com/lol/summoner/')
            ->once();
        $this->client->shouldReceive('request')
            ->with('v3/summoners/by-name/TitouTheKing', [
                'api_key' => 'key',
            ])->once()
            ->andReturn(file_get_contents('tests/Json/summoner.titoutheking.json'));

        $api = new Api('key', $this->client);
        $api->setRegion("euw");
        $titouTheKing = $api->summoner()->selectVersion('v3')->info('TitouTheKing');
        $api->league()->league($titouTheKing);
        $entry = $titouTheKing->league('TitouTheKing', "RANKED_FLEX_TT")->entry(23524892);
        $this->assertEquals('AmZDragonS77', $entry->playerOrTeamName);
    }

    public function testLeagueEntryArrayAccess()
    {
        $this->client->shouldReceive('baseUrl')->with('https://euw1.api.riotgames.com/lol/league/')
                     ->once();
        $this->client->shouldReceive('request')
                     ->with('v3/leagues/by-summoner/90879750', [
                        'api_key' => 'key',
                     ])->once()
                     ->andReturn(file_get_contents('tests/Json/league.90879750.json'));

        $api = new Api('key', $this->client);
        $api->setRegion("euw");
        $leagues = $api->league()->league(90879750);
        $first = $leagues[0][0];
        $this->assertEquals('TMP zuzuU', $first->playerOrTeamName);
    }

    public function testLeagueSummonerPlayerOrTeam()
    {
        $this->client->shouldReceive('baseUrl')->with('https://na1.api.riotgames.com/lol/league/')
                     ->once();
        $this->client->shouldReceive('request')
                     ->with('v3/leagues/by-summoner/272354', [
                        'api_key' => 'key',
                     ])->once()
                     ->andReturn(file_get_contents('tests/Json/league.272354.json'));

        $this->client->shouldReceive('baseUrl')->with("https://na1.api.riotgames.com/lol/summoner/")
            ->once();
        $this->client->shouldReceive('request')
                     ->with('v3/summoners/by-name/GamerXz', [
                        'api_key' => 'key',
                     ])->once()
                     ->andReturn(file_get_contents('tests/Json/summoner.gamerxz.json'));

        $api = new Api('key', $this->client);
        $gamerxz = $api->summoner()->selectVersion('v3')->info('GamerXz');
        $api->league()->league($gamerxz);
        $myTeam = $gamerxz->league('gamerxz')->entry('LLCoolAlvin');
        $this->assertEquals(2, $myTeam->miniSeries->target);
    }

    public function testPosition()
    {
        $this->client->shouldReceive('baseUrl')->with('https://euw1.api.riotgames.com/lol/league/')
                     ->once();
        $this->client->shouldReceive('request')
                     ->with('v3/positions/by-summoner/90879750', [
                        'api_key' => 'key',
                     ])->once()
                     ->andReturn(file_get_contents('tests/Json/league.90879750.position.json'));

        $api = new Api('key', $this->client);
        $api->setRegion("euw");
        $league = $api->league()->position(90879750);
        $this->assertEquals(90879750, $league[0]->playerOrTeamId);
        $this->assertEquals(70, $league[0]->leaguePoints);
    }

    public function testPositionMutliple()
    {
        $this->client->shouldReceive('baseUrl')->with('https://euw1.api.riotgames.com/lol/league/')
            ->once();
        $this->client->shouldReceive('request')
            ->with('v3/positions/by-summoner/23240923', [
                'api_key' => 'key',
            ])->once()
            ->andReturn(file_get_contents('tests/Json/league.23240923.position.multipleleague.json'));

        $this->client->shouldReceive('baseUrl')->with('https://euw1.api.riotgames.com/lol/summoner/')
            ->once();
        $this->client->shouldReceive('request')
            ->with('v3/summoners/by-name/TitouTheKing', [
                'api_key' => 'key',
            ])->once()
            ->andReturn(file_get_contents('tests/Json/summoner.titoutheking.json'));

        $api = new Api('key', $this->client);
        $api->setRegion("euw");
        $TitouTheKing = $api->summoner()->selectVersion('v3')->info('TitouTheKing');
        $league = $api->league()->position($TitouTheKing);
        $this->assertEquals(23240923, $league[1]->playerOrTeamId);
        $this->assertEquals("RANKED_FLEX_SR", $league[1]->queueType);
    }

    public function testMaster()
    {
        $this->client->shouldReceive('baseUrl')->with('https://euw1.api.riotgames.com/lol/league/')
            ->once();
        $this->client->shouldReceive('request')
            ->with('v3/masterleagues/by-queue/RANKED_SOLO_5x5', [
                'api_key' => 'key'
            ])->once()
            ->andReturn(file_get_contents('tests/Json/league.master.json'));

        $api = new Api('key', $this->client);
        $api->setRegion("euw");
        $league = $api->league()->master("RANKED_SOLO_5x5");
        $this->assertEquals(266, $league->entry('rank 10 incoming')->leaguePoints);
    }

    public function testChallenger()
    {
        $this->client->shouldReceive('baseUrl')->with('https://euw1.api.riotgames.com/lol/league/')
                     ->once();
        $this->client->shouldReceive('request')
                     ->with('v3/challengerleagues/by-queue/RANKED_SOLO_5x5', [
                        'api_key' => 'key'
                     ])->once()
                     ->andReturn(file_get_contents('tests/Json/league.challenger.json'));

        $api = new Api('key', $this->client);
        $api->setRegion("euw");
        $league = $api->league()->challenger("RANKED_SOLO_5x5");
        $this->assertEquals(891, $league->entry('MSF PowerOfEvil')->leaguePoints);
    }
}
