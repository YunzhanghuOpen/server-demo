<?php
/**
 * Created by PhpStorm.
 * User: philiptang
 * Date: 12/30/15
 * Time: 17:30
 */

namespace App\Helpers;


class Response
{

    /**
     * 请求错误返回 400，常见有『参数错误』、『对象不存在』
     * @param $code
     * @param null $context
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public static function error($code, $context = null)
    {
        $message = FaultCode::getMessage($code);
        $data = [
            'code' => $code,
            'message' => $message,
            'context' => ''
        ];
        if (isset($context)) {
            $data['context'] = $context;
        }
        return response()->json($data, 400, [], JSON_UNESCAPED_UNICODE);
    }

    /**
     * 请求正确返回 200
     *
     * @param $data
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public static function result($data)
    {
        return response()->json($data, 200, [], JSON_UNESCAPED_UNICODE);
    }

    /**
     * 请求异常的返回 如404、500等，不保证 body 内容，也不应该将 body 内容显示给用户
     *
     * @param int $status
     * @param string $msg
     */
    public static function exception($status = 500, $msg = '服务器错误')
    {
        abort($status, $msg);
    }


}