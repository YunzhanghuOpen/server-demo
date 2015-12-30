<?php

namespace App\Helpers;

class HMAC
{

    const TIME_SCOPE = 300; //second 5*60=300

    /**
     * 计算签名
     * @param $input
     * @param $key
     * @return string
     */
    public static function calculate($input, $key)
    {

        Logger::authorizationInfo('@HMAC calculate begin', func_get_args());

        $signPars = "";
        ksort($input);
        foreach ($input as $k => $v) {
            if ("sign" != $k && "" != $v) {
                $signPars .= $k . "=" . $v . "&";
            }
        }
        $signPars .= "key=" . $key;

        Logger::authorizationInfo('@HMAC calculate original string = ' . $signPars);

        $hash = strtolower(hash('sha256', $signPars));

        Logger::authorizationInfo('@HMAC calculate end, hash = ' . $hash);

        return $hash;
    }

    /**
     * 比较签名
     * @param $input
     * @param $key
     * @param $sign
     * @return bool
     */
    public static function compare($input, $key, $sign)
    {

        Logger::authorizationInfo('@HMAC compare begin', func_get_args());

        $hash = self::calculate($input, $key);

        Logger::authorizationInfo('@HMAC compare 比较签名，计算的签名:' . $hash);
        Logger::authorizationInfo('@HMAC compare 比较签名，链接中签名:' . $sign);

        $rst = $hash === $sign;

        Logger::authorizationInfo('@HMAC compare end, ret = ' . intval($rst));

        return $rst;
    }

    /**
     * 检查授权链接是否过期
     * @param $timestamp
     * @return bool
     */
    static function checkExpired($timestamp)
    {
        $offset = abs(time() - $timestamp);
        return $offset > self::TIME_SCOPE;
    }

}