<?php

use LeagueWrap\Api;
use LeagueWrap\Dto\MatchTimeline;
use Mockery as m;

class ApiMatchTimelineTest extends PHPUnit_Framework_TestCase
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

    public function testTimeline()
    {
        $this->client->shouldReceive('baseUrl')->with('https://na1.api.riotgames.com/lol/match/')
            ->once();
        $this->client->shouldReceive('request')
            ->with('v3/timelines/by-match/2544869868', [
                'api_key'         => 'key'
            ])->once()
            ->andReturn(file_get_contents('tests/Json/matchhistory.match.2544869868.timeline.json'));

        $api = new Api('key', $this->client);
        $match = $api->matchTimeline()->timeline(2544869868);
        $this->assertTrue($match instanceof LeagueWrap\Dto\MatchTimeline);
    }

    public function testTimelineFrame()
    {
        $this->client->shouldReceive('baseUrl')->with('https://na1.api.riotgames.com/lol/match/')
            ->once();
        $this->client->shouldReceive('request')
            ->with('v3/timelines/by-match/2544869868', [
                'api_key'         => 'key'
            ])->once()
            ->andReturn(file_get_contents('tests/Json/matchhistory.match.2544869868.timeline.json'));

        $api = new Api('key', $this->client);
        $match = $api->matchTimeline()->timeline(2544869868);

        $frame = $match->frames[1];
        $this->assertTrue($frame instanceof LeagueWrap\Dto\TimelineFrame);
        $this->assertTrue($frame->participantFrame(1) instanceof LeagueWrap\Dto\TimelineParticipantFrame);
        $this->assertTrue($frame->events[0] instanceof LeagueWrap\Dto\TimelineFrameEvent);
    }
}
