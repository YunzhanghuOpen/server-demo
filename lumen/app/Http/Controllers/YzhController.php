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
use App\Models\Notice;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use GuzzleHttp\Client;

class YzhController
{

    public $baseUrl = '';
    public $appId = '';
    public $appKey = '';

    const COMPONENT_AUTH = 'auth';
    const COMPONENT_CARD = 'card';
    const COMPONENT_INVEST = 'invest';


    private function getPartner()
    {
        return $this->appId;
    }

    private function getKey()
    {
        return $this->appKey;
    }

    public function __construct()
    {
        $this->baseUrl = config('yzh.baseUrl');
        $this->appId = config('yzh.appId');
        $this->appKey = config('yzh.appKey');
    }

    /**
     * H5接入方式的 URL
     *
     * @param $user
     * @param string $component
     * @return string
     */
    private function getH5Url($user, $component = '')
    {
        // sign
        $queryData = [
            'partner' => $this->getPartner(),
            'mobile' => $user->mobile,
            'timestamp' => time(),
            'user_id' => $user->uid,
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
     * @api {get} /yzh/{uid}/h5/main 云账户固定入口
     * @apiName GetH5Main
     * @apiGroup HTML5
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
     * @api {get} /yzh/{uid}/h5/component/auth 组件:实名认证
     * @apiName GetH5Auth
     * @apiGroup HTML5
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
     * @api {get} /yzh/{uid}/h5/component/card 组件:绑定安全卡
     * @apiName GetH5Card
     * @apiGroup HTML5
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
     * @api {get} /yzh/{uid}/h5/component/card 组件:投资
     * @apiName GetH5Invest
     * @apiGroup HTML5
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
     * @api {post} /yzh/api/real-name-request 实名认证请求
     * @apiName GetAPIRealNameRequest
     * @apiGroup API
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
            'user_id' => $user->uid,
            'sign' => '',

            'RealName' => $input['RealName'],
            'IDCard' => $input['IDCard'],
        ];

        return $this->postYzhAPI($queryData, '/api/real-name-request');

    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @api {post} /yzh/api/real-name-confirm 实名认证确认
     * @apiName GetAPIRealNameConfirm
     * @apiGroup API
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
            'user_id' => $user->uid,
            'sign' => '',

            'RealName' => $input['RealName'],
            'IDCard' => $input['IDCard'],
            'Captcha' => $input['Captcha'],
            'Ref' => $input['Ref'],
        ];

        return $this->postYzhAPI($queryData, '/api/real-name-confirm');
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @api {post} /yzh/api/bind-card-request 绑定银行卡请求
     * @apiName GetAPIBindCardRequest
     * @apiGroup API
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
            'user_id' => $user->uid,
            'sign' => '',

