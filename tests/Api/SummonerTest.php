<?php

use LeagueWrap\Api;
use Mockery as m;

class ApiSummonerTest extends PHPUnit_Framework_TestCase
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

    public function testDefaultVersion()
    {
        $api = new Api('key', $this->client);
        $oSummonerApi = $api->summoner();
        $this->assertEquals("v3", $oSummonerApi->getVersion());
    }

    public function testInfo()
    {
        $this->client->shouldReceive('baseUrl')->with('https://na1.api.riotgames.com/lol/summoner/')
                     ->once();
        $this->client->shouldReceive('request')
                     ->with('v3/summoners/by-name/bakasan', [
                        'api_key' => 'key',
                     ])->once()
                     ->andReturn(file_get_contents('tests/Json/summoner.bakasan.json'));

        $api = new Api('key', $this->client);
        $bakasan = $api->summoner()->info('bakasan');
        $this->assertEquals(74602, $bakasan->id);
    }

    public function testInfoId()
    {
        $this->client->shouldReceive('baseUrl')->with('https://na1.api.riotgames.com/lol/summoner/')
                     ->once();
        $this->client->shouldReceive('request')
                     ->with('v3/summoners/74602', [
                        'api_key' => 'key',
                     ])->once()
                     ->andReturn(file_get_contents('tests/Json/summoner.74602.json'));

        $api = new Api('key', $this->client);
        $summoner = $api->summoner();
        $summoner->info(74602);
        $this->assertEquals('bakasan', $summoner->bakasan->name);
    }

    public function testAccountId()
    {
        $this->client->shouldReceive('baseUrl')->with('https://na1.api.riotgames.com/lol/summoner/')
            ->twice();
        $this->client->shouldReceive('request')
            ->with('v3/summoners/by-account/101444', [
                'api_key' => 'key',
            ])->twice()
            ->andReturn(file_get_contents('tests/Json/summoner.74602.json'));

        $api = new Api('key', $this->client);
        $summoner = $api->summoner()->selectVersion('v3');
        $summoner->infoByAccountId(101444); //int accountId call
        $this->assertEquals('bakasan', $summoner->bakasan->name);

        $summoner->infoByAccountId('101444'); //string accountId call
        $this->assertEquals(101444, $summoner->bakasan->accountId);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testAccountIdFalseType()
    {
        $api = new Api('key', $this->client);
        $summoner = $api->summoner()->selectVersion('v3');
        $summoner->infoByAccountId(array(101444));
    }

    public function testInfoGetNull()
    {
        $this->client->shouldReceive('baseUrl')->with('https://na1.api.riotgames.com/lol/summoner/')
                     ->once();
        $this->client->shouldReceive('request')
                     ->with('v3/summoners/74602', [
                        'api_key' => 'key',
                     ])->once()
                     ->andReturn(file_get_contents('tests/Json/summoner.74602.json'));

        $api = new Api('key', $this->client);
        $summoner = $api->summoner()->selectVersion('v3');
        $summoner->info(74602);
        $this->assertTrue(is_null($summoner->nottherightname));
    }

    public function testInfoDistinguishesBetweenIntegerIdsAndNumericNames()
    {
        $this->client->shouldReceive('baseUrl')->with('https://na1.api.riotgames.com/lol/summoner/')
                     ->twice();
        $this->client->shouldReceive('request')
                     ->with('v3/summoners/by-name/1337', [
                        'api_key' => 'key',
                     ])->once()
                     ->andReturn(file_get_contents('tests/Json/summoner.1337.json'));
        $this->client->shouldReceive('request')
                     ->with('v3/summoners/74602', [
                        'api_key' => 'key',
                     ])->once()
                     ->andReturn(file_get_contents('tests/Json/summoner.74602.json'));

        $api = new Api('key', $this->client);
        $summonerDto = $api->summoner()->selectVersion('v3')->info('1337');
        $this->assertEquals('1337', $summonerDto->name);

        $summonerDto = $api->summoner()->selectVersion('v3')->info(74602);
        $this->assertEquals('bakasan', $summonerDto->name);
    }

    /**
     * @expectedException LeagueWrap\Response\Http404
     */
    public function testInfoSummonerNotFound()
    {
        $this->client->shouldReceive('baseUrl')->with('https://na1.api.riotgames.com/lol/summoner/')
                     ->once();
        $this->client->shouldReceive('request')
                     ->with('v3/summoners/by-name/bakasan', [
                        'api_key' => 'key',
                     ])->once()
                     ->andReturn(new LeagueWrap\Response('', 404));

        $api = new Api('key', $this->client);
        $bakasan = $api->summoner()->selectVersion('v3')->info('bakasan');
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testInfoWithWrongParameter()
    {
        $api = new Api('key', $this->client);
        $bakasan = $api->summoner()->info(array('bakasan'));
    }
}
