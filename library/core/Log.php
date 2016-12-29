<?php
/**
 * Hangzhou Yunshang Network Technology Inc.
 * http://www.wsy.com
 * ==============================================
 * @author: hypo
 * @date: 2016年7月6日
 * @time: 13:00:00
 * @desc: 日志
 */
namespace core;

class Log
{

    // 记录的字符串
    public $message;

    public $path;

    public $level;

    public function __construct()
    {
        $this->path  = "default";
        $this->level = "info";
    }

    /**
     * 添加信息，累计输出
     */
    public function append($message)
    {
        $this->message = $this->message . $message;
    }

    /**
     * 输出日志
     */
    public function output()
    {
        self::out($this->message, $this->path, $this->level);
    }

    /**
     * 输出
     */
    public static function out($message, $path = "default", $level = "info")
    {
        $message = date('Y-m-d H:i:s') . " [" . $level . "]:" . $message . "\n";
        $name    = self::getName($path);
        $path    = self::getPath($path);
        error_log($message, 3, $path . $name);
    }

    /**
     * 获取存放路径
     * @param  string $type 类型
     * @return string 路径
     */
    public static function getPath($path)
    {
        $log_path = ROOT_PATH . C('RUNTIME') . "/log";
        $path     = $log_path . "/" . $path . "/";

        // 脚本和网站都要用，所以赋予777
        // 手动创建目录,权限777
        if (!file_exists($log_path)) {
            mkdir($log_path, 0777);
        }

        // 不存在则创建文件，并且权限为777
        if (!file_exists($path)) {
            mkdir($path, 0777);
        }
        return $path;
    }

    /**
     * 获取日志名称
     */
    public static function getName($path)
    {
        return $path . "_" . date('Ymd') . ".log";
    }
}
