<?php

use LeagueWrap\Api;
use Mockery as m;

class StaticChampionTest extends PHPUnit_Framework_TestCase
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

    public function testGetChampionDefault()
    {
        $this->client->shouldReceive('baseUrl')->with('https://na1.api.riotgames.com/lol/static-data/')
                     ->once();
        $this->client->shouldReceive('request')
                     ->with('v3/champions', [
                        'api_key'  => 'key',
                        'dataById' => 'true',
                     ])->once()
                     ->andReturn(file_get_contents('tests/Json/Static/champion.json'));

        $api = new Api('key', $this->client);
        $champions = $api->staticData()->getChampions();
        $champion = $champions->getChampion(53);
        $this->assertEquals('Blitzcrank', $champion->name);
    }

    public function testArrayAccess()
    {
        $this->client->shouldReceive('baseUrl')->with('https://na1.api.riotgames.com/lol/static-data/')
                     ->once();
        $this->client->shouldReceive('request')
                     ->with('v3/champions', [
                        'api_key'  => 'key',
                        'dataById' => 'true',
                     ])->once()
                     ->andReturn(file_get_contents('tests/Json/Static/champion.json'));

        $api = new Api('key', $this->client);
        $champions = $api->staticData()->getChampions();
        $this->assertEquals('Blitzcrank', $champions[53]->name);
    }

    public function testGetChampionRegionFR()
    {
        $this->client->shouldReceive('baseUrl')->with('https://na1.api.riotgames.com/lol/static-data/')
                     ->once();
        $this->client->shouldReceive('request')
                     ->with('v3/champions', [
                        'api_key'  => 'key',
                        'dataById' => 'true',
                        'locale'   => 'fr_FR',
                     ])->once()
                     ->andReturn(file_get_contents('tests/Json/Static/champion.fr.json'));

        $api = new Api('key', $this->client);
        $champions = $api->staticData()->setLocale('fr_FR')
                                       ->getChampions();
        $champion = $champions->getChampion(69);
        $this->assertEquals('Étreinte du serpent', $champion->title);
    }

    public function testGetChampionById()
    {
        $this->client->shouldReceive('baseUrl')->with('https://na1.api.riotgames.com/lol/static-data/')
                     ->once();
        $this->client->shouldReceive('request')
                     ->with('v3/champions/266', [
                        'api_key' => 'key',
                        'locale'  => 'fr_FR',
                     ])->once()
                     ->andReturn(file_get_contents('tests/Json/Static/champion.266.fr.json'));

        $api = new Api('key', $this->client);
        $champion = $api->staticData()->setLocale('fr_FR')
                                      ->getChampion(266);
        $this->assertEquals('Aatrox', $champion->name);
    }

    public function testGetChampionByIdTags()
    {
        $this->client->shouldReceive('baseUrl')->with('https://na1.api.riotgames.com/lol/static-data/')
            ->once();
        $this->client->shouldReceive('request')
            ->with('v3/champions/266', [
                'api_key'   => 'key',
                'tags'      => 'tags',
                'locale'    => 'fr_FR',
            ])->once()
            ->andReturn(file_get_contents('tests/Json/Static/champion.266.fr.tags.json'));

        $api = new Api('key', $this->client);
        $champion = $api->staticData()->setLocale('fr_FR')->getChampion(266, 'tags');
        $this->assertContains('Tank', $champion->tags);
    }

    public function testGetChampionTags()
    {
        $this->client->shouldReceive('baseUrl')->with('https://na1.api.riotgames.com/lol/static-data/')
                     ->once();
        $this->client->shouldReceive('request')
                     ->with('v3/champions', [
                        'api_key'   => 'key',
                        'dataById'  => 'true',
                        'locale'    => 'fr_FR',
                        'tags'      => 'tags',
                     ])->once()
                     ->andReturn(file_get_contents('tests/Json/Static/champion.fr.tags.json'));

        $api = new Api('key', $this->client);
        $champions = $api->staticData()->setLocale('fr_FR')
                                       ->getChampion('all', 'tags');
        $champion = $champions->getChampion(412);
        $this->assertEquals('Support', $champion->tags[0]);
    }

    public function testGetChampionMultipleTags()
    {
        $this->client->shouldReceive('baseUrl')->with('https://na1.api.riotgames.com/lol/static-data/')
            ->once();
        $this->client->shouldReceive('request')
            ->with('v3/champions/10', [
                'api_key'   => 'key',
                'locale'    => 'fr_FR',
                'tags'      => array('lore', 'skins', 'spells'),
            ])->once()
            ->andReturn(file_get_contents('tests/Json/Static/champion.10.skins.spells.lore.json'));

        $api = new Api('key', $this->client);
        $champion = $api->staticData()->setLocale('fr_FR')
            ->getChampion(10, array('lore', 'skins', 'spells'));
        $this->assertEquals('Divine Blessing', $champion->spells[1]->name);
    }

    public function testGetChampionAll()
    {
        $this->client->shouldReceive('baseUrl')->with('https://na1.api.riotgames.com/lol/static-data/')
                     ->once();
        $this->client->shouldReceive('request')
                     ->with('v3/champions', [
                        'api_key'   => 'key',
                        'dataById'  => 'true',
                        'tags'      => 'all',
                     ])->once()
                     ->andReturn(file_get_contents('tests/Json/Static/champion.all.json'));

        $api = new Api('key', $this->client);
        $champions = $api->staticData()->getChampions('all');
        $champion = $champions->getChampion(412);
        $this->assertEquals('beginner_starter', $champion->recommended[0]->blocks[0]->type);
    }
}
