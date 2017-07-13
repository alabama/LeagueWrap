<?php

use Mockery as m;

class StaticProxyStaticChampionTest extends PHPUnit_Framework_TestCase
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
                     ->with('v3/champions', [
                        'freeToPlay' => 'false',
                        'api_key'    => 'key',
                     ])->once()
                     ->andReturn(file_get_contents('tests/Json/champion.json'));

        Api::setKey('key', $this->client);
        $champions = Champion::selectVersion('v3')->all();
        $this->assertTrue($champions->getChampion(55) instanceof LeagueWrap\Dto\Champion);
    }

    public function testFree()
    {
        $this->client->shouldReceive('baseUrl')
                     ->once();
        $this->client->shouldReceive('request')
                     ->with('v3/champions', [
                        'freeToPlay' => 'true',
                        'api_key'    => 'key',
                     ])->once()
                     ->andReturn(file_get_contents('tests/Json/champion.free.json'));

        Api::setKey('key', $this->client);
        $free = Champion::selectVersion('v3')->free();
        $this->assertEquals(10, count($free->champions));
    }
}
