<?php
/**
 * Created by PhpStorm.
 * User: philiptang
 * Date: 12/30/15
 * Time: 17:01
 */

namespace App\Http\Controllers;


use App\Helpers\CommonHelper;
use App\Helpers\FaultCode;
use App\Helpers\Response;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class UserController
{

    /**
     * 注册
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @api {post} /user/register 注册
     * @apiName PostRegister
     * @apiGroup User
     *
     * @apiParam {String} mobile 手机号
     * @apiParam {String} password 登录密码
     *
     * @apiSuccessExample Success-Response:
     *     {
     *         ok: true
     *     }
     */
    public function register(Request $request)
    {
        // 拒绝注册, 仅限登录
        return Response::error(FaultCode::RRJ);

        // input
        $input = $request->all();

        // validate
        $validator = Validator::make($input, [
            'mobile' => 'required|size:11',
            'password' => 'required|min:6',
        ]);
        if ($validator->fails()) {
            $errors = $validator->errors()->toArray();
            return Response::error(FaultCode::PARAMS_ERROR, $errors);
        }

        // duplicate
        $user = User::where('mobile', $input['mobile'])->first();
        if ($user) {
            return Response::error(FaultCode::USER_EXIST);
        }

        // new
        $user = new User();
        $user->mobile = $input['mobile'];
        $user->salt = CommonHelper::getSalt();
        $user->password = CommonHelper::makePassword($input['password'], $user->salt);
        $user->save();

        return Response::result([
            'ok' => true
        ]);
    }

    /**
     * 登录
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @api {post} /user/login 登录
     * @apiName PostLogin
     * @apiGroup User
     *
     * @apiParam {String} mobile 手机号
     * @apiParam {String} password 登录密码
     *
     * @apiSuccessExample Success-Response:
     *     {
     *         ok: true
     *     }
     */
    public function login(Request $request)
    {
        // input
        $input = $request->all();

        // validate
        $validator = Validator::make($input, [
            'mobile' => 'required|size:11',
            'password' => 'required|min:6',
        ]);
        if ($validator->fails()) {
            $errors = $validator->errors()->toArray();
            return Response::error(FaultCode::PARAMS_ERROR, $errors);
        }

        // grab
        $user = User::where('mobile', $input['mobile'])->first();
        if (is_null($user)) {
            return Response::error(FaultCode::USER_NOT_EXIST);
        }

        // password
        if (!CommonHelper::comparePassword($input['password'], $user->password, $user->salt)) {
            return Response::error(FaultCode::PASSWORD_NOT_MATCH);
        }

        return Response::result([
            'ok' => true
        ]);
    }

}