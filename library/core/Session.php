<?php
/**
 * Hangzhou Yunshang Network Technology Inc.
 * http://www.wsy.com
 * ==============================================
 * @author: hypo
 * @date: 2016年7月12日
 * @time: 13:00:00
 * @desc: session修改类
 */
namespace core;

class Session
{
    public static function start()
    {
        $GLOBALS['_SESSION']   = array();
        $session['session_id'] = self::get_session_id();
        $GLOBALS['_SESSION']   = $session;
        self::get_session_info();
        $GLOBALS['_SESSION']['session_id'] = $session['session_id'];
    }

    private static function get_session_id()
    {
        if (isset($_COOKIE[_C('SESSION_NAME', '_wsy_')])) {
            $session_id = $_COOKIE[_C('SESSION_NAME', '_wsy_')];
        } else {
            $session_id = md5(uniqid(mt_rand(), true));
            setcookie(_C('SESSION_NAME', '_wsy_'), $session_id, 0, _C('COOKIE_PATH', "/"), _C('COOKIE_DOMAIN', "wsy.com"), _C('COOKIE_SECURE', false));
        }
        return $session_id;
    }

    private static function get_session_info()
    {
        $redis      = Cache::getRedis();
        $session_id = $_SESSION['session_id'];
        $info       = $redis->hGet($session_id, 'data');
        if ($info) {
            $GLOBALS['_SESSION'] = unserialize($info);
        }
    }

    public static function end()
    {
        $redis      = Cache::getRedis();
        $session    = $GLOBALS['_SESSION'];
        $session_id = $session['session_id'];
        $data       = serialize($session);
        $redis->hSet($session_id, 'data', $data);
        $redis->expire($session_id, _C('SESSION_EXPIRE', 7200));
        unset($GLOBALS['_SESSION']);
    }
}
