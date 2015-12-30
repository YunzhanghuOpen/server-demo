# server-demo

![yun](https://www.yunzhanghu.com/img/logo.png)

## 用途

本项目为云账户产品下虚拟商户 Server，模拟并支持云账户接入。

## 功能

### App 本身
* 注册
* 登录

### 云账户对接

#### HTML5 方式

1. 固定入口
2. 组件：实名认证
3. 组件：绑定安全卡
4. 组件：投资达成

#### API 方式

1. 实名认证请求（发送验证码）
2. 实名认证确认（校验验证码并认证）


1. 绑定安全卡请求（发送验证码）
2. 绑定安全卡确认（校验验证码并绑定）


## 接口


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