            'RealName' => $input['RealName'],
            'IDCard' => $input['IDCard'],
            'BankMobile' => $input['BankMobile'],
            'CardNo' => $input['CardNo'],
            'SubBankNo' => $input['SubBankNo'],
        ];

        return $this->postYzhAPI($queryData, '/api/bind-card-request');
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @api {post} /yzh/api/bind-card-confirm 绑定银行卡确认
     * @apiName GetAPIBindCardConfirm
     * @apiGroup API
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
            'user_id' => $user->uid,
            'sign' => '',

            'RealName' => $input['RealName'],
            'IDCard' => $input['IDCard'],
            'BankMobile' => $input['BankMobile'],
            'CardNo' => $input['CardNo'],
            'SubBankNo' => $input['SubBankNo'],
            'Captcha' => $input['Captcha'],
            'Ref' => $input['Ref'],
        ];

        return $this->postYzhAPI($queryData, '/api/bind-card-confirm');
    }


    /*
     * Notice
     */


    /**
     * @param Request $request
     * @return Response
     *
     * @api {get} /yzh/notice/real-name 实名认证通知
     * @apiName GetNoticeRealName
     * @apiGroup Notice
     *
     * @apiParam {String} UserId 用户ID
     * @apiParam {String} RealName 真实姓名
     * @apiParam {String} IDCard 身份证号
     * @apiParam {String} IsNew 是否是新用户
     *
     * @apiSuccessExample Success-Response:
     * success
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
        if (!HMAC::compare($input, $this->getKey(), $input['sign'])) {

            Logger::AuthorizationError('@YzhController noticeRealName, sign error');

            return response('sign error');
        }

        // duplicate
        $row = Notice::where('ref', $input['Ref'])->first();
        if ($row) {
            Logger::AuthorizationInfo('@YzhController noticeRealName, duplicate, return success');
            return response('success');
        }

        // data 记录通知数据,跟据业务再做处理
        $notice = new Notice();
        $notice->ref = $input['Ref'];
        $notice->type = Notice::T_REAL_NAME;
        $notice->uid = $input['UserId'];
        $notice->result = json_encode($input);
        $notice->save();

        // response

        Logger::AuthorizationInfo('@YzhController noticeRealName, received, return success');

        return response('success');
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @api {get} /yzh/notice/bankcard 绑定安全卡通知
     * @apiName GetNoticeBankcard
     * @apiGroup Notice
     *
     * @apiParam {String} UserId 用户ID
     * @apiParam {String} CardNo 银行卡号
     * @apiParam {String} BankBranch 支行名称
     * @apiParam {String} BankMobile 银行卡预留手机号
     * @apiParam {String} RealName 用户真实姓名
     * @apiParam {String} IDCard 身份证号
     * @apiParam {String} IsFirstCard 率先绑定的第一张安全卡
     * @apiParam {String} Ref 消息流水号
     *
     * @apiSuccessExample Success-Response:
     * success
     */
    public function noticeBankcard(Request $request)
    {
        $input = $request->all();

        Logger::AuthorizationInfo('@YzhController noticeBankcard, start', $input);

        if (empty($input['sign'])) {

            Logger::AuthorizationError('@YzhController noticeBankcard, no sign');

            return response('no sign');
        }

        // sign
        if (!HMAC::compare($input, $this->getKey(), $input['sign'])) {

            Logger::AuthorizationError('@YzhController noticeBankcard, sign error');

            return response('sign error');
        }

        // duplicate
        $row = Notice::where('ref', $input['Ref'])->first();
        if ($row) {
            Logger::AuthorizationInfo('@YzhController noticeBankcard, duplicate, return success');
            return response('success');
        }

        // data 记录通知数据,跟据业务再做处理
        $notice = new Notice();
        $notice->ref = $input['Ref'];
        $notice->type = Notice::T_BANKCARD;
        $notice->uid = $input['UserId'];
        $notice->result = json_encode($input);
        $notice->save();

        // response

        Logger::AuthorizationInfo('@YzhController noticeBankcard, received, return success');

        return response('success');
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @api {get} /yzh/notice/investment 投资结果通知
     * @apiName GetNoticeInvestment
     * @apiGroup Notice
     *
     * @apiParam {String} UserId 用户ID
     * @apiParam {String} ProductName 理财产品名称
     * @apiParam {String} Amount 投资金额
     * @apiParam {String} DateTime 投资时间
     * @apiParam {String} Ref 消息流水号
     *
     * @apiSuccessExample Success-Response:
     * success
     */
    public function noticeInvestment(Request $request)
    {
        $input = $request->all();

        Logger::AuthorizationInfo('@YzhController noticeInvestment, start', $input);

        if (empty($input['sign'])) {

            Logger::AuthorizationError('@YzhController noticeInvestment, no sign');

            return response('no sign');
        }

        // sign
        if (!HMAC::compare($input, $this->getKey(), $input['sign'])) {

            Logger::AuthorizationError('@YzhController noticeInvestment, sign error');

            return response('sign error');
        }

        // duplicate
        $row = Notice::where('ref', $input['Ref'])->first();
        if ($row) {
            Logger::AuthorizationInfo('@YzhController noticeInvestment, duplicate, return success');
            return response('success');
        }

        // data 记录通知数据,跟据业务再做处理
        $notice = new Notice();
        $notice->ref = $input['Ref'];
        $notice->type = Notice::T_INVEST;
        $notice->uid = $input['UserId'];
        $notice->result = json_encode($input);
        $notice->save();

        // response

        Logger::AuthorizationInfo('@YzhController noticeInvestment, received, return success');

        return response('success');
    }

    /**
     * 请求云账户 API 服务
     *
     * @param $queryData
     * @return \Symfony\Component\HttpFoundation\Response
     */
    private function postYzhAPI($queryData, $pathName)
    {

        Logger::sysInfo('@YzhController postYzhAPI, begin', $queryData);

        $sign = HMAC::calculate($queryData, $this->getKey());
        $queryData['sign'] = $sign;

        $url = sprintf('%s%s', $this->baseUrl, $pathName);

        Logger::sysInfo('@YzhController postYzhAPI, url:' . $url);

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
            } else {

                Logger::sysInfo('@YzhController postYzhAPI, end, error', $result);

                return Response::error(FaultCode::YZH_RESP_ERR, $result);
            }
        } else {

            Logger::sysInfo('@YzhController postYzhAPI, end, error, no response content');

            return Response::error(FaultCode::YZH_RESP_EMPTY);
        }
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @api {post} /yzh/account/remove 解除与商户绑定关系
     * @apiName PostToolRemove
     * @apiGroup Tool
     *
     * @apiParam {String} partner 商户编号
     * @apiParam {String} user_id 用户ID
     *
     * @apiSuccessExample Success-Response:
     * HTTP/1.1 200 OK
     * {
     *   "ok": true
     * }
     *
     */
    public function remove(Request $request) {

        $input = $request->all();

        Logger::sysInfo('@YzhController remove, begin, clear', $input);

        $validator = Validator::make($input, [
            'partner' => 'required',
            'user_id' => 'required',
        ]);
        if ($validator->fails()) {
            $errors = $validator->errors()->toArray();
            return Response::error(FaultCode::PARAMS_ERROR, $errors);
        }

        if (!in_array($input['partner'], config('Flush'))) {
            return Response::error(FaultCode::ACCESS_DENIED);
        }

        $results = DB::connection('trial')->select('select I_USER_ID from dealer_user_relation where CH_DEALER_USER_ID = :id and CH_DEALER_CODE = :code',
            [
                'id' => $input['user_id'],
                'code' => $input['partner']
            ]
        );

        if (empty($results)) {
            return Response::error(FaultCode::USER_NOT_EXIST);
        }


        $log = [];
        foreach($results as $row) {
            $id = $row->I_USER_ID;
            $log[] = DB::connection('trial')->delete('delete from dealer_user_relation where I_USER_ID = :id', ['id' => $id]);
        }

        Logger::sysInfo('@YzhController remove, end, log', $log);

        return Response::result(['ok' => true]);

    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @api {post} /yzh/account/clear 彻底删除该商户下该用户
     * @apiName PostToolClear
     * @apiGroup Tool
     *
     * @apiParam {String} partner 商户编号
     * @apiParam {String} user_id 用户ID
     *
     * @apiSuccessExample Success-Response:
     * HTTP/1.1 200 OK
     * {
     *   "ok": true
     * }
     *
     */
    public function clear(Request $request) {

        $input = $request->all();

        Logger::sysInfo('@YzhController clear, begin, clear', $input);

        $validator = Validator::make($input, [
            'partner' => 'required',
            'user_id' => 'required',
        ]);
        if ($validator->fails()) {
            $errors = $validator->errors()->toArray();
            return Response::error(FaultCode::PARAMS_ERROR, $errors);
        }

        if (!in_array($input['partner'], config('Flush'))) {
            return Response::error(FaultCode::ACCESS_DENIED);
        }

        $results = DB::connection('trial')->select('select I_USER_ID from dealer_user_relation where CH_DEALER_USER_ID = :id and CH_DEALER_CODE = :code',
            [
                'id' => $input['user_id'],
                'code' => $input['partner']
            ]
        );

        if (empty($results)) {
            return Response::error(FaultCode::USER_NOT_EXIST);
        }


        $log = [];
        foreach($results as $row) {
            $id = $row->I_USER_ID;
            $log[] = DB::connection('trial')->delete('delete from users where I_USER_ID = :id', ['id' => $id]);
            $log[] = DB::connection('trial')->delete('delete from user_account where I_USER_ID = :id', ['id' => $id]);
            $log[] = DB::connection('trial')->delete('delete from user_bankcards where I_USER_ID = :id', ['id' => $id]);
            $log[] = DB::connection('trial')->delete('delete from dealer_user_relation where I_USER_ID = :id', ['id' => $id]);
        }

        Logger::sysInfo('@YzhController clear, end, log', $log);

        return Response::result(['ok' => true]);

    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @api {post} /yzh/account/flush 彻底删除该商户下所有用户
     * @apiName PostToolFlush
     * @apiGroup Tool
     *
     * @apiParam {String} partner 商户编号
     *
     * @apiSuccessExample Success-Response:
     * HTTP/1.1 200 OK
     * {
     *   "ok": true
     * }
     *
     */
    public function flush(Request $request) {

        $input = $request->all();

        Logger::sysInfo('@YzhController flush, begin, clear', $input);

        $validator = Validator::make($input, [
            'partner' => 'required',
        ]);
        if ($validator->fails()) {
            $errors = $validator->errors()->toArray();
            return Response::error(FaultCode::PARAMS_ERROR, $errors);
        }

        if (!in_array($input['partner'], config('Flush'))) {
            return Response::error(FaultCode::ACCESS_DENIED);
        }

        $results = DB::connection('trial')->select('select I_USER_ID from dealer_user_relation where CH_DEALER_CODE = :code', ['code' => $input['partner']]);

        if (empty($results)) {
            return Response::result(['ok' => true]);
        }

        $log = [];
        foreach($results as $row) {
            $id = $row->I_USER_ID;
            $log[] = DB::connection('trial')->delete('delete from users where I_USER_ID = :id', ['id' => $id]);
            $log[] = DB::connection('trial')->delete('delete from user_account where I_USER_ID = :id', ['id' => $id]);
            $log[] = DB::connection('trial')->delete('delete from user_bankcards where I_USER_ID = :id', ['id' => $id]);
            $log[] = DB::connection('trial')->delete('delete from dealer_user_relation where I_USER_ID = :id', ['id' => $id]);
        }

        Logger::sysInfo('@YzhController flush, end, log', $log);

        return Response::result(['ok' => true]);


    }


}