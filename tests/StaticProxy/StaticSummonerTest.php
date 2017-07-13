<?php

use Mockery as m;

class StaticProxyStaticSummonerTest extends PHPUnit_Framework_TestCase
{
    protected $client;

    public function setUp()
    {
        $this->client = m::mock('LeagueWrap\Client');
        LeagueWrap\StaticApi::mount();
    }

    public function tearDown()
    {
        m::close();
    }

    public function testInfo()
    {
        $this->client->shouldReceive('baseUrl')
                     ->once();
        $this->client->shouldReceive('request')
                     ->with('v3/summoners/by-name/bakasan', [
                        'api_key' => 'key',
                     ])->once()
                     ->andReturn(file_get_contents('tests/Json/summoner.bakasan.json'));

        Api::setKey('key', $this->client);
        $bakasan = Summoner::selectVersion('v3')->info('bakasan');
        $this->assertEquals(74602, $bakasan->id);
    }

    public function testAccountId()
    {
        $this->client->shouldReceive('baseUrl')
            ->once();
        $this->client->shouldReceive('request')
            ->with('v3/summoners/by-account/101444', [
                'api_key' => 'key',
            ])->once()
            ->andReturn(file_get_contents('tests/Json/summoner.bakasan.json'));

        Api::setKey('key', $this->client);
        $bakasan = Summoner::selectVersion('v3')->infoByAccountId(101444);
        $this->assertEquals(74602, $bakasan->id);
    }
}
