<?php

use LeagueWrap\Api;
use LeagueWrap\Dto\MatchTimeline;
use Mockery as m;

class ApiMatchTest extends PHPUnit_Framework_TestCase
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

    public function testMatch()
    {
        $this->client->shouldReceive('baseUrl')->with('https://na1.api.riotgames.com/lol/match/')
            ->once();
        $this->client->shouldReceive('request')
            ->with('v3/matches/1399898747', [
                'api_key' => 'key',
            ])->once()
            ->andReturn(file_get_contents('tests/Json/matchhistory.match.1399898747.json'));

        $api = new Api('key', $this->client);
        $match = $api->match()->match(1399898747);
        $this->assertTrue($match instanceof LeagueWrap\Dto\Match);
        $this->assertEquals("RANKED_SOLO_5x5", $match->get("queueType"));
        $this->assertEquals("SEASON2014", $match->get("seasonName"));
    }

    public function testMatchWithStatic()
    {
        $this->client->shouldReceive('baseUrl')
            ->with('https://na1.api.riotgames.com/lol/match/')
            ->once();
        $this->client->shouldReceive('baseUrl')
            ->with('https://na1.api.riotgames.com/lol/static-data/')
            ->times(3);
        $this->client->shouldReceive('request')
            ->with('v3/matches/1399898747', [
                'api_key' => 'key',
            ])->once()
            ->andReturn(file_get_contents('tests/Json/matchhistory.match.1399898747.json'));
        $this->client->shouldReceive('request')
            ->with('v3/champions', [
                'api_key'  => 'key',
                'dataById' => 'true',
            ])->once()
            ->andReturn(file_get_contents('tests/Json/Static/champion.json'));
        $this->client->shouldReceive('request')
            ->with('v3/summoner-spells', [
                'api_key'  => 'key',
                'dataById' => 'true',
            ])->once()
            ->andReturn(file_get_contents('tests/Json/Static/summonerspell.json'));
        $this->client->shouldReceive('request')
            ->with('v3/items', [
                'api_key' => 'key',
            ])->once()
            ->andReturn(file_get_contents('tests/Json/Static/items.json'));

        $api = new Api('key', $this->client);
        $api->attachStaticData();
        $match = $api->match()->match(1399898747);
        $this->assertEquals('LeBlanc', $match->team(0)->ban(0)->championStaticData->name);
    }

    public function testTeams()
    {
        $this->client->shouldReceive('baseUrl')->with('https://na1.api.riotgames.com/lol/match/')
            ->once();
        $this->client->shouldReceive('request')
            ->with('v3/matches/1399898747', [
                'api_key' => 'key',
            ])->once()
            ->andReturn(file_get_contents('tests/Json/matchhistory.match.1399898747.json'));

        $api = new Api('key', $this->client);
        $match = $api->match()->match(1399898747);
        $this->assertTrue($match->team(0) instanceof LeagueWrap\Dto\MatchTeam);
        $this->assertFalse($match->team(0)->win);
        $this->assertTrue($match->team(1)->win);
    }

    public function testBans()
    {
        $this->client->shouldReceive('baseUrl')->with('https://na1.api.riotgames.com/lol/match/')
            ->once();
        $this->client->shouldReceive('request')
            ->with('v3/matches/1399898747', [
                'api_key' => 'key',
            ])->once()
            ->andReturn(file_get_contents('tests/Json/matchhistory.match.1399898747.json'));

        $api = new Api('key', $this->client);
        $match = $api->match()->match(1399898747);
        $this->assertTrue($match->team(0)->ban(0) instanceof LeagueWrap\Dto\Ban);
    }

    /**
     * @expectedException LeagueWrap\Response\UnderlyingServiceRateLimitReached
     * @expectedExceptionMessage Did not receive 'X-Rate-Limit-Type' and 'Retry-After' headers
     */
    public function testUnderlyingServiceRateLimit()
    {
        $this->client->shouldReceive('baseUrl')->with('https://na1.api.riotgames.com/lol/platform/')
            ->once();
        $this->client->shouldReceive('request')
            ->with('v3/champions', [
                'freeToPlay' => 'false',
                'api_key'    => 'key',
            ])->once()
            ->andReturn(new LeagueWrap\Response('', 429));

        $api = new Api('key', $this->client);
        $champion = $api->champion();
        $champions = $champion->all();
    }

    /**
     * @expectedException LeagueWrap\Response\Http429
     * @expectedExceptionMessage Rate limit exceeded.
     */
    public function testNormalRateLimitReached()
    {
        $this->client->shouldReceive('baseUrl')->with('https://na1.api.riotgames.com/lol/platform/')
            ->once();
        $this->client->shouldReceive('request')
            ->with('v3/champions', [
                'freeToPlay' => 'false',
                'api_key'    => 'key',
            ])->once()
            ->andReturn(new LeagueWrap\Response('', 429, [
                'Retry-After'       => 123,
                'X-Rate-Limit-Type' => 'user',
            ]));

        $api = new Api('key', $this->client);
        $champion = $api->champion()->selectVersion('v3');
        $champions = $champion->all();
    }
}
