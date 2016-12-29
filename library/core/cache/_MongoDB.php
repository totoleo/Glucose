<?php
/**
 * Hangzhou Yunshang Network Technology Inc.
 * http://www.wsy.com
 * ==============================================
 * @author: wangzl
 * @date: 2016/11/21
 * @time: 上午11:36
 * @desc: _MongoDB.php
 */
namespace core\cache;

use \MongoDB\Driver\Manager;
use \MongoDB\Driver\Query;
use \MongoDB\Driver\BulkWrite;

class _MongoDB
{
    private $_manager;
    private $databaseName;

    public function __construct()
    {
        $host = _C('MONGO_HOST', '127.0.0.1');
        $port = _C('MONGO_PORT', 27017);
        $this->databaseName = _C('MONGO_NAME', 'local');
        if (class_exists('MongoDB\Driver\Manager')) {
            $uri = "mongodb://{$host}:{$port}";
            $this->_manager = new Manager($uri);
        } else {
            throw new \Exception('class: MongoDB not found!');
        }
    }

    /**
     * 查询collection
     * @param $collection
     * @param array $filter
     * @param array $options
     * @return \MongoDB\Driver\Cursor
     */
    public function find($collection, $filter = [], $options = [])
    {
        $query = new Query($filter, $options);
        $cursor = $this->_manager->executeQuery($this->databaseName . '.' . $collection, $query);
        return $cursor;
    }

    /**
     * 获取Bulk，处理 insert, update, delete
     * @return BulkWrite
     */
    public function getBulk()
    {
        $bulk = new BulkWrite();
        return $bulk;
    }

    /**
     * 执行Bulk
     * @param $collection
     * @param $bulk
     * @return \MongoDB\Driver\WriteResult
     */
    public function executeBulk($collection, $bulk)
    {
        $result = $this->_manager->executeBulkWrite($this->databaseName . '.' . $collection, $bulk);
        return $result;
    }
}