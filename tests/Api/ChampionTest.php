<?php

use LeagueWrap\Api;
use Mockery as m;

class ApiChampionTest extends PHPUnit_Framework_TestCase
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

    public function testAll()
    {
        $this->client->shouldReceive('baseUrl')->with('https://na.api.pvp.net/api/lol/na/')
                     ->once();
        $this->client->shouldReceive('request')
                     ->with('v1.2/champion', [
                        'freeToPlay' => 'false',
                        'api_key'    => 'key',
                     ])->once()
                     ->andReturn(new LeagueWrap\Response(file_get_contents('tests/Json/champion.json'), 200));

        $api = new Api('key', $this->client);
        $champion = $api->champion();
        $champion->selectVersion('v1.2');
        $champions = $champion->all();
        $this->assertTrue($champions->getChampion(53) instanceof LeagueWrap\Dto\Champion);
    }

    public function testAllArrayAccess()
    {
        $this->client->shouldReceive('baseUrl')->with('https://na.api.pvp.net/api/lol/na/')
                     ->once();
        $this->client->shouldReceive('request')
                     ->with('v1.2/champion', [
                        'freeToPlay' => 'false',
                        'api_key'    => 'key',
                     ])->once()
                     ->andReturn(file_get_contents('tests/Json/champion.json'));

        $api = new Api('key', $this->client);
        $champion = $api->champion();
        $champion->selectVersion('v1.2');
        $champions = $champion->all();
        $this->assertTrue($champions[53] instanceof LeagueWrap\Dto\Champion);
    }

    public function testFreeWillNotBeStoredPermanently()
    {
        $this->client->shouldReceive('baseUrl')->with('https://na.api.pvp.net/api/lol/na/')
                     ->twice();
        $this->client->shouldReceive('request')
                     ->with('v1.2/champion', [
                        'freeToPlay' => 'true',
                        'api_key'    => 'key',
                     ])->once()
                     ->andReturn(file_get_contents('tests/Json/champion.free.json'));
        $this->client->shouldReceive('request')
                     ->with('v1.2/champion', [
                        'freeToPlay' => 'false',
                        'api_key'    => 'key',
                     ])->once()
                     ->andReturn(file_get_contents('tests/Json/champion.json'));

        $api = new Api('key', $this->client);
        $champion = $api->champion();
        $champion->selectVersion('v1.2');
        $this->assertNotEquals($champion->free(), $champion->all());
    }

    public function testAllIterator()
    {
        $this->client->shouldReceive('baseUrl')->with('https://na.api.pvp.net/api/lol/na/')
                     ->once();
        $this->client->shouldReceive('request')
                     ->with('v1.2/champion', [
                        'freeToPlay' => 'false',
                        'api_key'    => 'key',
                     ])->once()
                     ->andReturn(file_get_contents('tests/Json/champion.json'));

        $api = new Api('key', $this->client);
        $champion = $api->champion();
        $champion->selectVersion('v1.2');
        $champions = $champion->all();
        $count = 0;
        foreach ($champions as $champion) {
            ++$count;
        }
        $this->assertEquals(count($champions), $count);
    }

    public function testFree()
    {
        $this->client->shouldReceive('baseUrl')->with('https://na.api.pvp.net/api/lol/na/')
                     ->once();
        $this->client->shouldReceive('request')
                     ->with('v1.2/champion', [
                        'freeToPlay' => 'true',
                        'api_key'    => 'key',
                     ])->once()
                     ->andReturn(file_get_contents('tests/Json/champion.free.json'));

        $api = new Api('key', $this->client);
        $champion = $api->champion();
        $champion->selectVersion('v1.2');
        $free = $champion->free();
        $this->assertEquals(10, count($free->champions));
    }

    public function testFreeCountable()
    {
        $this->client->shouldReceive('baseUrl')->with('https://na.api.pvp.net/api/lol/na/')
                     ->once();
        $this->client->shouldReceive('request')
                     ->with('v1.2/champion', [
                        'freeToPlay' => 'true',
                        'api_key'    => 'key',
                     ])->once()
                     ->andReturn(file_get_contents('tests/Json/champion.free.json'));

        $api = new Api('key', $this->client);
        $champion = $api->champion();
        $champion->selectVersion('v1.2');
        $free = $champion->free();
        $this->assertEquals(10, count($free));
    }

    public function testChampionById()
    {
        $this->client->shouldReceive('baseUrl')->with('https://na.api.pvp.net/api/lol/na/')
                     ->once();
        $this->client->shouldReceive('request')
                     ->with('v1.2/champion/10', [
                        'api_key' => 'key',
                     ])->once()
                     ->andReturn(file_get_contents('tests/Json/champion.10.json'));

        $api = new Api('key', $this->client);
        $kayle = $api->champion()->selectVersion('v1.2')->championById(10);
        $this->assertEquals(true, $kayle->rankedPlayEnabled);
    }

    public function testChampionByIdWithStaticImport()
    {
        $this->client->shouldReceive('baseUrl')->with('https://na.api.pvp.net/api/lol/na/')
                     ->once();
        $this->client->shouldReceive('baseUrl')->with('https://global.api.pvp.net/api/lol/static-data/na/')
            ->once();
        $this->client->shouldReceive('request')
                     ->with('v1.2/champion/10', [
                        'api_key' => 'key',
                     ])->twice()
                     ->andReturn(file_get_contents('tests/Json/champion.10.json'),
                                 file_get_contents('tests/Json/Static/champion.10.json'));

        $api = new Api('key', $this->client);
        $kayle = $api->attachStaticData()->champion()->selectVersion('v1.2')->championById(10);
        $this->assertEquals('Kayle', $kayle->championStaticData->name);
    }

    public function testAllRegionKR()
    {
        $this->client->shouldReceive('baseUrl')->with('https://kr.api.pvp.net/api/lol/kr/')
                     ->once()
                     ->with('https://kr.api.pvp.net/api/lol/kr/');
        $this->client->shouldReceive('request')
                     ->with('v1.2/champion', [
                        'freeToPlay' => 'false',
                        'api_key'    => 'key',
                     ])->once()
                     ->andReturn(file_get_contents('tests/Json/champion.kr.json'));

        $api = new Api('key', $this->client);
        $api->setRegion('kr');
        $champion = $api->champion();
        $champion->selectVersion('v1.2');
        $champions = $champion->all();
        $this->assertTrue($champions->getChampion(53) instanceof LeagueWrap\Dto\Champion);
    }

    public function testAllRegionRU()
    {
        $this->client->shouldReceive('baseUrl')
                     ->once()
                     ->with('https://ru.api.pvp.net/api/lol/ru/');
        $this->client->shouldReceive('request')
                     ->with('v1.2/champion', [
                        'freeToPlay' => 'false',
                        'api_key'    => 'key',
                     ])->once()
                     ->andReturn(file_get_contents('tests/Json/champion.ru.json'));

        $api = new Api('key', $this->client);
        $api->setRegion('ru');
        $champion = $api->champion();
        $champion->selectVersion('v1.2');
        $champions = $champion->all();
        $this->assertTrue($champions->getChampion(53) instanceof LeagueWrap\Dto\Champion);
    }

    /**
     * @expectedException LeagueWrap\Response\Http400
     * @expectedExceptionMessage Bad request.
     */
    public function testAllBadRquest()
    {
        $this->client->shouldReceive('baseUrl')->with('https://na.api.pvp.net/api/lol/na/')
                     ->once();
        $this->client->shouldReceive('request')
                     ->with('v1.2/champion', [
                        'freeToPlay' => 'false',
                        'api_key'    => 'key',
                     ])->once()
                     ->andReturn(new LeagueWrap\Response('', 400));

        $api = new Api('key', $this->client);
        $champion = $api->champion();
        $champion->selectVersion('v1.2');
        $champions = $champion->all();
    }

    public function testManuallySelectedVersion3()
    {
        $this->client->shouldReceive('baseUrl')
            ->once()
            ->with('https://na1.api.riotgames.com/lol/platform/');
        $this->client->shouldReceive('request')
            ->with('v3/champions', [
                'freeToPlay' => 'false',
                'api_key'    => 'key',
            ])->once()
            ->andReturn(file_get_contents('tests/Json/champion.json'));

        $api = new Api('key', $this->client);
        $api->setRegion('na');
        $champion = $api->champion();
        $champion->selectVersion('v3');
        $champions = $champion->all();
        $this->assertTrue($champions->getChampion(53) instanceof LeagueWrap\Dto\Champion);
    }

    public function testAutomaticallySelectedVersion3()
    {
        $this->client->shouldReceive('baseUrl')
            ->once()
            ->with('https://na1.api.riotgames.com/lol/platform/');
        $this->client->shouldReceive('request')
            ->with('v3/champions', [
                'freeToPlay' => 'false',
                'api_key'    => 'key',
            ])->once()
            ->andReturn(file_get_contents('tests/Json/champion.json'));

        $api = new Api('key', $this->client);
        $api->setRegion('na');
        $champion = $api->champion();
        $champions = $champion->all();
        $this->assertTrue($champions->getChampion(53) instanceof LeagueWrap\Dto\Champion);
    }

    public function testVersion3ForChampionById()
    {
        $this->client->shouldReceive('baseUrl')
            ->with('https://na1.api.riotgames.com/lol/platform/')
            ->once();
        $this->client->shouldReceive('request')
            ->with('v3/champions/10', [
                'api_key' => 'key',
            ])
            ->once()
            ->andReturn(file_get_contents('tests/Json/champion.10.json'));

        $api = new Api('key', $this->client);
        $kayle = $api->champion()->selectVersion('v3')->championById(10);
        $this->assertEquals(true, $kayle->rankedPlayEnabled);
    }
}
