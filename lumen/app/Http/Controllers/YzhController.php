<?php
/**
 * Created by PhpStorm.
 * User: philiptang
 * Date: 12/30/15
 * Time: 17:07
 */

namespace App\Http\Controllers;


use App\Helpers\FaultCode;
use App\Helpers\HMAC;
use App\Helpers\Logger;
use App\Helpers\Response;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use GuzzleHttp\Client;

class YzhController
{

    public $baseUrl = 'https://trial.yunzhanghu.com';

    const COMPONENT_AUTH = 'auth';
    const COMPONENT_CARD = 'card';
    const COMPONENT_INVEST = 'invest';


    private function getPartner() {
        return env('YZH_APP_ID');
    }

    private function getKey() {
        return env('YZH_APP_KEY');
    }

    /**
     * H5接入方式的 URL
     *
     * @param $user
     * @param string $component
     * @return string
     */
    private function getH5Url($user, $component = '') {
        // sign
        $queryData = [
            'partner' => $this->getPartner(),
            'mobile' => $user->mobile,
            'timestamp' => time(),
            'user_id' => $user->id,
            'sign' => '',
        ];
        // 是否使用组件
        if ($component) {
            $queryData['component'] = $component;
        }

        $sign = HMAC::calculate($queryData, $this->getKey());
        $queryData['sign'] = $sign;

        $queryParams = http_build_query($queryData);

        // url
        return sprintf('%s/autoLogin?%s', $this->baseUrl, $queryParams);
    }

    /*
     * HTML5
     */

    // fixed entry

    /**
     * @param $uid
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @api {get} /yzh/{uid}/h5/main H5 -- 云账户固定入口
     * @apiName GetH5Main
     * @apiGroup YZH
     *
     * @apiParam {String} uid 用户编号
     *
     * @apiSuccessExample Success-Response:
     *     {
     *         url: https://trial.yunzhanghu.com/autoLogin?partner=123456&mobile=18612341234&timestamp=1451495534&user_id=1&sign=5f1dc15d13779129f4a89a1e43f43e228a5f4d30295f8d4d3dac3cbe4461e1b6
     *     }
     */
    public function h5Main($uid)
    {
        // grab
        $user = User::find($uid);
        if (is_null($user)) {
            return Response::error(FaultCode::USER_NOT_EXIST);
        }

        $url = $this->getH5Url($user);

        return Response::result([
            'url' => $url
        ]);
    }

    // component

    /**
     * @param $uid
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @api {get} /yzh/{uid}/h5/component/auth H5 -- 组件:实名认证
     * @apiName GetH5Auth
     * @apiGroup YZH
     *
     * @apiParam {String} uid 用户编号
     *
     * @apiSuccessExample Success-Response:
     *     {
     *         url: https://trial.yunzhanghu.com/autoLogin?partner=123456&mobile=18612341234&timestamp=1451495691&user_id=1&sign=fd63ca45a06676f92326106413f830c9dc36c6ce94b410593a2bec51cbab31fd&component=auth
     *     }
     */
    public function componentAuth($uid)
    {
        // grab
        $user = User::find($uid);
        if (is_null($user)) {
            return Response::error(FaultCode::USER_NOT_EXIST);
        }

        // url
        $url = $this->getH5Url($user, self::COMPONENT_AUTH);

        return Response::result([
            'url' => $url
        ]);
    }

    /**
     * @param $uid
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @api {get} /yzh/{uid}/h5/component/card H5 -- 组件:绑定安全卡
     * @apiName GetH5Card
     * @apiGroup YZH
     *
     * @apiParam {String} uid 用户编号
     *
     * @apiSuccessExample Success-Response:
     *     {
     *         url: https://trial.yunzhanghu.com/autoLogin?partner=123456&mobile=18612341234&timestamp=1451495691&user_id=1&sign=fd63ca45a06676f92326106413f830c9dc36c6ce94b410593a2bec51cbab31fd&component=card
     *     }
     */
    public function componentCard($uid)
    {
        // grab
        $user = User::find($uid);
        if (is_null($user)) {
            return Response::error(FaultCode::USER_NOT_EXIST);
        }

        // url
        $url = $this->getH5Url($user, self::COMPONENT_CARD);

        return Response::result([
            'url' => $url
        ]);
    }

