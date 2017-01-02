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

class ApiController extends Controller
{
    private $status = [
        200 => 'success',
    ];

    public function __construct()
    {
        parent::__construct();
    }

    // 设置状态信息
    public function setStatus($state, $message)
    {
        $this->status[$state] = $message;
    }

    // 设置状态列表
    public function setStatuses($status = [])
    {
        $this->status = array_merge($this->status, $status);
    }

    // 返回JSON数据
    public function returnJson($data = [])
    {
        header("Content-type:text/json; charset=utf-8");
        echo json_encode($data, JSON_UNESCAPED_UNICODE);exit;
    }

    // 返回成功信息
    public function returnSuccessMsg($data = [])
    {
        $this->returnFailMsg(200, $data);
    }

    // 返回状态信息
    public function returnStateMsg($state, $data = [])
    {
        $this->returnCustomMsg($state, $this->status[$state], $data);
    }

    // 返回自定义信息
    public function returnCustomMsg($state, $message, $data = [])
    {
        $this->returnJson(['status' => $state, 'message' => $message, 'data' => $data]);
    }
}
