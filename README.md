# server-demo

![yun](https://www.yunzhanghu.com/img/logo.png)

## 用途

本项目为云账户产品下虚拟商户 Server，模拟并支持云账户接入。

## 功能

1. 注册、登录
1. 创建云账户入口链接
1. 接收【实名认证】通知
1. 接收【绑定安全卡】通知
1. 接收【投资达成】通知


## 接口暴露


```php

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
          
```
