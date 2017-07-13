<?php

use LeagueWrap\Api;
use Mockery as m;

class CurrentGameTest extends PHPUnit_Framework_TestCase
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

    public function testCurrentGame()
    {
        $this->client->shouldReceive('baseUrl')->with('https://euw1.api.riotgames.com/lol/spectator/')
                     ->once();
        $this->client->shouldReceive('request')
                     ->with('v3/active-games/by-summoner/90879750', [
                        'api_key' => 'key',
                     ])->once()
                     ->andReturn(file_get_contents('tests/Json/currentgame.90879750.json'));

        $api = new Api('key', $this->client);
        $api->setRegion('euw');
        $game = $api->currentGame()->currentGame(90879750);
        $this->assertTrue($game instanceof LeagueWrap\Dto\CurrentGame);
    }

    public function testCurrentGameGetBan()
    {
        $this->client->shouldReceive('baseUrl')->with('https://euw1.api.riotgames.com/lol/spectator/')
                     ->once();
        $this->client->shouldReceive('request')
                     ->with('v3/active-games/by-summoner/90879750', [
                        'api_key' => 'key',
                     ])->once()
                     ->andReturn(file_get_contents('tests/Json/currentgame.90879750.json'));

        $api = new Api('key', $this->client);
        $api->setRegion('euw');
        $game = $api->currentGame()->currentGame(90879750);

        $ban = $game->ban(2);
        $this->assertTrue($ban instanceof \LeagueWrap\Dto\Ban);
        $this->assertTrue($ban->teamId == 100);
        $this->assertTrue($ban->championId == 164);

        $noBan = $game->ban(900);
        $this->assertTrue(is_null($noBan));
    }

    public function testCurrentGameObserver()
    {
        $this->client->shouldReceive('baseUrl')->with('https://euw1.api.riotgames.com/lol/spectator/')
                     ->once();
        $this->client->shouldReceive('request')
                     ->with('v3/active-games/by-summoner/90879750', [
                        'api_key' => 'key',
                     ])->once()
                     ->andReturn(file_get_contents('tests/Json/currentgame.90879750.json'));

        $api = new Api('key', $this->client);
        $api->setRegion('euw');
        $game = $api->currentGame()->currentGame(90879750);

        $this->assertTrue($game->observers instanceof \LeagueWrap\Dto\Observer);
        $this->assertTrue($game->observers->encryptionKey == 'aQtZTDCPQQ9ZqiKC8iUwRL5NMp4ND/S1');
    }

    public function testCurrentGameParticipant()
    {
        $this->client->shouldReceive('baseUrl')->with('https://euw1.api.riotgames.com/lol/spectator/')
                     ->once();
        $this->client->shouldReceive('request')
                     ->with('v3/active-games/by-summoner/90879750', [
                        'api_key' => 'key',
                     ])->once()
                     ->andReturn(file_get_contents('tests/Json/currentgame.90879750.json'));

        $api = new Api('key', $this->client);
        $api->setRegion('euw');
        $game = $api->currentGame()->currentGame(90879750);

        $this->assertTrue($game->participant(90879750) instanceof \LeagueWrap\Dto\CurrentGameParticipant);
    }

    public function testCurrentGameParticipantMasteries()
    {
        $this->client->shouldReceive('baseUrl')->with('https://euw1.api.riotgames.com/lol/spectator/')
                     ->once();
        $this->client->shouldReceive('request')
                     ->with('v3/active-games/by-summoner/90879750', [
                        'api_key' => 'key',
                     ])->once()
                     ->andReturn(file_get_contents('tests/Json/currentgame.90879750.json'));

        $api = new Api('key', $this->client);
        $api->setRegion('euw');
        $game = $api->currentGame()->currentGame(90879750);

        $participant = $game->participant(90879750);
        $this->assertTrue($participant->mastery(6252) instanceof \LeagueWrap\Dto\Mastery);
        $this->assertTrue($participant->mastery(6252)->rank == 5);
    }

    public function testCurrentGameParticipantRunes()
    {
        $this->client->shouldReceive('baseUrl')->with('https://euw1.api.riotgames.com/lol/spectator/')
                     ->once();
        $this->client->shouldReceive('request')
                     ->with('v3/active-games/by-summoner/90879750', [
                        'api_key' => 'key',
                     ])->once()
                     ->andReturn(file_get_contents('tests/Json/currentgame.90879750.json'));

        $api = new Api('key', $this->client);
        $api->setRegion('euw');
        $game = $api->currentGame()->currentGame(90879750);

        $participant = $game->participant(90879750);
        $this->assertTrue($participant->rune(5273) instanceof \LeagueWrap\Dto\Rune);
        $this->assertTrue($participant->rune(5273)->count == 9);
    }

    public function testAttachStaticData()
    {
        $this->client->shouldReceive('baseUrl')->with('https://euw1.api.riotgames.com/lol/spectator/')
                     ->once();
        $this->client->shouldReceive('baseUrl')->with('https://euw1.api.riotgames.com/lol/static-data/')
            ->times(4);

        $this->client->shouldReceive('request')
                     ->with('v3/active-games/by-summoner/90879750', [
                        'api_key' => 'key',
                     ])->once()
                     ->andReturn(file_get_contents('tests/Json/currentgame.90879750.json'));
        $this->client->shouldReceive('request')
                     ->with('v3/champions', [
                        'api_key'  => 'key',
                        'dataById' => 'true',
                     ])->once()
                     ->andReturn(file_get_contents('tests/Json/Static/champion.euw.json'));
        $this->client->shouldReceive('request')
                     ->with('v3/summoner-spells', [
                        'api_key'  => 'key',
                        'dataById' => 'true',
                     ])->once()
                     ->andReturn(file_get_contents('tests/Json/Static/summonerspell.euw.json'));
        $this->client->shouldReceive('request')
                     ->with('v3/masteries', [
                        'api_key'  => 'key',
                     ])->once()
                     ->andReturn(file_get_contents('tests/Json/Static/mastery.euw.json'));
        $this->client->shouldReceive('request')
                     ->with('v3/runes', [
                        'api_key'  => 'key',
                     ])->once()
                     ->andReturn(file_get_contents('tests/Json/Static/rune.euw.json'));

        $api = new Api('key', $this->client);
        $api->setRegion('euw');
        $api->attachStaticData(true);
        $game = $api->currentGame()->currentGame(90879750);

        $participant = $game->participant(90879750);
        $rune = $participant->rune(5289);
        $this->assertTrue($rune->runeStaticData instanceof LeagueWrap\Dto\StaticData\Rune);
        $masteries = $participant->masteries;
        $this->assertTrue($masteries[6342]->masteryStaticData instanceof LeagueWrap\Dto\StaticData\Mastery);
    }
}
