<?php

namespace App\Helpers;

class CommonHelper
{

    /**
     * 比较密码
     * @param $inputPassword
     * @param $dbPassword
     * @param $salt
     * @return bool
     */
    public static function comparePassword($inputPassword, $dbPassword, $salt)
    {
        return crypt($inputPassword, $salt) == $dbPassword;
    }

    /**
     * 生成密码
     * @param $inputPassword
     * @param $salt
     * @return string
     */
    public static function makePassword($inputPassword, $salt)
    {
        return crypt($inputPassword, $salt);
    }

    /**
     * 生成盐
     * @return string
     */
    public static function getSalt()
    {
        return bcrypt(microtime(true) . mt_rand(0, 10000));
    }

    /**
     * @return string 随机六位数字
     */
    public static function makeCaptcha()
    {
        $pool = '0123456789';
        $word = '';
        for ($i = 0; $i < 6; $i++) {
            $word .= substr($pool, mt_rand(0, strlen($pool) - 1), 1);
        }
        return $word;
    }


}