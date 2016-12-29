<?php
/**
 *Hangzhou Yunshang Network Technology Inc.
 *http://www.wsy.com
 * ==============================================
 * @author: brave
 * @date: 2016-6-15
 * @time: 下午3:26:26
 */
/* * 
 * 功能：支付宝页面跳转同步通知页面
 * 版本：3.3
 * 日期：2012-07-23
 * 说明：
 * 以下代码只是为了方便商户测试而提供的样例代码，商户可以根据自己网站的需要，按照技术文档编写,并非一定要使用该代码。
 * 该代码仅供学习和研究支付宝接口使用，只是提供一个参考。

 *************************页面功能说明*************************
 * 该页面可在本机电脑测试
 * 可放入HTML等美化页面的代码、商户业务逻辑程序代码
 * 该页面可以使用PHP开发工具调试，也可以使用写文本函数logResult，该函数已被默认关闭，见alipay_notify_class.php中的函数verifyReturn
 */
define ( 'WSY', true );
require_once '../../includes/init_admin.php';
require_once(ROOT_PATH."payments/alipaywsy/alipay.config.php");
require_once(ROOT_PATH."payments/alipaywsy/lib/alipay_notify.class.php");
require_once (ROOT_PATH . 'model/flow/payModel.php');
//计算得出通知验证结果
$alipayNotify = new AlipayNotify($alipay_config);
$payModel = new payModel();
$verify_result = $alipayNotify->verifyReturn();
if($verify_result) {//验证成功
	//商户订单号	$out_trade_no = ltrim($_GET['out_trade_no'],'wsy') ;
	//支付宝交易号	$trade_no = $_GET['trade_no'];
	//交易状态
	$trade_status = $_GET['trade_status'];
	//交易金额
	$total_fee = $_GET['total_fee'];
	$json = json_encode($_GET);
    if($trade_status == 'TRADE_FINISHED' || $trade_status == 'TRADE_SUCCESS') {
    	$payment = array('payment_id'=>5,'payment_name'=>'支付宝','payment_code'=>'alipay');
        if($out_trade_no){
        	if($payModel->executPaysuccess($out_trade_no,$payment,$trade_no,$total_fee,$json,'return')){
        		header("location:".$siteurl."flow/pay_success.htm?orderid=$out_trade_no");
        	}else{
        		header("location:".$siteurl."flow/pay_success.htm?orderid=$out_trade_no&errtype=1");
        	}
        }else{
        	logResult('已支付,但支付宝返回的订单编号为空'.$json);
        }
    } else {
    	logResult('trade_status为空'.$json);
    }
    
} else {
    echo "验证失败";
}
