<?php
/**
 * Hangzhou Yunshang Network Technology Inc.
 * http://www.wsy.com
 * ==============================================
 * @author: wangzl
 * @date: 2016年7月25日
 * @time: 下午3:26:05
 * @desc: 档口屏蔽列表
 */
namespace tools;

class Curl
{
    private $curl;
    private $api;
    private $token;
    public function __construct()
    {
        $this->load();
        $this->curl  = new \curl();

        $this->curl->options['CURLOPT_USERPWD'] = '';
    }

    /**
     * 加载TopSDK
     */
    private function load()
    {
        $filePath = ROOT_PATH . '/library/org/curl/curl.php';
        if (!file_exists($filePath)) {
            E('CURL不存在');
        }
        require_once $filePath;

        $filePath = ROOT_PATH . '/library/org/curl/curl_response.php';
        if (!file_exists($filePath)) {
            E('curl_response不存在');
        }
        require_once $filePath;
    }

    public function post($url, $params = [])
    {
        return $this->sendRequest($url, $params, 'post');
    }

    public function get($url, $params = [])
    {
        return $this->sendRequest($url, $params, 'get');
    }

    public function sendRequest($url, $params = [], $method = "get")
    {
        if ("get" == $method) {
            // get
            $result = $this->curl->get($url, $params);
        } else {
            // post
            $result = $this->curl->post($url, $params);
        }

        $resp = $result->body ? $result->body : [];
        return $resp;
    }
}
