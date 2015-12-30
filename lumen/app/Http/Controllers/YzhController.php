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