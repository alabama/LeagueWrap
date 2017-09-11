<?php
/**
 * Created by PhpStorm.
 * User: alabama
 * Date: 18.07.2017
 * Time: 14:36
 */

namespace LeagueWrap\Enum;


class EnumConstantConverter
{
    const UNKNOWN = "UNKNOWN";
    /**
     * Returns the constant name as string
     *
     * @param int $constantValue            One of the constants values defined in this class
     *
     * @return string                       Returns the constant name as string of the given constant value
     */
    public static function convert(ConstantConvertableInterface $interface, $constantValue)
    {
        $class = new \ReflectionClass($interface);
        $constantList = array_flip($class->getConstants());

        if(!array_key_exists($constantValue, $constantList)) {
            $constantList[$constantValue] = EnumConstantConverter::UNKNOWN;
        }

        return $constantList[$constantValue];
    }
}
