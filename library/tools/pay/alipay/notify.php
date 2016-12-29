<?php
/**
 * Hangzhou Yunshang Network Technology Inc.
 * http://www.wsy.com
 * ==============================================
 * @author: wangzl
 * @date: 2016/8/22
 * @time: 17:46
 * @desc: notify.php
 */
namespace tools\pay\alipay;

require_once ROOT_PATH . '/library/org/payments/alipay/lib/alipay_notify.class.php';

class notify extends \AlipayNotify
{
    private $config;

    public function __construct()
    {
        $this->config = C('ALIPAY_CONFIG');
        $this->config['notify_url'] = C('SITE_URL') . $this->config['notify_url'];
        $this->config['return_url'] = C('SITE_URL') . $this->config['return_url'];
        parent::__construct($this->config);
    }
}