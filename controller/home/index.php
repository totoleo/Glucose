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

class index extends BaseController
{
    protected $layout = 'layout/main.htm';

    public function index()
    {
        $this->setTtitle('Hello Glucose!');
        $this->display();
    }
}
