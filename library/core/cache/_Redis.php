<?php
/**
 * Hangzhou Yunshang Network Technology Inc.
 * http://www.wsy.com
 * ==============================================
 * @author: wangzl
 * @date: 2016/11/21
 * @time: 上午11:28
 * @desc: _Redis.php
 */
namespace core\cache;

class _Redis extends \Redis
{
    public function __construct()
    {
        // 获取配置
        $ip = _C('REDIS_HOST', '127.0.0.1');
        $port = _C('REDIS_PORT', '6379');
        $db = _C('REDIS_DB', 0);
        // ..
        if (class_exists('Redis')) {
            $this->connect($ip, $port);
            if (C('REDIS_AUTH')) {
                $this->auth(C('REDIS_AUTH'));
            }
            $this->select($db);
        } else {
            throw new \Exception('class: Redis not found!');
        }
    }
}