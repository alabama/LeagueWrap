<?php

use LeagueWrap\Region;

class RegionTest extends PHPUnit_Framework_TestCase
{
    public function testIsLocked()
    {
        $region = new Region('euw');
        $this->assertTrue($region->isLocked(['na']));
    }

    public function testIsLockedFalse()
    {
        $region = new Region('euw');
        $this->assertFalse($region->isLocked([
            'na',
            'euw',
            'eune',
        ]));
    }

    public function testGetStandardizedDomain()
    {
        $region = new Region('na');
        $this->assertEquals('https://na1.api.riotgames.com/lol/', $region->getStandardizedDomain());
    }

    public function testGetStandardizedDomainBr()
    {
        $region = new Region('br');
        $this->assertEquals('https://br1.api.riotgames.com/lol/', $region->getStandardizedDomain());
    }

    public function testGetStandardizedDomainJp1()
    {
        $region = new Region('jp');
        $this->assertEquals('https://jp1.api.riotgames.com/lol/', $region->getStandardizedDomain());
    }


    public function testGetPlatformDomain()
    {
        $region = new Region('na');
        $this->assertEquals('https://na1.api.riotgames.com/lol/platform/', $region->getPlatformDomain());
    }

    public function testGetPlatformDomainBr()
    {
        $region = new Region('br');
        $this->assertEquals('https://br1.api.riotgames.com/lol/platform/', $region->getPlatformDomain());
    }

    public function testGetPlatformDomainJp1()
    {
        $region = new Region('jp');
        $this->assertEquals('https://jp1.api.riotgames.com/lol/platform/', $region->getPlatformDomain());
    }
}
