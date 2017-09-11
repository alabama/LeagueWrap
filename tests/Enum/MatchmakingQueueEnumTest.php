<?php
/**
 * Created by PhpStorm.
 * User: alabama
 * Date: 18.07.2017
 * Time: 15:18
 */

use LeagueWrap\Enum\MatchmakingQueueEnum;
use LeagueWrap\Enum\EnumConstantConverter;

class MatchmakingQueueEnumTest extends PHPUnit_Framework_TestCase
{
    public function testClassname()
    {
        $oMatchmakingQueueEnum = new MatchmakingQueueEnum();
        $this->assertEquals(MatchmakingQueueEnum::class, $oMatchmakingQueueEnum->getClassName());


    }

    public function testConvert()
    {
        $this->assertEquals("RANKED_FLEX_SR", MatchmakingQueueEnum::convert(440));

        $this->assertEquals("RANKED_SOLO_5x5", MatchmakingQueueEnum::convert(MatchmakingQueueEnum::RANKED_SOLO_5x5));
    }

    public function testConstantNotExist()
    {
        $this->assertEquals(EnumConstantConverter::UNKNOWN, MatchmakingQueueEnum::convert("does-not-exist_for_SURE"));
    }
}
