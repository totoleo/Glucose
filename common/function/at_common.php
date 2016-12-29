<?php
/**
 * Hangzhou Yunshang Network Technology Inc.
 * http://www.wsy.com
 * ==============================================
 * @author: wangzl
 * @date: 2016年7月26日
 * @time: 上午11:46:14
 * @desc: 常用函数
 */

/**
 * 从数组中取出指定数量的随机内容
 * @param array $array
 * @param number $num
 */
function array_rand_specified($array, $num = 0)
{
    $result = [];
    $size = count($array);
    if ($size <= 1) return $array;
    if ($num == 0 || $num > $size) $num = $size;
    if ($num >= $size) {
        while ($num != 0) {
            $num--;
            $array = array_values($array);
            $i = rand(0, $num);
            $result[] = $array[$i];
            unset($array[$i]);
        }
    } else {
        // 取出values, 赋下标为自然数
        $array = array_values($array);
        // 取出随机下标
        $rand = array_rand($array, $num);
        foreach ($rand as $val) {
            $result[] = $array[$val];
        }
    }
    return $result;
}

/**
 * 获取get请求的参数组合
 * @param array $ignore 忽略参数
 */
function getRequestParam($ignore = [])
{
    $result = '';
    // 预设忽略参数
    $ignore[] = 's';
    foreach ($_GET as $key => $val) {
        // 跳过忽略参数
		$val=htmlentities($val);
        if (in_array($key, $ignore)) continue;
        $result .= $key . '=' . $val . '&';
    }
    // 截掉最后的&符号
    if ($result) $result = substr($result, 0, strlen($result) - 1);
    return $result;
}

/**
 * 判断用户是否是移动端浏览器
 */
function userBrowserIsMobile()
{
    $useragent = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '';
    $useragent_commentsblock = preg_match('|\(.*?\)|', $useragent, $matches) > 0 ? $matches[0] : '';
    function CheckSubstrs($substrs, $text)
    {
        foreach ($substrs as $substr)
            if (false !== strpos($text, $substr)) {
                return true;
            }
        return false;
    }

    $mobile_os_list = array('Google Wireless Transcoder', 'Windows CE', 'WindowsCE', 'Symbian', 'Android', 'armv6l', 'armv5', 'Mobile', 'CentOS', 'mowser', 'AvantGo', 'Opera Mobi', 'J2ME/MIDP', 'Smartphone', 'Go.Web', 'Palm', 'iPAQ');
    $mobile_token_list = array('Profile/MIDP', 'Configuration/CLDC-', '160×160', '176×220', '240×240', '240×320', '320×240', 'UP.Browser', 'UP.Link', 'SymbianOS', 'PalmOS', 'PocketPC', 'SonyEricsson', 'Nokia', 'BlackBerry', 'Vodafone', 'BenQ', 'Novarra-Vision', 'Iris', 'NetFront', 'HTC_', 'Xda_', 'SAMSUNG-SGH', 'Wapaka', 'DoCoMo', 'iPhone', 'iPod');

    $found_mobile = CheckSubstrs($mobile_os_list, $useragent_commentsblock) || CheckSubstrs($mobile_token_list, $useragent);
    if ($found_mobile) {
        return true;
    } else {
        return false;
    }
}

/**
 * 判断是否是ajax异步请求方式
 * <br/><br/>
 * @desc
 * 原生js异步请求，需要设置requestHeader：
 * <br/>
 * xmlhttp.setRequestHeader("X-Requested-With", "XMLHttpRequest");
 * @return true & false
 */
function isAjaxRequest()
{
    return isset($_SERVER["HTTP_X_REQUESTED_WITH"]) && strtolower($_SERVER["HTTP_X_REQUESTED_WITH"]) == "xmlhttprequest" ? true : false;
}

/**
 * 发送手机短信
 * @param int $phone
 * @param string $content
 */
function smsSend($phone, $content)
{
    //发送验证码
    $client = new SoapClient('http://www.jianzhou.sh.cn/JianzhouSMSWSServer/services/BusinessService?wsdl', array('encoding' => 'UTF-8'));
    $param = array('account' => 'sdk_wsy', 'password' => '2016@PXS.WSY', 'destmobile' => $phone, 'msgText' => $content);    //接口参数
    $result = $client->sendBatchMessage($param); //接口方法
    return $result;
}

/**
 * 发送手机短信验证码
 * @param $check
 * @param $phone
 * @return mixed
 */
function smsSendCode($check, $phone)
{
    $content = "";
    $content .= "您的网商园验证码为" . $check;
    $content .= "，30分钟内有效。";
    $content .= "(请勿向任何人提供您收到的短信验证码)";
    $content .= "【网商园】";

    return smsSend($phone, $content);
}

/**
 * 检查图片类型
 * @param $filename
 * @return string
 */
function checkImageType($filename)
{
    $file = fopen($filename, "rb");
    $bin = fread($file, 2); //只读2字节
    fclose($file);
    $strInfo = unpack("c2chars", $bin);
    $typeCode = intval($strInfo['chars1'] . $strInfo['chars2']);
    switch ($typeCode) {
        case 255216:
            $fileType = 'jpg';
            break;
        case 7173:
            $fileType = 'gif';
            break;
        case 6677:
            $fileType = 'bmp';
            break;
        case 13780:
            $fileType = 'png';
            break;
        default:
            $fileType = 'other';
    }
    //Fix
    if ($strInfo['chars1'] == '-1' && $strInfo['chars2'] == '-40') {
        return 'jpg';
    }
    if ($strInfo['chars1'] == '-119' && $strInfo['chars2'] == '80') {
        return 'png';
    }
    return $fileType;
}

/**
 * 比较两个日期之间的差
 * @param $begin_time
 * @param $end_time
 * @return array
 */
function time_diff( $begin_time, $end_time )
{
    if ( $begin_time < $end_time ) {
        $starttime = $begin_time;
        $endtime = $end_time;
    } else {
        $starttime = $end_time;
        $endtime = $begin_time;
    }
    $timediff = $endtime - $starttime;
    $days = intval( $timediff / 86400 );
    $remain = $timediff % 86400;
    $hours = intval( $remain / 3600 );
    $remain = $remain % 3600;
    $mins = intval( $remain / 60 );
    $secs = $remain % 60;
    $res = array( "day" => $days, "hour" => $hours, "min" => $mins, "sec" => $secs );
    return $res;
}

/**
 * 返回在线旺旺
 * @param array $alis
 * @param string $default
 */
function alis_to_online($alis = [], $default = '')
{
    $url = 'https://amos.alicdn.com/muliuserstatus.aw?callback=C&site=cntaobao&charset=utf-8&uids=' . implode(';', $alis);
    $result = str_replace(['C(', ')'], '', \tools\Request::Get($url));
    $result = json_decode($result, true);
    if (is_array($result)) {
        if (isset($result['success']) && $result['success'] == 1) {
            $onlineList = [];
            foreach ($result['data'] as $key => $val) {
                if ($val == 1) $onlineList[] = $alis[$key];
            }
            if ($onlineList) return $onlineList;
        }
    }
    return $default;
}
?>