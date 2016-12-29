<?php
/**
 * Hangzhou Yunshang Network Technology Inc.
 * http://www.wsy.com
 * ==============================================
 * @author: hypo
 * @date: 2016年7月6日
 * @time: 13:00:00
 * @desc: 初始化类目
 */
namespace core;

class Init
{
    public static function start()
    {
        // 获取配置文件
        Init::setConfig();
        // 自动加载公共方法
        Init::autoload();
        // 自动加载路由
        Init::setRoute();
    }

    public static function setConfig()
    {
        global $_config;
        if (file_exists(ROOT_PATH . "/common/config/config.php")) {
            $_config = require_once ROOT_PATH . "/common/config/config.php";
        } else {
            header('HTTP/1.1 500 Internal Server Error');
            exit('<br><h2 style="font-size:40px">/(ㄒoㄒ)/~~</h2><h1>error:config is running with your mistake!</h1>');
        }
    }

    public static function autoload()
    {
        $at_path = ROOT_PATH . "/common/function/";
        $path    = scandir($at_path);
        foreach ($path as $k => $v) {
            if (strstr($v, 'at_')) {
                require_once $at_path . $v;
            }
        }
    }

    public static function setRoute()
    {
        global $_route;
        if (file_exists(ROOT_PATH . "/common/config/route.php")) {
            $_route = require_once ROOT_PATH . "/common/config/route.php";
        } else {
            $_route = [];
        }
    }
}
