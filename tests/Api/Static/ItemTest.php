<?php

use LeagueWrap\Api;
use Mockery as m;

class StaticItemTest extends PHPUnit_Framework_TestCase
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

    public function testGetItemDefault()
    {
        $this->client->shouldReceive('baseUrl')->with('https://na1.api.riotgames.com/lol/static-data/')
                     ->once();
        $this->client->shouldReceive('request')
                     ->with('v3/items', [
                        'api_key' => 'key',
                     ])->once()
                     ->andReturn(file_get_contents('tests/Json/Static/items.json'));

        $api = new Api('key', $this->client);
        $items = $api->staticData()->getItems();
        $item = $items->getItem(1001);
        $this->assertEquals('Boots of Speed', $item->name);
    }

    public function testArrayAccess()
    {
        $this->client->shouldReceive('baseUrl')->with('https://na1.api.riotgames.com/lol/static-data/')
                     ->once();
        $this->client->shouldReceive('request')
                     ->with('v3/items', [
                        'api_key' => 'key',
                     ])->once()
                     ->andReturn(file_get_contents('tests/Json/Static/items.json'));

        $api = new Api('key', $this->client);
        $items = $api->staticData()->getItems();
        $this->assertEquals('Boots of Speed', $items[1001]->name);
    }

    public function testGetItemRegionKR()
    {
        $this->client->shouldReceive('baseUrl')->with('https://na1.api.riotgames.com/lol/static-data/')
                     ->once();
        $this->client->shouldReceive('request')
                     ->with('v3/items', [
                        'api_key' => 'key',
                        'locale'  => 'ko_KR',
                     ])->once()
                     ->andReturn(file_get_contents('tests/Json/Static/items.kr.json'));

        $api = new Api('key', $this->client);
        $items = $api->staticData()->setLocale('ko_KR')
                                   ->getItems();
        $item = $items->getItem(1042);
        $this->assertEquals('단검', $item->name);
    }

    public function testGetItemById()
    {
        $this->client->shouldReceive('baseUrl')->with('https://na1.api.riotgames.com/lol/static-data/')
                     ->once();
        $this->client->shouldReceive('request')
                     ->with('v3/items/1051', [
                        'api_key' => 'key',
                     ])->once()
                     ->andReturn(file_get_contents('tests/Json/Static/item.1051.json'));

        $api = new Api('key', $this->client);
        $item = $api->staticData()->getItem(1051);
        $this->assertEquals('Brawler\'s Gloves', $item->name);
    }

    public function testGetItemGold()
    {
        $this->client->shouldReceive('baseUrl')->with('https://na1.api.riotgames.com/lol/static-data/')
                     ->once();
        $this->client->shouldReceive('request')
                     ->with('v3/items/1051', [
                        'api_key'  => 'key',
                        'tags' => 'gold',
                     ])->once()
                     ->andReturn(file_get_contents('tests/Json/Static/item.1051.gold.json'));
        $api = new Api('key', $this->client);
        $item = $api->staticData()->getItem(1051, 'gold');
        $this->assertEquals(400, $item->gold->base);
    }

    public function testGetItemAll()
    {
        $this->client->shouldReceive('baseUrl')->with('https://na1.api.riotgames.com/lol/static-data/')
                     ->once();
        $this->client->shouldReceive('request')
                     ->with('v3/items', [
                        'api_key'       => 'key',
                        'tags'          => 'all'
                     ])->once()
                     ->andReturn(file_get_contents('tests/Json/Static/items.all.json'));

        $api = new Api('key', $this->client);
        $items = $api->staticData()->getItems('all');
        $item = $items->getItem(1042);
        $this->assertEquals(0.12, $item->stats->PercentAttackSpeedMod);
    }

    public function testGetItemArray()
    {
        $this->client->shouldReceive('baseUrl')->with('https://na1.api.riotgames.com/lol/static-data/')
                     ->once();
        $this->client->shouldReceive('request')
                     ->with('v3/items', [
                        'api_key'      => 'key',
                        'tags' => array('gold','image')
                     ])->once()
                     ->andReturn(file_get_contents('tests/Json/Static/items.gold.image.json'));

        $api = new Api('key', $this->client);
        $items = $api->staticData()->getItems(['gold', 'image']);
        $item = $items->getItem(1042);
        $this->assertEquals(300, $item->gold->total);
    }
}
