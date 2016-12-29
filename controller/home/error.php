<?php
/**
 * Hangzhou Yunshang Network Technology Inc.
 * http://www.wsy.com
 * ==============================================
 * @author: wangzl
 * @date: 2016/11/19
 * @time: 下午2:27
 * @desc: error.php
 */
namespace home;

use common\BaseController;

class error extends BaseController
{
    public function __construct()
    {
        parent::__construct();
    }

    public function s()
    {
        $status = getParam('status');
        $status_msg = '找不到该信息';

        $error_status = C('ERROR_STATUS');
        if (isset($error_status[$status])) {
            $status_msg = $error_status[$status];
        }

        $this->assign('status', $status);
        $this->assign('status_msg', $status_msg);
        $this->display('error/s.htm');
    }
}