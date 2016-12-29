<?php
/**
 * Hangzhou Yunshang Network Technology Inc.
 * http://www.wsy.com
 * ==============================================
 * @author: wangzl
 * @date: 2016年7月11日
 * @time: 下午2:13:31
 * @desc:
 *  默认Action函数为index
 *  可以使用 *_get,*_post 区分请求方式
 *  如果使用了 *_get,*_post 优先调用
 */
namespace common;

use core\Controller;

class BaseController extends Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * 控制器默认首页
     * BaseController::index()
     */
    public function index()
    {
        if (C('IS_DEBUG')) {
            $msg = '<span style="font-size: 108px; font-weight: bold; display: block;">:)</span><p>请在控制中创建默认Action函数：index()</p>';
            $msg .= '<p>非调试模式时，未定义默认Action将会跳转到预先设定的404页面</p>';
            echo $msg;
            exit;
        } else {
            header('Location: ' . _C('PAGE_NOT_FOUND', '/'));
            exit;
        }
    }

    /*
     * 判断是否来自ajax请求
     */
    public function IsAjax()
    {
        if (isset($_SERVER["HTTP_X_REQUESTED_WITH"]) && strtolower($_SERVER["HTTP_X_REQUESTED_WITH"]) == "xmlhttprequest") {
            return true;
        } else {
            return false;
        }
    }
}
