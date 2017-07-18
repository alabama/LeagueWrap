<?php
/**
 * Created by PhpStorm.
 * User: alabama
 * Date: 18.07.2017
 * Time: 15:18
 */

use LeagueWrap\Enum\MatchmakingQueueEnum;

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

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testConstantNotExist()
    {
        MatchmakingQueueEnum::convert("does-not-exist_for_SURE");
    }
}
