<?php

use Mockery as m;

class CacheTest extends PHPUnit_Framework_TestCase
{
    protected $cache;
    protected $client;

    public function setUp()
    {
        $this->cache = m::mock('LeagueWrap\CacheInterface');
        $this->client = m::mock('LeagueWrap\Client');
    }

    public function tearDown()
    {
        m::close();
    }

    public function testRememberChampion()
    {
        $champions = file_get_contents('tests/Json/champion.free.json');
        $this->cache->shouldReceive('set')
                    ->once()
                    ->with('b985a810f732cf282a70472f4f4609e0', $champions, 60)
                    ->andReturn(true);
        $this->cache->shouldReceive('has')
                    ->twice()
                    ->with('b985a810f732cf282a70472f4f4609e0')
                    ->andReturn(false, true);
        $this->cache->shouldReceive('get')
                    ->once()
                    ->with('b985a810f732cf282a70472f4f4609e0')
                    ->andReturn($champions);

        $this->client->shouldReceive('baseUrl')
                     ->twice();
        $this->client->shouldReceive('request')
                     ->with('v3/champions', [
                        'freeToPlay' => 'true',
                        'api_key'    => 'key',
                     ])->once()
                     ->andReturn($champions);

        $api = new LeagueWrap\Api('key', $this->client);
        $champion = $api->champion()
                        ->remember(60, $this->cache);
        $champion->selectVersion('v3');
        $champion->free();
        $champion->free();
        $this->assertEquals(1, $champion->getRequestCount());
    }

    /**
     * @expectedException LeagueWrap\Response\HttpClientError
     * @expectedExceptionMessage Resource not found.
     */
    public function testRememberChampionClientError()
    {
        $this->cache->shouldReceive('set')
                    ->once()
                    ->andReturn(true);
        $this->cache->shouldReceive('has')
                    ->twice()
                    ->with('352987fd32cf12271afe59e80b9e86a1')
                    ->andReturn(false, true);

        $this->client->shouldReceive('baseUrl')
                     ->twice();
        $this->client->shouldReceive('request')
                     ->with('v3/champions/10101', [
                        'api_key'    => 'key',
                     ])->once()
                     ->andReturn(new LeagueWrap\Response(file_get_contents('tests/Json/champion.json'), 404));

        $api = new LeagueWrap\Api('key', $this->client);
        $champion = $api->champion()
                        ->remember(60, $this->cache);
        $champion->selectVersion('v3');
        try {
            $champion->championById(10101);
        } catch (LeagueWrap\Response\HttpClientError $exception) {
            $this->cache->shouldReceive('get')
                        ->once()
                        ->with('352987fd32cf12271afe59e80b9e86a1')
                        ->andReturn($exception);
            $champion->championById(10101);
        }
    }

    public function testRememberChampionCacheOnly()
    {
        $champions = file_get_contents('tests/Json/champion.free.json');
        $this->cache->shouldReceive('has')
                    ->twice()
                    ->with('b985a810f732cf282a70472f4f4609e0')
                    ->andReturn(true);
        $this->cache->shouldReceive('get')
                    ->twice()
                    ->with('b985a810f732cf282a70472f4f4609e0')
                    ->andReturn($champions);

        $this->client->shouldReceive('baseUrl')
                     ->twice();

        $api = new LeagueWrap\Api('key', $this->client);
        $api->setCacheOnly()
            ->remember(60, $this->cache);
        $champion = $api->champion();
        $champion->selectVersion('v3');
        $champion->free();
        $champion->free();
        $this->assertEquals(0, $champion->getRequestCount());
    }

    /**
     * @expectedException LeagueWrap\Exception\CacheNotFoundException
     */
    public function testRememberSummonerCacheOnlyNoHit()
    {
        $bakasan = file_get_contents('tests/Json/summoner.bakasan.json');
        $this->cache->shouldReceive('has')
                    ->once()
                    ->with('c572620afbbb0c0c430785ba87cc97ad')
                    ->andReturn(false);
        $this->client->shouldReceive('baseUrl')
                     ->once();

        $api = new LeagueWrap\Api('key', $this->client);
        $api->remember(null, $this->cache)
            ->setCacheOnly();
        $summoner = $api->summoner()->selectVersion('v3')->info('bakasan');
    }

    public function testRememberSummonerStaticProxy()
    {
        $bakasan = file_get_contents('tests/Json/summoner.bakasan.json');
        $this->cache->shouldReceive('set')
                    ->once()
                    ->with('c572620afbbb0c0c430785ba87cc97ad', $bakasan, 10)
                    ->andReturn(true);
        $this->cache->shouldReceive('has')
                    ->twice()
                    ->with('c572620afbbb0c0c430785ba87cc97ad')
                    ->andReturn(false, true);
        $this->cache->shouldReceive('get')
                    ->once()
                    ->with('c572620afbbb0c0c430785ba87cc97ad')
                    ->andReturn($bakasan);

        $this->client->shouldReceive('baseUrl')
                     ->twice();
        $this->client->shouldReceive('request')
                     ->with('v3/summoners/by-name/bakasan', [
                        'api_key' => 'key',
                     ])->once()
                     ->andReturn($bakasan);

        LeagueWrap\StaticApi::mount();
        Api::setKey('key', $this->client);
        Api::remember(10, $this->cache);
        Summoner::selectVersion('v3')->info('bakasan');
        Summoner::selectVersion('v3')->info('bakasan');
        $this->assertEquals(1, Summoner::getRequestCount());
    }

