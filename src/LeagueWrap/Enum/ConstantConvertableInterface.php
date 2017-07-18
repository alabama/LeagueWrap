<?php
/**
 * Created by PhpStorm.
 * User: alabama
 * Date: 18.07.2017
 * Time: 14:31
 */

namespace LeagueWrap\Enum;


interface ConstantConvertableInterface
{
    /**
     * @return returns the fully qualified classname  (namespace + classname)
     */
    public function getClassName();
}
