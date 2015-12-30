<?php
/**
 * Created by PhpStorm.
 * User: philiptang
 * Date: 12/30/15
 * Time: 16:23
 */

namespace App\Helpers;

use Monolog\Logger as mlogger;
use Monolog\Handler\StreamHandler;
use Monolog\Formatter\LineFormatter;
use Monolog\Handler\MongoDBHandler;
use Monolog\Formatter\MongoDBFormatter;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\Config\Exception\FileLoaderLoadException;
use Illuminate\Contracts\Support\Arrayable;


/**
 * 日志写入类
 *
 * Class Logger
 * @package App\Helpers
 */
class Logger
{

    protected static $_conf = [];
    protected static $_handlers = [];

    protected static function _loadConf()
    {
        if (empty(static::$_conf)) {
            static::$_conf = config('Log');
        }
    }

    /**
     * 根据配置生成日志操作的 handle
     * @param $name
     * @return mixed
     * @throws Exception
     */
    protected static function _loadHandle($name)
    {
        static::_loadConf();
        if (empty(static::$_handlers[$name])) {
            if (0 === strcasecmp('file', static::$_conf[$name]['handle'])) {
                static::_loadStreamHandler($name);
            } elseif (0 === strcasecmp('mongodb', static::$_conf[$name]['handle'])) {
                static::_loadMongoDBHandle($name);
            } else {
                throw new Exception('Log configure error.');
            }
        }
        return static::$_handlers[$name];
    }

    /**
     * 加载文件写入的操作类。
     * @see Monolog\Handler\StreamHandler
     *
     * @param $name
     */
    protected static function _loadStreamHandler($name)
    {
        $mlogger = new mlogger($name);
        // 日志文件加上日期后缀
        $logfile = static::$_conf[$name]['path'] . '.' . date('Ymd');
        $streamhander = new StreamHandler($logfile, mlogger::DEBUG, true, 0664);
        $streamhander->setFormatter(new LineFormatter(null, null, false, true));
        $mlogger->pushHandler($streamhander);
        static::$_handlers[$name] = $mlogger;
    }


    /**
     * 加载 MongoDB 的写入操作类
     * @see Monolog\Handler\MongoDBHandler
     *
     * @param $name
     */
    protected static function _loadMongoDBHandle($name)
    {
        //用到时再写
    }

    /**
     * 转换变量类型并写入日志。
     *
     * @param $name
     * @param $method
     * @param $message
     * @param $context
     * @return mixed
     */
    protected static function _write($name, $method, $message, $context)
    {
        $handle = static::_loadHandle($name);
        if ($context instanceof Arrayable) {
            $context = $context->toArray();
        } else {
            $context = (array)$context;
        }
        return $handle->$method($message, $context);
    }

    /**
     * 写入用户的正常信息。把 monolog 原有的各种类型简化成 info 和 error 两种类型。
     *
     * @param $message
     * @param array $context
     * @return mixed
     */
    public static function userInfo($message, $context = [])
    {
        return static::_write('user', 'info', $message, $context);
    }

    /**
     * 写入用户错误信息。把 monolog 原有的各种类型简化成 info 和 error 两种类型。
     *
     * @param $message
     * @param array $context
     * @return mixed
     */
    public static function userError($message, $context = [])
    {
        return static::_write('user', 'error', $message, $context);
    }

    /**
     * 写入事件操作结果的正常信息。把 monolog 原有的各种类型简化成 info 和 error 两种类型。
     *
     * @param $message
     * @param array $context
     * @return mixed
     */
    public static function eventInfo($message, $context = [])
    {
        return static::_write('event', 'info', $message, $context);
    }

    /**
     * 写入操作结果的错误信息。把 monolog 原有的各种类型简化成 info 和 error 两种类型。
     *
     * @param $message
     * @param array $context
     * @return mixed
     */
    public static function eventError($message, $context = [])
    {
        return static::_write('event', 'error', $message, $context);
    }

    /**
     * 写入系统相关操作结果的正常信息，比如抓取记录之类的
     *
     * @param $message
     * @param array $context
     * @return mixed
     */
    public static function sysInfo($message, $context = [])
    {
        return static::_write('sys', 'info', $message, $context);
    }

    /**
     * 写入系统相关操作结果的出错信息，比如抓取记录之类的
     *
     * @param $message
     * @param array $context
     * @return mixed
     */
    public static function sysError($message, $context = [])
    {
        return static::_write('sys', 'error', $message, $context);
    }

    /**
     * Middleware 操作的正常信息
     *
     * @param $message
     * @param array $context
     * @return mixed
     */
    public static function middlewareInfo($message, $context = [])
    {
        return static::_write('middleware', 'info', $message, $context);
    }

    /**
     * Middleware 操作的出错信息
     *
     * @param $message
     * @param array $context
     * @return mixed
     */
    public static function middlewareError($message, $context = [])
    {
        return static::_write('middleware', 'error', $message, $context);
    }

    /**
     * Job 操作的正常信息
     *
     * @param $message
     * @param array $context
     * @return mixed
     */
    public static function JobInfo($message, $context = [])
    {
        return static::_write('job', 'info', $message, $context);
    }

    /**
     * Job 操作的出错信息
     *
     * @param $message
     * @param array $context
     * @return mixed
     */
    public static function JobError($message, $context = [])
    {
        return static::_write('job', 'error', $message, $context);
    }

}