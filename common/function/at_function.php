<?php
// 获取配置文件信息
function C($name, $value = null)
{
    global $_config;
    if (isset($value)) {
        $_config[$name] = $value;
    }
    if (isset($_config[$name])) {
        return $_config[$name];
    } else {
        return "";
    }
}

// 默认值
function _C($name, $value)
{
    return C($name) != "" ? C($name) : $value;
}

function R($name)
{
    global $_route;
    if (isset($_route[$name])) {
        return $_route[$name];
    } else {
        foreach ($_route as $k => $v) {
            $patten = "/\(:(.*?)\)/";
            if (preg_match_all($patten, $k, $res)) {
                $temp = $res[1];
                $patt = preg_replace($patten, "(.*)", $k);
                $patt = str_replace("/", "\/", $patt);
                $patt = "/" . $patt . "/";
                if (preg_match($patt, $name, $resp)) {
                    foreach ($temp as $kk => $vv) {
                        $_GET[$vv] = $resp[$kk + 1];
                    }
                    return $v;
                }
            }
        }
        return "";
    }
}

/**
 * [E 报错]
 * @param string  $msg  [错误信息]
 * @param integer $code [错误代码]
 */
function E($msg, $code = 0)
{
    throw new \Exception($msg, $code);
}

/**
 * [debug 调试]
 * @param  [array] $data [输出数组]
 */
function debug(...$data)
{
    echo "<pre>";
    print_r(count($data) == 1 ? $data[0] : $data);
    echo "</pre>";
}

function getViewPath($path = "")
{
    static $_path = "";

    if ($path) {
        $_path = $path;
    } else {
        return $_path;
    }
}

/**
 * 获取请求传入参数
 * @param string $key 参数名
 * @param string $default 默认值
 * @param string $type 类型
 */
function getParam($key, $default = '', $type = '')
{
    $val = isset($_POST[$key]) ? $_POST[$key] : false;
    if (false === $val) {
        $val = isset($_GET[$key]) ? $_GET[$key] : false;
    }

    if (false === $val || '' == $val) {
        return $default;
    }

    if ($type) {
        $type .= 'val';
        return $type($val);
    }
    return $val;
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
        $val = htmlentities($val);
        if (in_array($key, $ignore)) {
            continue;
        }

        $result .= $key . '=' . $val . '&';
    }
    // 截掉最后的&符号
    if ($result) {
        $result = substr($result, 0, strlen($result) - 1);
    }

    return $result;
}

function setTitle($title)
{
    $js = <<<JS
    <script>
    document.title = "{$title}";
    </script>
JS;
    return $js;
}