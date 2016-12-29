<?php
/**
 * Hangzhou Yunshang Network Technology Inc.
 * http://www.wsy.com
 * ==============================================
 * @author: hypo
 * @date: 2016年7月6日
 * @time: 13:00:00
 * @desc: 控制器
 */
namespace core;

use core\plugins\Template;

class Controller extends Template
{
    private $layout        = '';
    private $layoutEnable  = false;
    private $layoutContent = '';
    private $layoutPath    = '';

    public function __construct()
    {
        parent::__construct();
    }

    public function response($data = [], $type = 'json')
    {
        header("Content-type:text/json;charset=utf-8");
        die(json_encode($data, JSON_UNESCAPED_UNICODE));
    }

    public function display($a = "", $data = [])
    {
        $a = $a ? $this->layoutPath . $a : getViewPath();
        if ($this->layoutEnable) {
            parent::assign($this->layoutContent, $a);
            parent::display($this->layout, $data);
        } else {
            parent::display($a, $data);
        }
    }

    /**
     * 设置母版模板功能
     * @param string $layout 母版模板路径
     * @param string $file include文件名称
     */
    public function setLayout($layout = false, $file = '_FILE_')
    {
        if (!$layout) {
            $this->layoutEnable = false;
        } else {
            $this->layoutEnable  = true;
            $this->layoutContent = $file;
            $this->layout        = $layout;
        }
    }

    /**
     * 设置模块路径
     */
    public function setTemplateDir($path)
    {
        $path = str_replace('\\', '/', trim($path));
        if (substr($path, strlen($path) - 1) != '/') {
            $path .= '/';
        }
        $this->layoutPath = $path;
    }
}
