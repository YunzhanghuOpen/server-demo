# server-demo

![yun](http://www.yunzhanghu.com/img/logo.png)

## 用途

本项目为云账户产品下虚拟商户 Server，模拟并支持云账户接入。

[接口文档](yunzhanghu.com)

## 功能

**App 本身**

* 注册
* 登录

**云账户对接**

HTML5 方式

1. 固定入口
2. 组件：实名认证
3. 组件：绑定安全卡
4. 组件：投资达成

API 方式

1. 实名认证请求（发送验证码）
1. 实名认证确认（校验验证码并认证）
1. 绑定安全卡请求（发送验证码）
1. 绑定安全卡确认（校验验证码并绑定）

## 部署

`vim lumen/.env`

```diff
-YZH_APP_ID=null
-YZH_APP_KEY=null
+YZH_APP_ID=xxxxxx
+YZH_APP_KEY=xxxxxxxxxxxxxxxxxxxxxx
```
