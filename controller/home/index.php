<?php
/**
 * Hangzhou Yunshang Network Technology Inc.
 * http://www.wsy.com
 * ==============================================
 * @author: wangzl
 * @date: 2016/11/19
 * @time: 下午12:28
 * @desc: index.php
 */
namespace home;

use common\BaseController;
use core\Cache;

class index extends BaseController
{
    public function index()
    {
        $this->display();
    }

    public function test()
    {
        $db = Cache::getMongoDb();

//        $bulk = $db->getBulk();
//        $bulk->insert([
//            'name' => 'hello'
//        ]);
//        $result = $db->executeBulk('users', $bulk);

//        $bulk = $db->getBulk();
//        $bulk->update(['name' => 'test'], ['name' => '1'], ['limit' => 1]);
//        $result = $db->executeBulk('users', $bulk);

//        $bulk = $db->getBulk();
//        $bulk->delete(['name' => '1']);
//        $result = $db->executeBulk('users', $bulk);

        $result = $db->find('users');
        foreach ($result as $cursor) {
            debug($cursor);
        }
    }
}