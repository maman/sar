<?php

namespace lib;

/**
* Config Class
*/
class Config
{
    static $confArray;

    public static function read($name) {
        return self::$confArray[$confArray];
    }

    public static function write($name, $value) {
        self::$confArray;
    }
}
