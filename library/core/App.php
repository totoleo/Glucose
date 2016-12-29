<?php
/**
 * Hangzhou Yunshang Network Technology Inc.
 * http://www.wsy.com
 * ==============================================
 * @author: hypo
 * @date: 2016年7月6日
 * @time: 13:00:00
 * @desc: APP路由选择
 */
namespace core;

use tools\Page;

class App
{
    public static function start()
    {
        self::loadController();
    }

    private static function loadController()
    {
        $action = isset($_GET['act']) ? trim($_GET['act']) : "";

        $url    = $_GET['r'] ?? C('DEFAULT_MOUDEL') . '/' . C('DEFAULT_ACTION');
        $method = strtolower($_SERVER['REQUEST_METHOD']);

        // JS 跨域请求时候用到
        if ("option" == $method) {

        }

        $pos = strpos($url, '.');
        if (false !== $pos) {
            $url = substr($url, 0, $pos);
        }

        $pos = strpos($url, '?');
        if (false !== $pos) {
            $url = substr($url, 0, $pos);
        }

        $routePath = R($url);
        $routePath = $routePath ? $routePath : $url;
        $path      = explode("/", $routePath);

        if (!$path[0]) {
            unset($path[0]);
        }

        $pathCount = count($path);
        if (1 == $pathCount || (!$action && 2 == $pathCount)) {
            $class = implode('\\', $path);
            if (!class_exists($class)) {
                array_unshift($path, C('DEFAULT_MOUDEL'));
            }
        }

        $pathCount = count($path);
        if (!$action) {
            // 不完整的路径自动补全action
            if ($pathCount < 3) {
                $action = isset($_POST['act']) ? trim($_POST['act']) : C('DEFAULT_ACTION');
            } else {
                $action = end($path);
                if (isset($path[0])) {
                    unset($path[count($path) - 1]);
                } else {
                    unset($path[count($path)]);
                }
                // 处理action名称
                $action = explode('-', $action);
                foreach ($action as $key => $val) {
                    if ($key === 0) continue;
                    $val[$key] = strtoupper(substr($val, 0, 1)) . substr($val, 1);
                }
                $action = implode($action);
            }
        }

        if ($path[1] == 'common') {
            header('Location: ' . C('PAGE_NOT_FOUND'));
        }
        if (isset($path[1]) && !$path[1]) {
            $path[1] = C('DEFAULT_CONTROLLER');
        }

        $class = implode('\\', $path);

        // echo "class  ----- : " . $class . "<br>";
        // echo "action --- : " . $action . "<br><br><br>";

        getViewPath(str_replace("\\", "/", $class . "\\" . $action . ".htm"));

        if (class_exists($class)) {
            $obj           = new $class();
            $method_action = $action . "_" . $method;
            if (method_exists($class, $method_action)) {
                // method 优先
                $obj->$method_action();
            } elseif (method_exists($class, $action)) {
                // 不存在调用默认方法
                $obj->$action();
            } else {
                E('not found function ' . $action, 404);
            }
        } else {
            E('not found class ' . $class, 404);
        }
    }
}
