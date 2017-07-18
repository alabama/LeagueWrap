<?php
/**
 * Created by PhpStorm.
 * User: alabama
 * Date: 18.07.2017
 * Time: 15:18
 */

use LeagueWrap\Enum\EnumConstantConverter;
use LeagueWrap\Enum\SeasonEnum;
use LeagueWrap\Enum\MatchmakingQueueEnum;

class EnumConstantConverterTest extends PHPUnit_Framework_TestCase
{
    public function testConvert()
    {
        $this->assertEquals("PRESEASON3", EnumConstantConverter::convert(new SeasonEnum(), 0));
        $this->assertEquals("SEASON2017", EnumConstantConverter::convert(new SeasonEnum(), "9"));

        $this->assertEquals("ONEFORALL_MIRRORMODE_5x5", EnumConstantConverter::convert(new MatchmakingQueueEnum, 78));
        $this->assertEquals("TEAM_BUILDER_RANKED_SOLO", EnumConstantConverter::convert(new MatchmakingQueueEnum, 420));
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testConstantNotExist()
    {
        EnumConstantConverter::convert(new SeasonEnum(), "does-not-exist_for_SURE");
    }
}
