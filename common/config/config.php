<?php
/**
 * @author: wangzl
 * @date: 2016年7月18日
 * @time: 11:25:27
 * @desc: 配置文件
 */
$error_status = require 'error.php';

return [
    // 数据库配置
    'DB_HOST'              => '127.0.0.1',
    'DB_PORT'              => '3306', // 端口
    'DB_NAME'              => '', // 数据库名
    'DB_USER'              => 'root', // 用户名
    'DB_PWD'               => '', // 密码
    'DB_PREFIX'            => '', // 数据库表前缀
    'DB_CHARSET'           => 'utf8', // 数据库编码默认采用utf8

    // 框架配置
    'DEFAULT_MOUDEL'       => 'home', // 默认模块
    'DEFAULT_CONTROLLER'   => 'index', // 默认控制器
    'DEFAULT_ACTION'       => 'index', // 默认处理器
    'DEFAULT_TEMPLATE'     => 'views', // 默认模板目录

    // redis缓存配置
    'REDIS_HOST'           => '127.0.0.1', // redis 地址
    'REDIS_PORT'           => 6379, // redis 端口
    'REDIS_DB'             => 1, // redis 数据库选择
    'REDIS_AUTH'           => '', // auth pass

    // solr
    'SOLR_SERVER_HOSTNAME' => '192.168.1.235',
    'SOLR_SERVER_PORT'     => 16010,
    'SOLR_SERVER_TIMEOUT'  => 5,

    // mongodb
    'MONGO_HOST'           => '127.0.0.1',
    'MONGO_PORT'           => 27017,
    'MONGO_NAME'           => 'local',

    // debug
    'IS_DEBUG'             => true, // 是否测试环境
    'SHOW_TRACE'           => true, // 是否显示trace记录

    // runtime
    'RUNTIME'              => '/runtime', // 可读写文件目录

    // error define
    'ERROR_STATUS'         => $error_status,

    // 特定页面
    'PAGE_NOT_FOUND'       => '/error-404.htm', // 页面找不到
];
