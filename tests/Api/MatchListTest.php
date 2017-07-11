<?php

use LeagueWrap\Api;
use Mockery as m;

class MatchListTest extends PHPUnit_Framework_TestCase
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

    public function testMatchList()
    {
        $this->client->shouldReceive('baseUrl')->with('https://na1.api.riotgames.com/lol/match/')
            ->once();
        $this->client->shouldReceive('request')
            ->with('v3/matchlists/by-account/101444', [
                'api_key' => 'key',
            ])->once()
            ->andReturn(file_get_contents('tests/Json/matchlist.accountId.101444.json'));

        $api = new Api('key', $this->client);
        $matches = $api->matchlist()->matchlist(101444);
        $this->assertTrue($matches instanceof LeagueWrap\Dto\MatchList);
        $this->assertTrue($matches->totalGames == 8);
        $this->assertTrue($matches->startIndex == 0);
        $this->assertTrue($matches->endIndex == $matches->totalGames);
    }

    public function testMatchListArrayAccess()
    {
        $this->client->shouldReceive('baseUrl')->with('https://na1.api.riotgames.com/lol/match/')
            ->once();
        $this->client->shouldReceive('request')
            ->with('v3/matchlists/by-account/101444', [
                'api_key' => 'key',
            ])->once()
            ->andReturn(file_get_contents('tests/Json/matchlist.accountId.101444.json'));

        $api = new Api('key', $this->client);
        $matchList = $api->matchlist()->matchlist(101444);
        $this->assertTrue($matchList->match(0) instanceof LeagueWrap\Dto\MatchReference);
    }

    public function testListSummoner()
    {
        $this->client->shouldReceive('baseUrl')->with('https://na1.api.riotgames.com/lol/match/')
            ->once();
        $this->client->shouldReceive('request')
            ->with('v3/matchlists/by-account/101444', [
                'api_key' => 'key',
            ])->once()
            ->andReturn(file_get_contents('tests/Json/matchlist.accountId.101444.json'));

        $this->client->shouldReceive('baseUrl')->with('https://na1.api.riotgames.com/lol/summoner/')
            ->once();
        $this->client->shouldReceive('request')
            ->with('v3/summoners/by-name/bakasan', [
                'api_key' => 'key',
            ])->once()
            ->andReturn(file_get_contents('tests/Json/summoner.bakasan.json'));

        $api = new Api('key', $this->client);
        $bakasan = $api->summoner()->selectVersion('v3')->info('bakasan');
        $matchList = $api->matchlist()->matchlist($bakasan);
        $this->assertTrue($bakasan->matchlist->match(0) instanceof LeagueWrap\Dto\MatchReference);
    }

    public function testListWithParams()
    {
        $startTime = 1283846202;
        $endTime = 1283846202 + 1000;
        $this->client->shouldReceive('baseUrl')->with('https://na1.api.riotgames.com/lol/match/')
            ->once();
        $this->client->shouldReceive('request')
            ->with('v3/matchlists/by-account/101444', [
                'api_key'      => 'key',
                'rankedQueues' => 4,                // => 'RANKED_SOLO_5x5',
                'seasons'      => 5,                // => 'SEASON2015',
                'championIds'  => '1,2,3',
                'beginIndex'   => 1,
                'endIndex'     => 4,
                'beginTime'    => $startTime,
                'endTime'      => $endTime,
            ])->once()
            ->andReturn(file_get_contents('tests/Json/matchlist.accountId.101444.json'));

        $api = new Api('key', $this->client);
        $matchList = $api->matchlist()->matchlist(101444, 4, 5, [1, 2, 3], 1, 4, $startTime, $endTime);
        $this->assertTrue($matchList->match(0) instanceof LeagueWrap\Dto\MatchReference);
    }

    public function testParseParams()
    {
        $class = new ReflectionClass('LeagueWrap\Api\MatchList');
        $method = $class->getMethod('parseParams');
        $method->setAccessible(true);

        $matchApi = (new Api('key', $this->client))->matchList();

        $expected = [
            'rankedQueues' => 'RANKED_SOLO_5x5,RANKED_TEAM_3x3',
            'seasons'      => 'SEASON2015',
            'championIds'  => '1,2,3',
            'beginIndex'   => 1,
        ];

        $result = $method->invoke($matchApi, ['RANKED_SOLO_5x5', 'RANKED_TEAM_3x3'], ['SEASON2015'], [1, 2, 3], 1);
        $this->assertEquals($expected, $result);
    }

    //recent matchlist

    public function testRecent()
    {
        $this->client->shouldReceive('baseUrl')->with('https://na1.api.riotgames.com/lol/match/')
            ->once();
        $this->client->shouldReceive('request')
            ->with('v3/matchlists/by-account/101444/recent', [
                'api_key' => 'key',
            ])->once()
            ->andReturn(file_get_contents('tests/Json/matchlist.accountId.101444.recent.json'));

        $api = new Api('key', $this->client);
        $games = $api->matchlist()->recent(101444);
        $this->assertTrue($games instanceof LeagueWrap\Dto\MatchList);
    }

    public function testRecentArrayAccess()
    {
        $this->client->shouldReceive('baseUrl')->with('https://na1.api.riotgames.com/lol/match/')
            ->once();
        $this->client->shouldReceive('request')
            ->with('v3/matchlists/by-account/101444/recent', [
                'api_key' => 'key',
            ])->once()
            ->andReturn(file_get_contents('tests/Json/matchlist.accountId.101444.recent.json'));

        $api = new Api('key', $this->client);
        $matchList = $api->matchlist()->recent(101444);
        $this->assertTrue($matchList[0] instanceof LeagueWrap\Dto\MatchReference);
    }

    public function testRecentSummoner()
    {
        $this->client->shouldReceive('baseUrl')->with('https://na1.api.riotgames.com/lol/match/')
            ->once();
        $this->client->shouldReceive('request')
            ->with('v3/matchlists/by-account/101444/recent', [
                'api_key' => 'key',
            ])->once()
            ->andReturn(file_get_contents('tests/Json/matchlist.accountId.101444.recent.json'));

        $this->client->shouldReceive('baseUrl')->with('https://na1.api.riotgames.com/lol/summoner/')
            ->once();
        $this->client->shouldReceive('request')
            ->with('v3/summoners/by-name/bakasan', [
                'api_key' => 'key',
            ])->once()
            ->andReturn(file_get_contents('tests/Json/summoner.bakasan.json'));

        $api = new Api('key', $this->client);
        $bakasan = $api->summoner()->selectVersion('v3')->info('bakasan');
        $matchList = $api->matchlist()->recent($bakasan);
        $this->assertTrue($bakasan->recentMatchList(0) instanceof LeagueWrap\Dto\MatchReference);
    }

    public function testRecentRoleSummoner()
    {
        $this->client->shouldReceive('baseUrl')->with('https://na1.api.riotgames.com/lol/match/')
            ->once();
        $this->client->shouldReceive('request')
            ->with('v3/matchlists/by-account/101444/recent', [
                'api_key' => 'key',
            ])->once()
            ->andReturn(file_get_contents('tests/Json/matchlist.accountId.101444.recent.json'));

        $this->client->shouldReceive('baseUrl')->with('https://na1.api.riotgames.com/lol/summoner/')
            ->once();
        $this->client->shouldReceive('request')
            ->with('v3/summoners/by-name/bakasan', [
                'api_key' => 'key',
            ])->once()
            ->andReturn(file_get_contents('tests/Json/summoner.bakasan.json'));

        $api = new Api('key', $this->client);
        $bakasan = $api->summoner()->selectVersion('v3')->info('bakasan');
        $matchList = $api->matchlist()->recent($bakasan);

        $matchReference = $bakasan->recentMatchList(0);
        $this->assertEquals("DUO_SUPPORT", $matchReference->role);
    }

    public function testRecentStatsSummonerRaw()
    {
        $this->client->shouldReceive('baseUrl')->with('https://na1.api.riotgames.com/lol/match/')
            ->once();
        $this->client->shouldReceive('request')
            ->with('v3/matchlists/by-account/101444/recent', [
                'api_key' => 'key',
            ])->once()
            ->andReturn(file_get_contents('tests/Json/matchlist.accountId.101444.recent.json'));

        $this->client->shouldReceive('baseUrl')->with('https://na1.api.riotgames.com/lol/summoner/')
            ->once();
        $this->client->shouldReceive('request')
            ->with('v3/summoners/by-name/bakasan', [
                'api_key' => 'key',
            ])->once()
            ->andReturn(file_get_contents('tests/Json/summoner.bakasan.json'));

        $api = new Api('key', $this->client);
        $bakasan = $api->summoner()->selectVersion('v3')->info('bakasan');
        $matchList = $api->matchlist()->recent($bakasan);

        $matchReferenceRaw = $bakasan->recentMatchList(0)->raw();
        $this->assertEquals("DUO_SUPPORT", $matchReferenceRaw['role']);
    }
}
