<?php

class ResponseTest extends PHPUnit_Framework_TestCase
{
    public function testSetCode()
    {
        $response = new LeagueWrap\Response('foo...bar!', 200);
        $this->assertEquals(200, $response->GetCode());
    }

    public function testConstructor() {
        $code = 200;
        $content = 'foo...bar';
        $headers = array("foo" => array("bar"));

        $response = new \LeagueWrap\Response($content, $code, $headers);
        $this->assertEquals($code, $response->getCode());
        $this->assertEquals($content, $response->__toString());
        $this->assertEquals($content, $response);
        $this->assertEquals(array("foo" => "bar"), $response->getHeaders());
    }
}
