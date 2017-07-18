<?php
/**
 * Created by PhpStorm.
 * User: alabama
 * Date: 18.07.2017
 * Time: 15:18
 */

use LeagueWrap\Enum\SeasonEnum;

class SeasonEnumTest extends PHPUnit_Framework_TestCase
{
    public function testClassname()
    {
        $oSeasonEnum = new SeasonEnum();
        $this->assertEquals(SeasonEnum::class, $oSeasonEnum->getClassName());


    }

    public function testConvert()
    {
        $this->assertEquals("SEASON2015", SeasonEnum::convert(5));

        $this->assertEquals("PRESEASON2017", SeasonEnum::convert(SeasonEnum::PRESEASON2017));
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testConstantNotExist()
    {
        SeasonEnum::convert("does-not-exist_for_SURE");
    }
}
