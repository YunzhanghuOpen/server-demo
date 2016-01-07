<?php
/**
 * Created by PhpStorm.
 * User: philiptang
 * Date: 12/30/15
 * Time: 17:31
 */

namespace App\Helpers;


use Illuminate\Support\Facades\Log;

class FaultCode
{

    const SUCCESSFUL = '0';

    const PARAMS_ERROR = '1';
    const ACCESS_DENIED = '2';

    const USER_NOT_EXIST = '100';
    const USER_EXIST = '101';
    const MOBILE_ILLEGAL = '102';
    const PASSWORD_NOT_MATCH = '103';
    const RRJ = '104';

    const YZH_RESP_EMPTY = '200';
    const YZH_RESP_ERR = '201';


    static public function getMessage($code)
    {
        $config = [
            self::SUCCESSFUL => '操作成功',

            self::PARAMS_ERROR => '参数错误',
            self::ACCESS_DENIED => '禁止访问',

            self::USER_EXIST => '该用户已经存在',
            self::USER_NOT_EXIST => '该用户不存在',
            self::MOBILE_ILLEGAL => '手机号格式不正确',
            self::PASSWORD_NOT_MATCH => '您的密码不正确',
            self::RRJ => '注册拒绝',

            self::YZH_RESP_EMPTY => '请求云账户无响应',
            self::YZH_RESP_ERR => '云账户返回失败结果',
        ];

        Log::info('config', $config);

        return $config[$code];
    }


}