    public function testCaching4xxError()
    {
        $response = new LeagueWrap\Response('', 404);
        $exception = new LeagueWrap\Response\Http404('', 404);
        $this->cache->shouldReceive('set')
                    ->once()
                    ->with('c572620afbbb0c0c430785ba87cc97ad', m::any(), 10)
                    ->andReturn(true);
        $this->cache->shouldReceive('has')
                    ->twice()
                    ->with('c572620afbbb0c0c430785ba87cc97ad')
                    ->andReturn(false, true);
        $this->cache->shouldReceive('get')
                    ->once()
                    ->with('c572620afbbb0c0c430785ba87cc97ad')
                    ->andReturn($exception);

        $this->client->shouldReceive('baseUrl')
                     ->twice();
        $this->client->shouldReceive('request')
                     ->with('v3/summoners/by-name/bakasan', [
                        'api_key' => 'key',
                     ])->once()
                     ->andReturn($response);

        LeagueWrap\StaticApi::mount();
        Api::setKey('key', $this->client);
        Api::remember(10, $this->cache);
        try {
            Summoner::selectVersion('v3')->info('bakasan');
        } catch (LeagueWrap\Response\Http404 $e) {
        }
        try {
            Summoner::selectVersion('v3')->info('bakasan');
        } catch (LeagueWrap\Response\Http404 $e) {
        }

        $this->assertEquals(1, Summoner::getRequestCount());
    }

    public function testNoCaching4xxError()
    {
        $response = new LeagueWrap\Response('', 404);
        $this->cache->shouldReceive('has')
                    ->twice()
                    ->with('c572620afbbb0c0c430785ba87cc97ad')
                    ->andReturn(false, false);

        $this->client->shouldReceive('baseUrl')
                     ->twice();
        $this->client->shouldReceive('request')
                     ->with('v3/summoners/by-name/bakasan', [
                        'api_key' => 'key',
                     ])->twice()
                     ->andReturn($response);

        LeagueWrap\StaticApi::mount();
        Api::setKey('key', $this->client);
        Api::remember(10, $this->cache);
        Api::setClientErrorCaching(false);
        try {
            Summoner::selectVersion('v3')->info('bakasan');
        } catch (LeagueWrap\Response\Http404 $e) {
        }
        try {
            Summoner::selectVersion('v3')->info('bakasan');
        } catch (LeagueWrap\Response\Http404 $e) {
        }

        $this->assertEquals(2, Summoner::getRequestCount());
    }

    public function testCaching5xxError()
    {
        $response = new LeagueWrap\Response('', 500);
        $exception = new LeagueWrap\Response\Http500('', 500);

        $this->cache->shouldReceive('set')
                    ->once()
                    ->with('c572620afbbb0c0c430785ba87cc97ad', m::any(), 10)
                    ->andReturn(true);
        $this->cache->shouldReceive('has')
                    ->twice()
                    ->with('c572620afbbb0c0c430785ba87cc97ad')
                    ->andReturn(false, true);
        $this->cache->shouldReceive('get')
                    ->once()
                    ->with('c572620afbbb0c0c430785ba87cc97ad')
                    ->andReturn($exception);

        $this->client->shouldReceive('baseUrl')
                     ->twice();
        $this->client->shouldReceive('request')
                     ->with('v3/summoners/by-name/bakasan', [
                        'api_key' => 'key',
                     ])->once()
                     ->andReturn($response);

        LeagueWrap\StaticApi::mount();
        $api = new LeagueWrap\Api('key', $this->client);
        $api->remember(10, $this->cache);
        $api->setServerErrorCaching();
        $summoner = $api->summoner()->selectVersion('v3');
        try {
            $summoner->info('bakasan');
        } catch (LeagueWrap\Response\Http500 $e) {
        }
        try {
            $summoner->info('bakasan');
        } catch (LeagueWrap\Response\Http500 $e) {
        }

        $this->assertEquals(1, $summoner->getRequestCount());
    }

    public function testNoCaching5xxError()
    {
        $response = new LeagueWrap\Response('', 500);
        $exception = new LeagueWrap\Response\Http500('', 500);
        $this->cache->shouldReceive('has')
                    ->twice()
                    ->with('c572620afbbb0c0c430785ba87cc97ad')
                    ->andReturn(false, false);

        $this->client->shouldReceive('baseUrl')
                     ->twice();
        $this->client->shouldReceive('request')
                     ->with('v3/summoners/by-name/bakasan', [
                        'api_key' => 'key',
                     ])->twice()
                     ->andReturn($response);

        LeagueWrap\StaticApi::mount();
        Api::setKey('key', $this->client);
        Api::remember(10, $this->cache);
        try {
            Summoner::selectVersion('v3')->info('bakasan');
        } catch (LeagueWrap\Response\Http500 $e) {
        }
        try {
            Summoner::selectVersion('v3')->info('bakasan');
        } catch (LeagueWrap\Response\Http500 $e) {
        }

        $this->assertEquals(2, Summoner::getRequestCount());
    }
}
