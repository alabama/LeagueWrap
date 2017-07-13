<?php

use LeagueWrap\Api;
use Mockery as m;

class FeaturedGamesTest extends PHPUnit_Framework_TestCase
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

    public function testFeaturedGames()
    {
        $this->client->shouldReceive('baseUrl')->with('https://na1.api.riotgames.com/lol/spectator/')
                     ->once();
        $this->client->shouldReceive('request')
                     ->with('v3/featured-games', [
                        'api_key' => 'key',
                     ])->once()
                     ->andReturn(file_get_contents('tests/Json/featuredgames.na.json'));

        $api = new Api('key', $this->client);
        $featuredGames = $api->currentGame()->featuredGames();
        $this->assertEquals(3260844514, $featuredGames[0]->gameId);
    }
}
