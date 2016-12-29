<?php
/**
 * Hangzhou Yunshang Network Technology Inc.
 * http://www.wsy.com
 * ==============================================
 * @author: wangzl
 * @date: 2016/8/22
 * @time: 17:36
 * @desc: ali.php
 */
namespace tools\pay\alipay;

require_once ROOT_PATH . '/library/org/payments/alipay/lib/alipay_submit.class.php';

class submit extends \AlipaySubmit
{
    protected $config;

    public function __construct()
    {
        $this->config = C('ALIPAY_CONFIG');
        $this->config['notify_url'] = C('SITE_URL') . $this->config['notify_url'];
        $this->config['return_url'] = C('SITE_URL') . $this->config['return_url'];
        parent::__construct($this->config);
    }

    /**
     * 提交支付宝支付
     * @param $subject
     * @param $tradeNo
     * @param $totalFee
     * @param string $body
     */
    public function submitPay($subject, $tradeNo, $totalFee, $body = '')
    {
        $params = [
            "service"       => $this->config['service'],
            "partner"       => $this->config['partner'],
            "seller_id"     => $this->config['seller_id'],
            "payment_type"	=> $this->config['payment_type'],
            "notify_url"	=> $this->config['notify_url'],
            "return_url"	=> $this->config['return_url'],
            "anti_phishing_key" => $this->config['anti_phishing_key'],
            "exter_invoke_ip" => $this->config['exter_invoke_ip'],
            "out_trade_no"	=> $tradeNo,
            "subject"	=> $subject,
            "total_fee"	=> $totalFee,
            "body"	=> $body,
            "_input_charset" => trim(strtolower($this->config['input_charset']))
        ];

        //建立请求
        $html_text = $this->buildRequestForm($params, "get", "确认");
        echo $html_text;
    }

    /**
     * 设置回调地址
     * @param $returnUrl
     */
    public function setReturnUrl($returnUrl)
    {
        $this->config['return_url'] = $returnUrl;
    }

    /**
     * 设置通知地址（异步）
     * @param $notifyUrl
     */
    public function setNotifyUrl($notifyUrl)
    {
        $this->config['notify_url'] = $notifyUrl;
    }
}