    /**
     * @param $uid
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @api {get} /yzh/{uid}/h5/component/card H5 -- 组件:投资
     * @apiName GetH5Invest
     * @apiGroup YZH
     *
     * @apiParam {String} uid 用户编号
     *
     * @apiSuccessExample Success-Response:
     *     {
     *         url: https://trial.yunzhanghu.com/autoLogin?partner=123456&mobile=18612341234&timestamp=1451495691&user_id=1&sign=fd63ca45a06676f92326106413f830c9dc36c6ce94b410593a2bec51cbab31fd&component=invest
     *     }
     */
    public function componentInvest($uid)
    {
        // grab
        $user = User::find($uid);
        if (is_null($user)) {
            return Response::error(FaultCode::USER_NOT_EXIST);
        }

        // url
        $url = $this->getH5Url($user, self::COMPONENT_INVEST);

        return Response::result([
            'url' => $url
        ]);
    }

    // API 方式

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @api {get} /yzh/api/real-name-request API -- 实名认证请求
     * @apiName GetAPIRealNameRequest
     * @apiGroup YZH
     *
     * @apiParam {String} uid 用户编号
     * @apiParam {String} RealName 真实姓名
     * @apiParam {String} IDCard 身份证号
     *
     * @apiSuccessExample Success-Response:
     *     {
     *         Ref: 151120185800437765
     *     }
     */
    public function apiRealNameRequest(Request $request)
    {
        $input = $request->all();
        $validator = Validator::make($input, [
            'uid' => 'required',
            'RealName' => 'required',
            'IDCard' => 'required',
        ]);
        if ($validator->fails()) {
            $errors = $validator->errors()->toArray();
            return Response::error(FaultCode::PARAMS_ERROR, $errors);
        }

        // grab
        $user = User::find($input['uid']);
        if (is_null($user)) {
            return Response::error(FaultCode::USER_NOT_EXIST);
        }

        // request
        $queryData = [
            'partner' => $this->getPartner(),
            'mobile' => $user->mobile,
            'timestamp' => time(),
            'user_id' => $user->id,
            'sign' => '',

            'RealName' => $input['RealName'],
            'IDCard' => $input['IDCard'],
        ];

        return $this->postYzhAPI($queryData);

    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @api {get} /yzh/api/real-name-request API -- 实名认证确认
     * @apiName GetAPIRealNameConfirm
     * @apiGroup YZH
     *
     * @apiParam {String} uid 用户编号
     * @apiParam {String} RealName 真实姓名
     * @apiParam {String} IDCard 身份证号
     * @apiParam {String} Captcha 验证码
     * @apiParam {String} Ref 流水号
     *
     * @apiSuccessExample Success-Response:
     *     {
     *         Realname: 张三
     *         IDCard: 360222195106025328
     *         Mobile: 18612341234
     *         Gender: 女
     *         IsNew: 1
     *     }
     */
    public function apiRealNameConfirm(Request $request)
    {
        $input = $request->all();
        $validator = Validator::make($input, [
            'uid' => 'required',
            'RealName' => 'required',
            'IDCard' => 'required',
            'Captcha' => 'required',
            'Ref' => 'required',
        ]);
        if ($validator->fails()) {
            $errors = $validator->errors()->toArray();
            return Response::error(FaultCode::PARAMS_ERROR, $errors);
        }

        // grab
        $user = User::find($input['uid']);
        if (is_null($user)) {
            return Response::error(FaultCode::USER_NOT_EXIST);
        }

        // request
        $queryData = [
            'partner' => $this->getPartner(),
            'mobile' => $user->mobile,
            'timestamp' => time(),
            'user_id' => $user->id,
            'sign' => '',

            'RealName' => $input['RealName'],
            'IDCard' => $input['IDCard'],
            'Captcha' => $input['Captcha'],
            'Ref' => $input['Ref'],
        ];

        return $this->postYzhAPI($queryData);
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @api {get} /yzh/api/bind-card-request API -- 绑定银行卡请求
     * @apiName GetAPIBindCardRequest
     * @apiGroup YZH
     *
     * @apiParam {String} uid 用户编号
     * @apiParam {String} RealName 真实姓名
     * @apiParam {String} IDCard 身份证号
     * @apiParam {String} BankMobile 开户手机号
     * @apiParam {String} CardNo 借记卡号
     * @apiParam {String} SubBankNo 联行号
     *
     * @apiSuccessExample Success-Response:
     *     {
     *         Ref: 151120185800437765
     *     }
     */
    public function apiBindCardRequest(Request $request)
    {
        $input = $request->all();
        $validator = Validator::make($input, [
            'uid' => 'required',
            'RealName' => 'required',
            'IDCard' => 'required',
            'BankMobile' => 'required',
            'CardNo' => 'required',
            'SubBankNo' => 'required',
        ]);
        if ($validator->fails()) {
            $errors = $validator->errors()->toArray();
            return Response::error(FaultCode::PARAMS_ERROR, $errors);
        }

        // grab
        $user = User::find($input['uid']);
        if (is_null($user)) {
            return Response::error(FaultCode::USER_NOT_EXIST);
        }

        // request
        $queryData = [
            'partner' => $this->getPartner(),
            'mobile' => $user->mobile,
            'timestamp' => time(),
            'user_id' => $user->id,
            'sign' => '',

            'RealName' => $input['RealName'],
            'IDCard' => $input['IDCard'],
            'BankMobile' => $input['BankMobile'],
            'CardNo' => $input['CardNo'],
            'SubBankNo' => $input['SubBankNo'],
        ];

        return $this->postYzhAPI($queryData);
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @api {get} /yzh/api/bind-card-confirm API -- 绑定银行卡确认
     * @apiName GetAPIBindCardConfirm
     * @apiGroup YZH
     *
     * @apiParam {String} uid 用户编号
     * @apiParam {String} RealName 真实姓名
     * @apiParam {String} IDCard 身份证号
     * @apiParam {String} BankMobile 开户手机号
     * @apiParam {String} CardNo 银行卡号
     * @apiParam {String} SubBankNo 联行号
     * @apiParam {String} Captcha 验证码
     * @apiParam {String} Ref 流水号
     *
     * @apiSuccessExample Success-Response:
     *     {
     *     }
     */
    public function apiBindCardConfirm(Request $request)
    {
        $input = $request->all();
        $validator = Validator::make($input, [
            'uid' => 'required',
            'RealName' => 'required',
            'IDCard' => 'required',
            'BankMobile' => 'required',
            'CardNo' => 'required',
            'SubBankNo' => 'required',
            'Captcha' => 'required',
            'Ref' => 'required',
        ]);
        if ($validator->fails()) {
            $errors = $validator->errors()->toArray();
            return Response::error(FaultCode::PARAMS_ERROR, $errors);
        }

        // grab
        $user = User::find($input['uid']);
        if (is_null($user)) {
            return Response::error(FaultCode::USER_NOT_EXIST);
        }

        // request
        $queryData = [
            'partner' => $this->getPartner(),
            'mobile' => $user->mobile,
            'timestamp' => time(),
            'user_id' => $user->id,
            'sign' => '',

            'RealName' => $input['RealName'],
            'IDCard' => $input['IDCard'],
            'BankMobile' => $input['BankMobile'],
            'CardNo' => $input['CardNo'],
            'SubBankNo' => $input['SubBankNo'],
            'Captcha' => $input['Captcha'],
            'Ref' => $input['Ref'],
        ];

        return $this->postYzhAPI($queryData);
    }


    /*
     * Notice
     */


    public function noticeRealName(Request $request)
    {
        $input = $request->all();

        Logger::AuthorizationInfo('@YzhController noticeRealName, start', $input);

        if (empty($input['sign'])) {

            Logger::AuthorizationError('@YzhController noticeRealName, no sign');

            return response('no sign');
        }

        // sign
        if (HMAC::compare($input, $this->getKey(), $input['sign'])) {

            Logger::AuthorizationError('@YzhController noticeRealName, sign error');

            return response('sign error');
        }

        // duplicate TODO


        // data

        // response
    }

    public function noticeBankcard()
    {
        return '';
    }

    public function noticeInvestment()
    {
        return '';
    }

    /**
     * 请求云账户 API 服务
     *
     * @param $queryData
     * @return \Symfony\Component\HttpFoundation\Response
     */
    private function postYzhAPI($queryData) {

        Logger::sysInfo('@YzhController postYzhAPI, begin', $queryData);

        $sign = HMAC::calculate($queryData, $this->getKey());
        $queryData['sign'] = $sign;

        $url = sprintf('%s/api/real-name-request', $this->baseUrl);
        $client = new Client([
            'timeout' => 10 * 60
        ]);
        $response = $client->request('post', $url, ['form_params' => $queryData]);
        $contents = $response->getBody()->getContents();

        Logger::sysInfo('@YzhController postYzhAPI, response: ' . $contents);

        if ($contents) {
            $result = json_decode($contents, true);


            if (strval($result['code']) === '0000') {

                Logger::sysInfo('@YzhController postYzhAPI, end, successful');

                return Response::result($result['data']);
            }
            else {

                Logger::sysInfo('@YzhController postYzhAPI, end, error', $result);

                return Response::error(FaultCode::YZH_RESP_ERR, $result);
            }
        }
        else {

            Logger::sysInfo('@YzhController postYzhAPI, end, error, no response content');

            return Response::error(FaultCode::YZH_RESP_EMPTY);
        }
    }


}