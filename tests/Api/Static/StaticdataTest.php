<?php

use \Mockery as m;
use LeagueWrap\Api;

class StaticdataTest extends PHPUnit_Framework_TestCase
{
    protected $client;

    public function setUp()
    {
        $this->client = m::mock('LeagueWrap\Client');
    }

    public function tearDown()
    {
        m::close();
    }

    /**
     * Helper function to access a protected method.
     *
     * @param $class string classname
     * @param $name string method name
     *
     * @return ReflectionMethod
     */
    private function getMethod($class, $name)
    {
        $rClass = new ReflectionClass($class);
        $method = $rClass->getMethod($name);
        $method->setAccessible(true);

        return $method;
    }

    public function testAppendId()
    {

        $method = $this->getMethod('LeagueWrap\Api\Staticdata', 'appendId');

        $api = new Api('key', $this->client);
        $staticData = $api->staticData();

        // test various params
        $this->assertTrue($method->invoke($staticData, 1));
        $this->assertFalse($method->invoke($staticData, null));
        $this->assertFalse($method->invoke($staticData, 'all'));
    }

    public function testSetUpParamsAll()
    {
        $method = $this->getMethod('LeagueWrap\Api\Staticdata', 'setUpParams');

        $api = new Api('key', $this->client);
        $staticData = $api->staticData();

        $params = $method->invoke($staticData, '', null, null, 'tags', 'tags');
        $expected = [
        ];
        $this->assertEquals($expected, $params);
    }

    public function testSetUpParamsAllData()
    {
        $method = $this->getMethod('LeagueWrap\Api\Staticdata', 'setUpParams');

        $api = new Api('key', $this->client);
        $staticData = $api->staticData();

        $params = $method->invoke($staticData, '', null, 'all', 'tags', 'tags');
        $expected = [
            'tags' => 'all',
        ];
        $this->assertEquals($expected, $params);
    }

    public function testSetUpParamsDataArray()
    {
        $method = $this->getMethod('LeagueWrap\Api\Staticdata', 'setUpParams');

        $api = new Api('key', $this->client);
        $staticData = $api->staticData();

        $data = ['string1', 'string2'];
        $params = $method->invoke($staticData, '', null, $data, 'tags', 'tags');
        $this->assertEquals(['tags' => array('string1', 'string2')], $params);
    }

    public function testSetUpParamsDataArraySingleItem()
    {
        $method = $this->getMethod('LeagueWrap\Api\Staticdata', 'setUpParams');

        $api = new Api('key', $this->client);
        $staticData = $api->staticData();

        $data = ['string1', 'string2'];
        $params = $method->invoke($staticData, '', 1, $data, 'tags', 'tags');
        $this->assertEquals(['tags' => ['string1', 'string2']], $params);
    }

    public function testSetUpParamsId()
    {
        $method = $this->getMethod('LeagueWrap\Api\Staticdata', 'setUpParams');

        $api = new Api('key', $this->client);
        $staticData = $api->staticData();

        $params = $method->invoke($staticData, '', 1, null, 'tags', 'tags');
        $expected = [];
        $this->assertEquals($expected, $params);
    }

    public function testSetUpParamsIdData()
    {
        $method = $this->getMethod('LeagueWrap\Api\Staticdata', 'setUpParams');

        $api = new Api('key', $this->client);
        $staticData = $api->staticData();

        $params = $method->invoke($staticData, '', 1, 'all', 'tags', 'tags');
        $expected = [
            'tags' => 'all',
        ];
        $this->assertEquals($expected, $params);
    }

    public function testSetUpParamsDataById()
    {
        $method = $this->getMethod('LeagueWrap\Api\Staticdata', 'setUpParams');

        $api = new Api('key', $this->client);
        $staticData = $api->staticData();

        $params = $method->invoke($staticData, 'champions', null, 'all', 'tags', 'tags');
        $expected = [
            'dataById' => 'true',
            'tags' => 'all',
        ];
        $this->assertEquals($expected, $params);
    }

    public function testLanguage()
    {
        $method = $this->getMethod('LeagueWrap\Api\Staticdata', 'setUpParams');

        $api = new Api('key', $this->client);
        $staticData = $api->staticData();
        $staticData->setLocale('fr_FR');

        $params = $method->invoke($staticData, 'champions', 266, 'tags', 'tags', 'tags');
        $expected = [
            'locale'    => 'fr_FR',
            'tags' => 'tags',
        ];
        $this->assertEquals($expected, $params);
    }
}
