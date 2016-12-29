<?php
/**
 * Hangzhou Yunshang Network Technology Inc.
 * http://www.wsy.com
 * ==============================================
 * @author: wangzl
 * @date: 2016/12/7
 * @time: 下午3:37
 * @desc: test.php
 */
namespace home;

use common\BaseController;
use core\Cache;

class test extends BaseController
{
    private $redis;
    private $cacheName = 'test:zSets:list';

    public function __construct()
    {
        parent::__construct();
        $this->redis = Cache::getRedis();
    }

    public function demo()
    {
        $size = $this->redis->zSize($this->cacheName);
        debug($size);

//        $this->redis->zAdd($cacheName, 1, 'aaa');
//        $this->redis->zAdd($cacheName, 2, 'bbb');
//        $this->redis->zAdd($cacheName, 3, 'ccc');
//        $this->redis->zAdd($cacheName, 0, 'ddd');
//        $this->redis->zAdd($cacheName, -1, 'eee');
//        $this->redis->zAdd($cacheName, 2, 'fff');
//        $this->redis->zAdd($cacheName, -2, 'ggg');
        $this->redis->zAdd($this->cacheName, 3, 'ggg');

        $list = $this->redis->zRange($this->cacheName, 0, -1);
        debug($list);
    }

    public function index()
    {
        $range = $this->redis->zRange($this->cacheName, 0, -1);

        $list = [];
        foreach ($range as $val) {
            $val = json_decode($val, true);
            $list[$val['sort']] = $val['title'];
        }

        $this->assign('list', $list);
        $this->display();
    }

    public function callAdd()
    {
        $title = getParam('title');
        $sort = getParam('sort');
        $this->redis->zAdd($this->cacheName, $sort, json_encode([
            'title' => $title,
            'sort' => $sort,
        ], JSON_UNESCAPED_UNICODE));
    }

    public function callDel()
    {
        $sort = getParam('sort');
        $this->redis->zRemRangeByScore($this->cacheName, $sort, $sort);
    }
}