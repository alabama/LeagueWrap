<?php

use LeagueWrap\Api;
use Mockery as m;

class StatusTest extends PHPUnit_Framework_TestCase
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

    public function testGetShardStatusDefault()
    {
        $this->client->shouldReceive('baseUrl')->with('https://na1.api.riotgames.com/lol/status/')
            ->once();
        $this->client->shouldReceive('request')
            ->with('v3/shard-data', [
                'api_key' => 'key',
            ])->once()
            ->andReturn(file_get_contents('tests/Json/shard-data.na.json'));

        $api = new Api('key', $this->client);
        $api->setRegion('na');
        $shardStatus = $api->status()->shardData();

        $this->assertTrue($shardStatus instanceof \LeagueWrap\Dto\ShardStatus);
        $this->assertCount(0, $shardStatus->getService('Game')->incidents);
    }

    public function testGetShardStatusWithRegion()
    {
        $this->client->shouldReceive('baseUrl')->with('https://euw1.api.riotgames.com/lol/status/')
            ->once();
        $this->client->shouldReceive('request')
            ->with('v3/shard-data', [
                'api_key' => 'key',
            ])->once()
            ->andReturn(file_get_contents('tests/Json/shard-data.euw.json'));

        $api = new Api('key', $this->client);
        $api->setRegion('euw');
        $shardStatus = $api->status()->shardData();

        $this->assertTrue($shardStatus instanceof \LeagueWrap\Dto\ShardStatus);
    }
}
