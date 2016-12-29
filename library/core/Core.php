<?php
/**
 * Hangzhou Yunshang Network Technology Inc.
 * http://www.wsy.com
 * ==============================================
 * @author: hypo
 * @date: 2016年7月6日
 * @time: 13:00:00
 * @desc: 核心类
 */
namespace core;

class Core
{
    public static function start()
    {
        // 注册AUTOLOAD方法
        spl_autoload_register('core\Core::autoload');
        // 设定错误和异常处理
        register_shutdown_function('core\Core::fatalError');
        set_error_handler('core\Core::appError');
        set_exception_handler('core\Core::appException');

        // 初始化加载配置及方法
        Init::start();

        // 修改session
        // Session::start();

        // 执行控制器
        App::start();

        // 结束session 控制
        // Session::end();
    }

    public static function load()
    {
        spl_autoload_register('core\Core::autoload');
        // 初始化加载配置及方法
        Init::start();
    }

    // 自动加载
    public static function autoload($class)
    {
        // 设置自定义加载类库目录，固定
        $library_path = ROOT_PATH . "/library/";
        if ($class) {
            // 对model service library 特殊处理
            $file = str_replace('\\', '/', $class) . '.php';
            if (strstr($file, 'Service') || strstr($file, 'Model')) {
                $path = ROOT_PATH . '/';
            } else {
                $path = $library_path;
            }
            // 对controller进行处理
            if (!file_exists($path . $file)) {
                $path = ROOT_PATH . "/controller/";
            }
            $loadPath = $path . $file;
            // echo $loadPath . "<br>";
            if (file_exists($loadPath)) {
                require $loadPath;
            }
        }
    }

    public static function fatalError()
    {
        if ($e = error_get_last()) {
            switch ($e['type']) {
                case E_ERROR:
                case E_PARSE:
                case E_CORE_ERROR:
                case E_COMPILE_ERROR:
                case E_USER_ERROR:
                    ob_end_clean();
                    self::errorShow($e, 500);
                    break;
            }
        }
    }

    public static function appError($errno, $errstr, $errfile, $errline)
    {
        $e            = [];
        $e['type']    = $errno;
        $e['message'] = $errstr;
        $e['file']    = $errfile;
        $e['line']    = $errline;

        switch ($errno) {
            case E_ERROR:
            case E_PARSE:
            case E_CORE_ERROR:
            case E_COMPILE_ERROR:
            case E_USER_ERROR:
                ob_end_clean();
                self::errorShow($e, 500);
                break;
            default:
                self::errorShow($e);
                break;
        }
    }

    /**
     * @param \Exception $e
     */
    public static function appException($e)
    {
        $error            = array();
        $error['message'] = $e->getMessage();
        $trace            = $e->getTrace();
        if ('E' == $trace[0]['function']) {
            $error['file'] = $trace[0]['file'];
            $error['line'] = $trace[0]['line'];
        } else {
            $error['file'] = $e->getFile();
            $error['line'] = $e->getLine();
        }
        $error['trace'] = $e->getTraceAsString();
        $error['type']  = "Exception";

        $status = $e->getCode() ? $e->getCode() : 500;
        self::errorShow($error, $status);
    }

    // todo 添加日志
    public static function errorShow($e, $status = 404)
    {
        if (isset($e['type']) && isset($e['message']) && isset($e['file']) && isset($e['line'])) {
            $errorStr = "[" . $e['type'] . "] " . $e['message'] . " " . $e['file'] . " 第 " . $e['line'] . " 行.<br>";
        } else {
            $errorStr = "这错误也是绝了！！";
        }

        if (!isset($e['trace'])) {
            ob_start();
            debug_print_backtrace();
            $e['trace'] = ob_get_clean();
        }

        if (C('IS_DEBUG')) {
            self::send_http_status($status);
            self::debug($errorStr);
            if (C('SHOW_TRACE')) {
                self::debug($e['trace']);
            }
        } else {
            if (E_NOTICE != $e['type']) {
                Log::out($errorStr, "system");
                header("location: " . C('PAGE_NOT_FOUND'));
                exit;
            }
        }
    }

    /**
     * 发送HTTP状态
     * @param integer $code 状态码
     * @return void
     */
    public static function send_http_status($code)
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

    /**
     * [debug 调试]
     * @param  [array] $data [输出数组]
     */
    public static function debug($data)
    {
        echo "<pre>";
        print_r($data);
        echo "</pre>";
    }
}
