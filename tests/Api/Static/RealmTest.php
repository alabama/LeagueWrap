<?php

use LeagueWrap\Api;
use Mockery as m;

class StaticRealmTest extends PHPUnit_Framework_TestCase
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

    public function testGetRealmNA()
    {
        $this->client->shouldReceive('baseUrl')->with('https://na1.api.riotgames.com/lol/static-data/')
                     ->once();
        $this->client->shouldReceive('request')
                     ->with('v3/realms', [
                        'api_key' => 'key',
                     ])->once()
                     ->andReturn(file_get_contents('tests/Json/Static/realm.json'));

        $api = new Api('key', $this->client);
        $na = $api->staticData()->getRealm();
        $this->assertEquals('en_US', $na->l);
    }

    public function testGetRealmKR()
    {
        $this->client->shouldReceive('baseUrl')->with('https://kr.api.riotgames.com/lol/static-data/')
                     ->once();
        $this->client->shouldReceive('request')
                     ->with('v3/realms', [
                        'api_key' => 'key',
                     ])->once()
                     ->andReturn(file_get_contents('tests/Json/Static/realm.kr.json'));

        $api = new Api('key', $this->client);
        $kr = $api->setRegion('kr')
                   ->staticData()->getRealm();
        $this->assertEquals('ko_KR', $kr->l);
    }
}
