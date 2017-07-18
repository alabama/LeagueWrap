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

    /**
     * Returns the constant name as string
     *
     * @param int $constantValue            One of the constants values defined in this class
     *
     * @return string                       Returns the constant name as string of the given constant value
     *
     * @throws \InvalidArgumentException    Will be thrown if the given seasonId does not exist as a constant
     */
    public static function convert(ConstantConvertableInterface $interface, $constantValue)
    {
        $class = new \ReflectionClass($interface);
        $constantList = array_flip($class->getConstants());

        if(!array_key_exists($constantValue, $constantList)) {
            throw new \InvalidArgumentException("There is no constant name for the given constant value: {$constantValue}");
        }

        return $constantList[$constantValue];
    }
}
