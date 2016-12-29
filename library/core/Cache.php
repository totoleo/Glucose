<?php
/**
 * Hangzhou Yunshang Network Technology Inc.
 * http://www.wsy.com
 * ==============================================
 * @author: wangzl
 * @date: 2016/11/21
 * @time: 上午10:03
 * @desc: Cache.php
 */
namespace core;

use core\cache\_File;
use core\cache\_MongoDB;
use core\cache\_Redis;

class Cache
{
    /**
     * @param string $name
     * @param callable $func function
     * @return mixed
     */
    protected static function getObj($name, $func)
    {
        if (!isset($GLOBALS[$name]) || !$GLOBALS[$name]) {
            $GLOBALS[$name] = $func();
        }
        return $GLOBALS[$name];
    }

    /**
     * @return _Redis
     */
    public static function getRedis()
    {
        $name = '_REDIS';
        return self::getObj($name, function () {
            return new cache\_Redis();
        });
    }

    /**
     * @return _MongoDB
     */
    public static function getMongoDb()
    {
        $name = '_MONGODB';
        return self::getObj($name, function () {
            return new cache\_MongoDB();
        });
    }

    /**
     * @return _File
     */
    public static function getFile()
    {
        $name = '_FILE';
        return self::getObj($name, function () {
            return new cache\_File();
        });
    }
}