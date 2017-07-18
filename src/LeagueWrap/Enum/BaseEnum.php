<?php
/**
 * Created by PhpStorm.
 * User: alabama
 * Date: 18.07.2017
 * Time: 14:40
 */

namespace LeagueWrap\Enum;


abstract class BaseEnum implements ConstantConvertableInterface
{
    public function getClassName()
    {
        return get_class($this);
    }

    /**
     * @param $constantValue
     *
     * @return string
     */
    public static function convert($constantValue)
    {
        return EnumConstantConverter::convert(new static(), $constantValue);
    }
}
