<?php
/**
 * Hangzhou Yunshang Network Technology Inc.
 * http://www.wsy.com
 * ==============================================
 * @author: wangzl
 * @date: 2016年6月22日
 * @time: 下午1:56:25
 * @desc: 文件缓存
 */
namespace core\cache;

class _File
{
    private $hash;
    private $data = array();
    private $cachePath;

    public function __construct()
    {
        $this->cachePath = ROOT_PATH . C('RUNTIME') . '/cache/files';
        if (!file_exists($this->cachePath)) {
            die('目录不存在：' . $this->cachePath);
        }
    }

    public function setConfig($hash)
    {
        $this->hash = $hash;
        $this->read();
    }

    private function getCacheFile()
    {
        return $this->cachePath . '/' . md5($this->hash);
    }

    /**
     * 获取缓存内容
     */
    private function read()
    {
        $filePath = $this->getCacheFile();
        if (file_exists($filePath)) {
            $file = fopen($filePath, 'r');
            $size = filesize($filePath);
            $data = unserialize(fread($file, $size));
            fclose($file);
            $this->data = $data;
        }
    }

    /**
     * 写入到缓存文件
     */
    private function save()
    {
        $file = fopen($this->getCacheFile(), 'w');
        fwrite($file, $this->toString());
        fclose($file);
    }

    /**
     * 转换成文档格式
     */
    private function toString()
    {
        $data = serialize($this->data);
        return $data;
    }

    /**
     * 取出缓存
     * @param string $key 缓存名称
     */
    public function get($key = NULL)
    {
        if (!$key) return $this->data;
        return isset($this->data[$key]) ? $this->data[$key] : NULL;
    }

    /**
     * 写到缓存
     * @param string $key 缓存名称
     * @param array $val 缓存内容
     */
    public function set($key, $val)
    {
        $this->data[$key] = $val;
        $this->save();
    }

    /**
     * 添加到缓存
     * 不写入缓存文件, 可以用 commit 写入到文件
     * 缓存数据量大的时候可以使用
     * @param string $key
     * @param array $val
     */
    public function add($key, $val)
    {
        $this->data[$key] = $val;
    }

    /**
     * 写入到缓存文件
     */
    public function commit()
    {
        $this->save();
    }

    /**
     * 清除缓存
     * @param string $key 缓存名称 为空清空所有
     */
    public function clear($key = NULL)
    {
        if ($key) {
            unset($this->data[$key]);
        } else {
            $this->data = array();
        }
        $this->save();
    }
}