<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

$app->get('/', function () use ($app) {
    return response('welcome');
});

/**
 * 注册/登录
 */
$app->post('/user/register', 'UserController@register');
$app->post('/user/login', 'UserController@login');


/**
 * 云账户接入
 */


// HTML5 方式

$app->get('/yzh/{uid}/h5/main', 'YzhController@h5Main');
$app->get('/yzh/h5/main/guest', 'YzhController@h5MainGuest');
$app->get('/yzh/{uid}/h5/component/auth', 'YzhController@componentAuth');
$app->get('/yzh/{uid}/h5/component/card', 'YzhController@componentCard');
$app->get('/yzh/{uid}/h5/component/invest', 'YzhController@componentInvest');

// API 方式

$app->post('/yzh/api/real-name-request', 'YzhController@apiRealNameRequest');
$app->post('/yzh/api/real-name-confirm', 'YzhController@apiRealNameConfirm');
$app->post('/yzh/api/bind-card-request', 'YzhController@apiBindCardRequest');
$app->post('/yzh/api/bind-card-confirm', 'YzhController@apiBindCardConfirm');


/**
 * 接收云账户通知
 */
$app->get('/yzh/notice/real-name', 'YzhController@noticeRealName');
$app->get('/yzh/notice/bankcard', 'YzhController@noticeBankcard');
$app->get('/yzh/notice/investment', 'YzhController@noticeInvestment');

/**
 * 清除云账户账号
 */
$app->post('/yzh/account/remove', 'YzhController@remove');
$app->post('/yzh/account/clear', 'YzhController@clear');
$app->post('/yzh/account/flush', 'YzhController@flush');


/**
 * server 运行状态查询
 */
$app->get('/status', 'StatusController@index');



