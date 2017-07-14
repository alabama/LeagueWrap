<?php

use LeagueWrap\Api;
use LeagueWrap\Response;
use LeagueWrap\Response\Http404;
use LeagueWrap\Response\Http429;
use Mockery as m;


class Http429Test extends \PHPUnit_Framework_TestCase
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

    private function getHeaders()
    {
        $headers['Content-Type']                     = ["application/json;charset=utf-8"];
        $headers['Retry-After']                      = ["5"];
        $headers['X-App-Rate-Limit']                 = ["100:120,20:1"];
        $headers['X-App-Rate-Limit-Count']           = ["101:120,1:1"];
        $headers['X-Method-Rate-Limit']              = ["20000:10,1200000:600"];
        $headers['X-Method-Rate-Limit-Count']        = ["20:10,101:600"];
        $headers['X-Rate-Limit-Count']               = ["101:120,1:1"];
        $headers['X-Rate-Limit-Type']                = ["application"];
        $headers['Content-Length']                   = ["62"];
        $headers['Connection']                       = ["keep-alive"];
        return $headers;
    }

    private function simulateWithResponse(\LeagueWrap\Response $response)
    {
        $this->client->shouldReceive('baseUrl')->with('https://na1.api.riotgames.com/lol/platform/')
            ->once();
        $this->client->shouldReceive('request')
            ->with('v3/champions', [
                'freeToPlay' => 'false',
                'api_key'    => 'key',
            ])->once()
            ->andReturn($response);
    }

    public function testConstructor() {
        $code = 429;

        $http429 = new Http429("foo...bar", $code);
        $this->assertEquals($code, $http429->getCode());
    }

    public function testNotRateExceeded() {
        try {
            $aHeader = $this->getHeaders();
            unset($aHeader["Retry-After"]);
            $this->simulateWithResponse(
                new Response('{"status": {"status_code": 429, "message": "Rate limit exceeded"}}',
                    429,
                    $aHeader
                )
            );

            $api = new Api('key', $this->client);
            $api->champion()->selectVersion('v3')->all();
        } catch (Response\Http429 $e) {
            $this->assertTrue($e->hasResponse());
            $this->assertFalse($e->getResponse()->hasHeader('Retry-After'));
            $this->assertFalse($e->isRateExceeded());
        }
    }

    public function testWrongHeaders()
    {
        try {
            $aHeader = array("foo" => array("bar"));
            $this->simulateWithResponse(
                new Response('{"status": {"status_code": 429, "message": "Rate limit exceeded"}}',
                    429,
                    $aHeader
                )
            );

            $api = new Api('key', $this->client);
            $api->champion()->selectVersion('v3')->all();
        } catch (Response\Http429 $e) {
            $this->assertTrue($e->hasResponse());
            $this->assertFalse($e->isRateExceeded());
        }
    }

    public function testIsRateExceeded()
    {
        try {
            $aHeader = $this->getHeaders();
            $this->simulateWithResponse(
                new Response('{"status": {"status_code": 429, "message": "Rate limit exceeded"}}',
                    429,
                    $aHeader
                )
            );

            $api = new Api('key', $this->client);
            $api->champion()->selectVersion('v3')->all();
        } catch (Response\Http429 $e) {
            $this->assertTrue($e->hasResponse());
            $this->assertTrue($e->getResponse()->hasHeader('Retry-After'));
            $this->assertTrue($e->isRateExceeded());
        }
    }

    public function testGetRetryAfterSecondsNotExists()
    {
        try {
            $aHeader = $this->getHeaders();
            unset($aHeader["Retry-After"]);
            $this->simulateWithResponse(
                new Response('{"status": {"status_code": 429, "message": "Rate limit exceeded"}}',
                    429,
                    $aHeader
                )
            );

            $api = new Api('key', $this->client);
            $api->champion()->selectVersion('v3')->all();
        } catch (Response\Http429 $e) {
            $this->assertTrue($e->hasResponse());
            $this->assertFalse($e->getResponse()->hasHeader('Retry-After'));
            $this->assertEquals(0, $e->getRetryAfterInSeconds());
        }
    }

    public function testGetRetryAfterSecondsWrongHeader()
    {
        try {
            $aHeader = array("foo" => array("bar"));
            $this->simulateWithResponse(
                new Response('{"status": {"status_code": 429, "message": "Rate limit exceeded"}}',
                    429,
                    $aHeader
                )
            );

            $api = new Api('key', $this->client);
            $api->champion()->selectVersion('v3')->all();
        } catch (Response\Http429 $e) {
            $this->assertTrue($e->hasResponse());
            $this->assertFalse($e->isRateExceeded());
            $this->assertEquals(0, $e->getRetryAfterInSeconds());
        }
    }

    public function testGetRetryAfterSecondsFive()
    {
        try {
            $aHeader = $this->getHeaders();
            $this->simulateWithResponse(
                new Response('{"status": {"status_code": 429, "message": "Rate limit exceeded"}}',
                    429,
                    $aHeader
                )
            );

            $api = new Api('key', $this->client);
            $api->champion()->selectVersion('v3')->all();
        } catch (Response\Http429 $e) {
            $this->assertTrue($e->hasResponse());
            $this->assertTrue($e->isRateExceeded());
            $this->assertEquals(5, $e->getRetryAfterInSeconds());
        }
    }
}