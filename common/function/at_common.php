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
    $size   = count($array);
    if ($size <= 1) {
        return $array;
    }

    if ($num == 0 || $num > $size) {
        $num = $size;
    }

    if ($num >= $size) {
        while ($num != 0) {
            $num--;
            $array    = array_values($array);
            $i        = rand(0, $num);
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
 * 判断请求是否是手机端
 */
function request_is_mobile()
{
    $useragent               = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '';
    $useragent_commentsblock = preg_match('|\(.*?\)|', $useragent, $matches) > 0 ? $matches[0] : '';
    function CheckSubstrs($substrs, $text)
    {
        foreach ($substrs as $substr) {
            if (false !== strpos($text, $substr)) {
                return true;
            }
        }

        return false;
    }

    $mobile_os_list    = array('Google Wireless Transcoder', 'Windows CE', 'WindowsCE', 'Symbian', 'Android', 'armv6l', 'armv5', 'Mobile', 'CentOS', 'mowser', 'AvantGo', 'Opera Mobi', 'J2ME/MIDP', 'Smartphone', 'Go.Web', 'Palm', 'iPAQ');
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
function request_is_ajax()
{
    return isset($_SERVER["HTTP_X_REQUESTED_WITH"]) && strtolower($_SERVER["HTTP_X_REQUESTED_WITH"]) == "xmlhttprequest" ? true : false;
}

/**
 * 检查图片类型
 * @param $filename
 * @return string
 */
function image_check_type($filename)
{
    $file = fopen($filename, "rb");
    $bin  = fread($file, 2); //只读2字节
    fclose($file);
    $strInfo  = unpack("c2chars", $bin);
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

function image_size($url, $type = 'curl', $isGetFilesize = true)
{
    // 若需要获取图片体积大小则默认使用 fread 方式
    $type = $isGetFilesize ? 'fread' : $type;

    if ('fread' == $type) {
        // 或者使用 socket 二进制方式读取, 需要获取图片体积大小最好使用此方法
        $handle = @fopen($url, 'rb');

        if (!$handle) {
            return false;
        }

        // 只取头部固定长度168字节数据
        $dataBlock = fread($handle, 168);
    } else {
        // 据说 CURL 能缓存DNS 效率比 socket 高
        $ch = curl_init($url);
        // 超时设置
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);
        // 取前面 168 个字符 通过四张测试图读取宽高结果都没有问题,若获取不到数据可适当加大数值
        curl_setopt($ch, CURLOPT_RANGE, '0-167');
        // 跟踪301跳转
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        // 返回结果
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $dataBlock = curl_exec($ch);

        curl_close($ch);

        if (!$dataBlock) {
            return false;
        }

    }

    // 将读取的图片信息转化为图片路径并获取图片信息,经测试,这里的转化设置 jpeg 对获取png,gif的信息没有影响,无须分别设置
    // 有些图片虽然可以在浏览器查看但实际已被损坏可能无法解析信息
    $size = getimagesize('data://image/jpeg;base64,' . base64_encode($dataBlock));
    if (empty($size)) {
        return false;
    }

    $result['width']  = $size[0];
    $result['height'] = $size[1];

    // 是否获取图片体积大小
    if ($isGetFilesize) {
        // 获取文件数据流信息
        $meta = stream_get_meta_data($handle);
        // nginx 的信息保存在 headers 里，apache 则直接在 wrapper_data
        $dataInfo = isset($meta['wrapper_data']['headers']) ? $meta['wrapper_data']['headers'] : $meta['wrapper_data'];

        foreach ($dataInfo as $va) {
            if (preg_match('/length/iU', $va)) {
                $ts             = explode(':', $va);
                $result['size'] = trim(array_pop($ts));
                break;
            }
        }
    }

    if ('fread' == $type) {
        fclose($handle);
    }

    return $result;
}

/**
 * 比较两个日期之间的差
 * @param $begin_time
 * @param $end_time
 * @return array
 */
function time_diff($begin_time, $end_time)
{
    if ($begin_time < $end_time) {
        $starttime = $begin_time;
        $endtime   = $end_time;
    } else {
        $starttime = $end_time;
        $endtime   = $begin_time;
    }
    $timediff = $endtime - $starttime;
    $days     = intval($timediff / 86400);
    $remain   = $timediff % 86400;
    $hours    = intval($remain / 3600);
    $remain   = $remain % 3600;
    $mins     = intval($remain / 60);
    $secs     = $remain % 60;
    $res      = array("day" => $days, "hour" => $hours, "min" => $mins, "sec" => $secs);
    return $res;
}

// 手机号码验证格式
function check_phone($phone)
{
    $pattern = '/^1[34578]{1}\d{9}$/';
    if (preg_match($pattern, $phone)) {
        return true;
    } else {
        return false;
    }
}

// 验证邮箱
function check_email($email)
{
    $pattern = "/^([0-9A-Za-z\\-_\\.]+)@([0-9a-z]+\\.[a-z]{2,3}(\\.[a-z]{2})?)$/i";
    if (preg_match($pattern, $email)) {
        return ture;
    } else {
        return false;
    }
}

/**
 * 获取客户端IP地址
 * @param integer $type 返回类型 0 返回IP地址 1 返回IPV4地址数字
 * @param boolean $adv 是否进行高级模式获取（有可能被伪装）
 * @return mixed
 */
function get_client_ip($type = 0, $adv = false)
{
    $type      = $type ? 1 : 0;
    static $ip = null;
    if (null !== $ip) {
        return $ip[$type];
    }

    if ($adv) {
        if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $arr = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
            $pos = array_search('unknown', $arr);
            if (false !== $pos) {
                unset($arr[$pos]);
            }

            $ip = trim($arr[0]);
        } elseif (isset($_SERVER['HTTP_CLIENT_IP'])) {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (isset($_SERVER['REMOTE_ADDR'])) {
            $ip = $_SERVER['REMOTE_ADDR'];
        }
    } elseif (isset($_SERVER['REMOTE_ADDR'])) {
        $ip = $_SERVER['REMOTE_ADDR'];
    }
    // IP地址合法验证
    $long = sprintf("%u", ip2long($ip));
    $ip   = $long ? array($ip, $long) : array('0.0.0.0', 0);
    return $ip[$type];
}

/**
 * 登录时获取浏览器标识
 */
function get_session_ip()
{
    return isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : "";
}

/**
 * 发送HTTP状态
 * @param integer $code 状态码
 * @return void
 */
function send_http_status($code)
{
    static $_status = array(
        // Informational 1xx
        100 => 'Continue',
        101 => 'Switching Protocols',
        // Success 2xx
        200 => 'OK',
        201 => 'Created',
        202 => 'Accepted',
        203 => 'Non-Authoritative Information',
        204 => 'No Content',
        205 => 'Reset Content',
        206 => 'Partial Content',
        // Redirection 3xx
        300 => 'Multiple Choices',
        301 => 'Moved Permanently',
        302 => 'Moved Temporarily ', // 1.1
        303 => 'See Other',
        304 => 'Not Modified',
        305 => 'Use Proxy',
        // 306 is deprecated but reserved
        307 => 'Temporary Redirect',
        // Client Error 4xx
        400 => 'Bad Request',
        401 => 'Unauthorized',
        402 => 'Payment Required',
        403 => 'Forbidden',
        404 => 'Not Found',
        405 => 'Method Not Allowed',
        406 => 'Not Acceptable',
        407 => 'Proxy Authentication Required',
        408 => 'Request Timeout',
        409 => 'Conflict',
        410 => 'Gone',
        411 => 'Length Required',
        412 => 'Precondition Failed',
        413 => 'Request Entity Too Large',
        414 => 'Request-URI Too Long',
        415 => 'Unsupported Media Type',
        416 => 'Requested Range Not Satisfiable',
        417 => 'Expectation Failed',
        // Server Error 5xx
        500 => 'Internal Server Error',
        501 => 'Not Implemented',
        502 => 'Bad Gateway',
        503 => 'Service Unavailable',
        504 => 'Gateway Timeout',
        505 => 'HTTP Version Not Supported',
        509 => 'Bandwidth Limit Exceeded',
    );
    if (isset($_status[$code])) {
        header('HTTP/1.1 ' . $code . ' ' . $_status[$code]);
        // 确保FastCGI模式下正常
        header('Status:' . $code . ' ' . $_status[$code]);
    }
}
