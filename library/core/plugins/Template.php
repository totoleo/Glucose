<?php
/**
 * Hangzhou Yunshang Network Technology Inc.
 * http://www.wsy.com
 * ==============================================
 * @author: wangzl
 * @date: 2016年7月13日
 * @time: 上午10:05:49
 * @desc: Smarty.php
 */
namespace core\plugins;

require_once ROOT_PATH . '/library/org/smarty/Smarty.class.php';

class Template
{
    public $_smarty_tmp;

    public function __construct()
    {
        $this->_smarty_tmp = new \Smarty();
        // 设置模板路径
        $this->_smarty_tmp->setTemplateDir(ROOT_PATH . '/' . C('DEFAULT_TEMPLATE'));
        // 设置编译路径(需读写权限)
        $this->_smarty_tmp->setCompileDir(ROOT_PATH . C('RUNTIME') . '/smarty/compiled');
        // 设置缓存路径
        $this->_smarty_tmp->setCacheDir(ROOT_PATH . C('RUNTIME') . '/smarty/caches');
        // 设置缓存类型
        // $this->_smarty_tmp->caching = self::CACHING_LIFETIME_CURRENT;
        // 设置缓存有效时间(秒)
        // $this->_smarty_tmp->setCacheLifetime(3600);
        // 是否重复编译
        $this->_smarty_tmp->setForceCompile(false);
        // 是否启用PHP代码
        // $this->_smarty_tmp->allow_php_templates = true;
    }

    public function assign($name, $value)
    {
        $this->_smarty_tmp->assign($name, $value);
    }

    public function display($name, $data = [])
    {
        foreach ($data as $key => $value) {
            $this->_smarty_tmp->assign($key, $value);
        }
        $this->_smarty_tmp->display($name);
    }
}
