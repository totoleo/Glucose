<?php
/**
 * Hangzhou Yunshang Network Technology Inc.
 * http://www.wsy.com
 * ==============================================
 * @author: wangzl
 * @date 2016年6月13日
 * @time 下午10:06:45
 * @desc Solr操作类
 */
namespace core;

class Solr {
    const PATH_GOODS  = 'solr/core0';
    
    const ORDER_ASC   = \SolrQuery::ORDER_ASC;
    const ORDER_DESC  = \SolrQuery::ORDER_DESC;

    protected $config = [];
    protected $client;
    protected $params;

    protected $defaultField = 'title';

    public function __construct() {
        if (!class_exists('SolrClient'))
            die('5016: Plugin does not exist');
        $this->params = new \SolrQuery();
        $this->setField($this->defaultField);

        $this->config['hostname'] = C('SOLR_SERVER_HOSTNAME');
        $this->config['port']     = C('SOLR_SERVER_PORT');
        $this->config['timeout']  = C('SOLR_SERVER_TIMEOUT');
        $this->config['path']     = self::PATH_GOODS;

        // client初始化
        $this->initClient(true);
    }
    /**
     * 初始化SolrClient
     * @param string $compel 强制初始化
     */
    protected function initClient($compel = false) {
        if (!$this->client || $compel)
            $this->client = new \SolrClient($this->config);
        return $this->client;
    }
    /**
     * 重新设置查询参数
     */
    public function reset() {
        unset($this->params);
        $this->params = new \SolrQuery();
        $this->setField($this->defaultField);
        return $this;
    }
    /**
     * 查询SOLR
     * @return SolrQueryResponse
     */
    public function select() {
        try {
            $resp = $this->client->query($this->params);
        } catch (\Exception $e) {
            throw $e; // 查询遇错
        }
        return $resp;
    }
    /**
     * 添加或更新document
     * @param string $doc SolrInputDocument对象
     * @return SolrUpdateResponse
     */
    public function addOrUpdateDoc($doc) {
        $resp = $this->client->addDocument($doc);
        return $resp;
    }
    /**
     * 删除document
     * @param string $id 主键PK
     * @return SolrUpdateResponse
     */
    public function delDoc($id) {
        $resp = $this->client->deleteById($id);
        return $resp;
    }
    /**
     * 提交操作
     * @return SolrUpdateResponse
     */
    public function commit() {
        $resp = $this->client->commit();
        return $resp;
    }
    /**
     * 连接SOLR服务器测试
     * @return SolrPingResponse
     */
    public function ping() {
        try {
            $resp = $this->client->ping();
        } catch (\Exception $e) {
            die($e->getMessage());
        }
        return $resp;
    }
    /**
     * 特殊字符过滤
     * Filter special characters
     */
    public function FSC($str) {
        $special = ["'",'"','\\','/','(',')','[',']','{','}','*','?','^','&','+',':','：','~','`','!','-'];
        $str = str_replace($special, ' ', $str);
        return trim($str);
    }
    /**
     * 设置SOLR服务器地址
     * @param string $host
     * @param number $port
     * @return Solr
     */
    public function setHost($host, $port = 8983) {
        $this->config['hostname'] = $host;
        $this->config['port'] = $port;
        return $this;
    }
    /**
     * 设置查询处理器
     * @param string $handler 处理器名称，如 select, query
     * @return Solr
     */
    public function setHandler($handler) {
        $this->client->setServlet(\SolrClient::SEARCH_SERVLET_TYPE, $handler);
        return $this;
    }
    /**
     * 设置SOLR查询core
     * @param string $path
     * @return Solr
     */
    public function setPath($path) {
        $this->config['path'] = $path;
        return $this;
    }
    /**
     * 设置查询超时时间
     * @param string $timeout
     * @return Solr
     */
    public function setTimeout($timeout) {
        $this->config['timeout'] = $timeout;
        return $this;
    }
    /**
     * 设置查询内容，q中填写查询内容，查询字段放在field里面
     * @param string $q 查询内容
     * @param string $field 查询字段
     * @return Solr
     */
    public function setQuery($q, $field = false) {
        $this->params->setQuery($q);
        if ($field) 
            $this->setField($field);
        return $this;
    }
    /**
     * 设置默认查询字段
     * @param string $field
     */
    public function setField($field) {
        $this->setParam('df', $field);
        return $this;
    }
    /**
     * 设置edismax查询模式
     * @param boolean $bool
     */
    public function setEdismax($bool = false) {
        if ($bool) {
            $this->setParam('defType', 'edismax');
        } else {
            $this->setParam('defType', '');
        }
    }
    /**
     * 添加过滤查询
     * 除主要查询外其他条件都放在这个里
     * @param string $fq
     */
    public function addFilterQuery($fq) {
        $this->params->addFilterQuery($fq);
        return $this;
    }
    /**
     * 设置查询高亮
     * @param string $bool
     */
    public function setHighlight($bool = false, $pre = '<b>', $post = '</b>') {
        $this->params->setHighlight($bool);
        if ($bool) 
        {
            $this->params->setHighlightSimplePre($pre);
            $this->params->setHighlightSimplePost($post);
        }
        return $this;
    }
    /**
     * 添加高亮字段
     * @param string $field
     * @return Solr
     */
    public function addHighlightField($field) {
        $this->params->addHighlightField($field);
        return $this;
    }
    /**
     * 设置返回内容从那一条开始
     * @param int $start
     */
    public function setStart($start) {
        $this->params->setStart($start);
        return $this;
    }
    /**
     * 设置返回内容的总条数
     * @param int $rows
     */
    public function setRows($rows) {
        $this->params->setRows($rows);
        return $this;
    }
    /**
     * 设置开始行数和返回数量
     * @param string $offset
     * @param string $size
     * @return Solr
     */
    public function setLimit($offset, $size) {
        $this->setStart($offset);
        $this->setRows($size);
        return $this;
    }
    /**
     * 添加排序筛选
     * @param string $field 排序字段
     * @param enum $order 排序方式
     */
    public function addSortField($field, $order = self::ORDER_DESC) {
        $this->params->addSortField($field, $order);
        return $this;
    }
    /**
     * 开启统计
     * @param boolean $bool
     */
    public function setFacet($bool) {
        $this->params->setFacet($bool);
        return $this;
    }
    /**
     * 添加统计字段
     * @param string $field
     */
    public function addFacetField($field) {
        $this->params->addFacetField($field);
        return $this;
    }
    /**
     * 添加查询参数
     * @param string $name
     * @param string $value
     */
    public function addParam($name, $value) {
        $this->params->addParam($name, $value);
        return $this;
    }
    /**
     * 设置查询参数
     * @param string $name
     * @param string $value
     */
    public function setParam($name, $value) {
        $this->params->setParam($name, $value);
        return $this;
    }
    
    public function __destruct() {
        unset($this->params);
        unset($this->client);
        unset($this);
    }
}
?>